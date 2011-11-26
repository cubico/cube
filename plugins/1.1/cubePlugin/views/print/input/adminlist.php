<?php 
	$ctrl=Controller::getInstance();	
	
	$controlFile=$ctrl->getControlFile();
	$file=$controlFile['file'];
	$objclass=$controlFile['class'];

	
	$action_url=explode("/",$vars['url']);
	$action=end($action_url);
	$module=prev($action_url);
	if (!$module) $module=$ctrl->getRoute('module');
	
	$actions=Site::getInstance()->getConfiguration('actions');
	$act=array_keys($actions);
	
	foreach($act as $a){
		if (preg_match("/".$module."$/",$a)){
			$file=$actions[$a];
			if (preg_match("/^plugins\/(.*)\/(.*)$/",$a,$args)){
				$objclass=ucfirst($args[2]).ucfirst($args[1])."Actions";
			}
		}
	}
	
	include_once $file; 
	$c=new $objclass;
	
	$request=Request::getInstance();
	$includes=isset($vars['include'])?$vars['include']:array();
	if (!is_array($includes)) $includes=array($includes);
	
	$vincludes=array();
	foreach($includes as $inc){
		$vincludes[$inc]=$request->getParameter($inc,null);	
	}
	$request->include=$vincludes;
	
	$class = $vars['class'];
	if (!$class) $class = "input-text";
	
	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
	
	ob_start();
	
	//if (isset($vars['ajax']) && $vars['ajax']){
		eval("\$ret=\$c->execute".ucfirst($action)."(\$request);");
		$json=ob_get_clean();
		$ret=json_decode($json,true);
		//echo _r($ret);die();
		if (isset($vars['template']))
			$returnVal=preg_replace("/%%([^%]*)%%/","'.htmlentities(\$ret['data'][0]['$1'],ENT_QUOTES,'UTF-8').'",$vars['template']);
		else
			$returnVal=htmlentities($ret['data'][0]['text'],ENT_QUOTES,'UTF-8');

?>
<span  class="<?php echo $class ?>" <?php echo $js; ?> name="<?php echo $vars['internalname']; ?>"><?php eval("echo '$returnVal';"); ?></span>