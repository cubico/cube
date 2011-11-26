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
	
	
	foreach($vars['options'] as $option => $label) {
        if (strtolower($option) != strtolower($vars['value'])) {
            $img="/img/icon/radio_button_uncheck.png";
        } else {
            $img="/img/icon/radio_button.png";
        }
        $labelint = (int) $label;
        if ("{$label}" == "{$labelint}") {
        	$label = $option;
        }
        
        $value = parseText(htmlentities($label, ENT_QUOTES, 'UTF-8'));
		
        //if ($vars['disabled']) $disabled = ' disabled="yes" ';else $disabled=""; 
        $vars['js']='onclick="$(this).blur();"';
        
        echo '<img class="'.$class.'" src="'.$img.'" />'.$value;
        
		echo "</span>".(isset($vertical)?"<br/>":"")."<span>";
    }
?></span>