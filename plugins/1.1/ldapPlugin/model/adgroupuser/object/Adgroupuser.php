<?php
class Adgroupuser extends Activedir{
	
	protected $FIELDS=array("description","samaccountname","primarygroupid","cn","info","objectcategory");
    
   public function __construct(){
		$this->setLogic(false);	// delete are logic 
		
		$this->ObjCateg='group';
		$this->Tipus=LdapAD::ADLDAP_SECURITY_GLOBAL_GROUP;
	}
	
	public function adModify($pks,$modifiedFields,$forceInsertUpdate=null){
		$ldap=$this->getModel()->getDatabase()->getConn();
		//// parámetro false -> ojo, ldap->adldap_schema no recibe los atributos puros de ldap (no queremos traducc)
		//return $ldap->user_modify($pks['samaccountname'],$modifiedFields,false);	
	}
}
?>