<?php

	/**
	 * Elgg file input
	 * Displays a file input field
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * 
	 */
	
	if (isset($vars['deleteText'])){
		$deleteText=parseText($vars['deleteText']);
	}else{
		$deleteText=Viewer::_echo('button:deletelink');
	}

	if (isset($vars['maxUploadText'])){
		$maxUploadText=parseText($vars['maxUploadText']);
	}else{
		$maxUploadText=Viewer::_echo(Config::get('settings:uploadify:error_max_updloads'));
	}
	
	$templateLink='<div class="deletefileuploaded"><span title="'.$deleteText.'">%s</span><a href="%s" target="blank">%s</a></div>';
	
	$uploadedFile='';
	if (isset($vars['value'])){
		$value2=$vars['value'];	
		if (!empty($value2)){
			$links=explode(",",$value2);
			$tmp=array();
			foreach($links as $i=>$link){
				if (!empty($link)) {
					$info=explode(":",$link);
					$uploadedFile.=sprintf($templateLink,'',$info[1],$info[0]);
					$tmp[]=$link;
				}
				
			}
			$value=implode(",",$tmp);
		}else $value="";
	}else {$value="";}
	
    
    $class = $vars['class'];
	if (!$class) $class = "input-file";

	$js=(isset($vars['js'])?$vars['js']:"");
	
	$internal=$vars['internalname'];
	$strippedname=strtr($internal,"[].","___");
	$id=preg_replace("/[\[\]\.]/","_",$internal);
	
$form_body='<div id="uploadedlinks'.$strippedname.'">'.$uploadedFile.'</div>';
echo $form_body;

?>