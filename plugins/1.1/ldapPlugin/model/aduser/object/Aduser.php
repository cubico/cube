<?php
class Aduser extends Activedir{
	private $pathDn;
        
	public function __construct($path=null){
		$this->setLogic(false);	// delete are logic 
		$this->pathDn=$path;
		$this->ObjClasse='user';
		$this->ObjCateg='person';
		$this->Tipus=AduserPeer::ADLDAP_NORMAL_ACCOUNT;
		
	}

	public function adModify($pks,$modifiedFields,$forceInsertUpdate=null){
		//// parámetro false -> ojo, ldap->adldap_schema no recibe los atributos puros de ldap (no queremos traducc)
		//return $ldap->user_modify($pks['samaccountname'],$modifiedFields,false);
                
		$ldap=$this->getModel()->getDatabase();
		$conn=$ldap->getConn();
				
		$filter="(&(objectCategory=person)(objectClass=user)(samaccounttype=". AduserPeer::ADLDAP_NORMAL_ACCOUNT .")";
		foreach($pks as $i=>$j) $filter.="($i=$j)";
		$filter.=")";
		
                if ($forceInsertUpdate===null || $forceInsertUpdate===false){
                    $sr=ldap_search($conn,$ldap->getBaseDn(),$filter,array('distinguishedname'));
                    $entries = ldap_get_entries($conn, $sr);
                }
                
                if ($entries['count']>0){
                    $entries = ldap_get_entries($conn, $sr);
                    $user_dn=$entries[0]['dn'];
                    return ldap_modify($conn,$user_dn,$modifiedFields);
                }else{
                    $modifiedFields['container']=explode('/',$this->pathDn);
                    $attrib=array_merge($pks,$modifiedFields);
                    //echo _r($attrib);
                    return $this->create($attrib,$ldap);
                }
	}
        
	private function create($attributes,$ldap)
	{

		// crear nombre completo            
		if (!array_key_exists("givenname", $attributes)){throw new LDAPException("Missing compulsory field [firstname]",11); }
		if (!array_key_exists("sn", $attributes)){ throw new LDAPException("Missing compulsory field [surname]",12); }

		$attributes["sn"]=strtoupper($attributes["sn"]);
		$attributes["givenname"]=strtoupper($attributes["givenname"]);
		$attributes["displayname"] = $attributes["sn"].', '.$attributes["givenname"]; 
		$cn=$attributes["givenname"].' '.$attributes["sn"];

		// Check for compulsory fields
		//if (!array_key_exists("username", $attributes)){ return "Missing compulsory field [username]"; }
		//if (!array_key_exists("mail", $attributes)){ throw new LDAPException("Missing compulsory field [email]",13); }

		if (!array_key_exists("container", $attributes)){throw new LDAPException("Missing compulsory field [container]",14); }
		if (!is_array($attributes["container"])){ throw new LDAPException("Container attribute must be an array.",15); }

		$nif=$attributes["samaccountname"];

		$attributes["cn"]=$cn;
		$attributes['uid'] = $nif;
		$attributes["objectClass"][0] =     "top";
		$attributes["objectClass"][1] =     "person";
		$attributes["objectClass"][2] =     "organizationalPerson";
		$attributes["objectClass"][3] =     "user"; 
		$attributes["userAccountControl"] = 544;    // crea el usuario con la cuenta activa
		$attributes["pwdlastset"] =         "-1";           // el usuario debe cambiar la contraseña en el proximo inicio de sesión            
		$attributes['uidNumber'] =          "1001";
		$attributes['gidNumber'] =          "512";
		//$attributes['userPassword'] =   $nif; //"{MD5}".base64_encode(pack("H*",md5($nif)));            

		$attributes['homeDirectory'] = "/home/".$nif;

		// Determine the container
		$container = array_reverse($attributes["container"]);
		$container = "OU=" . implode(", OU=",$container);
		unset($attributes["container"]);

		$dn="CN=" . $cn . "," . $container . "," . $ldap->getBaseDn();
		//echo '<br/>'.$dn._r($attributes);//die();

		return ldap_add($ldap->getConn(), $dn, $attributes);
	}
        
        
	public function isAccountDisabled(){
		$peer=$this->getModel();
		$props=$peer->getAccountControl($this->AccountControl);
		//$val=$peer->accountControl($props);
		return in_array('ACCOUNTDISABLE',$props);
	}
}
?>