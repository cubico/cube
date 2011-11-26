<?php
/**
 * protocolproperties.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.2
 *
 * Fast server, active options;
 * Slow server, deactivate options!
 *
 * true to active, false to deactivate
 *
 */

if(!class_exists('ProtocolProperties')){
    define('VSMS_PROPERTIES_VERSION','1.2');

    class ProtocolProperties {
        var $debug = true;
        var $m_Host = 'sms.lleida.net';
        var $m_Port = 2048;

        // ACK's
        var $m_ackAcuses = false;
        var $m_ackChecker = false;
        var $m_ackIncoming = false;
        var $m_ackDeliver = false;

        function ProtocolProperties(){
        }

        function setHost($newHost){
            $newHost = trim($newHost);
            if(!empty($newHost)){
                $this->m_Host = $newHost;
            }
        }

        function setPort($newPort){
            if($newPort < 0 || $newPort > 32767){
                $this->m_Port = 2048;
            }
            else{
                $this->m_Port = $newPort;
            }
        }

        function setDebugMode($deb){
            if(is_bool($deb)) $this->debug = $deb;
            else $this->debug = true;
        }

        function getDebugMode(){
            return $this->debug;
        }

        function getHost(){
            return $this->m_Host;
        }

        function getPort(){
            return $this->m_Port;
        }

        function activeAllAck(){
            $this->m_ackAcuses = true;
            $this->m_ackChecker = true;
            $this->m_ackIncoming = true;
            $this->m_ackDeliver = true;
        }

        function deactiveAllAck(){
            $this->m_ackAcuses = false;
            $this->m_ackChecker = false;
            $this->m_ackIncoming = false;
            $this->m_ackDeliver = false;
        }

        function setAcusesAck($bln){
            if(is_bool($bln)) $this->m_ackAcuses = $bln;
            else $this->m_ackAcuses = false;
        }

        function setCheckerAck($bln){
            if(is_bool($bln)) $this->m_ackChecker = $bln;
            else $this->m_ackChecker = false;
        }

        function setIncomingAck($bln){
            if(is_bool($bln)) $this->m_ackIncoming = $bln;
            else $this->m_ackIncoming = false;
        }

        function setDeliverAck($bln){
            if(is_bool($bln)) $this->m_ackDeliver = $bln;
            else $this->m_ackDeliver = false;
        }

        function isActivatedAcusesAck(){
            return $this->m_ackAcuses;
        }

        function isActivatedCheckerAck(){
            return $this->m_ackChecker;
        }

        function isActivatedIncomingAck(){
            return $this->m_ackIncoming;
        }

        function isActivatedDeliverAck(){
            return $this->m_ackDeliver;
        }
    }
} // if(!class_exists('ProtocolProperties'))
?>