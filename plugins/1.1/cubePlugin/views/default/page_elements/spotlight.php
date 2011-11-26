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
<div class="clearfloat"></div>
<div id="layout_spotlight">
<div id="wrapper_spotlight">
	
<div class="collapsable_box no_space_after">
	<div class="collapsable_box_header">
<?php
	$display=Session::get('spotlight','block');
	if ($display=='') $display='none';
	
?>
	<a href="javascript:void(0);" class="toggle_box_contents" 
		onClick="$.post('/aplicacions.php/default/spotlight?display='+$('#boxspot').get(0).style.display)"><?php echo ($display=='none')?'+':'-';?></a>
	<h1><?php echo Viewer::_echo("mailing:title"); ?></h1>
	</div>
	<div id="boxspot" class="collapsable_box_content" style="display:<?php echo $display;?>">
<?php
	
	if (!empty($context) && Viewer::viewExists("spotlight/{$context}")) {
		echo Viewer::view("spotlight/{$context}");
	} else {
		echo Viewer::view("spotlight/default");
	}

?>
	</div><!-- /.collapsable_box_content -->
</div><!-- /.collapsable_box -->
	
</div><!-- /#wrapper_spotlight -->
</div><!-- /#layout_spotlight -->