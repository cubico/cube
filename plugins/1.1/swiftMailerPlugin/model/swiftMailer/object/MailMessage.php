<?php
require_once dirname(__FILE__).'/../../../lib/swift_required.php';

class MailMessage extends Swift_Message{
	public function __construct(){
		parent::construct();
	}
	
	static public function newInstance(){
		return parent::newInstance();
	}
}
?>
