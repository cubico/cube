<?php 
class Log
{
	const SUCCESS=0; 
	const ERROR=1;
	const WARNING=2;
	
	static private $messages=array(); 
	static private $cont;
	
	static public function itemsTemplate($time,$method,$type,$message,$error,$pos){
		
		$c=Viewer::view('canvas/column',array('class'=>'logger','width'=>'60px','render'=>$time),'log');
		$c.=Viewer::view('canvas/column',array('class'=>'logger','width'=>'250px','render'=>$method),'log');
		$c.=Viewer::view('canvas/column',array('class'=>'logger link '.$type,'width'=>'30px','render'=>''),'log');
		$c.=Viewer::view('canvas/column',array('class'=>'logger','width'=>'500px','render'=>utf8_encode($message)),'log');
		$c.=Viewer::view('canvas/column',array('class'=>'logger'.' icon '.$error,'width'=>'30px','render'=>''),'log');
		$content=Viewer::view('canvas/row',array('class'=>'item '.$type.$pos,'width'=>'920px','content'=>$c),'log');
		
		return $content;
	}
	
	static private function template($cur,$ajax=false){
			
			$method=strtoupper($cur['method']);
			$message=trim($cur['message']);

		if (!$ajax){
		 	$content=self::itemsTemplate($cur['time'],$method,$cur['type'],$message,$cur['error'],$cur['pos']);
		}else
			$content=array(	"time"=>$cur['time'], "method"=>$method, "type"=>$cur['type'],
							"message"=>$message, "error"=>$cur['error'],"pos"=>$cur['pos']);
			
		return $content;
	}
	
	static public function add($hook, $entity_type, $returnvalue, $params){
		
		if (Config::get('settings:logs:enabled','site',true))
		{
			$errors=array("success","error","warning");
			$params['type']=$entity_type;
			$params['time']=microtime(true);
			$params['error']=$errors[$params['error']];
			$params['subtype']=$params['class'];
			if (Controller::isAjaxRequest()) $params['ajax']=true;
			self::$messages[]=$params;
		}
	}
	
	
	static public function add2($method,$message,$type='user',$subtype='',$error=0,$time=null)
	{
		$log_enabled=Config::get('settings:logs:enabled','site',true);
		
		if ($log_enabled)
		{
			if ($time==null) $time=microtime();
			$errors=array("success","error","warning");
			$params=array("method"=>$method,"message"=>$message,"type"=>$type,"subtype"=>$subtype,"time"=>$time,"error"=>$errors[$error]);
			self::$messages[]=$params;
		}
	}
	
	
	static function get($type=null)
	{
		if ($type==null) return self::$messages;
		
		$data=array();
		foreach(self::$messages as $msg)
		{
			if ($msg['type']==$type) $data[]=$msg;	
		}
		return $data;
	}
	
	static public function headersTemplate($title='title'){
		$hc=Viewer::view('canvas/column',array('class'=>'logger '.$title,'width'=>'60px','render'=>'TIME'),'log');
		$hc.=Viewer::view('canvas/column',array('class'=>'logger '.$title,'width'=>'250px','render'=>'METHOD'),'log');
		$hc.=Viewer::view('canvas/column',array('class'=>'logger '.$title,'width'=>'30px','render'=>'TYPE'),'log');
		$hc.=Viewer::view('canvas/column',array('class'=>'logger '.$title,'width'=>'500px','render'=>'MESSAGE'),'log');
		$hc.=Viewer::view('canvas/column',array('class'=>'logger '.$title,'width'=>'30px','render'=>'INFO'),'log');
		
		return Viewer::view('canvas/row',array('class'=>$title.' header','width'=>'920px','content'=>$hc),'log');
	}
	
	static public function plainText($log)
	{
		$info="";
		$time=null;
		$ajaxinfo=array();
		if (count($log)==0) return false;
		
		$first=$log[0]['time'];
		
		$tipos=array();
		foreach($log as $i=>$cur)
		{
			$tiempo=$cur['time'];
			$time=$tiempo-$first;
			
			$stime=($time>1)?(number_format($time,3,",",".")." s"):(number_format($time*1000,3,",",".")." ms");
			
			$cur['time']=$stime;
			$cur['pos']=(($i%2)==0)?' impar':' par';
			
			if (!Controller::isAjaxRequest()){
				
				$info.=self::template($cur); 
				$tipos[$cur['type']]=$cur['pos'];
			}else{
				$ajaxinfo[]=self::template($cur,true);
			}
		}
		
		if (Controller::isAjaxRequest()) return json_encode($ajaxinfo); 
		
		$h=Viewer::view('canvas/column',array('class'=>'console','width'=>'400px','render'=>'<b>CUBE v.'.Config::get('settings:version').'</b> - debug console - dev mode'));
		krsort($tipos);
		
		$imgblk='<img title="%s" src="/img/clear.gif" class="clear" />';
		
		$h.=Viewer::view('canvas/column',array('class'=>'logger linkh reset','width'=>'16px','render'=>sprintf($imgblk,'reset')));
		foreach($tipos as $tip=>$pos){
			$h.=Viewer::view('canvas/column',array('class'=>'logger linkh '.$tip,'width'=>'16px','render'=>sprintf($imgblk,$tip)));
		}
		$h.=Viewer::view('canvas/column',array('class'=>'logger tmp main','width'=>'16px',
												'render'=>Viewer::view('output/link',array(
													'title'=>'principal',
													'js'=>'onClick="$(\'#divlogger #principal\').toggle();"',
													'img'=>sprintf($imgblk,'main')
												)
						)),'log');
		
		$mainmenu=Viewer::view('canvas/row',array('class'=>'menu','width'=>'920px','content'=>$h),'log');
		$headers=Viewer::view('canvas/column',array('class'=>'console','width'=>'100%','render'=>"<div style=\"padding:5px;text-align:right;\">Main ({$stime})</div>"),'log')
				.self::headersTemplate();
		
		$str = <<<EOT
				<!-- DEBUG -->
			<link rel="stylesheet" href="/css/logger.css" type="text/css" media="screen" />
				<div id="consolelogger">
					<span id="buttonlogger" style="cursor:pointer;background:#fff;" class="logger">LOG</span>
					<div id="divlogger" class="logger" style="width:925px;display:none;margin:10px;">$mainmenu<div id="principal">$headers $info</div></div>
				</div>
				<!-- END DEBUG -->
EOT;

		echo $str;
	}
}
?>