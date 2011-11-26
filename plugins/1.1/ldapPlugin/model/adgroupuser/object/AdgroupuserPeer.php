<?php
class AdgroupuserPeer extends ActivedirPeer{
	
	protected $FIELDS=array("description","samaccountname", //"distinguishedname",
							"primarygroupid","cn","useraccountcontrol",
							"info");
	
	public function configure()
	{
		
	}
	
	public function __construct(){
		parent::__construct();
		$this->setBase("Intranet/aplicaciones");
	}
	
	public function doSelect($select,$hidrate=true){
		$a=$this->executeSelect($select,$hidrate);
		//echo '<br/><br/>-------****------------'.get_class($this).'<br/>'.var_export($select,true)._r($a).'<br/>---****--------------';
		return $a; 
	}
	
	public function retrieveByColumns($info=array(),$hidrate=true,$fields=null,$order=null){
		if (isset($info['User'])){
			$peer=new AdgroupPeer();
			
			/// select segun usuario!
			 $grups=array();
			 //echo _r($info).'aaaa';die();
			 if ($fields===null) $fields=array('memberof');
			 else $fields=array_keys(array_intersect($peer->getPHPNames(),$fields));

			 $ok=$this->groupsByUser($info['User']['value'],$grups,$fields,$peer);
			 if ($ok) {unset($info['User']); return $grups;}
			 else return array();
		}
		
		$r=parent::retrieveByColumns($info,$hidrate,$fields,$order);
		return $r;
	}
	
	public function groupsByUser($user,&$grups=null,$fields=null,$peer=null)	/// es el StoreLDAPUser de Ldap
	{
		$idcategs=array();
		// sacamos las categorias a las que esta asignado (recursivo!) para soporte de perfiles!
		try{
			if ($peer===null) $peer=new AdgroupPeer();
			if ($fields===null) $fields=array('memberof');
			else if (!in_array('memberof',$fields)) $fields[]='memberof';

			$groups=$this->all_user_groups($user,array('memberof'));  // los grupos a los q pertenece
			$idcategs=$peer->getInfoGroups($groups,true,$fields);

			
			//echo $user._r($idcategs);die();
			// ordenamos las categorias para tener todas las del mismo "grupo" (intra_anatomia, p.ex)
			if ($idcategs!=null) ksort($idcategs);
			//echo _r($idcategs);

		}catch(Exception $e){
			//echo $e->getMessage();
		
		} // no ha encontrado el usuario, o no tiene grupos!
		
		if ($grups!==null) $grups=$idcategs;
		return (count($idcategs)>0);
  	}

	public function all_user_groups($username,$fields)
	{
		$db=$this->ldap;
		$conn=$db->getConn();
		
		if ($username===NULL){ return (false); }
		if (!$db->getBind()){ return (false); }

		$genfilter="(&(objectCategory=group)(%s))";
		$sr=ldap_search($conn,$db->getBaseDn(),"samaccountname=".$username,$fields);
		
		if ($sr===null)
		{
			throw new LDAPException(LdapException::LDAPEXCEPTION_NOTROBAT_USER_TEXT,LDAPEXCEPTION_NOTROBAT_USER);
			return false;
		}
		
		/// extraer grupos de usuario
		$entries = $db->getEntries($sr,$fields);
		$grp=(array) $entries[0]['memberof'];
		$groups=array();
		
		// AD does not return the primary group in the ldap query, we may need to fudge it
		//if ($db->hasRealPrimaryGroup()) $grp[]=$db->group_cn($entries[0]['primarygroupid']);
		//else $grp[]="CN=Domain Users,CN=Users,".$db->getBaseDn();
		$filterg='|';
		
		foreach($grp as $k=>$v){
			if (is_numeric($k) && preg_match("/^CN=(".AdgroupPeer::GROUP_FILTER."[^,]*),(.*)$/i",$v,$args)) {
				//$filterg.='(name='.$args[1].')';
				$groups[]=$args[1];
			}
		}
		//echo _r($groups);
		return $groups;
	}

	
	

}
?>