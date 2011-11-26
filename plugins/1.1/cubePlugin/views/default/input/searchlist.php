<?php  
	$strippedname=strtr($vars['internalname'],"[].","___"); 
	$value=isset($vars['value'])?$vars['value']:'';
	$loader=isset($vars['loader'])?$vars['loader']:'<img src="/img/ajax-loader2.gif" />';
	$minChars=isset($vars['minChars'])?intval($vars['minChars']):0;
	if (isset($vars['url'])) $scriptPath=$vars['url'];
	else $scriptPath=isset($vars['scriptPath'])?$vars['scriptPath']:'/';
	
	$scriptPath=Route::url($scriptPath);
	
	$display='display:inline';
	if (isset($vars['display']) && !$vars['display']) $display='display:none';
	
	$class = isset($vars['class'])?$vars['class']:'input-text';
		
	//echo _r($vars);
	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
	
	$force=isset($vars['force_in_list'])?$vars['force_in_list']:false;
	
	$restrict=isset($vars['restrict'])?$vars['restrict']:false;
	$restrictval=$restrict?"''":"\$(\"input[name='".$vars['internalname']."']\").val()";
	//$restrictval="''";
	
	if (isset($vars['readonly']) && $vars['readonly']) $js.=" readonly=\"readonly\"";
	//$defaultValue=(!empty($value) && (!isset($vars['ajax']) || !$vars['ajax']))?$value:'';
	$defaultValue=(!empty($value))?$value:'';

?><script type="text/javascript">
	 var pulldowns<?php echo $strippedname; ?>;
	 
	 function lookup_<?php echo $strippedname; ?>() {					
                var inputString=$('#inputString_<?php echo $strippedname; ?>').val();
		
		if(inputString.length == 0) {
			// Hide the suggestion box.			
			$('#suggestions_<?php echo $strippedname; ?>').hide();
		}

		if (inputString.length >=<?php echo $minChars;?>){
                            
                
		$('#loader_<?php echo $strippedname; ?>').show();
		<?php if (isset($vars['init'])) echo "if (is_callable('".$vars['init']."')) {".$vars['init']."();}";	?>
		$.ajax({
		  url: '<?php echo $scriptPath;?>',
		  type: "POST",
		  data: {
		  			'queryString' : inputString,<?php if (isset($vars['template'])) echo "'template': '".$vars['template']."',";?>
		  			'class'       : '<?php echo $strippedname; ?>'
		  },
		  cache: false,
		  timeout: 60000,
		  dataType: 'json',
		  success: function(data){	
			var capa = '';
		  	
		  	pulldowns<?php echo $strippedname; ?>.hide();
		  	
		  	jQuery.each(data, function(i, val) {
		  	  <?php if (isset($vars['template'])): ?>
		  	  var label='<?php echo preg_replace("/%%([^%]*)%%/","'+val.text.$1+'",$vars['template']);?>';
		  	  <?php else: ?>
		  	  var label=val.text;
		  	  <?php endif; ?>
		  	  
		  	  capa += '<div class="autocomplete_<?php echo $strippedname; ?>" id="'+val.value+'">'+label+'</div>';
		    });
		  	
		  	//if(data.length >0) {
				$('#suggestions_<?php echo $strippedname; ?>').show();				
				/*$('#autoSuggestionsList_<?php echo $strippedname; ?>').html(data);*/	
							

				$('#autoSuggestionsList_<?php echo $strippedname; ?>').html(capa);
				$('[class=autocomplete_<?php echo $strippedname; ?>]').click(function(){
						var valor='';
						var id=$(this).attr('id');
						if (id!='') valor=$(this).html(); 
						$('#divString_<?php echo $strippedname ?>').html(valor).show();
						$('#divId_<?php echo $strippedname ?>').html(id);
						$("input[name='<?php echo $vars['internalname'];?>']").val(id);
					
					<?php 
						// si hay que hacer alguna acción la pondremos al clicar un elemento del autocompletado
						if (isset($vars['success'])) echo "if (is_callable('".$vars['success']."')) {".$vars['success']."(data,false,{value:id, text: valor});}";
					?>
					$('#suggestions_<?php echo $strippedname; ?>').hide();
					pulldowns<?php echo $strippedname; ?>.show();	
					
				});					
			$('#loader_<?php echo $strippedname; ?>').hide();										
			
			$('#boxspotactivitats').click(function() {
 				$('#suggestions_<?php echo $strippedname; ?>').hide(); 
 				
			});
			
			
		  $("input[name='<?php echo $vars['internalname'];?>']").val(<?php echo $restrict?"''":"this.value"; ?>);
		  $('#inputString_<?php echo $strippedname; ?>').val('');
		  
		  }
		});
     } // if (inputString.length>=minchars)
	} // lookup
	
	$(document).ready(function(){
			<?php if (!empty($value) && isset($vars['ajax']) && $vars['ajax']): ?>
					$('#loader_<?php echo $strippedname; ?>').show();
					$.ajax({
				  		url: '<?php echo $scriptPath;?>',
				  		type: "POST",
				  		data: {
				  			'id' : '<?php echo $value; ?>',
				  			<?php if (isset($vars['template'])) echo "'template': '".$vars['template']."',";?>
				  			'class': '<?php echo $strippedname; ?>'
				  			},
				  		cache: false,
				  		dataType: 'json',
				  		timeout: 60000,
				  		success: function(data){			  		
				  			
				  			//if (data!='') $('#inputString_<?php echo $strippedname; ?>').val(data);
				  			if (data!='') {

								$.each(data,function(i,e){
									if (e.value=='<?php echo $value; ?>'){
										<?php if (isset($vars['template'])): ?>
										  var label='<?php echo preg_replace("/%%([^%]*)%%/","'+e.text.$1+'",$vars['template']);?>';
										<?php else: ?>
										 var label=e.text;
										<?php endif; ?>
										$('#divString_<?php echo $strippedname; ?>').html(label);
									}
		  	  					});
				  			}
				  			else {
				  				var valorActual=$("input[name='<?php echo $vars['internalname'];?>']").val();
								//$('#divString_<?php echo $strippedname; ?>').html(<?php echo $restrictval; ?>);
								//$('#divId_<?php echo $strippedname; ?>').html(<?php echo $restrictval; ?>);

								$('#divString_<?php echo $strippedname; ?>').html(valorActual);
								$('#divId_<?php echo $strippedname; ?>').html(valorActual);

								$('#inputString_<?php echo $strippedname; ?>').val('');
				  			}
				  			$('#loader_<?php echo $strippedname; ?>').hide();
				  			<?php if (isset($vars['success'])) {echo "if (is_callable('".$vars['success']."')) {".$vars['success']."(data,true);}";} ?>
				  		}
				  	});
			<?php endif; ?> 
			
			<?php  if (!isset($vars['readonly']) || !$vars['readonly']): ?>
				
				$('#inputString_<?php echo $strippedname; ?>').keyup(function(event){
					var code = (event.keyCode ? event.keyCode : event.which);
  					if (code == '13') lookup_<?php echo $strippedname; ?>();  										
				});
				
			<?php endif;?>

			$('#inputString_<?php echo $strippedname; ?>').click(function(){
				//$('#divString_<?php echo $strippedname; ?>').hide();
				$('#suggestions_<?php echo $strippedname; ?>').hide();
				pulldowns<?php echo $strippedname; ?>.show();
			});

			$('#inputString_<?php echo $strippedname; ?>').focus(function(){

				var $input=$('[name="inputString_<?php echo $strippedname; ?>"]');
				var $div=$('#divString_<?php echo $strippedname; ?>');
				var $hidden=$('[name="<?php echo $vars['internalname'];?>"]');
				
				//$input.val($div.html());
				$div.hide();
			});
			
			$('#inputString_<?php echo $strippedname; ?>').blur(function(){

				//var descrip=$('#divString_<?php echo $strippedname; ?>').html();
				var $input=$('[name="inputString_<?php echo $strippedname; ?>"]');
				var $div=$('#divString_<?php echo $strippedname; ?>');
				var $hidden=$('[name="<?php echo $vars['internalname'];?>"]');

            var valor=$input.val();
				if (valor=='') valor=$('#divId_<?php echo $strippedname; ?>').html();

				if ($input.val()=='') $div.show();
				<?php if ($force===false):?>$hidden.val(valor);<?php endif; ?>
			});
			
			pulldowns<?php echo $strippedname; ?>=$('select:visible');
			
			/*
			var searchlists<?php echo $strippedname; ?>=$('.searchlist input[name!="inputString_<?php echo $strippedname; ?>"]');
			
			jQuery.each(searchlists<?php echo $strippedname; ?>, function(i, val) {
		  		$.merge(pulldowns<?php echo $strippedname; ?>,$(val).parents('.searchlist'));
		    });
			*/
			
			$('#divString_<?php echo $strippedname; ?>').click(function(){
				$(this).hide();
				$('#inputString_<?php echo $strippedname; ?>').focus();
			});

         <?php if (isset($vars['width_inputString'])){?> var dimW=<?php echo "'".$vars['width_inputString']."'"; }
					else{ ?> var dimW=$('#inputString_<?php echo $strippedname; ?>').innerWidth()+'px'; <?php }?>;

			$('#divString_<?php echo $strippedname; ?>').css('width',dimW).show();

			//$('#divString_<?php echo $strippedname; ?>').css('width',$('#inputString_<?php echo $strippedname; ?>').innerWidth()+'px').show();
		
                
                });
		
</script>
<div class="searchlist" style="font-size: 1em;position:relative;<?php echo $display; ?>;overflow:visible;">
	<div style="display:inline;">
		<input type="hidden" id="<?php echo $vars['internalname'];?>" name="<?php echo $vars['internalname'];?>" value="<? echo $defaultValue; ?>" />
		<input <?php if ($force!==false){ echo 'title="'.$force.'"';}?> class="<?php echo $class; ?>" <?php echo $js; ?> <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> type="text" name="inputString_<?php echo $strippedname; ?>" id="inputString_<?php echo $strippedname; ?>"  />	
		<span style="display:none;" id="loader_<?php echo $strippedname; ?>"><?php echo $loader; ?></span>
		<?php if(!isset($vars['button_disabled']) || $vars['button_disabled']===false){ ?>
		<a title="<?php echo Viewer::_echo('search'); ?>" style="cursor:pointer;" onClick="lookup_<?php echo $strippedname; ?>();"><img src="/img/icon/magnifier.png"/></a>
		<?php } ?>
		<div class="suggestionsBox" id="suggestions_<?php echo $strippedname; ?>" style="display: none;">
			<div class="suggestionList" id="autoSuggestionsList_<?php echo $strippedname; ?>"></div>
		</div>
		<div style="position:absolute;top: 0px;left: 3px;color:#333;width:400px;" name="divString_<?php echo $strippedname; ?>" id="divString_<?php echo $strippedname; ?>"><?php echo (!empty($value) && (!isset($vars['ajax']) || !$vars['ajax']))?$value:''; ?></div>
		<div style="display:none;" id="divId_<?php echo $strippedname; ?>"><?php echo $defaultValue; ?></div>
	</div>		
</div>