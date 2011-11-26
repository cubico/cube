<?php
class Adgroup extends Activedir{
	
	
	
	public function __construct(){
		$this->setLogic(false);	// delete are logic 
		
		$this->ObjCateg='group';
		$this->Tipus=LdapAD::ADLDAP_SECURITY_GLOBAL_GROUP;
	}
	
	public function adModify($pks,$modifiedFields,$forceInsertUpdate=null){
		//$ldap=$this->getModel()->getDatabase()->getConn();
		//// parámetro false -> ojo, ldap->adldap_schema no recibe los atributos puros de ldap (no queremos traducc)
		//return $this->ldap->user_modify($pks['samaccountname'],$modifiedFields,false);
	}
}
?>