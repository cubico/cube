<?php
	/**
	 * Elgg plugin manifest class
	 * 
	 * This file renders a plugin for the admin screen, including active/deactive, manifest details & display plugin
	 * settings.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 */


	$plugin = $vars['plugin'];
	$details = $vars['details'];
	
	$active = $details['active'];
	$manifest = $details['manifest'];
	
	// Check elgg version if available
	$version_check_valid = true;
	/*$version_check_valid = false;
	if ($manifest['elgg_version'])
		$version_check_valid = check_plugin_compatibility($manifest['elgg_version']);*/
	
	
	$ts = time();
	$token=Controller::generateActionToken($ts);
	//$r=Request::getInstance();
?>
<div class="plugin_details <?php if ($active) echo "active"; else echo "not-active" ?>">
	<div class="admin_plugin_reorder">
		<?php
			if ($vars['order'] > 10) {
?>
			<a href="<?php echo $vars['url']; ?>/reorder?plugin=<?php echo $plugin; ?>&order=1&__elgg_token=<?php echo $token; ?>&__elgg_ts=<?php echo $ts; ?>"><?php echo Viewer::_echo("top"); ?></a>
			<a href="<?php echo $vars['url']; ?>/reorder?plugin=<?php echo $plugin; ?>&order=<?php echo $vars['order'] - 11; ?>&__elgg_token=<?php echo $token; ?>&__elgg_ts=<?php echo $ts; ?>"><?php echo Viewer::_echo("up"); ?></a>
<?php
			}
		?> 
		<?php
			if ($vars['order'] < $vars['maxorder']) {
?>
			<a href="<?php echo $vars['url']; ?>/reorder?plugin=<?php echo $plugin; ?>&order=<?php echo $vars['order'] + 11; ?>&__elgg_token=<?php echo $token; ?>&__elgg_ts=<?php echo $ts; ?>"><?php echo Viewer::_echo("down"); ?></a>
			<a href="<?php echo $vars['url']; ?>/reorder?plugin=<?php echo $plugin; ?>&order=<?php echo $vars['maxorder'] + 11; ?>&__elgg_token=<?php echo $token; ?>&__elgg_ts=<?php echo $ts; ?>"><?php echo Viewer::_echo("bottom"); ?></a>
<?php
			}
		?> 
	</div><div class="clearfloat"></div>
	<div class="admin_plugin_enable_disable">
		<?php if ($plugin!='cubePlugin'): if ($active) { ?>
			<a href="<?php echo $vars['url']; ?>/disable?plugin=<?php echo $plugin; ?>&__elgg_token=<?php echo $token; ?>&__elgg_ts=<?php echo $ts; ?>"><?php echo Viewer::_echo("disable"); ?></a>
		<?php } else { ?>
			<a href="<?php echo $vars['url']; ?>/enable?plugin=<?php echo $plugin; ?>&__elgg_token=<?php echo $token; ?>&__elgg_ts=<?php echo $ts; ?>"><?php echo Viewer::_echo("enable"); ?></a>
		<?php } endif; ?>
	</div>

	<h3><?php echo $plugin; ?><?php if (Viewer::view("settings/{$plugin}/edit")) { ?> <a class="pluginsettings_link">[<?php echo Viewer::_echo('settings'); ?>]</a><?php } ?></h3>
	
	<?php if (Viewer::view("settings/{$plugin}/edit")) { ?>
	<div class="pluginsettings">
			<div id="<?php echo $plugin; ?>_settings">
				<?php echo Viewer::view("object/plugin", array('plugin' => $plugin, 'entity' => null /*find_plugin_settings($plugin)*/)) ?>
			</div>
	</div>
	<?php } ?>
	
	<?php

		if ($manifest) {
	
	?>
		<div class="plugin_description"><?php echo Viewer::view('output/longtext',array('value' => $manifest['description'])); ?></div>
	<?php

		}
	
	?>
	
	<p><a class="manifest_details"><?php echo Viewer::_echo("admin_plugins:label:moreinfo"); ?></a></p>

	<div class="manifest_file">
	
	<?php if ($manifest) { ?>
		<?php if ((!$version_check_valid) || (!isset($manifest['cube_version']))) { ?>
		<div id="version_check">
			<?php 
				if (!isset($manifest['elgg_version']))
					echo Viewer::_echo('admin_plugins:warning:elggversionunknown');
				else
					echo Viewer::_echo('admin_plugins:warning:elggtoolow');
			?>
		</div>
		<?php } ?>
		<div><?php echo Viewer::_echo('admin_plugins:label:version') . ": ". $manifest['version'] ?></div>
		<div><?php echo Viewer::_echo('admin_plugins:label:author') . ": ". $manifest['author'] ?></div>
		<div><?php echo Viewer::_echo('admin_plugins:label:copyright') . ": ". $manifest['copyright'] ?></div>
		<div><?php echo Viewer::_echo('admin_plugins:label:licence') . ": ". $manifest['license'] ?></div>
		<div><?php echo Viewer::_echo('admin_plugins:label:website') . ": "; ?><a href="<?php echo $manifest['website']; ?>"><?php echo $manifest['website']; ?></a></div>
	<?php } ?>

	</div>
	
</div>