<?php
/**
 * socket.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 2.1
 */

include_once dirname(__FILE__).'/logger.php';
include_once dirname(__FILE__).'/socket-const.php';
include_once dirname(__FILE__).'/socket-php4.php';
include_once dirname(__FILE__).'/socket-php5.php';

if(!class_exists('Socket')){
    define('VSMS_SOCKET_VERSION', '2.1');

        /**
        * Esta clase sera una abstraccion, me explico.
        * Para PHP4 utilizare los sockets propiamente dichos
        * Para PHP5 utilizare los descriptores de ficheros
        */
    class Socket {
        var $con; // Objecte tipo PHP4 o PHP5
        var $objID;

        function Socket($connData = array()){
            $this->objID = $this->getObjectID(12);

            /*$varray = explode('.', phpversion());
            if(intval($varray[0]) >= 5){
                // PHP5 or Newer
                $this->con = new Socket5($connData);
            }
            else{
                // PHP4 or Older
                $this->con = new Socket4($connData);
            }*/

            $this->con = new Socket5($connData);
            
            if(SOCKET_DEBUG){
                $log = new LoggerSMS();
                $log->debug('[ Socket   ] '.$this->objID.' Object created. Version '.VSMS_SOCKET_VERSION);
                $log->debug('[ Socket   ] '.$this->objID.' PHP'.$varray[0]);
            }
        }

        function isConnected(){
            return $this->con->isConnected();
        }

        function connect($connData = array()){
            return $this->con->connect($connData);
        }

        function sendData($data){
            return $this->con->sendData($data);
        }

        function getData(&$data) {
            return $this->con->getData($data);
        }

        function disconnect(){
            $this->con->disconnect();
            $this->con = null;
        }

        function getObjectID($len){
            if(!isset($this->objID) || $this->objID == ''){
                return substr(md5(rand(0,999)), 0, $len);
            }
            else{
                return $this->objID;
            }
        }
    }
} // if(!class_exists('socket'))
?>