<?php
class WsServerException extends cubeException {

	public function __construct($missatge, $nerror) {		
		parent::__construct( self::getPrefix().$missatge,$nerror);
	}

	private static function getPrefix(){
		return "[WsServer ".strftime('%d/%m/%Y %H:%M',time())."]:<br/>";
	}

}

?>