<?php

class MailTransport{
		private $host;
		private $port;
		private $encryption;
		private $separator;
		/**
		* Create a new SmtpTransport, optionally with $host, $port and $security.
		* @param string $host
		* @param int $port
		* @param int $security
		*/
	  public function __construct($host = 'localhost', $port = 25,$security = null)
	  {
		 $this->host=$host;
		 $this->port=$port;
		 $this->encryption=$security;
		 $this->separator=md5(time());
	  }
	  
	  
	  public function getHost(){ return $this->host;}
	  public function getPort(){ return $this->port;}
	  public function getEncryption(){ return $this->encryption;}
	  
	  /**
		* Create a new SmtpTransport instance.
		* @param string $host
		* @param int $port
		* @param int $security
		* @return Swift_SmtpTransport
		*/
	  public static function newInstance($host = 'localhost', $port = 25,$security = null)
	  {
		 return new self($host, $port, $security);
	  }
	  
	  private function addHeaders($message){
			$cc=$message->getCc();
			$bcc=$message->getBcc();
			
		   $cabecera  =								"From: {$message->getFromName()} <{$message->getFrom()}>".PHP_EOL;
			if (count($cc)>0) $cabecera .=		"Cc: ".implode(", ",$cc).PHP_EOL;
			if (count($bcc)>0) $cabecera .=		"Bcc: ".implode(", ",$bcc).PHP_EOL;
			$cabecera .=								"X-Sender: <{$message->getFrom()}>".PHP_EOL;
			$cabecera .=								"X-Mailer: PHP\n"; //mailer				
			//if (!empty($this->priority)) $cabecera .= "X-Priority: {$this->priority}".PHP_EOL; //
			//if (!empty($this->ifError))  $cabecera .= "Return-Path: <{$this->ifError}>".PHP_EOL;
			$cabecera .=								"Reply-To: ".implode(", ",$message->getTo()).PHP_EOL; 
			
			// main header (multipart mandatory)			
			$cabecera .= "MIME-Version: 1.0".PHP_EOL;			
			$cabecera .= "Content-Type: multipart/mixed; boundary=\"".$this->separator."\"".PHP_EOL.PHP_EOL;			
			$cabecera .= "Content-Transfer-Encoding: 7bit".PHP_EOL;			
			$cabecera .= "This is a MIME encoded message.".PHP_EOL.PHP_EOL;
				
			return $cabecera;
		}
		
		private function addFiles($message){
			$headers="";
			$cont=1;
			$files=$message->getFiles();
			$separator=$this->separator;
			foreach($files as $i=>$pdf){
			//echo $pdf;die();
				/// recoger datos del pdf
				
				if ($pdf[0]=='txt'){			
					$contenido= file_get_contents($pdf[1]);
				}else{
					$contenido=$pdf[1];
				}
				if (empty($i) || is_numeric($i)) $pdfname='attachment'.$cont; //faltaria la extension!! 
				else $pdfname=$i;
				
				$attachment = chunk_split(base64_encode($contenido)); 
				
				// attachment
				$headers .= "--".$separator.PHP_EOL;			
				$headers .= "Content-Type: application/octet-stream; name=\"".$pdfname."\"".PHP_EOL;			
				$headers .= "Content-Transfer-Encoding: base64".PHP_EOL;			
				$headers .= "Content-Disposition: attachment".PHP_EOL.PHP_EOL;			
				$headers .= $attachment.PHP_EOL.PHP_EOL;			
				
				$cont++;		
			}
			$headers .= "--".$separator."--";
			//return $msg;
			return $headers;
		}
		
		private function addHtml($message){
			
			$html=$message->getBody();
			
			if (!preg_match("/DOCTYPE/i",$html))
				$doctype="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 3.2//EN\">\n";
			else 
				$doctype="";	
			
			// message			
			$headers = "--".$this->separator.PHP_EOL;		
			$headers .= "Content-Type: ".$message->getContentType()."; charset= ".$message->getCharset().PHP_EOL;			
			$headers .= "Content-Transfer-Encoding: 8bit".PHP_EOL.PHP_EOL;			
			$headers .= $doctype.$html.PHP_EOL.PHP_EOL;		

			$attach=false;
			if (count($message->getFiles())>0)	{$attach=true;	$files=$this->addFiles($message);}else $files='';
			if ($attach) return $headers.$files;
			return $headers;
		}
		
	  public function send($message, &$failedRecipients=array()){
			
			$contenido=$this->addHtml($message);
			
			try{
				ini_set('SMTP',$this->getHost());			//For Win32 only.
				ini_set('smtp_port',$this->getPort());		//For Win32 only.
				if(!@mail(implode(", ",$message->getTo()), $message->getSubject(),"",$this->addHeaders($message).$contenido)) return 1;
			}catch(CubeException $e){
				throw new CubeException ($e->getMessage(),$e->getCode());
			}
			return 0;
		}
	}
?>
