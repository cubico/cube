<?php 
/*
 * EJEMPLO *
	$channel=array(	"title"=>"Notícies - Camp de Tarragona",
	 				"link"=>".",
	 				"description"=>"Unitat de Tecnologies de la Informació i Comunicació",
	 				"language"=>"ca-es");
	
	$image=array(	"title"=>"Unitat de Tecnologies de la Informació i Comunicació",
	 				"url"=>"/rss/img/rsswriter.gif",
	 				"link"=>"/",
	 				"width"=>90,
	 				"height"=>36);
	
	$item1=array("title"=>"Resolució del Fons d’Acció Social (FAS)",
				"link"=>"http://camptarragona/suport-als-professionals/recursos-humans/281-fons-daccio-social-fas");
	$item2=array("title"=>"Jornada Gastronòmica Asturiana a la cafeteria de l'Hospital ",
				"link"=>"http://camptarragona/suport-als-professionals/serveis-generals/hostaleria/278-jornada-gastronomica-asturiana");
	
	ob_start();
	$item1['description']=ob_get_clean();
	
	ob_start();
	$item2['description']=ob_get_clean();
	
	RSS::create()
		//->setHost("http://www.example.com")
	 	//->setHeader('Content-Type: text/plain; charset="utf-8"')
		->setSpecification(0.91)
		->addChannel($channel)
		->addImage($image)
		->addItem($item1)
		->addItem($item2)
		->output();
*/

class RSS{

	private $channel;
	private $specification;
	private $header;
	private $channels;
	private $host;
	
	static public function create(){
		$obj=new RSS();
		return $obj; 
	}
	
	public function __construct(){
		
		$this->channels=array();
		$this->channel=0;
		
		$host=$_SERVER["SERVER_NAME"];
		$protocol=(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')?"https://":"http://";
		$this->host=$protocol.$host;
	}
	
	public function setHost($v){ $this->host=$v; return $this;}
	public function setHeader($v){ $this->header=$v; return $this;}
	public function setSpecification($v){ $this->specification=$v; return $this;}
	public function addChannel($v=array())
	{
		$this->channel=$this->channel+1;
		$id=$this->channel;
		$this->channels[$id]['info']=$v;
		return $this;
	}
	
	public function addItem($v,$channelId=null){
		if ($channelId==null) $channelId=$this->channel;
		$this->channels[$channelId][]=$v;
		return $this;
	}
	
	public function addImage($v,$channelId=null){
		if ($channelId==null) $channelId=$this->channel;
		$this->channels[$channelId]['image']=$v;
		return $this;
	}
	
	private function parseOption($key,$value){
		//$value=htmlentities($value);
		switch($key){
			case 'description': return '<![CDATA['. $value .']]>';
			case 'url':
			case 'link': 
				if ($value==".") return $this->host.$_SERVER['REQUEST_URI'];
				if (preg_match('#^[a-z][a-z0-9\+.\-]*\://#i', $value)) return $value;
				return $this->host.$value;
		}
		return $value;
	}
	
	public function output($debug=false){
		
		$output="<rss version=\"".$this->specification."\">";
		foreach($this->channels as $channel){
			$output.="<channel>\n";
			foreach($channel['info'] as $key=>$value){
				$output.="<{$key}>".$this->parseOption($key,$value)."</{$key}>\n";
			}
			unset($channel['info']);
			if (isset($channel['image'])){
				$output.="<image>\n";
				foreach($channel['image'] as $key=>$value){
					$output.="<{$key}>".$this->parseOption($key,$value)."</{$key}>\n";
				}
				$output.="</image>\n";
				unset($channel['image']);
			}
			foreach($channel as $k=>$item){
				$output.="<item>\n";
				foreach($item as $key=>$value){
					
					$output.="<{$key}>".$this->parseOption($key,$value)."</{$key}>\n";
				}
				$output.="</item>\n";
			}
			$output.="</channel>\n";
		}
		$output.="</rss>";
		
		if ($this->header==null) Header('Content-Type: text/xml; charset="utf-8"');
		else Header($this->header);
		
		if (!$debug) Header('Content-Length: '.strval(strlen($output)));
		echo $output;
	}
}

?>