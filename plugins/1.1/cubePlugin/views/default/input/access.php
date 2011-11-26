<?php

	/**
	 * Elgg access level input
	 * Displays a pulldown input field
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
	 * 
	 */
	
	//////////////////////////////
	
	if (isset($vars['class'])) $class = $vars['class'];
	if (!$class) $class = "input-access";
	
	$default_access=Config::get('settings:views:default_access');
	$types=Config::get('settings:views:access_types');
	$i18n_support=Config::get("settings:i18n:enabled");
		
	if (!array_key_exists('value', $vars) || $vars['value'] == $default_access)
		$vars['value'] =$types[$default_access]['value']; 
			
		if ((!isset($vars['options'])) || (!is_array($vars['options'])))
		{
			$temp=array(""=>"");
			foreach($types as $k=>$v)
			{
				/*
				if (!isset($v['text']) && $i18n_support) // soporte i18n?
					eval("\$text=LANG_ACCESS_".strtoupper($k).";");
				else $text=$v['text'];
				*/
				
				if ($i18n_support) 
				{
					$text=Viewer::_echo('access:'.$k); //si no existe en lang, te pasa el nombre de la variable
					if ($text=='access:'.$k) $text=null; // si nos devuelve el mismo nombre, es que no existia en lang.
				}
				echo $text;
				
				if ($text===null)
				{
					if (!isset($v['text'])) $text=$k;
					else $text=$v['text'];
				}
				$temp[$v['value']]=$text;
			}
		
			$vars['options'] = array();
			$vars['options'] = $temp; 
		}
		
		if (is_array($vars['options']) && sizeof($vars['options']) > 0) {	 
			 
?>

<select name="<?php echo $vars['internalname']; ?>" <?php if (isset($vars['js'])) echo $vars['js']; ?> <?php if ((isset($vars['disabled'])) && ($vars['disabled'])) echo ' disabled="yes" '; ?> class="<?php echo $class; ?>">
<?php

    foreach($vars['options'] as $key => $option) {
        if ($key != $vars['value']) {
            echo "<option value=\"{$key}\">". htmlentities($option, ENT_QUOTES, 'UTF-8') ."</option>";
        } else {
            echo "<option value=\"{$key}\" selected=\"selected\">". htmlentities($option, ENT_QUOTES, 'UTF-8') ."</option>";
        }
    }

?> 
</select>

<?php

		}		

?>