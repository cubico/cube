<?php  
	$strippedname=strtr($vars['internalname'],"[].","___"); 
	$value=isset($vars['value'])?$vars['value']:'';
	$minChars=isset($vars['minChars'])?intval($vars['minChars']):3;
	$loader=isset($vars['loader'])?$vars['loader']:'<img src="/img/ajax-loader2.gif" />';
	
	if (isset($vars['url'])) $scriptPath=$vars['url'];
	else $scriptPath=isset($vars['scriptPath'])?$vars['scriptPath']:'/';
	
	$scriptPath=Route::url($scriptPath);
	
	$display='display:inline';
	if (isset($vars['display']) && !$vars['display']) $display='display:none';
	
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
	
	if (isset($vars['readonly']) && $vars['readonly']) $js.=" readonly=\"readonly\"";
	
?><script type="text/javascript">
	
	 function lookup_<?php echo $strippedname; ?>(inputString) {
		if(inputString.length == 0) {		
			// Hide the suggestion box.			
			$('#suggestions_<?php echo $strippedname; ?>').hide();
			
		} else {		
			if (inputString.length >=<?php echo $minChars;?>){ // || inputString=='*'){
				$('#loader_<?php echo $strippedname; ?>').show();
				
				$.ajax({
				  url: '<?php echo $scriptPath;?>',
				  type: "POST",
				  data: {
				  			'queryString' : inputString,
				  			'class'       : '<?php echo $strippedname; ?>'
				  },
				  cache: false,
				  timeout: 60000,
				  success: function(data){				  
				  	//if(data.length >0) {
						$('#suggestions_<?php echo $strippedname; ?>').show();
						$('#autoSuggestionsList_<?php echo $strippedname; ?>').html(data);
						
						$('.autocomplete_<?php echo $strippedname; ?>').click(function(){
							<?php 
								//if (isset($vars['success'])) {
								//	echo $vars['success']."(response);\n";
								//}
								//else {
									echo "\$('#inputString_".$strippedname."').val($(this).html());\n";
									echo "\$(\"input[name='".$vars['internalname']."']\").val($(this).attr('id'));\n"; 
								//}
								
								// si hay que hacer alguna acción la pondremos al clicar un elemento del autocompletado
								if (isset($vars['success'])) echo "if (is_callable('".$vars['success']."')) {".$vars['success']."(data);}";		
							?>
						});
					//}
					$('#loader_<?php echo $strippedname; ?>').hide();										
					
					$('#inputString_<?php echo $strippedname; ?>').focusout(function() {
 						//$('#suggestions_<?php echo $strippedname; ?>').hide(); 									
 						setTimeout("$('#suggestions_<?php echo $strippedname; ?>').hide();", 100);
					});
					
				  }
				});
			}
		}
	} // lookup
	
	var timer<?php echo $strippedname; ?>;
	var interval<?php echo $strippedname; ?>;
			
	var timerFunc<?php echo $strippedname; ?>=function(){
		timer<?php echo $strippedname; ?>=timer<?php echo $strippedname; ?>+1;
		if (timer<?php echo $strippedname; ?>==6) {
			var valor=$('#inputString_<?php echo $strippedname; ?>').val();
			lookup_<?php echo $strippedname; ?>(valor);
			timer<?php echo $strippedname; ?>=0;
			clearInterval(interval<?php echo $strippedname; ?>);
		} 
	}
	
	$(document).ready(function(){
			
			<?php if (!empty($value)): ?>
					$('#loader_<?php echo $strippedname; ?>').show();
					$.ajax({
				  		url: '<?php echo $scriptPath;?>',
				  		type: "POST",
				  		data: {
				  			'id' : $("input[name='<?php echo $vars['internalname'];?>']").val(),
				  			'class': '<?php echo $strippedname; ?>'
				  			},
				  		cache: false,
				  		timeout: 60000,
				  		success: function(data){				  
				  			$('#inputString_<?php echo $strippedname; ?>').val(data);
				  			$('#loader_<?php echo $strippedname; ?>').hide();
				  		}
				  	});
			<?php endif; if (!isset($vars['readonly']) || !$vars['readonly']): ?>
				
				
				$('#inputString_<?php echo $strippedname; ?>').keyup(function(){
					timer<?php echo $strippedname; ?>=0;
					if (interval<?php echo $strippedname; ?>!=undefined) clearInterval(interval<?php echo $strippedname; ?>);
					interval<?php echo $strippedname; ?> = setInterval("timerFunc<?php echo $strippedname; ?>()",100);
				});
				
				$('#inputString_<?php echo $strippedname; ?>').blur(function(){
					$("input[name='<?php echo $vars['internalname'];?>']").val(this.value);
				});
			<?php endif;?>
		});
</script>
<div style="position:relative;<?php echo $display; ?>">
	<input type="hidden" id="<?php echo $vars['internalname'];?>" name="<?php echo $vars['internalname'];?>" value="<? echo $value; ?>" />  
	<input <?php echo $js; ?> <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> type="text" value="" name="inputString_<?php echo $strippedname; ?>" id="inputString_<?php echo $strippedname; ?>"  />
	<span style="display:none;" id="loader_<?php echo $strippedname; ?>"><?php echo $loader; ?></span>
	<div class="suggestionsBox" id="suggestions_<?php echo $strippedname; ?>" style="display: none; position:absolute;top:20px;z-index:5000;">
	<div class="suggestionList" id="autoSuggestionsList_<?php echo $strippedname; ?>"></div>
	</div>
</div>