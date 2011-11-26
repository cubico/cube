<?php

class MailMessage{
		private $body;
		private $charset;
		private $contentType;
		private $subject;
		
		private $from;
		private $fromname;
		private $to;
		private $cc;
		private $bcc;
		private $files;
		
		/**
		* Create a new Message.
		* Details may be optionally passed into the constructor.
		* @param string $subject
		* @param string $body
		* @param string $contentType
		* @param string $charset
		*/
		public function __construct($subject = null, $body = null, $contentType = null, $charset = null)
		{
			$this->charset = !isset($charset)?'UTF-8':$charset;
			$this->subject=isset($subject)?$subject:'';
			$this->body=isset($body)?$body:'';
			$this->contentType=isset($contentType)?$contentType:'text/html';
		}
		
		/**
		* Create a new Message.
		* @param string $subject
		* @param string $body
		* @param string $contentType
		* @param string $charset
		* @return Swift_Mime_Message
		*/
		public static function newInstance($subject = null, $body = null,$contentType = null, $charset = null)
		{
			return new self($subject, $body, $contentType, $charset);
		}
		
		public function setContentType($contentType){$this->contentType=$contentType; return $this;}
		public function setBody($body){$this->body=$body; return $this;}
		public function setSubject($subject){$this->subject=$subject; return $this;}
		public function setCharset($charset){$this->charset=$charset; return $this;}
		public function setFrom($from,$name='dummy'){$this->from=$from; $this->fromname=$name;return $this;}
		public function setTo($to){$this->to=(array)$to; return $this;}
		public function setCc($cc){$this->cc=(array)$cc; return $this;}
		public function setBcc($bcc){$this->bcc=(array)$bcc; return $this;}
		public function addTo($to){  if (is_array($to)) $this->to=array_merge($this->to,$to);else $this->to[]=$to; return $this;}
		public function addCc($cc){  if (is_array($cc)) $this->cc=array_merge($this->cc,$cc);else $this->cc[]=$cc; return $this;}
		public function addBcc($cc){  if (is_array($bcc)) $this->bcc=array_merge($this->bcc,$bcc);else $this->bcc[]=$bcc; return $this;}
		
		public function attach($filename,$pdf=null,$bin='txt'){ 
			$this->files[$filename]=array($bin,$pdf); 
			return $this;
		}
				
		public function getContentType(){return $this->contentType;}
		public function getBody(){return $this->body;}
		public function getSubject(){return $this->subject;}
		public function getCharset(){return $this->charset;}
		public function getFrom(){return $this->from;}
		public function getFromName(){return $this->fromname;}
		public function getTo(){return $this->to;}
		public function getCc(){return $this->cc;}
		public function getBcc(){return $this->bcc;}
		public function getFiles(){return $this->files;}
	}
	
?>
