<?php

	/**
	 * Elgg text input
	 * Displays a text input field
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['disabled'] If true then control is read-only
	 * @uses $vars['class'] Class override
	 */

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
	$strippedname=strtr($vars['internalname'],"[].,","___");

	$updateIfError=isset($vars['updateIfError'])?$vars['updateIfError']:true;
	$paramBY = isset($vars['searchby'])?$vars['searchby']:'param'; // registrar dos posibles llamadas diferentes (nhis, cip)
	?>
	
<script type="text/javascript">
	$(document).ready(function(){
		$("input[name='<?php echo $vars['internalname']; ?>']").keypress(function(event) {
  			var code = (event.keyCode ? event.keyCode : event.which);
  			if (code == '13') searchFunction<?php echo $strippedname;?>();
		});
	});
</script>
<script type="text/javascript">
<?php
if (isset($vars['extra'])){ 
	$extra=",extra: extraParams{$strippedname}()"; 
	?>var extraParams<?php echo $strippedname; ?>=function(){
		var params={};
		<?php
		$bool=preg_match("/(.*)[\[]([^\]]*)[\]]/",$vars['internalname'],$args);						
		if ($bool && isset($vars['extra'])) {
			
			foreach($vars['extra'] as $k=>$v){
				
				if (is_numeric($k)) {$v2=explode('.',$v); $k=$v;$v=isset($v2[1])?$v2[1]:$v2[0];}
				
				if (strpos($k,'.')!==false){ 
					if ($k[0]=='.') $k = substr($k, 1);
				}else if (strpos($args[2],'.')!==false){
					$kk=explode(".",$args[2]);
					$k=$kk[0].'.'.$k;
				}else if (strpos($args[2],Form::FILTER_PREFIX)!==false){
					$k=Form::FILTER_PREFIX.$k;
				}
				?> var elem=$("[name='<?php echo $args[1]."[".$k."]"; ?>']");
				var val;
				if (elem.length>0){
					if (elem.is('input') || elem.is('select')) val=elem.val();
					else val=elem.html();
				}
				params['<?php echo $k; ?>']=val;<?php
			}
		
		}
		?>
		return params;
	}
	<?php 
	}else $extra=''; ?>

	var searchFunction<?php echo $strippedname;?>=function(){
		
		var param=$("input[name='<?php echo $vars['internalname']; ?>']").val();
		$("span[name='Response<?php echo $vars['internalname']; ?>']").html('<img src="/img/ajax-loader2.gif" />');
		<?php if (isset($vars['init'])) echo "if (is_callable('".$vars['init']."')) {".$vars['init']."();}";	?>
		$.ajax({
			  url: "<?php echo Route::url($vars['url']);?>",
			  type: "POST",
			  cache: false,
			  timeout: 60000,
			  data: {	<?php echo $paramBY; ?>: param, 
			  			<?php if (isset($vars['template'])) echo "'template': '".$vars['template']."',";?>
			  			error_message: '<?php echo isset($vars['url_message'])?addslashes($vars['url_message']):''; ?>'
			  			<?php echo $extra; ?>
			  		},
			  dataType: "json",
			  success: function(html){	
			  		<?php echo !$updateIfError?'if (!html.error){':''; ?> 	 	
					<?php 
						$args=array();
						$bool=preg_match("/(.*)[\[]([^\]]*)[\]]/",$vars['internalname'],$args);						
						if ($bool && isset($vars['update'])) {
							
								foreach($vars['update'] as $k=>$v){
									
									if (is_numeric($k)) {$v2=explode('.',$v); $k=$v;$v=isset($v2[1])?$v2[1]:$v2[0];}
									
									if (strpos($k,'.')!==false){ 
										if ($k[0]=='.') $k = substr($k, 1);
									}else if (strpos($args[2],'.')!==false){
										$kk=explode(".",$args[2]);
										$k=$kk[0].'.'.$k;
									}else if (strpos($args[2],Form::FILTER_PREFIX)!==false){
										$k=Form::FILTER_PREFIX.$k;
									}
									?> var elem=$("[name='<?php echo $args[1]."[".$k."]"; ?>']");
									
									if (elem.length>0 && !html.error){
										if (elem.is('input') || elem.is('select')) elem.val(html.<?php echo $v; ?>);
										else elem.html(html.<?php echo $v; ?>);
									}
									
									//alert($(html).toString());
									<?php 
								}
							}
						?>
					<?php echo !$updateIfError?'}':''; ?>
					if (html.error==1)
						$("span[name='Response<?php echo $vars['internalname']; ?>']").html(html.message);
					else
						$("span[name='Response<?php echo $vars['internalname']; ?>']").html('');
					
					<?php if (isset($vars['success'])) echo "if (is_callable('".$vars['success']."')) {".$vars['success']."(html);}";	?>	
				
					
			  }
			});
	}
</script>
<input type="text" <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> <?php echo $js; ?> name="<?php echo $vars['internalname']; ?>" value="<?php echo htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $class ?>"/>
<?php if(!isset($vars['button_disabled']) || $vars['button_disabled']===false){ ?>
<a title="<?php echo Viewer::_echo('search'); ?>" style="cursor:pointer;margin: 0 10px 0 10px;" onClick="searchFunction<?php echo $strippedname;?>();"><img src="/img/icon/magnifier.png"/></a>
<span style="color:#f00" name="Response<?php echo $vars['internalname']; ?>"></span>
<?php } ?> 