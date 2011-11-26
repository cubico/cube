<?php

	/**
	 * Elgg text input
	 * Displays a text input field
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['disabled'] If true then control is read-only
	 * @uses $vars['class'] Class override
	 */

	
	$class = $vars['class'];
	if (!$class) $class = "input-text";
	
	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
	
?>
<span  class="<?php echo $class ?>" <?php echo $js; ?> name="<?php echo $vars['internalname']; ?>"><?php echo htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'); ?></span>