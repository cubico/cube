<?php
class AdcomputerPeer extends ActivedirPeer{
	
	
	protected $FIELDS=array("cn","name","displayname","description","distinguishedname","operatingsystem","operatingsystemservicepack");
	
	public function configure()
	{
	
	}
	// devuelve false si no existe el ordenador, true si existe y string si hay un usuario en el ordenador
	public function getComputerUser($ip){

		$host=explode(".",gethostbyaddr($ip));
		$computerName=strtoupper($host[0]);
		$computer=$this->retrieveByPK($computerName);

		if ($computer===null) return false;
		else if ($computer->Description!==null) return $computer->Description;
		return true;
	}
	
}
?>