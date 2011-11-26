<?php
	require_once dirname(__FILE__).'/../../../lib/swift_required.php';
	
	class SwiftMailer{
		static private $instance;
		
		static public function getInstance(){
			if (!isset(self::$instance)) {
				$conf=Config::get('swiftMailerPlugin:mail');
				$transport = Swift_SmtpTransport::newInstance($conf['smtpHost'],$conf['smtpPort']);
				self::$instance = Swift_Mailer::newInstance($transport);
			}
			return self::$instance ;
		}
		
		
	}
?>