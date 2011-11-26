<?php
/**
 * bulksend.php
 * @author David Tapia (c) 2007 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.0
 */

if(!class_exists('BulkSend')){
    define('VSMS_BULK_VERSION', '1.0');

    class BulkSend {
        var $sendElements = 0;
        var $failedsRecipients = array();

        function BulkSend($n) {
            $this->sendElements = $n;
        }

        function addFailRecipient($failedRecipient) {
            $this->failedsRecipients[] = $failedRecipient;
        }

        function getFailRecipientAt($i) {
            // El indice debe de ser 0..length
            if(array_key_exists($i, $this->failedsRecipients)) return $this->failedsRecipients[$i];
            else return "";
        }

        function isEmptyFailRecipient(){
            if(isset($this->failedsRecipients) && count($this->failedsRecipients) > 0) return false;
            else return true;
        }

        function isAllSendFailed(){
            if(isset($this->failedsRecipients) && count($this->failedsRecipients) == $this->sendElements) return true;
            return false;
        }

        function getFailedsRecipients() {
            return $this->failedsRecipients;
        }

        function getFailedsRecipientsToStrings() {
            if(!isset($this->failedsRecipients) || count($this->failedsRecipients) == 0) return null;
            $r = array();
            foreach ($this->failedsRecipients as $f){
                $r[] = $f;
            }
            return $r;
        }

        function getSendElements() {
            return $this->sendElements;
        }
    }
} // if(!class_exists('BulkSend'))
?>