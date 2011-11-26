<?php
	require_once dirname(__FILE__).'/../../../lib/simplesms.php';
	
	class cubeSMS{
		static private $instance;
		static private $sms;
		
		// singleton
		
		static public function createInstance($host=null){							
			if (!isset(self::$instance) && $host!==null) {
				self::$instance = new cubeSMS($host); 
				return self::$instance ;
			}
			return null;
		}
		
		static public function getInstance(){							
			return self::$instance ;
		}
		
		public function __construct($host){
			$conf=Config::get('smsPlugin:'.$host);
			self::$sms=new SimpleSMS($conf['user'], $conf['password']);
		}
		
		static private function getValidNumber($num){
		    if(eregi('34([0-9]{9})', $num)) return '+'.$num;
		    else if(eregi('([0-9]{9})', $num)) return '+34'.$num;
		    else return '+'.$num;
		}
		
		/* Ejemplo 
		 * 
		 * $sms->enviarSMS(array(
		        'id'=>'',
		        'remitente'=>'ICS',
		        'mobil'=>$telef,
		        'text'=>$mens,
		        'contenido'=>'1',
		        // MMS - Waplink - Wappush
		        'asunto'=>'',
		        'mimetype'=>'',
		        'ruta'=>'',
		        // SMS Certificado
		        'mail'=>'test@mydomain.com',
		        'lang'=>'ES',
		    ));
		 
		 */
		public function enviaSMS($remitent,$mobil,$missatge){
			return $this->enviarSMS(array(
						        	'remitente'=>$remitent,
						        	'mobil'=>$mobil,
						        	'text'=>$missatge,
						        	'contenido'=>'1'));
		}
		
		static private function enviarSMS($registre=''){
			$resultado = '0';
			
			$txt = eregi_replace('<NOMBRE>', $registre['remitente'], $registre['text']);
		
			if (substr($registre['mobil'], 0, 1) == "6" && strlen($registre['mobil']) == 9){
				$dst = self::getValidNumber($registre['mobil']);
			}else{
			    return -1;
			}
			$sms=self::$sms;
			$sms->connect();
		
			if(!$sms->GetConnectionStatus()){
		        $resultado = -2;
			}
			else{
		      	$idEnvio = isset($registre['id'])?$registre['id']:'';
		        switch ($registre['contenido']) {
			        case 1:
			        	$resultado = $sms->sendTextSMS($idEnvio, $txt, $dst, '');
		       		break;
		        	case 2:
		        		$resultado = $sms->sendWapPush($idEnvio, $registre['asunto'], $txt, $dst, $registre['mimetype'], $sms->getFileContentBase64($registre['ruta']));
		       		break;
		        	case 3:
		        		$resultado = $sms->sendWapLink($idEnvio, $txt, $registre['ruta'], $dst);
		       		break;
		        	case 4:
		       			$resultado = $sms->sendMMS($idEnvio, $registre['asunto'], $txt, $dst, $registre['mimetype'], $sms->getFileContentBase64($registre['ruta']));
		       		break;
			        case 5:
			            $resultado = $sms->acuseOnCertifiedSMS($registre['mail'], $registre['lang']);
						if ($resultado < 0) return -3;
			        	$resultado = $sms->sendTextSMS($idEnvio, $txt, $dst, '');
		       		break;
		        	default:
		           		$resultado = 0;
		           	break;
		        }
		       	$sms->disconnect();
		   }
		   return $resultado;
		}
	}
?>