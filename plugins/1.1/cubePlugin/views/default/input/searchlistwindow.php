searchlist...<?php
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
	
	if (isset($vars['readonly']) && $vars['readonly']) $js.=" readonly=\"readonly\"";
	
	$strippedname=strtr($vars['internalname'],"[].","___");
	if (!isset($vars['img'])) $img="/img/icon/address_book.png";else $img=$vars['img'];
	$scriptPath=isset($vars['scriptPath'])?$vars['scriptPath']:'/';
	$minChars=isset($vars['minChars'])?intval($vars['minChars']):3;
	
?>
<script type="text/javascript">
	
	 function lookup_<?php echo $strippedname; ?>(inputString) {
		if(inputString.length == 0) {		
			// Hide the suggestion box.			
			$('#suggestions_<?php echo $strippedname; ?>').hide();
			
		} else {			
			if (inputString.length >=<?php echo $minChars;?>){
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
				  	if(data.length >0) {
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
							?>
							setTimeout("$('#suggestions_<?php echo $strippedname; ?>').hide();", 200);
						});
					}
					$('#loader_<?php echo $strippedname; ?>').hide();
				  }
				});
			}
		}
	} // lookup
	
				
</script>	
<input type="text" <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> <?php echo $js; ?> name="<?php echo $vars['internalname']; ?>" id="inputString_<?php echo $strippedname; ?>" value="<?php echo htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $class ?>"/>
<div id="content">
<img style="cursor:pointer" title="Cercar" src="<?php echo $img; ?>" id="preferences" />
</div> 

	<script type="text/javascript" src="/js/modal/jquery.js"></script>
	<script type="text/javascript" src="/js/modal/interface.js"></script>
	<script type="text/javascript" src="/js/modal/jquery.form.js"></script>


	<script type="text/javascript">
		$(document).ready(function()
		{
			$('#layer1').Draggable(
					{
						zIndex: 	20,
						ghosting:	false,
						opacity: 	0.7,
						handle:	'#layer1_handle'
					}
				);	
			$('#layer1_form').ajaxForm({
				target: '#content',
				success: function() 
				{
					$("#layer1").hide();
				}				
			});			
			$("#layer1").hide();
						
			$('#preferences').click(function()
			{
				$("#layer1").show();
			});
			
			$('#close').click(function()
			{
				$("#layer1").hide();
			});
			
			$('#submit').click(function(){
			var params = {}; 
		
				$('#layer1_content')
				.find("input:checked, input[type='text'], input[type='hidden'], option[selected], textarea") 
				.filter(":enabled") 
				.each(function() { params[ this.name || this.id || this.parentNode.name || this.parentNode.id ] = this.value; }); 
				
				//params['mode']="envia";
				
				$.ajax({
				  		url: '<?php echo $scriptPath;?>',
				  		type: "POST",
				  		data: params,
				  		cache: false,
				  		timeout: 60000,
				  		success: function(data){				  
				  			
				  		}
				  	});
					
			});				
			
			/*$("input[name='<?php echo $vars['internalname']; ?>']").keyup(function(){
					lookup_<?php echo $strippedname; ?>(this.value);
				});*/
				
			$('#inputString_<?php echo $strippedname; ?>').keyup(function(){
					lookup_<?php echo $strippedname; ?>(this.value);
				});
								
		});
		
		
	</script> 
	
	<?php 
	

    $searchContent=isset($vars['searchContent'])?$vars['searchContent']:'canvas/searchlist/favorite';
          #formId: 	
	if (isset($vars['windowList']))
	{
		$wl=$vars['windowList']; 
		echo Viewer::view($wl,$vars);
	}
	else
	{
	?>
	<div id="layer1">
		<div id="layer1_handle">			
			<a href="#" id="close">[ x ]</a>
			Afegir a Favorits
		</div>
		<div id="layer1_content">	
			<?php			
			echo Viewer::view($searchContent,$vars);
			?>
			<input type="button" id="submit" value="Desar"/>			
		</div>		
	</div>
<?php
}
?>
