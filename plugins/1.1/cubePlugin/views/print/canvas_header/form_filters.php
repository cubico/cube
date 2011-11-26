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

if (Session::get($vars['id'])!='none'){
?>
<div style="margin:0 20px;">
<h2><?php echo Viewer::_echo("form:title:search_print"); ?></h2>
<?php echo $vars['content']; ?>
</div><!-- /#wrapper_spotlight -->
<div style="clear:both"></div>
<?php } ?>