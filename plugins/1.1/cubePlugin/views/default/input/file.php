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
	
	
	if (isset($vars['infoText'])){
		//(isset($vars['infoText'])?$vars['infoText']:Viewer::_echo()).	
		$infoText=parseText($vars['infoText']);
	}else{
		$infoText=Viewer::_echo(Config::get('settings:uploadify:button_description'));
	}
	
	$maxUploadFiles=isset($vars['maxUploadFiles'])?$vars['maxUploadFiles']:Config::get('settings:uploadify:maxUploadFiles');
	
	$maxUploadFilesize=ini_get('upload_max_filesize');
	$infoText=sprintf($infoText,$maxUploadFiles,$maxUploadFilesize);
	
	//$vars['readonly']=true;
	$readonly=(!isset($vars['readonly']) || !$vars['readonly'])?false:true;
	if (!$readonly)
	$deleteButton='<img src="'.(isset($vars['deleteButton'])?$vars['deleteButton']:'/img/icon/cross_small.png').'" />';
	else $deleteButton=" &nbsp; ";
	$templateLink='<div class="deletefileuploaded"><span title="'.$deleteText.'">%s</span><a href="%s" target="blank">%s</a></div>';
	
	
	//echo _r($vars);
	
	$uploadedFile='';
	if (isset($vars['value'])){
		$value2=$vars['value'];	
		if (!empty($value2)){
			$links=explode(",",$value2);
			
			$tmp=array();
			foreach($links as $i=>$link){
				if (!empty($link)) {
					$info=explode(":",$link);
					$uploadedFile.=sprintf($templateLink,$deleteButton,$info[1],$info[0]);
					$tmp[]=$link;
				}
				
			}
			$value=implode(",",$tmp);
		}else $value="";
	}else {$value="";}
	
    //echo "aaaa";return 0;
    $class = $vars['class'];
	if (!$class) $class = "input-file";

	if (!defined('INPUT_FILE_UPLOADIFY')){ define('INPUT_FILE_UPLOADIFY',true);
		?><script type="text/javascript" src="<?php echo Config::get('settings:uploadify:scriptPath'); ?>swfobject.js"></script><?php
		?><script type="text/javascript" src="<?php echo Config::get('settings:uploadify:scriptPath'); ?>jquery.uploadify.v2.1.0.min.js"></script><?php
		?><script type="text/javascript" src="/js/jquery/jquery.md5.js"></script><?php
	} 
	
	$infoText='<span style="float:left;margin:0 15px;font-size:1.2em;">'.$infoText.'</span>';
				
	$path=isset($vars['scriptPath'])?$vars['scriptPath']:(Config::get('settings:uploadify:scriptPath'));
	$folder=isset($vars['folder'])?$vars['folder']:(Config::get('settings:uploadify:folder'));
	$cancelImg=isset($vars['cancelImg'])?$vars['cancelImg']:(Config::get('settings:uploadify:cancelImg'));
	$multi=(isset($vars['multi'])?$vars['multi']:(Config::get('settings:uploadify:default_multi')));
	if ($multi) $multi="true";else $multi="false";
	$auto=isset($vars['auto'])?$vars['auto']:(Config::get('settings:uploadify:default_auto'));
	if ($auto) $auto="true";else $auto="false";
	
	$uploadifyInput="uploadify".$vars['internalname'];
	
	$js=(isset($vars['js'])?$vars['js']:"");
	
	$internal=$vars['internalname'];
	$strippedname=strtr($internal,"[].","___");
	$id=preg_replace("/[\[\]\.]/","_",$internal);
	$disabled=(isset($vars['disabled']) && $vars['disabled'])?' disabled="yes"':"";
	
	$session_id=session_id();
	
	if (!isset($vars['img'])) $img="/img/icon/magnifier.png";else $img=$vars['img'];
    $buttonImg="\n		,'buttonImg': '{$img}'";
    //$buttonImg="";
	
	$prefix=isset($vars['prefix'])?$vars['prefix']:time();
	
	$fileDesc=isset($vars['fileDesc'])?$vars['fileDesc']:"";
	$fileExt=isset($vars['fileExt'])?$vars['fileExt']:"*.*";
	
	$buttonText=isset($vars['buttonText'])?$vars['buttonText']:(Config::get('settings:uploadify:button_text'));
	$description=Config::get('settings:uploadify:button_description');
	
	$cancelButton=isset($vars['cancelButton'])?"<a href=\"javascript:jQuery('#{$id}').uploadifyClearQueue();\">{$vars['cancelButton']}</a>\n":"";
	$uploadButton=isset($vars['uploadButton'])?"<a href=\"javascript:jQuery('#{$id}').uploadifyUpload();\">{$vars['uploadButton']}</a>\n":"";
	
	$successFunction=isset($vars['onSuccess'])?($vars['onSuccess']."(event,queueID,fileObj);"):"";
	
	$sizeLimit=isset($vars['sizeLimit'])?"sizeLimit: ".$vars['sizeLimit']:"";
	if (isset($vars['fileQueue'])){
      	$fileQueue="\n		,'queueID': ".$vars['fileQueue'];
      	$divFileQueue='<div id="'.$vars['fileQueue'].'"></div>';
  	}else{
    	$fileQueue="";
    	$divFileQueue="";
  	}
	//$fileQueue="";$divFileQueue='<div id="'.$id.'Queue"></div>';
	//$uploadedFile.=sprintf($templateLink,$deleteButton,$link,$link);
  	$templateLinkAjax=sprintf($templateLink,$deleteButton,'\'+valor+\'','\'+name+\'');
  	$errorIfNoUnLink=addslashes(Viewer::_echo('error:notunlink'));
	$form_body = <<<EOT
<script type="text/javascript">
$(document).ready(function() {
	var initUpload{$id}=false;
	
	function addActionLinkUpload{$id}(inputvalues,valor,bool){
		var currentvalue=inputvalues.val()
		if (currentvalue.length>0 && bool) valor+=',';
		inputvalues.val(valor+currentvalue);
		$(".deletefileuploaded span").click(function(){
					var file=$(this).next("a").attr('href');
					var obj=$(this).parents('div.deletefileuploaded');
					
					var inputvalues=$('input[name="{$internal}"]');
					var currentvalue=inputvalues.val();
					
					$.ajax({
				        url: '{$path}uploadify.php',
				        type: "POST",
				        data: {'link' : file},
				        cache: false,
				        timeout: 60000,
				        success: function(data){      
				         	
				        	var expr=new RegExp('(^|,)([^:]*):'+file);
				         	var newvalue=currentvalue.replace(expr,'');
				         	obj.remove();
				         	inputvalues.val(newvalue);
							//if (data==0) alert('$errorIfNoUnLink');		
				        }
				    });
				});
	}

	$("#{$id}").uploadify({
		'uploader'       : '{$path}uploadify.swf',
		'script'         : '{$path}uploadify.php',
		'scriptData'	  : {prefix: '{$prefix}', PHPSESSID: '{$session_id}'},
		'cancelImg'      : '{$cancelImg}',
		'folder'         : '{$folder}' ,
		'fileDesc'       : '{$fileDesc}' ,
		'buttonText'	 : '{$buttonText}',
		'fileExt'        : '{$fileExt}' 
		{$sizeLimit}{$buttonImg},
		'multi'          : {$multi},
		'width': 20,
		'height': 20,
		'auto'			 : {$auto},
		'onError': function(event,queueId,fileObj,errorObj){
			alert(errorObj.type+':'+errorObj.info);
		},
		'onAllComplete': function(event,queueID,fileObj){
			initUpload{$id}=false;
			{$successFunction}
		},
		'onSelect': function(event,queueId,fileObj){
			var values=$('input[name="{$internal}"]').val();
			if (values.split(',').length>={$maxUploadFiles}){
				$(this).uploadifyCancel();
			}
		},
		'onComplete':	function(event,queueId,fileObj,response,data){
			var inputvalues=$('input[name="{$internal}"]');
			//alert(fileObj.name+","+fileObj.filePath+","+fileObj.size+","+data.fileCount+","+data.speed);
			//var valor='{$folder}{$prefix}'+fileObj.name;
			var name=fileObj.name.toLowerCase();
			var extension = (name.substring(name.lastIndexOf("."))); 
			
			var valor='{$folder}'+$.md5('{$prefix}'+name.substring(name.lastIndexOf("'")+1))+extension;
			
			// si no es el último ponemos ,
			var currentvalue=inputvalues.val();
			
			var expr=new RegExp('[,]{0,1}'+valor);
			if (currentvalue.match(expr)==null){
				if (currentvalue.split(',').length<{$maxUploadFiles}){
					$('#uploadedlinks{$strippedname}').append('{$templateLinkAjax}');
					addActionLinkUpload{$id}(inputvalues,name+':'+valor,true);
				}else alert('{$maxUploadText}');			
				
			}
		}{$fileQueue}
	});
	
	addActionLinkUpload{$id}($('input[name="{$internal}"]'),'',false);
});
</script>
{$divFileQueue}
<input type="hidden" value="{$value}" name="{$internal}" id="{$internal}" /><br/>
EOT;

if (!$readonly){
$form_body.='<span class="uploadifyInputFile" '.$js.'>'.
			$infoText.'<input  disabled="disabled" type="file" name="'.$id.'" id="'.$id.'" />'.
			'</span>'.$cancelButton.$uploadButton;
}
$form_body.='<div id="uploadedlinks'.$strippedname.'">'.$uploadedFile.'</div>';

echo $form_body;

?>