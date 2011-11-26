<?php

	/**
	 * Elgg administration plugin main screen
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 */

		$ts = time();
		$token=Controller::generateActionToken($ts);
		$r=Request::getInstance();

	// Description of what's going on
		$buttons = " <a class='enableallplugins' href=\"".Route::url("plugins/enableall?___token=$token&___ts=$ts")."\">".Viewer::_echo('enableall')."</a>  <a class='disableallplugins' href=\"".Route::url("plugins/disableall?__elgg_token=$token&__elgg_ts=$ts")."\">".Viewer::_echo('disableall')."</a> ";
		echo "<div class=\"contentWrapper\"><span class=\"contentIntro\">" . $buttons . autop(Viewer::_echo("admin_plugins:description")) . "<div class='clearfloat'></div></span></div>";

		$limit = ($r->limit===null)?10:$r->limit;
		$offset = ($r->offset===null)?0:$r->offset;
		$max=count($vars['installed_plugins'])*10;
	
	// Get the installed plugins
		
		$n = 0;
		//echo _r($vars['installed_plugins'],true);
		foreach ($vars['installed_plugins'] as $plugin => $item)
		{
			
			//if (($n>=$offset) && ($n < $offset+$limit)){
				echo Viewer::view("admin/plugins_opt/plugin", array('url'=>Route::url('plugins'),'plugin' => $plugin, 'details' => $item, 'maxorder' => $max, 'order' => $item['order']));
			//} $n++;
			
		}
		
	// Diplay nav
	/*
		if ($count) 
		{
			 echo elgg_view('navigation/pagination',array(
												'baseurl' => $_SERVER['REQUEST_URI'],
												'offset' => $offset,
												'count' => $count,
														));
		}
	*/
?>
