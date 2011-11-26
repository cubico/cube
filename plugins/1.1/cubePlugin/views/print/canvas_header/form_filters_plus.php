<?php

	/**
	 * Elgg spotlight
	 * The spotlight area that displays across the site
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008
	 * @link http://elgg.org/
	 * 
	 */
?>

<div id="layout_spotlight" style="margin:10px;">
<div id="wrapper_spotlight">
	
<div class="collapsable_box no_space_after" id="<?php echo $vars['id']; ?>">
	<div class="collapsable_box_header" >
<?php
	$display=Session::get($vars['id'],'block');	// por defecto block
	if ($display=='') $display='none';
?>
	<a href="javascript:void(0);" class="toggle_box_contents" 
		onClick="$.post('<?php echo Route::url("default/spotlight"); ?>?id=<?php echo $vars['id'];?>&display='+$('#boxspot<?php echo $vars['id']; ?>').get(0).style.display)"><?php echo ($display=='none')?'+':'-';?></a>
	<h1><?php echo Viewer::_echo("form:title:search"); ?></h1>
	</div>
	<div id="boxspot<?php echo $vars['id']; ?>" class="collapsable_box_content" style="display:<?php echo $display;?>">
<?php
	echo $vars['content'];
?>
	</div><!-- /.collapsable_box_content -->
</div><!-- /.collapsable_box -->
	
</div><!-- /#wrapper_spotlight -->
</div><!-- /#layout_spotlight -->