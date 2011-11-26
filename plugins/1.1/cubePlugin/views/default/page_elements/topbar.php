<?php

	/**
	 * Elgg top toolbar
	 * The standard elgg top toolbar
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
<div id="elgg_topbar">
	<div id="elgg_topbar_container_left">
		<?php /* 
		<div class="toolbarimages">
			<a href="<?php echo $SESSION['user']->getURL(); ?>"><img class="user_mini_avatar" src="<?php echo $SESSION['user']->getIcon('topbar'); ?>"></a>
		</div>
		*/ ?>

		<div class="toolbarimages" style="margin:2px;">
			<a href="/"><img  src="/img/gohome.png" title="<?php echo Viewer::_echo('gohome');?>"/></a>
		</div>
		<div class="toolbarlinks2">		
		<?php
			//allow people to extend this top menu
			echo Viewer::view('elgg_topbar/extend', $vars);
		?>
			<!-- <a href="<?php echo $vars['url']; ?>pg/settings/" class="usersettings"><?php echo Viewer::_echo('settings'); ?></a>  -->
		</div>
	</div>

	<div id="elgg_topbar_container_search">
		<!--
			<form id="searchform" action="<?php echo $vars['url']; ?>search/" method="get">

			<input type="text" size="21" name="tag" value="<?php echo Viewer::_echo('search'); ?>" onclick="if (this.value=='<?php echo Viewer::_echo('search'); ?>') { this.value='' }" class="search_input" />
			<input type="submit" value="<?php echo Viewer::_echo('go'); ?>" class="search_submit_button" />
		</form>
		-->
	</div>
	<div><?php echo Viewer::view('page_elements/logout'); ?></div>
</div><!-- /#elgg_topbar -->

<div style="clear:both;height:10px;"></div>