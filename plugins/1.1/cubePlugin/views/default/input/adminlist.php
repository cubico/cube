<?php 
	
	$strippedname=strtr($vars['internalname'],"[]., ","_____");  
	//$vars['value']= json_encode(array('item0'=>'manu'));
	$css=(isset($vars['class']) && !empty($vars['class']))?$vars['class']:'adminlist';
	$title=isset($vars['title'])?$vars['title']:Viewer::_echo('button:add');
	$img1=isset($vars['addImage'])?$vars['addImage']:'/img/crystal/16x16/actions/edit_add.png';
	$img2=isset($vars['delImage'])?$vars['delImage']:'/img/crystal/16x16/actions/edit_remove.png';
	$img3=isset($vars['revertImage'])?$vars['revertImage']:'/img/crystal/16x16/actions/undo.png';
	$width=isset($vars['width'])?$vars['width']:'300px';
	$height=isset($vars['maxGridHeight'])?intval($vars['maxGridHeight']):60;
	
	$actions=isset($vars['actions'])?$vars['actions']:array();
	
	$readonly=(isset($vars['readonly']) && $vars['readonly'])?true:false;
	
	if (isset($vars['url'])) $scriptPath=$vars['url'];
	else $scriptPath=isset($vars['scriptPath'])?$vars['scriptPath']:'/';
	
	$scriptPath=Route::url($scriptPath);
	
	if (isset($vars['js'])){
		$js='';
		if (is_array($vars['js'])) foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		else $js=$vars['js'];
	}else { $js='style="float:right;margin:0px;font-size:1.2em;width:480px;"'; }
	
	$marcat_per_esborrar='marcat per esborrar';
?>
<script type="text/javascript" src="/js/jquery/jquery.json.js"></script>
<script type="text/javascript">
var adminlistCount<?php echo $strippedname; ?>=0;
var adminlistValues<?php echo $strippedname; ?>={};
var adminlistIncludeVar<?php echo $strippedname; ?>;

function adminlistInclude<?php echo $strippedname; ?>(){
	var include={};
<?php 
$bool=preg_match("/(.*)[\[]([^\]]*)[\]]/",$vars['internalname'],$args);

if ($bool && isset($vars['include'])) {
	if (!is_array($vars['include'])) $vars['include']=array($vars['include']);
		
	foreach($vars['include'] as $k){
			
		if (strpos($k,'.')!==false){ 
			if ($k[0]=='.') $k = substr($k, 1);
		}else if (strpos($args[2],'.')!==false){
			$kk=explode(".",$args[2]);
			$k=$kk[0].'.'.$k;
		}else if (strpos($args[2],Form::FILTER_PREFIX)!==false){
			$k=Form::FILTER_PREFIX.$k;
		}
		
		$name=$args[1]."[".$k."]";
		
		?> var elem=$("[name='<?php echo $name; ?>']");
		
		if (elem.length>0){
			if (elem.is('input') || elem.is('select')){ 
				include['<?php echo $k;?>']=elem.val();
			}else{
				include['<?php echo $k;?>']=elem.html();
			}
		}
		<?php 
	}
} ?>
	return include;
}


function adminlistTemplate<?php echo $strippedname; ?>(value,label,i){
	var ret='<div class="adminlistrow"><div style="float:right;margin:0 10px 0 0;">';
			<?php if (!$readonly): ?>
		
		if (label.search(/<?php echo $marcat_per_esborrar;?>/)==-1){			
			ret+='<a title="Esborrar" href="javascript:adminlistCallback<?php echo $strippedname;?>(\'d\',\''+value+'\');">'+
				'<img src="<?php echo $img2; ?>" /></a>';
		}else{	
			ret+='<a title="Desfer" href="javascript:adminlistCallback<?php echo $strippedname;?>(\'\',\''+value+'\');">'+
				'<img src="<?php echo $img3; ?>" /></a>';
		}	
			<?php endif; ?>
		ret+='</div>';
		
		<?php if (count($actions)>0){ foreach($actions as $i=>$data): ?>
		ret+='<div style="float:right;margin:0 1px 0 0;">';
		ret+='<a title="<?php echo $data['title']; ?>" style="cursor:pointer;" onClick="<?php echo $data['function'];?>(\''+i+'\',\''+value+'\',this);">';
				<?php if (isset($data['img'])): 
						if (!preg_match("/^<img/",$data['img']) && !empty($data['img'])) $data['img']='<img src="'.$data['img'].'" />';	// fuera del htmlentities!
				?>
					ret+='<?php echo $data['img'].(isset($data['value'])?$data['value']:''); ?>';
				<?php else: ?>
					ret+='<?php echo $data['title']; ?>';
				<?php endif; ?>
		ret+='</a></div>';	
		<?php endforeach; } ?>
		
		ret+='<div class="<?php echo $css;?>'+((i%2)?' odd':' even')+'" id="'+value+'">'+label+'</div></div>';
	
	return ret;
}

function addItemadminlist<?php echo $strippedname; ?>(data,obj,input){
	var capa = '';
  	var strcapa=new Array();
  	var cont=0;
  	adminlistCount<?php echo $strippedname; ?>=0;
  	jQuery.each(data, function(i, val) {
		  <?php if (isset($vars['template'])): ?>
	  	  var label='<?php echo preg_replace("/%%([^%]*)%%/","'+val.$1+'",$vars['template']);?>';
	  	  <?php else: ?>
	  	  var label=val.text;
	  	  <?php endif; ?>
	  	  var valor=(val.value==undefined)?i:val.value;
	  	  var modif=(val.modif==undefined)?'':val.modif;
	  	  if (modif=='d') label+='<i>  <?php echo $marcat_per_esborrar;?></i>';
	  	  
	  	  capa += adminlistTemplate<?php echo $strippedname; ?>(valor,label,cont);
	  	  strcapa.push('"'+valor+'": {"text":"'+val.text+'","modif":"'+modif+'"}');
	  	  adminlistValues<?php echo $strippedname; ?>[valor]={text: val.text, modif: modif};
	  	  adminlistCount<?php echo $strippedname; ?>++;
	  	  cont++;
	});
	
	if (strcapa.length>0){
		$('[name="<?php echo $vars['internalname'];?>"]').val('{'+strcapa.join(',')+'}');
		obj.html(capa).width(input.width()+40).height(Math.min(obj.children().length*20,<?php echo $height; ?>));
	}
}

var adminlistCallback<?php echo $strippedname; ?>=function(action,obj,firstcall){
		
		$('#adminlistError<?php echo $strippedname; ?>').html('');
		var value;
		var input=$('[name="input<?php echo $strippedname;?>"]');
		var o=$('#adminlist<?php echo $strippedname; ?>');
		
		if (firstcall){
			if (action=='a') value=input.val();
			
	  	  	$('#adminlist<?php echo $strippedname; ?>').html('<img src="/img/ajax-loader2.gif" />');
			
			var params={'action': action, 'include': adminlistIncludeVar<?php echo $strippedname; ?>
	  					<?php if (isset($vars['template'])) echo ", 'template': '".$vars['template']."'";?>
						<?php if (isset($vars['extra'])) {$extra=$vars['extra'];foreach($extra as $x=>$y) echo ", '{$x}':'{$y}'";} ?>};
		  	if (value!=undefined) params['value']=value;
		  	
		  	$.ajax({
		  		url: '<?php echo $scriptPath; ?>',
		  		data: params,
		  		type: "POST",
		  		cache: false,
		  		dataType: 'json',
		  		timeout: 60000,
		  		success: function(datajson){
		  			if (datajson.error==0 && datajson.data){
  						addItemadminlist<?php echo $strippedname; ?>(datajson.data,o,input);
  						if (datajson.data.length==0)
  							$('#adminlist<?php echo $strippedname; ?>').html('');
  					} 
  						
					$('#adminlistError<?php echo $strippedname; ?>').html(datajson.message);
				}
		  	});
		  	
	  	}else{<?php //else: ?>
	  		
	  		if (action=='i'){
	  			var l=o.children().length;
	  			var n=adminlistCount<?php echo $strippedname; ?>;
	  			
	  			if (input.val().length>0){
		  			<?php if (isset($vars['ajax']) && $vars['ajax']): ?>
		  			var params={'action': action, 'value': input.val(), 'include': adminlistIncludeVar<?php echo $strippedname; ?>
	  				<?php if (isset($vars['template'])) echo ", 'template': '".$vars['template']."'";?>};
		  				
		  			$.ajax({url: '<?php echo $scriptPath; ?>',data: params,type: "POST",cache: false,
		  					dataType: 'json',timeout: 60000,success: function(datajson){
		  						if (datajson.error!=0) alert(datajson.message);
		  						else {
		  							<?php if (isset($vars['template'])): ?>
						  	  		var label='<?php echo preg_replace("/%%([^%]*)%%/","'+datajson.data.$1+'",$vars['template']);?>';
						  	  		<?php else: ?>
						  	  		var label=datajson.data.text;
						  	  		<?php endif; ?>
		  							
		  							adminlistCount<?php echo $strippedname; ?>++;
		  							adminlistValues<?php echo $strippedname; ?>[datajson.data.value]={'text':label,'modif':'i'};
		  							
		  							var capa=adminlistTemplate<?php echo $strippedname; ?>(datajson.data.value,label,n);
		  							if (l==0) o.html(capa);
		  							else $('#adminlist<?php echo $strippedname; ?>:last').append(capa);
		  						}
		  					}});
		  			<?php else: ?>
		  			
		  			<?php if (isset($vars['template'])): ?>
					var datanew={value: '', text: input.val(), modif: 'i'} // simulamos request ajax
					var label='<?php echo preg_replace("/%%([^%]*)%%/","'+datanew.$1+'",$vars['template']);?>';
					<?php else: ?>
					var label=input.val();
					<?php endif; ?>
						  	  		
		  			var capa=adminlistTemplate<?php echo $strippedname; ?>('new'+n,label,n);
		  			if (l==0) o.html(capa);
		  			else $('#adminlist<?php echo $strippedname; ?>:last').append(capa);
		  			
		  			adminlistCount<?php echo $strippedname; ?>++;
		  			adminlistValues<?php echo $strippedname; ?>['new'+n]={'text':input.val(),'modif':'i'};
		  			<?php endif; ?>
		  			input.val('');
	  			}
	  		}else if (action=='d' || action==''){
	  			<?php if (isset($vars['ajax']) && $vars['ajax']): ?>
		  		
		  		var params={'action': action, 'value': $('#'+obj).attr('id'), 'include': adminlistIncludeVar<?php echo $strippedname; ?>
	  			<?php if (isset($vars['template'])) echo ", 'template': '".$vars['template']."'";?>};
		  		
		  		$.ajax({url: '<?php echo $scriptPath; ?>',data: params,type: "POST",cache: false,
		  				dataType: 'json',timeout: 60000,success: function(datajson){
		  					alert(datajson.message);
		  				}});
		  		<?php else: ?>
	  			if (adminlistValues<?php echo $strippedname; ?>[$('#'+obj).attr('id')].modif=='i')
	  				delete adminlistValues<?php echo $strippedname; ?>[$('#'+obj).attr('id')];
	  			else
	  				adminlistValues<?php echo $strippedname; ?>[$('#'+obj).attr('id')].modif=action;
	  			<?php endif; ?>	
	  			
	  			if (action=='d') 
	  				$('#'+obj).parent().remove();
	  			else{
	  				var textMark=$('#'+obj).html();
	  				textMark=textMark.replace(/<?php echo $marcat_per_esborrar;?>/,'');
	  				$('#'+obj).html(textMark);
	  				$('#'+obj).prev().html('');
	  			}
	  		}
	  		
	  		if ($(adminlistValues<?php echo $strippedname; ?>).length>0){
	  			var vv=$.toJSON(adminlistValues<?php echo $strippedname; ?>);
	  			$('[name="<?php echo $vars['internalname'];?>"]').val(vv);
	  		}
	  		
	  		o.height(Math.min(o.children().length*20,<?php echo $height; ?>));
	  	<?php //endif; ?>
	  	}
	  	
	  	<?php if (isset($vars['success'])) echo "if (is_callable('".$vars['success']."')) {".$vars['success']."(html);}";	?>
}	

$(document).ready(function(){
	adminlistIncludeVar<?php echo $strippedname; ?>=adminlistInclude<?php echo $strippedname; ?>();
	<?php if (!isset($vars['value']) ||  empty($vars['value'])): ?> 
		adminlistCallback<?php echo $strippedname; ?>('',null,true);
	<?php else: ?>
		var vv=$.parseJSON($('[name="<?php echo $vars['internalname'];?>"]').val());
	  	var input=$('[name="input<?php echo $strippedname;?>"]');
	  	var obj=$('#adminlist<?php echo $strippedname; ?>');
	  	addItemadminlist<?php echo $strippedname; ?>(vv,obj,input);
	<?php endif; ?>
});
</script>

<div <?php echo $js; ?>>
	<?php 
		if (!$readonly){
			echo Viewer::view('input/hidden',array('internalname'=>$vars['internalname'],'js'=>'style="width:'.$width.'"','value'=>$vars['value']),$vars['viewtype']); 
			echo Viewer::view('input/text',array('class'=>$vars['class'],'internalname'=>'input'.$strippedname,'js'=>'style="width:'.$width.'"'),$vars['viewtype']);
		}
	?>
	<div style="float:right;margin: 0 10px 0 0;" id="adminlistError<?php echo $strippedname; ?>"></div>
	<div style="float:right;margin: 0 10px 0 10px;"><?php 
		if (!$readonly) echo Viewer::view('output/link',array('title'=>$title, 'img'=>$img1,'href'=>'javascript:adminlistCallback'.$strippedname.'(\'i\');')); 
	?></div>
	<div id="adminlist<?php echo $strippedname; ?>" style="overflow-x:none;overflow-y:auto;height:16px;"></div>
</div>