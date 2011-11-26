<?php
/**
 * logger.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 1.5
 */

if(!class_exists('LoggerSMS')){
    define('VSMS_LOGGER_VERSION', '1.5');
    class LoggerSMS{
        var $file;
        function __construct(){
            
        	$filename=realpath(dirname(__FILE__).'/../').DIRECTORY_SEPARATOR.Config::get('smsPlugin:logfile');
        	if(!preg_match("/.log$/",$filename)) $filename = $filename.'.log';
        	$this->file=$filename;
        }

        function debug($log){
            if(strpos(strtoupper($log), "LOGIN") === false){
                // No contiene la palabra LOGIN
                // Miro si es un MMS
                if(strpos(strtoupper($log), " MMSMSG ") == true){
                    // Quito los ficheros MMS del log
                    $log = "MMSMSG [...] Logger version ".VSMS_LOGGER_VERSION;
                    $log ="\n".' ['.date('Y-m-d H:i:s').'] Debug: '.$this->__adds($log);
                }
                else{
                    $log ="\n".' ['.date('Y-m-d H:i:s').'] Debug: '.$this->__adds($log);
                }
            }
            else{
                // Quito el passwd del comando login
                // Pudes poner el texto que mas te guste
                $log = "LOGIN [...] Logger version ".VSMS_LOGGER_VERSION;
                $log ="\n".' ['.date('Y-m-d H:i:s').'] Debug: '.$this->__adds($log);
            }
            
            $fp = fopen($this->file, 'a+');
            $err=fwrite($fp, $log);
            fclose($fp);
        }

        function __adds($text){
            if(!get_magic_quotes_gpc()) return addslashes(trim($text));
            else return trim($text);
        }

        function __strip($text){
            if(!get_magic_quotes_gpc()) return stripslashes(trim($text));
            else return trim($text);
        }
    }
} // if(!class_exists('logger'))
?>