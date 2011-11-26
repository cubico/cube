<?php
class AdgroupPeer extends ActivedirPeer{

	const GROUP_FILTER='intra_';
	protected $FIELDS=array("description","samaccountname","primarygroupid","cn","info" ,"distinguishedname");
  //// Autogenerate by Adgroupuser //// 
	public function doSelectJoinAduser($id){
		$criteria=array();
		$peer=new AdgroupuserPeer();
		$criteria['Id']=array('value'=>$id);
		$data=$peer->retrieveByColumns($criteria,false);
		return $data;
	}

	public function filterWhenCreated($ultim_acces,$grups){
		if (empty($ultim_acces)) $ultim_acces='01/01/1970 00:00:00';
			//echo $ultim_acces;die();
		 //$ultim_acces='17/03/2010 19:38:10';
		 $ua=preg_split('/[ :\/-]/',$ultim_acces);
		 $utime=mktime($ua[3],$ua[4],$ua[5],$ua[1],$ua[0],$ua[2]);
		 $res=array();
		 //////// miramos en grupos de usuario e importantes, si ha habido una creación después de la última entrada del usuario
		 /////// nos guardamos en $res los que "no ha visto" el usuario
		 foreach ($grups as $cur){
			 $i=$cur['samaccountname'];
			 if (!isset($res[$i])){
					// SI fecha ultima actualizacion - fecha creacion del grupo < 0
				 // EL USUARIO, SI TIENE PERFIL ACTIVADO, NO HA VISTO ESTA APLICACION PORQUE ES MÁS NUEVA
				// echo '<br/>'.$i.':<br/>'.strftime("%d/%m/%Y %H:%M:%S",$utime).' -  '.strftime("%d/%m/%Y %H:%M:%S",ActivedirPeer::toDate($cur['whencreated']));
				 $diff=$utime-ActivedirPeer::toDate($cur['whencreated']);
				 if ($diff<=0) $res[$i]=$cur;	// nos guardamos el grupo. Puede ser que no lo tenga en el perfil = no lo va a ver.
			 }
		 }
		 
		 return $res;
	  }


	public function recursiveGroup($group,&$res,$fields){
		
		if (!preg_match("/^".self::GROUP_FILTER."/i",$group)){ return (false); }
		$ldap=$this->ldap;
		//$fields=$this->FIELDS;$fields[]='memberof';
		$filter="(&(objectCategory=group)(name={$group}))";
		
		$sr=ldap_search($ldap->getConn(),$ldap->getBaseDn(),$filter,$fields);
		$entries =$ldap->getEntries($sr,$fields);
		$member=$entries[0]['memberof'];
		unset($entries[0]['memberof']);
		//echo '<br/>***'.$group._r($entries[0]);
		if (!isset($res[$group])) $res[$group]=$entries[0];
		if ($member!=null){
			if (!is_array($member)) $member=array($member);
			foreach($member as $cur){
				if (preg_match("/^CN=(".AdgroupPeer::GROUP_FILTER."[^,]*),(.*)$/i",$cur,$args)){
					$this->recursiveGroup($args[1],$res,$fields);
				}
			}
		}
	}
	

	public function getInfoGroups($info,$recursive=false,$fields=null){
		//$recursive=false;
		$db=$this->getDatabase();
		$this->ldap=$db;
		$conn=$db->getConn();

		if ($fields===null) $fields=$this->FIELDS;
		if ($recursive && !in_array('memberof',$fields)) $fields[]='memberof';
		
		$gg=array();
		$genfilter="(&(objectCategory=group)(%s))";
		$max=count($info);
		$entries2=array();
		for($i=0;$i<$max;$i++){
			$name=$info[$i];
			if (preg_match("/^".self::GROUP_FILTER."/i",$name)){
				$filter=sprintf($genfilter,"name={$name}");
				$sr=ldap_search($conn,$db->getBaseDn(),$filter,$fields);
				$entries3 = $db->getEntries($sr,$fields);
				if ($recursive){
					//$memberof=$entries3[0]['memberof'];
					unset($entries3[0]['memberof']);
					//echo '<br/>Recursivo para '.$name.': ';
					$this->recursiveGroup($name,$entries2,$fields);
				}
				else if (!isset($gg[$name])) $gg[$name]=$entries3[0];
			}
		}
		
		if ($recursive) $gg=$entries2;
		//echo _r($gg);
		return $gg;
	}

	public function doSelectAll($hidrate=false,$fields=null)
	{
		$r=$this->retrieveByColumns(array('Id'=>array('value'=>self::GROUP_FILTER.'*')),$hidrate,$fields);
		return $r; 
	}
}
?>