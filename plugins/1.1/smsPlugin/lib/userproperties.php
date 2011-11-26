<?php
/**
 * userproperties.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.1
 */
if(!class_exists('UserProperties')){
    define('VSMS_UPROPERTIES_VERSION','1.1');

    class UserProperties {
        var $m_User = '';
        var $m_Pass = '';
        var $m_Credit = 0;
        var $m_MailDeliveryReceipt = '';
        var $m_CustomizedSender = '';
        var $m_AllowAnswer = true;
        var $m_Cert = false;
        var $m_Lang = 'ES';
        var $m_Type = 'D';

        function UserProperties($user, $passwd){
            $this->setDefaultProperties($user, $passwd);
        }

        function setUser($newUser){
            $newUser = trim($newUser);
            if(!empty($newUser)){
                $this->m_User = $newUser;
            }
        }

        function setPassword($newPass){
            $newPass = trim($newPass);
            if(!empty($newPass)){
                $this->m_Pass = $newPass;
            }
        }

        function setLang($lang){
            if($lang == '' || strlen($lang) > 2){
                $lang = 'ES';
            }
            $this->m_Lang = strtoupper($lang);
        }

        function setAcuseType($type){
            if($type == '' || strlen($type) > 1){
                $type = 'D';
            }
            $this->m_Type = strtoupper($type);
        }

        function setCredit($credit){
            $this->m_Credit = $credit;
        }

        function setMailDeliveryReceipt($newMailDeliveryReceipt){
            $mail = trim($this->replaceChars($newMailDeliveryReceipt));
            $mail = ereg_replace(' ', '', $mail);
            if(strtoupper($mail) == 'INTERNAL'){
                $this->m_MailDeliveryReceipt = 'INTERNAL';
            }
            else $this->m_MailDeliveryReceipt = $mail;
        }

        function setCustomizedSender($newCustomizedSender){
            $nCustomizedSender = trim($this->replaceChars($newCustomizedSender));
            if($this->isInternationalNumberFormat($nCustomizedSender)){
                $i_mas = strpos($nCustomizedSender, '+');
                if($i_mas === false){
                    if(strlen($nCustomizedSender) > 14){
                        $nCustomizedSender = substr($nCustomizedSender, 0, 15);
                    }
                }
                else{
                    if(strlen($newCustomizedSender) > 15){
                        $nCustomizedSender = substr($nCustomizedSender, 0, 16);
                    }
                }
            }
            else{
                if(strlen($nCustomizedSender) > 10){
                    $nCustomizedSender = substr($nCustomizedSender, 0, 9);
                }
            }
            $this->m_CustomizedSender = $nCustomizedSender;
        }

        function setAllowAnswer($newAllowAnswer){
            if(is_bool($newAllowAnswer)) $this->m_AllowAnswer = $newAllowAnswer;
            else $this->m_AllowAnswer = true;
        }

        function setCertifiedSMS($newCert){
            if(is_bool($newCert)) $this->m_Cert = $newCert;
            else $this->m_Cert = false;
        }

        function setDefaultProperties($user, $passwd){
            if(strlen($user) > 0){
                $this->m_User = trim($user);
            }
            else{
                $this->m_User = '';
            }

            if(strlen($passwd) > 0){
                $this->m_Pass = trim($passwd);
            }
            else{
                $this->m_Pass = '';
            }

            $this->m_Credit = 0;
            $this->m_MailDeliveryReceipt = '';
            $this->m_CustomizedSender = '';
            $this->m_AllowAnswer = true;
            $this->m_Cert = false;
            $this->m_Lang = 'ES';
        }

        function getUser(){
            return $this->m_User;
        }

        function getPassword(){
            return $this->m_Pass;
        }

        function getCredit(){
            return $this->m_Credit;
        }

        function getMailDeliveryReceipt(){
            return $this->m_MailDeliveryReceipt;
        }

        function getCustomizedSender(){
            return $this->m_CustomizedSender;
        }

        function getLang(){
            return $this->m_Lang;
        }

        function getAcuseType(){
            return $this->m_Type;
        }

        function isAllowAnswer(){
            return $this->m_AllowAnswer;
        }

        function isCertifiedSMS(){
            return $this->m_Cert;
        }

        // ================================================
        // PRIVATE methods, use under your responsibility
        // ================================================

        function replaceChars($str){
            $str2 = ereg_replace('\n', '', $str);
            $str2 = ereg_replace('\r', '', $str2);
            return $str2;
        }

        function isInternationalNumberFormat($str){
            return(ereg('\+?[0-9]', $str) == 1);
        }

        function charAt($str, $pos){
            return (substr($str, $pos, 1)) ? substr($str, $pos, 1) : -1;
        }

        // ================================================
        // Deprecated methods, use under your responsibility
        // ================================================

            /* @Deprecated */
        function getAllowAnswer(){
            return $this->m_AllowAnswer;
        }
    }
} // if(!class_exists('ProtocolProperties'))
?>