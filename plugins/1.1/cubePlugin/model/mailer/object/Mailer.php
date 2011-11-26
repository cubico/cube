<?php
	
	class Mailer{
		static private $instance=array();
		private $transport;
		
		static public function getInstance($mconfig=null){
			if ($mconfig==null) $mconfig='mail';
			
			if (!isset(self::$instance[$mconfig])) {
				$conf=Config::get('cubePlugin:'.$mconfig);
				$conf['smtpHost']='smtp.parcsanitari.local';
				$transport = MailTransport::newInstance($conf['smtpHost'],$conf['smtpPort']);
				self::$instance[$mconfig] = new Mailer($transport);
			}
			return self::$instance[$mconfig];
		}
		
		public function __construct($transport){
			$this->_transport=$transport;
		}
		
		public function send(MailMessage $message, &$failedRecipients = null)
		{
			$failedRecipients = (array) $failedRecipients;
			//if (!$this->_transport->isStarted()){$this->_transport->start();}
			return $this->_transport->send($message, $failedRecipients);
		}
	}
	
	/////////////////////////////////////////////////////////////
	
	class Mailer_old
	{
		private $host;
		private $port;
		private $from;
		private $fromName;
		private $ifError;
		private $priority;
		private $to;
		private $title;
		private $images;
		private $files;
		private $html;
		private $msg;
		private $cc;
		private $bcc;
		private $separator;
		
		public function __construct($host,$port){
			$this->host=$host;
			$this->port=$port;
			$this->to=array();
			$this->cc=array();
			$this->bcc=array();
			$this->separator=md5(time());
		}
		
		static public function getInstance($host=null,$port=null){
			return new Mailer_old($host,$port);
		}
		
		public function smtphost($host){ $this->host=$host; return $this;}
		public function smtpport($port){ $this->port=$port; return $this;}
		public function from($from){ $this->from=$from; return $this;}
		public function fromName($name){ $this->fromName=$name; return $this;}
		public function toIfError($toerror){ $this->ifError=$toerror; return $this;}
		public function priority($n){ $this->priority=$n; return $this;}
		public function to($to){ $this->to[]=$to; return $this;}
		public function cc($cc){ $this->cc[]=$cc; return $this;}
		public function bcc($bcc){ $this->bcc[]=$bcc; return $this;}
		public function title($title){ $this->title=$title; return $this;}
		public function image($img){ $this->file('',$img,'txt'); return $this;}
		
		public function file($filename,$pdf=null,$bin='txt'){ 
			$this->files[$filename]=array($bin,$pdf); 
			return $this;
		}
		public function html($html){ $this->html[]=$html; return $this;}
		public function send(&$html="",$send=true){
			$contenido=$this->addHtml($html);
			try{
				if ($send) {
					//For Win32 only.
					ini_set('SMTP',$this->host);
					ini_set('smtp_port',$this->port);
					return !@mail(implode(", ",$this->to), $this->title,"",$this->addHeaders().$contenido);
				}
			}catch(CubeException $e){
				return false;
			}
			return false;
		}
		
		private function addHeaders(){
			$cabecera  = 							"From: {$this->fromName} <{$this->from}>".PHP_EOL;
			if (count($this->cc)>0) $cabecera .= 	"Cc: ".implode(", ",$this->cc).PHP_EOL;
			if (count($this->bcc)>0) $cabecera .= 	"Bcc: ".implode(", ",$this->bcc).PHP_EOL;
			$cabecera .= 							"X-Sender: <{$this->from}>".PHP_EOL;
			$cabecera .= 							"X-Mailer: PHP\n"; //mailer				
			if (!empty($this->priority)) $cabecera .= "X-Priority: {$this->priority}".PHP_EOL; //
			if (!empty($this->ifError))  $cabecera .= "Return-Path: <{$this->ifError}>".PHP_EOL;
			$cabecera .= 							"Reply-To: ".implode(", ",$this->to).PHP_EOL; 
			
			// main header (multipart mandatory)			
			$cabecera .= "MIME-Version: 1.0".PHP_EOL;			
			$cabecera .= "Content-Type: multipart/mixed; boundary=\"".$this->separator."\"".PHP_EOL.PHP_EOL;			
			$cabecera .= "Content-Transfer-Encoding: 7bit".PHP_EOL;			
			$cabecera .= "This is a MIME encoded message.".PHP_EOL.PHP_EOL;
				
			return $cabecera;
		}
		/*
		private function addImages(){
			$headers ="";
			
			foreach($this->images as $i=>$image){
				/// recoger datos de la imagen
				$fp = fopen($image, "r"); 	
				$contenido = fread($fp, filesize($image)); 
				fclose($fp); 
				
				$attachment = chunk_split(base64_encode($contenido)); 
				
				/// añadir cabecera de imagen
				$headers .= "--".$this->separator.PHP_EOL;			
				$headers .= "Content-Type: image/jpeg".PHP_EOL;		
				$headers .= "Content-ID: <image{$i}>".PHP_EOL;		
				$headers .= "Content-Transfer-Encoding: base64".PHP_EOL;			
				$headers .= "Content-Disposition: inline; filename=\"image{$i}.jpg\"".PHP_EOL.PHP_EOL;			
				$headers .= $attachment.PHP_EOL.PHP_EOL;
				
				
				//$msg.="\n--==boundary-1\n".
				//	 "Content-Type: image/jpeg\n".
				//	 "Content-ID: <image{$i}>\n".
				//	 "Content-Transfer-Encoding: base64\n".
				//	 "Content-Disposition: inline; filename=\"image{$i}.jpg\"\n".
	 			//	chunk_split(base64_encode($contenido)).
				//	"\n--==boundary-1--";
				$headers .= "--".$this->separator."--";
			}
			
			
			return $headers ;
		}
		*/
		
		private function addFiles(){
			$headers="";
			$cont=1;
			foreach($this->files as $i=>$pdf){
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
				$headers .= "--".$this->separator.PHP_EOL;			
				$headers .= "Content-Type: application/octet-stream; name=\"".$pdfname."\"".PHP_EOL;			
				$headers .= "Content-Transfer-Encoding: base64".PHP_EOL;			
				$headers .= "Content-Disposition: attachment".PHP_EOL.PHP_EOL;			
				$headers .= $attachment.PHP_EOL.PHP_EOL;			
				
				$cont++;		
			}
			$headers .= "--".$this->separator."--";
			//return $msg;
			return $headers;
		}
		
		private function addHtml(&$html){
			
			$html=implode("\n",$this->html);
			
			if (!preg_match("/DOCTYPE/i",$html))
				$doctype="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 3.2//EN\">\n";
			else 
				$doctype="";	
			
			// message			
			$headers = "--".$this->separator.PHP_EOL;		
			$headers .= "Content-Type: text/html; charset= utf-8".PHP_EOL;			
			$headers .= "Content-Transfer-Encoding: 8bit".PHP_EOL.PHP_EOL;			
			$headers .= $doctype.$html.PHP_EOL.PHP_EOL;		

			$attach=false;
			if (count($this->files)>0)	{$attach=true;	$files=$this->addFiles();}else $files='';
			if ($attach) return $headers.$files;
			return $headers;
		}
		
		
	}
		
	
?>