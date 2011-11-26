<?php
class WsClientException extends cubeException {

	public function __construct($missatge, $nerror) {		
		parent::__construct( self::getPrefix().$missatge,$nerror);
	}

	private static function getPrefix(){
		return "[WSClient ".strftime('%d/%m/%Y %H:%M',time())."]:<br/>";
	}

}

?>