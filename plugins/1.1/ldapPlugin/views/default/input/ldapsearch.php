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
	

?>
<script type="text/javascript">
	function cercaCIP(){	
		var cip=$("input[name='<?php echo $vars['internalname']; ?>']").val();
		
		$.ajax({
			  url: "<?php echo Route::url("ldapSearchs/retrievebyNif");?>?param="+cip,
			  type: "POST",
			  cache: false,
			  timeout: 60000,
			  dataType: "json",
			  success: function(html){			  	 	
					<?php 
						preg_match("/(.*)[\[](.*)\.(.*)[\]]/",$vars['internalname'],$args);						
						if (isset($vars['update'])) {
							
								foreach($vars['update'] as $k=>$v){
									if (is_numeric($k)) $k=$v;
									?>	
									var elem=$("[name='<?php echo $args[1]."[".$args[2].".".$k."]"; ?>']");
									
									if (elem.length==0){
										elem=$("[name='<?php echo $args[1]."[".$k."]"; ?>']");
									}
									
									if (elem.length>0){
										if (elem.is('input')) elem.val(html.<?php echo $v; ?>);
										else elem.html(html.<?php echo $v; ?>);
									}
									
									<?php 
								}
							}
						?>
					$("span[name='Response<?php echo $vars['internalname']; ?>']").html(html.message);
					
					<?php if (isset($vars['success'])) echo $vars['success']."();";?>	
				
					
			  }
			});
	}
</script>
<input type="text" <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> <?php echo $js; ?> name="<?php echo $vars['internalname']; ?>" value="<?php echo htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $class ?>"/>
<?php if(!isset($vars['button_disabled']) || $vars['button_disabled']===false){ ?>
<a style="cursor:pointer;margin: 0 10px 0 10px;" onClick="cercaCIP();"><img src="/img/icon/magnifier.png"/></a>
<span style="color:#f00" name="Response<?php echo $vars['internalname']; ?>">...</span>
<?php } ?> 