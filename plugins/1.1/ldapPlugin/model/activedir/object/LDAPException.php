<?php
class LDAPException extends Exception
{
	const LDAPEXCEPTION_NOTROBAT=1; 
	const LDAPEXCEPTION_NOTROBAT_TEXT="No hi ha informació de l'ordinador";
	
	const LDAPEXCEPTION_OUNOTROBAT=7; 
	const LDAPEXCEPTION_OUNOTROBAT_TEXT="No hi ha informació de la unitat organitzativa";
	
	const LDAPEXCEPTION_NOGRUPS_TEXT= "No tens grups assignats";
	const LDAPEXCEPTION_NOGRUPS=2;
	
	const LDAPEXCEPTION_USUINCORRECTE_TEXT= "Usuari/Contrasenya Incorrecta";
	const LDAPEXCEPTION_USUINCORRECTE=3;
	
	const LDAPEXCEPTION_USUBUIT_TEXT= "Usuari i/o Contrasenya buides";
	const LDAPEXCEPTION_USUBUIT=4;
	
	const LDAPEXCEPTION_GENERIC_TEXT= "Error LDAP";
	const LDAPEXCEPTION_GENERIC=6;
	
	const LDAPEXCEPTION_NOPERMISPLANA_TEXT= "Usuari sense privilegis per aquesta plana";
	const LDAPEXCEPTION_NOPERMISPLANA=5;
	
	const LDAPEXCEPTION_NOPERMIS_TEXT= "Usuari sense privilegis a la Intranet";
	const LDAPEXCEPTION_NOPERMIS=8;
	
	const LDAPEXCEPTION_NOTROBAT_USER=9; 
	const LDAPEXCEPTION_NOTROBAT_USER_TEXT="No hi ha informació de l'usuari";
	
	const LDAPEXCEPTION_DISABLED_TEXT="Usuari deshabilitat en el directori actiu";
	const LDAPEXCEPTION_DISABLED=10;
	
	// Redefine the exception so message isn't optional
    public function __construct($message, $code = 0) {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct($message, $code);
    }

    // custom string representation of object */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

?>