<?php 
	if (isset($vars['output'])){
		if (isset($vars['conditions'])){
			 
			foreach($vars['conditions'] as $conditions){
				//echo _r($conditions['credentials']);	
				$s=Session::parseCredentials($conditions['credentials']);
				
				if ($s=='') $condi=true; else eval("\$condi=$s;");
				if ($condi){
					foreach($conditions['modifiers'] as $modifier=>$value){
						$vars[$modifier]=$value;
					}
				}
			}	
		}
		//echo _r($vars);
		echo Viewer::view("output/".$vars['output'],$vars);
	
	}else{
		echo sprintf(Viewer::_echo('form:typenotdefined'),preg_replace("/^(.*)\/(.*)\.php$/","$2",$vars['file']));
	}
	
?>