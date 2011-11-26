<?php
class Cube_users extends Cube{
	// // Executed at Cube::delete() method
	public function logicDelete(){	
		// $this->
		//return true;	// not execute save method (ex. condition credentials) 
	}

	// Executed at Cube::save() method
	public function logicInsert(){
		// $this->
	}
	
	// Executed at Cube::getObjectFilter() method
	public function logicFilter(){
		// $this->
	}
		
	public function __construct(){
		$this->setLogic(false);	// logic delete 
	}
}
?>