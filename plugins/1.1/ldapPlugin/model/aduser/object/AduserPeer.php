<?php
class AduserPeer extends ActivedirPeer{

	const ADLDAP_NORMAL_ACCOUNT=805306368;
	
	protected $ldap;
	protected $FIELDS=array("useraccountcontrol","distinguishedname","url","homephone","mobile",
									"description","givenname","sn","samaccountname","mail","sn","pager",
									"department","displayname","telephonenumber","primarygroupid",
									"physicaldeliveryofficename","title","wwwhomepage","first","last","userpassword",
									"cn","userprincipalname","lastname","name","info","othertelephone", //"memberof",
									"otherhomephone","otherpager","badPasswordTime","pwdlastset");
	
	public function configure()
	{
	
	}

	public function __construct(){
		parent::__construct();
	}

	//validate a users login credentials
	public function authenticate($username,$password,$prevent_rebind=false){
		
		try{
			$this->getDatabase()->authenticate($username,$password,$prevent_rebind);
		}catch(CubeException $e){
			//echo _r($e->getTrace());
			//echo $e->getCode().' '.$e->getMessage();
			// si se produce una excepción no se puede validar
			return false;
		}
		return true;
	}
	
	//////// crear usuario ldap / borrar usuario ldap
	public function enableUser($id){
		$control=$this->accountControl(array('NORMAL_ACCOUNT'));
		return $this->setControl($id,$control);
	}

	public function disableUser($id){
		$control=$this->accountControl(array('ACCOUNTDISABLE'));
		return $this->setControl($id,$control);
	}

	private function setControl($id,$control){
		$ldap=$this->getDatabase();
		$conn=$ldap->getConn();

		$obj=$this->retrieveByPk($id);
		//echo _r($obj).$control;die();
		$mod["userAccountControl"][0]=$control;
		return @ldap_modify($conn,$obj->Dn,$mod);

	}
		  
		  
	//// Autogenerate by Adgroupuser //// 
	public function doSelectJoinAdgroup($user){
		$criteria=array();
		$peer=new AdgroupuserPeer();
		$criteria['User']=array('value'=>$user);
		$data=$peer->retrieveByColumns($criteria,false);
		return $data;
	}
       
}
?>