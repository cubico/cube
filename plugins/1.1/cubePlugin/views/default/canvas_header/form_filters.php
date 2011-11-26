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
	
<?php
	$display=Session::get($vars['id'],isset($vars['default_display'])?$vars['default_display']:'none');	// por defecto none! (block)
	if ($display=='') $display='none';
?>


<div class="collapsable_box no_space_after" id="<?php echo $vars['id']; ?>" style="display:<?php echo $display;?>">
	<div class="collapsable_box_header" >
	<h1><?php echo Viewer::_echo("form:title:search"); ?></h1>
	</div>
	<div id="boxspot<?php echo $vars['id']; ?>" class="collapsable_box_content" >
<?php
	echo $vars['content'];
?>
	</div><!-- /.collapsable_box_content -->
	<div class="minicontentWrapper" ></div>	
</div><!-- /.collapsable_box -->
</div><!-- /#wrapper_spotlight -->
</div><!-- /#layout_spotlight -->