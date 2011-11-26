<?php

	/**
	 * Elgg confirmation link
	 * A link that displays a confirmation dialog before it executes
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['text'] The text of the link
	 * @uses $vars['href'] The address
	 * @uses $vars['confirm'] The dialog text
	 * 
	 */

if (!isset($vars['entity'])) $vars['entity']=array();

if (isset($vars['action'])) $vars['href']=$vars['url']."/".Route::parseValues($vars['action'],$vars['entity']);
if (isset($vars['img'])) {$img=$vars['img'];}else {$img="";}
if (!preg_match("/^<img/",$img) && !empty($img)) $img='<img src="'.$img.'" />';

if (isset($vars['value'])) $value=$vars['value'];else $value="LINK";
$value=htmlentities($value, ENT_QUOTES, 'UTF-8');

	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	
	}
 echo $img.$value; 
?>