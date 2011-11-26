<?php
	if (!defined('PULLDOWN_COMPONENT_VIEW') && isset($vars['editable']) && $vars['editable']){
		define('PULLDOWN_COMPONENT_VIEW',true);
		// http://stuff.rajchel.pl/jec/demos/#demo-1
		?><script type="text/javascript" src="/js/jquery/jquery.jec.js"></script><?php 
	}

	/**
	 * Elgg pulldown input
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
	 * @uses $vars['options'] An array of strings representing the options for the pulldown field
	 * @uses $vars['options_values'] An associative array of "value" => "option" where "value" is an internal name and "option" is 
	 * 								 the value displayed on the button. Replaces $vars['options'] when defined. 
	 */
	//echo _r($vars);die();
	$css = $vars['class'];
	if (!$css) $css = "input-pulldown";
	if (!isset($vars['value'])) $vars['value']=array();
	if (!is_array($vars['value'])) {
		if ($vars['value']=="") $empty=true;else $empty=false;
		$vars['value']=array($vars['value']); 
	}
	$data=array();
	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
	
	$size=isset($vars['size'])?' size="'.$vars['size'].'"':'';
	//echo "pulldown---------------"._r($vars);
	// select multiple
	$multi=(isset($vars['multiple'])?$vars['multiple']:false);
	$strippedname=strtr($vars['internalname'],"[].,","___");
	if (isset($vars['align']) && $vars['align']=='vertical') echo '<div style="margin:10px 0;">';
	
	//echo _r($vars);
	?>
	<script type="text/javascript">
		$(document).ready(function(){
	<?php if (isset($vars['readonly']) && $vars['readonly']): ?>
			$('#<?php echo $strippedname; ?>').pulldownReadonly();
	<?php elseif (isset($vars['buttonAllSelected'])): ?>
			$('#allbutton<?php echo $strippedname; ?>').html('<?php echo $vars['buttonAllSelected']; ?>');
			$('#allbutton2<?php echo $strippedname; ?>').html('<?php echo $vars['buttonAllNotSelected']; ?>');
				
			$('#allbutton<?php echo $strippedname; ?>').data('status',false);
				
			$('#allbutton2<?php echo $strippedname; ?>').click(function(){
				$('#<?php echo $strippedname; ?> option:selected').removeAttr('selected').css('background','#fff');
				$(this).data('status',false);
			});
			$('#allbutton<?php echo $strippedname; ?>').click(function(){
				$('#<?php echo $strippedname; ?> option').attr('selected','selected').css('background','#39f');
				$(this).data('status',true);
			});
	<?php endif; 
			
		if (isset($vars['editable']) && $vars['editable']){
			if (isset($vars['max_length'])) $maxlength='maxLength: '.$vars['max_length'].', ';
			else $maxlength='';
			?>$('#<?php echo $strippedname; ?>')
						.jec({<?php echo $maxlength;?>blinkingCursor: true, blinkingCursorInterval: 500})
						.jecValue('<?php echo $vars['value'][0];?>');<?php

		}
		?>



		});
	</script>
	
	<?php
	//echo "******"._r($vars);
	ob_start();		

	if (isset($vars['blank_option'])){ 
		 $vars['blank_option']=parseText($vars['blank_option']);
		echo "<option ".($empty?"selected=\"selected\"":"")." value=\"\">".$vars['blank_option']."</option>";
	}

	if (isset($vars['empty_option'])){ 
		$vars['empty_option']=parseText($vars['empty_option']);
		echo "<option ".(($vars['value'][0]==Query::NULL)?"selected=\"selected\"":"")." value=\"".(Query::NULL)."\">".$vars['empty_option']."</option>";
	}
	
	if (isset($vars['options_values']))
	{
		foreach($vars['options_values'] as $value => $option) {
	        $option=parseText($option);
			if (!in_array($value,$vars['value'])) {
	            echo "<option value=\"$value\">". htmlentities($option, ENT_QUOTES, 'UTF-8') ."</option>";
	        } else {
	            echo "<option value=\"$value\" ".(!$empty?"selected=\"selected\"":"").">". htmlentities($option, ENT_QUOTES, 'UTF-8') ."</option>";
	        }
	    }
	}
	else if (isset($vars['options']))
	{
	    foreach($vars['options'] as $option) {
	        if (!in_array($option,$vars['value'])) {
	            echo "<option>". htmlentities($option, ENT_QUOTES, 'UTF-8') ."</option>";
	        } else {
	            echo "<option ".(!$empty?"selected=\"selected\"":"").">". htmlentities($option, ENT_QUOTES, 'UTF-8') ."</option>";
	        }
	    }
	}
	else if (isset($vars['query']) && !isset($vars['parameters']['through_class']))
	{
		$paramModel=$vars['query'];
		
		if (is_array($paramModel)) {
			$src=explode(".",$vars['query']['select']);
			$class=$src[0]."Peer";
			$select=$src[1];
		}else{
			$select=$vars['query'];
		}
		$peer=new $class();
		$data=$peer->doSelect($peer->getQuery($select),false);
	}	
	else if( isset($vars['assignTo']) || isset($vars['peerMethod']) ){  
		
		if (isset($vars['assignTo']) && !preg_match("/[.]/",$vars['assignTo'])){
			
			$class=$vars['assignTo']."Peer";
			$peer=new $class();
			//$data=$peer->doSelectAll(false);
			
			if (isset($vars['parameters']['through_class'])){
				if (isset($vars['parameters']['query'])){
					$paramModel=$vars['parameters']['query'];
					if (!is_array($paramModel))
						$data=$peer->doSelect($peer->getQuery($paramModel),false);
					else if (isset($paramModel['select']))
						$data=$peer->doSelect($peer->getQuery($paramModel['select']),false);
					
				}else if (isset($vars['parameters']['peerMethod'])){
					$methods=get_class_methods($peer);
					$paramModel=$vars['parameters']['peerMethod'];
						
					if (!is_array($paramModel)) $method=$paramModel;
					else if (isset($paramModel['method'])) $method=$paramModel['method'];
					if (in_array($method,$methods)) $data=$peer->{$method}();

				}else {
					$data=$peer->doSelectAll(false);
				}
			}else $data=$peer->doSelectAll(false);

		}else if (isset($vars['peerMethod'])){
			$paramModel=$vars['peerMethod'];
			$src=explode(".",$paramModel['method']);
			$class=$src[0]."Peer";
			$peer=new $class();
			$methods=get_class_methods($peer);
			
			if (in_array($src[1],$methods)){ 
				
				if (!isset($paramModel['value'])) $data=$peer->{$src[1]}(); 
				else $data=$peer->{$src[1]}($paramModel['value'],$vars['peerMethod']['text']);
			}
		}
	}	
	
	if (isset($paramModel)){
		if (is_array($paramModel)){
			if (isset($paramModel['blank_option']))
				echo "<option ".($empty?"selected=\"selected\"":"")." value=\"\">".$paramModel['blank_option']."</option>";	
						
			if (isset($paramModel['empty_option'])) 
				echo "<option ".(($vars['value'][0]==Query::NULL)?"selected=\"selected\"":"")." value=\"".(Query::NULL)."\">".$paramModel['empty_option']."</option>";	
		}
		
		foreach($data as $cur){
			
			if (!is_array($paramModel) || !isset($paramModel['value'])){
				$option=reset($cur);
				$text=next($cur);
			}else{
				$option=$cur[$paramModel['value']];
				$text= $cur[$paramModel['text']];
			}
			
			if (!in_array($option,$vars['value'])) {
				echo "<option value=\"{$option}\">". htmlentities($text, ENT_QUOTES, 'UTF-8') ."</option>";
	        } else {
	        	$selected=(($multi)?'class="selected"':'');
	            echo "<option value=\"{$option}\" selected=\"selected\" {$selected}>". htmlentities($text, ENT_QUOTES, 'UTF-8') ."</option>";
	        }
		}
	}
		
	$optionsHTML=ob_get_clean();

if ((!isset($vars['readonly']) || !$vars['readonly']) && isset($vars['buttonAllSelected'])): ?>
<div style="padding: 10px;">
	<a style="font-size: 1em;cursor:pointer" id="allbutton<?php echo $strippedname;?>"></a>
	<a style="font-size: 1em;margin: 0 10px;cursor:pointer" id="allbutton2<?php echo $strippedname;?>"></a>
</div>
<?php endif; ?>
<select id="<?php echo $strippedname;?>" name="<?php echo $vars['internalname'].(($multi)?"[]\" multiple=\"multiple\"":"\"").$size;?> <?php echo $js; ?> <?php if ($vars['disabled']===true) echo ' disabled="yes" '; ?> class="<?php echo $css; ?>">
<?php echo $optionsHTML; ?>
</select>
<?php if (isset($vars['align']) && $vars['align']=='vertical') echo '</div>';?>