<span class="radio-button">
<?php

	/**
	 * Elgg radio input
	 * Displays a radio input field
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
	 * @uses $vars['options'] An array of strings representing the options for the radio field as "label" => option
	 * 
	 */
	
	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
	
	$class = $vars['class'];
	if (!$class) $class = "input-radio";
	
	if (isset($vars['query'])) // si pasamos query, creamos el options que despues leeremos
	{
		$vars['options']=array();
		
		$src=explode(".",$vars['query']['select']);
		$class=$src[0]."Peer";
		$peer=new $class();
		$data=$peer->doSelect($peer->getQuery($src[1]),false);
		
		foreach($data as $cur){
			
			$option=$cur[$vars['query']['value']];
			$text= $cur[$vars['query']['text']];
			
			$vars['options'][$option]=$text;
		}
	}
	
	if (isset($vars['align']) && $vars['align']=='vertical') {$vertical=true;echo "<br/>";}
	
	
	if (isset($vars['default']) && empty($vars['value']) && $vars['mode']!='filter')
	{
		$vars['value']=$vars['default'];
	}
	
	if (isset($vars['options']) && count($vars['options'])>0){
	
		foreach($vars['options'] as $option => $label) {
	        if (strtolower($option) != strtolower($vars['value'])) {
	            $selected = "";
	        } else {
	            $selected = "checked = \"checked\"";
	        }
	        $labelint = (int) $label;
	        if ("{$label}" == "{$labelint}") {
	        	$label = $option;
	        }
	        
	        $val=htmlentities($label, ENT_QUOTES, 'UTF-8');
	        if ($val==$label) $value=parseText($val);else $value=$label;
			
	        if ($vars['disabled']) $disabled = ' disabled="yes" ';else $disabled=""; 
	        echo "<input type=\"radio\" $disabled {$js} name=\"{$vars['internalname']}\" value=\"".htmlentities($option, ENT_QUOTES, 'UTF-8')."\" {$selected} class=\"$class\" />{$value}";
	        
			echo "</span>".(isset($vertical)?"<br/>":"").'<span  class="radio-button">';
	    }
	}
?></span>