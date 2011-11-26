<?php
	class MyUser extends User{
		
		public function __construct(){
			
		}
		
		private function getLdap(){
			
		}
		
		static public function getProperties($prop=null){
			if (Session::is_set('ldap_user')){
				$ret=Session::get('ldap_user');
				
				if ($prop==null) return $ret; 
				else if ($ret!=null && $ret->{$prop}!=null) return $ret->{$prop};

			}
			return null;
		}
		

		static public function setProperties($props){
			Session::set('ldap_user',$props);
		}


		static public function rolsEspecifics($type,$props=null){
			
			if ($props===null && Session::is_set('ldap_user')) $ret=Session::get('ldap_user');
			else if ($props!==null) $ret=$props;
			else return null;

			$categ=explode(';',$ret->Titulacio);
			switch($type){
				case AduserPeer::CATEG_PROFESSIONAL_METGE:			
				case AduserPeer::CATEG_PROFESSIONAL_INFERMERA:		
				case AduserPeer::CATEG_PROFESSIONAL_ADMINISTRATIU: return ($type==AduserPeer::getCategoria($categ[0]));
				case AduserPeer::CATEG_ES_PRIMARIA: return $ret->UsuariPrimaria;
				case AduserPeer::CATEG_ES_HOSPITAL: return $ret->UsuariHospital;
			}
		}

		static public function getGroups(){
			
			if (Session::is_set('ldap_user_groups')) return Session::get('ldap_user_groups');
			return array();
		}

		
		static public function setGroups($idcategs){
			Session::set('ldap_user_groups',$idcategs);
		}

		static private $hashLogin;
		static public function getHash(){
			return self::$hashLogin;
		}

		/// devuelve 0 si todo ha ido bien. devuelve valor negativo para cada error
		static public function authenticateUser($login,$password,$sessionLogin=null){
			
			try{
				$peer=new AduserPeer();
				$aduser=$peer->retrieveByPK($login);
				/// el usuario debe cambiar el password en la siguiente sesion
				// Se lo cambiamos a '***' para que pueda entrar
				//echo _r($aduser);
				if ($aduser===null) return -4;

				if ($aduser->DataUltimPassword===null) {
					//$peer->changePassword($login,'***');
					return -3;
				}

				/// autentifica usuario, tambien mira si la cuenta esta deshabilitada
				$validationOK=$peer->authenticate($login,$password);
				
				//echo _r($login)._r($password)._r($validationOK,true);die();
				if ($validationOK) {
					if ($sessionLogin!==null){
						// si pasamos sessionLogin es que queremos validarnos como ese usuario
						$aduser=$peer->retrieveByPK($sessionLogin);
					}
					// construimos info de centros accesibles por aplicacion
					$aduser->buildInfo();

					// el usuario existe, pero no sabemos si su cuenta esta deshabilitada
					// si lo está no dejamos autenticar!!!!
					if ($aduser===null || $aduser->isAccountDisabled()) return -2;

					/// guardamos la fecha de login
					$aduser2=new Aduser();
					$aduser2->Nif=$login;
					/// guarda la hora GMT+0 porque en el ldap la hora de creación se guarda tambien así.
					$aduser2->UltimAccesIntra=gmdate("d/m/Y H:i:s", time()); //strftime("%d/%m/%Y %H:%M:%S",time());
					$aduser2->save();

					/// ponemos todas las propiedades en sesion
					self::setProperties($aduser);
					return 0;
				}
			}catch(CubeException $e){ return $e->getMessage(); }
			return -1;
		}
		
		static public function removeAllCredentials(){
			//Session::un_set();
			Session::removeAllCredentials();
		}

		static public function isLogged(){
			return Session::hasCredential('is_logged');
		}

		
		static public function executeLogout($login=null){
			self::removeAllCredentials();
			Session::un_set();
			Session::set('first_login',false);	// validacion en otras intranets!
		}

		
		static public function executeLogin($login=null,$removeAllCredentials=true){
			$props=Session::get('ldap_user');
			if ($login===null) $login=$props->Nif;
			
			if ($removeAllCredentials) self::removeAllCredentials();
			Session::addCredential('is_logged');
			//echo _r(Session::getCredentials());
			//if (!Session::is_set('ldap_user_groups'))
			{
				$idcategs=array();

				//self::$ldap->storeLDAPGroups($login,$idcategs);
				$peer=new AdgroupuserPeer();
				$peer->groupsByUser($login,$idcategs,array('samaccountname','whencreated','memberof'));
				//echo 'aaaaaa'._r($idcategs,true);die();
				//// TODO: mirar los grupos que "no ha visto" el usuario (mirar svn anteriores!!!)
				
				$categ=explode(';',$props->Titulacio);

				if ($props->UsuariExtern) $filter='nus';
				else if ($props->OrdinadorPrimaria || $props->OrdinadorHospital) $filter='camptgn';
				else $filter='xarxaics';
				
				$peer2=new AdouPeer();
				$grupos_visibles=$peer2->filterVisibleGroups(AduserPeer::getCategoria($categ[0]),$filter);

				//// actualizamos perfil con los grupos que tocan y la fecha de acceso
				/// OJO: si no puede ver ninguna aplicacion no se actualizara la fecha del acceso
				// (se supone que no va a pasar nunca, que almenos veran algun icono!)
				
				if (count($grupos_visibles)>0 || count($idcategs)>0){

					$error=false;
					$a=new Aduser();
					$a->Nif=$props->Nif;
					
					/// si no tiene nada en el perfil, le ponemos los grupos visibles
					if ($props->Aplicacions===null || trim($props->Aplicacions)==''){
						/// añadir a grupos visibles, las que tiene asignadas (se supone que las hará servir más que otras, no?)
						$paraPerfil=AduserPeer::createProfile($grupos_visibles,array_keys($idcategs));
						$a->Aplicacions=trim(implode(';',$paraPerfil),';');
					}

					$a->UltimAccesIntra=gmdate("d/m/Y H:i:s",time());
					$a->save($error);
					
					if (!$error) {
						if ($a->Aplicacions!==null) $props->Aplicacions=$a->Aplicacions;
						$props->UltimAccesIntra=$a->UltimAccesIntra;
					}
				}
				/// guardar en el perfil las nuevas aplicaciones
				/// (no hace falta añadirlas a idcategs porque las importantes no generan credentials)
				//echo $props->Nif._r($grups2); //._r($grups);die();
				if (!empty($idcategs)){
					$categs=array_keys($idcategs);
					self::setGroups($categs);
					foreach($categs as $categ) Session::addCredential($categ);
				}
			}

			//// rols especifics
			
			
			if ($props->UsuariPrimaria) Session::addCredential('es_primaria');
			if ($props->UsuariHospital) Session::addCredential('es_hospital');
			Session::addCredential('es_'.$props->TitulacioGrup);
		}
		
		static public function getLogin()	{ return self::getProperties('Nif'); }
		static public function getName() 	{ return self::getProperties('Nom'); }

		static public function getComputerUser(){

			$ip=Util::getRemoteInfo('ip');
			$peer=new AdcomputerPeer();
			$user=$peer->getComputerUser($ip);
			return ($user!==false)?self::getUser($user):false;
		}
		
		static public function getUser($user,$pass=null){
			//$user=self::$ldap->getLDAPName($user,$pass);
			$peer=new AduserPeer();
			$aduser=$peer->retrieveByPK($user);
			if ($aduser===null || $aduser->isAccountDisabled()) return false;
			return $aduser;
		}
	}
?>