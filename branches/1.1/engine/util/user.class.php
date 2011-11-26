<?php 
	class User{
		
		static private $instancia;	
				
		static public function removeAllCredentials(){}
		static public function authenticateUser($login,$pass,$sessionLogin=null){}
		
		static public function executeLogin(){}
		static public function executeLogout(){}
		
		static public function getLogin(){}
		static public function getName(){}
		
		static public function createInstance($class) {
			if (self::$instancia == NULL) 
			{
				self::$instancia = new $class();
			}
			
	       	return self::$instancia;
		}
		
		static public function getInstance() {
	       	return self::$instancia;
		}
	}
?>