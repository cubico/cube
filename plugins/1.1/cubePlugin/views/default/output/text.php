<?php

	/**
	 * Elgg text output
	 * Displays some text that was input using a standard text field
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['text'] The text to display
	 * 
	 */

if (isset($vars['title']) && isset($vars['entity'])){
	$title='title="'.Route::parseValues($vars['title'],$vars['entity']).'"';
}else $title='';

if (isset($vars['img'])) {$img=$vars['img'];}else {$img="";}
if (!preg_match("/^<img/",$img) && !empty($img)) $img='<img src="'.$img.'" '.$title.' />';	// fuera del htmlentities!
if (isset($vars['class'])) {$class='class="'.$vars['class'].'"';}else {$class="";}
if (isset($vars['html']) && $vars['html']) $html=true;else $html=false;

if (isset($vars['js']))
{
	$js='';
	if (is_array($vars['js']))
	{
		foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
	}
	else $js=$vars['js'];
}
?><div <?php echo $js; ?> <?php echo $class; ?> name="<?php echo $vars['internalname']; ?>">
    <?php  // stripslashes(stripslashes(parseText( because parseText make addslashes
		echo $img.((!$html)?htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'):stripslashes(stripslashes(parseText($vars['value']))));  
?></div>