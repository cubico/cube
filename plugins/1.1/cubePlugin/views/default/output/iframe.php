<?php
	/**
	 * Display a page in an embedded window
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] Source of the page
	 * 
	 */
if (!isset($vars['entity'])) $vars['entity']=array();
if (isset($vars['action'])) $vars['value']=$vars['url']."/".Route::parseValues($vars['action'],$vars['entity']);
if (isset($vars['js'])) $vars['style']=$vars['js'];

?>
<div style="clear:both;">
	<iframe src="<?php echo $vars['value']; ?>"  
			name="contentiframe" 
			id="contentiframe"
			<?php echo (isset($vars['style'])?$vars['style']:''); ?>
			scrolling="auto" frameborder="0" id="muestra">No Support for iframes!</iframe>
</div>
<script type="text/javascript">
	$('#contentiframe').load(function() {
		//resizeIframe('contentiframe','contentiframe');
	});
	<?php 
		if (isset($vars['script']) && $vars['script']!==false){
			echo $vars['script'];  
		} 
	?>
</script>