<?php
/**
 * socket.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.5
 */

include_once dirname(__FILE__).'/logger.php';
include_once dirname(__FILE__).'/socket-const.php';

// Pones los flush en su lugar, esto es a TRUE!
ob_implicit_flush();

if(!class_exists('Socket')){
    define('VSMS_SOCKET4_VERSION', '1.5');

    class Socket4 {
        var $con; // El socket
        var $estat;
        var $log;
        var $objID;

        function Socket4($connData = array()){
            $this->objID = $this->getObjectID(12);
            $this->log = new LoggerSMS();
            $this->estat = SOCKET_NOCONNECT;
            if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Object created. Version '.VSMS_SOCKET4_VERSION);
            $this->connect($connData);
        }

        function isConnected(){
            if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Ask for connection status - '.$this->estat);
            if(!isset($this->con)){
                if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Ooops! Without connection!');
                return false;
            }
            return ($this->estat == SOCKET_CONNECT) ? true : false;
        }

        function connect($connData = array()){
            if(is_array($connData)) extract($connData);

            if(!isset($host)) $host = HOST;
            if(!isset($port)) $port = PORT;

            if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Try to connect with the server '.$host.':'.$port);

            $this->con = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($this->con < 0) {
                if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Create error. '.socket_strerror($this->con));
                return SOCKET_NOCONNECT;
            }
            else{
                $address = gethostbyname(trim($host));
                // $socbln - Boolean and error return for socket functions
                if (($socbln = socket_connect($this->con, $address, $port)) < 0){
                    if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Connect error. '.socket_strerror($socbln));
                    return SOCKET_NOCONNECT;
                }
            }

            if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Connected!');
            $this->estat = SOCKET_CONNECT;
            return SOCKET_CONNECT;
        }

        function sendData($data){
            if(!$this->isConnected()) return false;
            $d = str_replace("\n", "\r\n", $data);
            socket_write($this->con, $d, strlen($d));
            if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Send data: '.$d);
        }

        function getData(&$data) {
            if($this->isConnected()) {
                // Warning: socket_read() unable to read from socket [104]: Connection reset by peer
                if(false === ($data = socket_read($this->con, SOCKET_BUFFER, PHP_NORMAL_READ))) {
                    if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Get data error. '.socket_strerror($this->con));
                    return false;
                }
                $data = trim($data);
                if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Get data: '.$data);
                return true;
            }
            return false;
        }

        function disconnect(){
            if(SOCKET_DEBUG) $this->log->debug('[ Socket4  ] '.$this->objID.' Disconnected!');
            socket_close($this->con);
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