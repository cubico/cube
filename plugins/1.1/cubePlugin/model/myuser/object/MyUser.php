<?php
	class MyUser extends User{

		public function __construct(){
		}
		
		static public function getProperties($prop=null){
			if (Session::is_set('sqlite_user')){
				$ret=Session::get('sqlite_user');
				
				if ($prop==null) return $ret; 
				else if ($ret!=null && $ret->{$prop}!=null) return $ret->{$prop};

			}
			return null;
		}
		
		/// devuelve 0 si todo ha ido bien. devuelve valor negativo para cada error
		static public function authenticateUser($login,$password,$sessionLogin=null){
			
			try{
				
				$peer=new Cube_usersPeer();
				$bduser=$peer->retrieveByColumns(array(
					'CubUsrNickname'=>array('value'=>$login),
				));
				
				if (empty($bduser)) return -2;
				if ($bduser[0]->CubUsrActive!=1) return -3;

				/// autentifica usuario, tambien mira si la cuenta esta deshabilitada
				$validationOK=($bduser[0]->CubUsrPassword==md5($password));
				
				if ($validationOK) {
					/*if ($sessionLogin!==null){
						// si pasamos sessionLogin es que queremos validarnos como ese usuario
						$bduser2=$peer->retrieveByColumns(array(
							'CubUsrNickname'=>array('value'=>$sessionLogin),
							'CubUsrActive'=>array('value'=>1)
						));
						if (empty($bduser2)) return -2;
					}*/
										
					/// guardamos la fecha de login
					$bduser[0]->CubUsrLastLogin=time();
					$bduser[0]->save();
					/// ponemos todas las propiedades en sesion
					self::setProperties($bduser[0]);
					return 0;
				}
			}catch(CubeException $e){ return $e->getMessage(); }
			return -1;
		}
		
		static public function removeAllCredentials(){
			Session::removeAllCredentials();
		}

		static public function isLogged(){
			//return true;
			return Session::hasCredential('is_logged');
		}

		static public function executeLogout($login=null){
			self::removeAllCredentials();
			Session::un_set();
			Session::set('first_login',false);	// validacion en otras intranets!
		}
		
		static public function executeLogin($login=null){
			Session::addCredential('is_logged');
		}
		
		static public function setProperties($props){
			Session::set('sqlite_user',$props);
		}
		
		static public function getGroups(){
			
			return array();
		}
	}
?>