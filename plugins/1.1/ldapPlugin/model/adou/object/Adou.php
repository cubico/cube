<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor. (&(objectCategory=organizationalUnit)(objectClass=organizationalUnit)(ou=Usuaris*))
 */
class Adou extends Activedir{
	
	public function __construct(){
		$this->setLogic(false);	// delete are logic 
		$this->ObjCateg='organizationalUnit';
		$this->ObjClass='organizationalUnit';
	}
	
	public function adModify($pks,$modifiedFields,$forceInsertUpdate=null){
		//$ldap=$this->getModel()->getDatabase()->getConn();
		//// parámetro false -> ojo, ldap->adldap_schema no recibe los atributos puros de ldap (no queremos traducc)
		//return $ldap->user_modify($pks['samaccountname'],$modifiedFields,false);
	}
	
}

?>
