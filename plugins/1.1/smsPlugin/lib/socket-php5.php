<?php
/**
 * socket-php5.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.1
 */

include_once dirname(__FILE__).'/logger.php';
include_once dirname(__FILE__).'/socket-const.php';

// Implementacion con descriptores de ficheros.
// See stream_socket_client(), stream_socket_server(), fsockopen()

// Pones los flush en su lugar, esto es a TRUE!
ob_implicit_flush();

if(!class_exists('Socket')){
    define('VSMS_SOCKET5_VERSION', '1.1');

    class Socket5 {
        var $con; // El descriptor de fichero
        var $estat;
        var $log;
        var $objID;

        function Socket5($connData = array()){
            $this->objID = $this->getObjectID(12);
            $this->log = new LoggerSMS();
            $this->estat = SOCKET_NOCONNECT;
            if(SOCKET_DEBUG){
                $this->log->debug('[ Socket5  ] '.$this->objID.' Object created. Version '.VSMS_SOCKET5_VERSION);
            }
            $this->connect($connData);
        }

        function isConnected(){
            if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Ask for connection status - '.$this->estat);
            if(!isset($this->con)){
                if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Ooops! Without connection!');
                return false;
            }
            return ($this->estat == SOCKET_CONNECT) ? true : false;
        }

        function connect($connData = array()){
            if(is_array($connData)) extract($connData);

            if(!isset($host)) $host = HOST;
            if(!isset($port)) $port = PORT;

            if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Try to connect with the server '.$host.':'.$port);

            $this->con = stream_socket_client("tcp://".$host.":".$port, $errno, $errstr, 30);
            //socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            if (!$this->con) {
                if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Create error. '.$errstr.' ('.$errno.')');
                $this->estat = SOCKET_NOCONNECT;
                return SOCKET_NOCONNECT;
            }

            if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Connected!');
            $this->estat = SOCKET_CONNECT;
            return SOCKET_CONNECT;
        }

        function sendData($data){
            if(!$this->isConnected()) return false;
            $d = str_replace("\n", "\r\n", $data);
            fwrite($this->con, $d, strlen($d));
            if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Send data: '.$d);
        }

        function getData(&$data) {
            if($this->isConnected()) {
                if(false === ($data = fgets($this->con, SOCKET_BUFFER))) {
                    if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Get data error. ');
                    return false;
                }
                $data = trim($data);
                if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Get data: '.$data);
                return true;
            }
            return false;
        }

        function disconnect(){
            if(SOCKET_DEBUG) $this->log->debug('[ Socket5  ] '.$this->objID.' Disconnected!');
            fclose($this->con);
            $this->con = false;
            $this->estat = SOCKET_NOCONNECT;
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