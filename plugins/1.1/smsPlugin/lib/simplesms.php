<?php
/**
 * simplesms.php
 * @author David Tapia (c) 2008 - LleidaNetworks Serveis Telem&agrave;tics, S.L.
 * @version 2.0.10
 *
 * No Maps on memory, only send and receive.
 * No warranty for bulk sends or trans! No Maps on memory!
 *
 * Sample code: See simple.php
 *
 */

include_once dirname(__FILE__).'/constants.php';
include_once dirname(__FILE__).'/socket.php';
include_once dirname(__FILE__).'/logger.php';

if(!class_exists('SimpleSMS')){
    define('SimpleSMS_VERSION','2.0.10');

    define('GENERICS', 'LOGIN|SALDO|INFONUM|TARIFA|PONG|USERAGENT|QUIT');
    define('MT', '(D?F?[BU]?)?SUBMIT|WAPLINK|DST|MSG|FILEMSG|MMSMSG|ENVIA|(ACUSE(ON|OFF|ACK)?)|TRANS');
    define('RCVSMS', 'ALLOWANSWER|INCOMINGMO|INCOMINGMOACK');
    define('PREMIUM', 'DELIVER|(B|WAPLINK)?RESP');
    define('MSIDSN', 'CHECKALL|CHECKNETWORK');
    define('RCOMMANDS', 'OK|BYE|RSALDO|RINFONUM|RTARIFA|PING|REJDST|ACK|RTRANS|RCHECKALL|RCHECKALLACK|RCHECKNETWORK|RCHECKNETWORKACK');
    define('COMMANDS', '('.GENERICS.'|'.MT.'|'.RCVSMS.'|'.PREMIUM.'|'.MSIDSN.'|'.RCOMMANDS.')');

    class SimpleSMS {
        var $log;
        var $objID;
        var $socket;
        var $user;
        var $pass;
        var $liniaEnviada;
        var $credit;
        var $contadorCMD = 0;
        var $debug = true;
        var $CS = 'ICS'; // Custimized sender
        var $isConnected = false;

        function SimpleSMS($user, $password){
            if(!isset($this->objID) || $this->objID == '') $this->objID = $this->getObjectID(12);
            if($this->log == null) $this->log = new LoggerSMS();
            $this->user = trim($user);
            $this->pass = trim($password);
        }

        function connect(){
            if($this->socket == null){
            	if($this->user == '' || $this->pass == ''){
                    return $this->fireError(SMSMASS_ERR_NO_USER_OR_PASSWORD_FOUND, 'No user or password found', true);
                }
                else{
                	$socketProperties = array('host' => HOST, 'port' => PORT);
                    $this->socket = new Socket($socketProperties);
                    if($this->socket->isConnected()){
                        return $this->enviarDadesTCP('LOGIN '.$this->user.' '.$this->pass, 0);
                    }
                    else{
                    	return $this->fireError(SMSMASS_CONN_ERROR, 'No connected', true);
                    }
                }
            }
        }

        function disconnect(){
            if($this->isConnected){
                return $this->enviarDadesTCP('QUIT', 0);
            }
        }

        function getConnectionStatus(){
            if($this->socket != null) return ($this->socket->isConnected() && $this->isConnected);
            else return false;
        }

        function setDebugMode($d){
            if(is_bool($d)){
                $this->debug = $d;
            }
            else{
                $this->debug = true;
            }
        }

        function getDebugMode(){
            return $this->debug;
        }

        function setAllowAnswer($estat){
            if(is_bool($estat)){
                $sw = 'OFF';
                if($estat) $sw = 'ON';
                if($this->isConnected){
                    return $this->enviarDadesTCP('ALLOWANSWER '.$sw, 0);
                }
            }
            return -1;
        }

        function setCustomizedSender($newCustomizedSender){
            $CS = trim($this->replaceChars($newCustomizedSender));
            if($this->isInternationalNumberFormat($CS)){
                $i_mas = strpos($CS, '+');
                if($i_mas === false){
                    if(strlen($CS) > 14)
                    $CS = substr($CS, 0, 15);
                }
                else{
                    if(strlen($newCustomizedSender) > 15)
                    $CS = substr($CS, 0, 16);
                }
            }
            else{
                if(strlen($CS) > 10)
                $CS = substr($CS, 0, 9);
            }
            $this->CS = $CS;
        }

        function getCustomizedSender(){
            return $this->CS;
        }

        function acuseOn($mail){
            if(isset($mail) && $mail != ''){
                if($this->isConnected){
                    return $this->enviarDadesTCP('ACUSEON '.$mail, 0);
                }
            }
            return -1;
        }

        function acuseOnCertifiedSMS($mail, $lang='ES'){
            if(isset($mail) && $mail != ''){
                if($lang == '' || strlen($lang) != 2){
                    $lang = 'ES';
                }
                if($this->isConnected){
                    return $this->enviarDadesTCP('ACUSEON [lang='.strtoupper($lang).'] [cert_type=D] '.$mail, 0);
                }
            }
            return -1;
        }

        function acuseOff(){
            if($this->isConnected){
                return $this->enviarDadesTCP('ACUSEOFF ', 0);
            }
            return -1;
        }

        function getCredits(){ // SALDO
            if($this->isConnected){
                return $this->enviarDadesTCP('SALDO ', 0);
            }
            return 0;
        }

        function getPrice($num){
            if($this->isConnected){
                return $this->enviarDadesTCP('TARIFA '.$num, 0);
            }
        }

        function getOperatorInfo($num){
            if(!isset($num) || $num == '') return -1;
            else{
                if($this->isConnected){
                    return $this->enviarDadesTCP('INFONUM '.$num, 0);
                }
            }
        }

        function getFileContentBase64($file) {
            $fh = fopen($file, 'rb');
            if (!$fh) {
                if($this->debug == true) $this->log->debug('[ Protocol ] '.$this->objID.' Can not open the file '.$file);
                return null;
            }
            $file_content = fread($fh, filesize($file));
            fclose($fh);
            $encodedfile = base64_encode($file_content);
            return $encodedfile;
        }

        // ATTENTION!
        // Store the credit that the protocol receipt from the server
        // The server ever verify the credit.
        // You can change this value has you want. If you don't have credit in the server face...the server never send the SMS!
        function setCredit($newCredit){
            if(is_float($newCredit)){
                $this->credit = $newCredit;
                if($newCredit < 1){
                    if($this->debug == true) $this->log->debug('[ Protocol ] '.$this->objID.' Insuficient credit! '.$file);
                }
            }
        }

        // -----------------------------------------------------
        // $text, $data, $URL, $recipients pueden ser arrays.
        // -----------------------------------------------------

        // $dateTime FORMAT YYYYMMDDHHmm
        // 197609081600 == 1976.August.8 at 16:00 == 8 de Agosto de 1.976 a las 16:00

        function sendTextSMS(&$idEnvio, $text, $recipients, $dateTime){
            if(!$this->isConnected){
                return -1;
            }
            else return $this->sendSMS($idEnvio, '', $text, $recipients, $dateTime);
        }

            /* ENCODE YOUR DATA BEFORE with getFileContentBase64() */
        // Sample:
        // $sms->sendBinarySMS($id, getFileContentBase64($filename), $dst, '');
        function sendBinarySMS(&$idEnvio, $data, $recipients, $dateTime){
            if(!$this->isConnected){
                return -1;
            }
            else return $this->sendSMS($idEnvio, 'B', $data, $recipients, $dateTime);
        }

            /* ENCODE YOUR DATA BEFORE with base64_encode() */
        // Sample:
        // $sms->sendUnicodeTextSMS($id, base64_encode('My UNICODE info'), $dst, '');
        function sendUnicodeTextSMS(&$idEnvio, $text, $recipients, $dateTime){
            if(!$this->isConnected){
                return -1;
            }
            else return $this->sendSMS($idEnvio, 'U', $text, $recipients, $dateTime);
        }

            /* ENCODE YOUR DATA BEFORE with getFileContentBase64() */
        // Sample:
        // $sms->sendWapPush($id, $subject, $text, $dst, $mimeType, getFileContentBase64($filename));
        function sendWapPush($idEnvio, $subject, $text, $recipients, $mimeType, $fileData){
            if(!$this->isConnected){
                return -1;
            }
            else return $this->sendMSG($idEnvio, 'FILE', $subject, $text, $recipients, $mimeType, $fileData);
        }

            /* ENCODE YOUR DATA BEFORE with getFileContentBase64() */
        // Sample:
        // $sms->sendMMS($id, $subject, $text, $dst, $mimeType, getFileContentBase64($filename));
        function sendMMS($idEnvio, $subject, $text, $recipients, $mimeType, $fileData){
            if(!$this->isConnected){
                return -1;
            }
            else return $this->sendMSG($idEnvio, 'MMS', $subject, $text, $recipients, $mimeType, $fileData);
        }

        function sendWapLink(&$idEnvio, $subject, $URL, $recipients){
            if(!$this->isConnected){
                return -1;
            }

            if(!isset($subject) || $subject == ''){
                return -1;
            }

            if(is_array($URL) && is_array($recipients)){
                if(!$this->isAnyEmpty($URL) && !$this->isAnyEmpty($recipients)){
                    if(count($URL) == count($recipients)){
                        // 2 arrays,
                        return $this->doWapLinkTrans($idEnvio, $subject, $URL, $recipients);
                    }
                }
            }
            else if(!is_array($URL) && is_array($recipients)){
                if(!$this->isAnyEmpty($recipients) && isset($URL)){
                    // The same URL for all recipeints
                    return $this->doWapLinkTrans($idEnvio, $subject, $URL, $recipients);
                }
            }
            else if(is_array($URL) && !is_array($recipients)){
                if(!$this->isAnyEmpty($URL) && isset($recipients)){
                    // Multiple $URLs for only one recipient? ok...
                    $ok = true;
                    foreach($URL as $u){
                        $ok = $this->enviarDadesTCP('WAPLINK '.$recipients.' '.$u.' '.$subject, $idEnvio);
                        $idEnvio++;
                    }
                    return $ok;
                }
            }
            else{
                return $this->enviarDadesTCP('WAPLINK '.$recipients.' '.$URL.' '.$subject, $idEnvio);
            }
            return -1;
        }


        // ============================================================================
        // PRIVATE methods, use under your responsibility
        // ============================================================================


        function getObjectID($len){
            // Only for debugger info
            if(!isset($this->objID) || $this->objID == ''){
                return substr(md5(rand(0,999)), 0, $len);
            }
            else{
                return $this->objID;
            }
        }

        function doBye(){
            $this->isConnected = false;
            $this->socket->disconnect();
            $this->socket = null;
            return 1;
        }

        function fireError($errCode, $errMsg, $disconnect){
            if($this->debug == true) $this->log->debug('[ Error    ] '.$this->objID.':'.$errCode.' '.$errMsg);
            if($disconnect == true){
                return $this->doBye();
            }
            return -1;
        }

        function enviarDadesTCP($cadena, $id){
            if($this->socket->isConnected()){
                $this->contadorCMD++;
                if($id == 0) $id = $this->contadorCMD;
                $this->socket->sendData($id.' '.$cadena."\n");
                $this->liniaEnviada = $id.' '.$cadena."\n";
                if($this->debug == true) $this->log->debug('[ Protocol ] '.$this->objID.' Client: '.$id.' '.$cadena);
                return $this->getResponse();
            }
            else return $this->fireError(SMSMASS_ERR_CANT_SEND_DATA, 'Can\'t send data, lost connection', true);
        }

        function getResponse(){
            $read = true;
            $ok = true;
            while($read){
                if(!$this->socket->getData($data)) break;
                if($data == '') $read = false;
                else if(eregi(RCOMMANDS, strtoupper($data))) $read = false;
                return $this->getValidCommands($data);
            }
            return $ok;
        }

        function getValidCommands($cmds){
            $xComandos = explode("\n", $cmds);
            $ok = true;
            foreach ($xComandos as $cmd) {
                if($cmd != '' && $cmd != " "){
                    $ok = $this->dataArrival($cmd);
                }
            }
            return $ok;
        }

        function dataArrival($cmd){
            if($this->debug == true) $this->log->debug('[ Protocol ] '.$this->objID.' Server: '.$cmd);
            if(strtoupper($cmd) == "PING TIMEOUT"){
                $this->fireError(SMSMASS_ERR_PING_TIMEOUT, ''.$cmd, true);
            }
            else{
                $split = $this->parseCommand($cmd);
                if($split != null){
                    return $this->processCommand($split);
                }
                else{
                    return $this->fireError(SMSMASS_ERR_PROTOCOL_ERROR, 'Protocol error - Command unknow : '.$cmd, false);
                }
            }
        }

        function parseCommand($tagCmd){
            $cmd = explode(" ", $tagCmd);
            if($cmd == false) return null;
            else if(isset($cmd) && count($cmd) == 1) return null;
            else if(isset($cmd) && count($cmd) > 1) {
                if($this->isCommand($cmd[1])) return $cmd;
                else return null;
            }
            else return null;
        }

        function isCommand($cmd){
            $tmp = eregi(COMMANDS, strtoupper($cmd));
            return $tmp;
        }

        function replaceChars($str){
            $str2 = ereg_replace("\n", "", $str);
            $str2 = ereg_replace("\r", "", $str2);
            return $str2;
        }

        function processCommand($split){
            if(is_array($split)){
                $idEnvio = 0;
                $idRebut = intval($split[0]);
                $splitcmd = strtoupper($split[1]);

                switch(true) {
                    case (ereg('(D?F?[BU]?)?SUBMITOK', $splitcmd) || $splitcmd === 'WAPLINKOK' || $splitcmd === 'CHECKALLOK' || $splitcmd === 'CHECKNETWORKOK') :
                    // Can be BSUBMITOK, USUBMITOK, etc...
                    $e = floatval($split[2]);
                    $dok = explode(' ', $split[3], 2);
                    $d = floatval($dok[0]) / 100;
                    $this->setCredit($e + $d);
                    return 1;
                    break;

                    case $splitcmd === 'OK':
                    return $this->rebutOK($split, $this->liniaEnviada);
                    break;

                    case $splitcmd === 'NOOK':
                    return $this->rebutNOOK($split, $this->liniaEnviada);
                    break;

                    case $splitcmd === 'RTARIFA':
                    if(count($split) >= 6)
                    return implode(" ", array_slice($split, 2));
                    else
                    return -1;
                    break;

                    case ($splitcmd === 'RSALDO'):
                    $e = floatval($split[2]);
                    $dok = explode(' ', $split[3], 2);
                    $d = floatval($dok[0]) / 100;
                    $this->setCredit($e + $d);
                    return $this->credit;
                    break;

                    case $splitcmd === 'RINFONUM':
                    if(count($split) >= 5){
                        return $split[2].' '.$split[3].' '.$split[4];
                    }
                    else return -1;
                    break;

                    case $splitcmd === 'RTRANS':
                    $splitcmd2 = strtoupper($split[2]);
                    switch(true){
                        case $splitcmd2 === 'INICIAR': break;
                        case $splitcmd2 === 'ABORTAR': break;
                        case $splitcmd2 === 'FIN':
                        if(strtoupper($split[3]) == 'OK'){
                            $e = floatval($split[2]);
                            $dok = explode(' ', $split[3], 2);
                            $d = floatval($dok[0]) / 100;
                            $this->setCredit($e + $d);
                        }
                        else if(strtoupper($split[3]) == 'NOOK'){
                            return -1;
                        }
                        break;
                    }
                    return 1;
                    break;

                    case $splitcmd === 'REJDST':
                    $rebutjats = explode(' ',$split[2]);
                    $rebutjats = ' REJDST list: '.$rebutjats;
                    return $rebutjats;
                    break;

                    case $splitcmd === 'BYE':
                    return $this->doBye();
                    break;

                    case $splitcmd === 'PING':
                    $this->enviarDadesTCP('PONG '.$split[2], 0);
                    break;

                    case $splitcmd === 'USERAGENTOK':
                    return 1;
                    break;

                    default	:
                    return 0;
                }
            }
            else{
                return $this->fireError(SMSMASS_ERR_PROTOCOL_ERROR, 'Protocol error - Command unknow : '.$cmd, false);
            }
        }

        function rebutOK($split, $enviat){
            if(count($split) > 3){
                if(is_float($split[2].'.'.$split[3])){
                    $this->setCredit(floatval($split[2].'.'.$split[3]));
                }
            }

            if(eregi('LOGIN', $enviat)){
                $this->isConnected = true;
                return $this->enviarDadesTCP('USERAGENT Simple API PHP version '.SimpleSMS_VERSION, 0);
            }
            return 1;
        }

        function rebutNOOK($split, $enviat){
            if($this->debug == true) $this->log->debug('[ Protocol ] '.$this->objID.' NOOK : '.$enviat);
            if(eregi('LOGIN', $enviat)){
                $this->isConnected = false;
				
                if(eregi('already', $split[1])){
                    return $this->fireError(SMSMASS_ERR_ALREADY_LOGGED, 'You are already logged', true);
                }
                else{
                    return $this->fireError(SMSMASS_ERR_INVALID_USER_OR_PASSWORD, 'Invalid user or password', true);
                }
            }
            return -1;
        }

        function sendSMS(&$idEnvio, $mode, $text, $recipients, $dateTime){
            if(is_array($text) && is_array($recipients)){
                if(!$this->isAnyEmpty($text) && !$this->isAnyEmpty($recipients)){
                    if(count($text) == count($recipients)){
                        return $this->doSubmitTrans($idEnvio, $mode, $text, $recipients, $dateTime);
                    }
                }
            }
            else if(!is_array($text) && is_array($recipients)){
                if(!$this->isAnyEmpty($recipients) && isset($text)){
                    // The same text for all recipeints
                    if($this->CS == ''){
                        $ok = $this->sendNDST($idEnvio, $recipients, 30);
                        $ok = $this->enviarDadesTCP('MSG '.$text, idEnvio);
                        $ok = $this->enviarDadesTCP('ENVIA', $idEnvio);
                        return $ok;
                    }
                    else{
                        return $this->doSubmitTrans($idEnvio, $mode, $text, $recipients, $dateTime);
                    }
                }
            }
            else if(is_array($text) && !is_array($recipients)){
                if(!$this->isAnyEmpty($text) && isset($recipients)){
                    // Multiple texts for only one recipient? ok...
                    $ok = true;
                    foreach($text as $t){
                        $ok = $this->doSubmit($idEnvio, $mode, $t, $recipients, $dateTime);
                        $idEnvio++;
                    }
                    return $ok;
                }
            }
            else{
                return $this->doSubmit($idEnvio, $mode, $text, $recipients, $dateTime);
            }
            return -1;
        }

        function isInternationalNumberFormat($str){
            return(ereg("\+?[0-9]", $str) == 1);
        }

        function doSubmit($idEnvio, $mode, $text, $recipient, $dateTime){
            // Modo desconocido -> Submit normal
            if($mode != 'U' && $mode != 'B'){
                $mode = '';
            }

            $dt = '';
            $dt2 = $dt;
            $cs = $this->CS;

            if(ereg("[0-9]{12}", $dateTime)){
                // Transform the dateTime to UTC [+-] HHMM

                $utc = date('O'); // UTC del servidor!
                if(intval($utc) >= 0) $utc = str_replace('+', '-', $utc);
                else $utc = str_replace('-', '+', $utc);
                $dt = 'D';
                $dt2 = $dateTime.$utc.' ';
            }

            if($cs != ''){
                if($this->isInternationalNumberFormat($cs)){
                    $cs = str_replace('+', '', $cs);
                }
                else{
                    $cs = str_replace(' ', '%', $cs);
                }
                $mode = 'F'.$mode;
                $cs = $cs.' ';
            }
            // Enviem el missatge
            return $this->enviarDadesTCP($dt.$mode.'SUBMIT '.$dt2.$cs.$recipient.' '.$text, $idEnvio);
        }

        function doSubmitTrans($idEnvio, $mode, $text, $recipient, $dateTime){
            // Modo desconocido -> Submit normal
            if($mode != '' && $mode != 'U' && $mode != 'B'){
                $mode = '';
            }

            $aa = ' ';

            $dt = '';
            $dt2 = $dt;
            if(ereg("[0-9]{12}", $dateTime)){
                // Transform the dateTime to UTC/GMT

                $utc = date('O'); // UTC del servidor!
                //$utime = strtotime($dateTime);
                //$utc = date('O', $utime);
                if(intval($utc) >= 0) $utc = str_replace('+', '-', $utc);
                else $utc = str_replace('-', '+', $utc);
                $dt = 'D';
                $dt2 = $dateTime.$utc.' ';
            }

            $cs = $this->CS;
            if($cs != ''){
                if($this->isInternationalNumberFormat($cs)){
                    $cs = str_replace('+', '', $cs);
                }
                else{
                    $cs = str_replace(' ', '%', $cs);
                }
                $mode = 'F'.$mode;
                $cs = $cs.' ';
            }

            // Iniciem la transaccio
            $ok = $this->enviarDadesTCP('TRANS INICIAR', $idEnvio);
            if($ok != -1){
                $j = 0;
                $MAX = 0;
                $textToSend = '';
                $blnNextText = false;
                if(is_array($text) && count($text) > 0){
                    $blnNextText = true;
                    $textToSend = $text[$j];
                    $MAX = count($text);
                }
                else $textToSend = $text;

                // Enviem els missatges
                $ok = 1;
                $max =count($recipient);
                for($i = 0; $i < $max && $ok != -1; $i++){
                    // Enviem el missatge
                    $ok = $this->enviarDadesTCP($dt.$mode.'SUBMIT'.$aa.$dt2.$cs.$recipient[$i].' '.$textToSend, $idEnvio);
                    // Si hi ha mes RECIPIENTS que TEXT envia l'ultim.
                    if($blnNextText && $j < $MAX){
                        $j++;
                        $textToSend = $text[$j];
                    }
                }

                if($ok == -1) return -1;

                // Finalitzem la transaccio
                return $this->enviarDadesTCP('TRANS FIN', $idEnvio);
            }
            else return -1;
        }

        function isAnyEmpty($s){
            if(isset($s)){
                $blnEmpty = false;
                $max = count($s);
                for($i = 0; $i < $max && !$blnEmpty; $i++){
                    if(empty($s[$i]) || $s[$i] == '') $blnEmpty = true;
                }
                return $blnEmpty;
            }
            else return true;
        }

        function sendNDST($idEnvio, $r, $n){
            if(is_array($r)){
                $telefons = '';
                $contarEnvios = 0;
                $ok = 0;
                foreach($r as $v){
                    $telefons .= $v.' ';
                    if($i % $n == 0){
                        $ok = $this->enviarDadesTCP('DST '.$telefons, $idEnvio);
                        $telefons = '';
                        $contarEnvios++;
                    }
                }

                if($telefons != ''){
                    $ok = $this->enviarDadesTCP('DST '.$telefons, $idEnvio);
                }
                return $ok;
            }
            else if($r != ''){
                return $this->enviarDadesTCP('DST '.$r, $idEnvio);
            }
        }

        function doWapLinkTrans($idEnvio, $subject, $URL, $recipients){
            // Iniciem la transaccio
            $ok = $this->enviarDadesTCP('TRANS INICIAR', $idEnvio);
            if($ok != -1){
                // Si URL es un unico string envia ese string,
                // Si es un array, enviara elemento a elemento, si hay mas destinatarios que URL
                // se les enviara la ultima URL del array.
                $j = 0;
                $MAX = 0;
                $urlToSend = '';
                if(is_array($URL) && count($URL) > 0){
                    $urlToSend = $URL[$j];
                    $MAX = count($URL);
                }
                else $urlToSend = $URL;

                // Enviem els missatges
                $ok = 1;
                $countRecipients = count($recipients);
                for($i = 0; $i < $countRecipients && $ok != -1; $i++){
                    // Enviem el missatge
                    $ok = $this->enviarDadesTCP('WAPLINK '.$recipients[$i].' '.$urlToSend.' '.$subject, $idEnvio);
                    // Si hi ha mes RECIPIENTS que TEXT envia l'ultim.
                    if($j < $MAX){
                        $j++;
                        $urlToSend = $URL[$j];
                    }
                }

                if($ok == -1) return -1;

                // Finalitzem la transaccio
                return $this->enviarDadesTCP('TRANS FIN', $idEnvio);
            }
            else return -1;
        }

        function sendMSG($idEnvio, $tipus, $subject, $text, $recipients, $mimeType, $fileData){
            if(!isset($subject) || $subject == ''){
                return $this->fireError(SMSMASS_NOOK_SUBJECT_NOT_FOUND, 'Subject not found', false);
            }

            if(!isset($mimeType) || $mimeType == '' || !isset($fileData) || $fileData == ''){
                return $this->fireError(SMSMASS_NOOK_MIMETYPE_OR_DATA_NOT_FOUND, 'Mimetype or data not found', false);
            }
            else{
                $ok = true;
                $ok = $this->sendNDST($idEnvio, $recipients, 30);
                if($ok != -1){
                    // Enviem el texte
                    $txt = '';
                    if(is_array($text) && count($text > 0)) $txt = $text[0];
                    else $txt = $text;

                    //$txt = str_replace('\n', '<br/>', $txt);
                    //$txt = str_replace('$', '$$', $txt);
                    //$txt = str_replace('&', '&amp;', $txt);
                    $ok = $this->enviarDadesTCP($tipus.'MSG '.$mimeType.' '.$fileData.' '.$subject.'|'.$txt, $idEnvio);
                    if($ok != -1){
                        // Enviem el missatge
                        $ok = $this->enviarDadesTCP('ENVIA', $idEnvio);
                        return $ok;
                    }
                    else return -1;
                }
                else return -1;
            }
        }
    }
} // if(!class_exists('SimpleSMS'))

?>