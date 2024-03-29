<?php
	/**
	 * Create a form for data submission.
	 * Use this view for forms rather than creating a form tag in the wild as it provides
	 * extra security which help prevent CSRF attacks.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['body'] The body of the form (made up of other input/xxx views and html
	 * @uses $vars['method'] Method (default POST)
	 * @uses $vars['enctype'] How the form is encoded, default blank
	 * @uses $vars['action'] URL of the action being called
	 * 
	 */
	if (isset($vars['internalid'])) { $id = $vars['internalid']; } else { $id = ''; }
	if (isset($vars['internalname'])) { $name = $vars['internalname']; } else { $name = ''; }
	$body = $vars['body'];
	$action = $vars['action'];
	if (isset($vars['enctype'])) { $enctype = $vars['enctype']; } else { $enctype = ''; }
	if (isset($vars['method'])) { $method = $vars['method']; } else { $method = 'POST'; }

	// Generate a security header
	$security_header = "";
	if ($vars['disable_security']!=true)
	{
		$ts = time();
		$token = Controller::generateActionToken($ts);
		$security_header = Viewer::view('input/hidden', array('internalname' => '___token', 'value' => $token));
		$security_header .= Viewer::view('input/hidden', array('internalname' => '___ts', 'value' => $ts));
	}
	echo $body; 
?>