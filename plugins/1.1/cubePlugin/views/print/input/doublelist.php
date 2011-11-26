<?php
	
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

	$class = $vars['class'];
	if (!$class) $class = "input-pulldown";
	if (!isset($vars['value'])) $vars['value']=array();
	if (!is_array($vars['value'])) $vars['value']=array($vars['value']);
	$tag1='ul';$tag2='li';
	
	if (isset($vars['align']) && $vars['align']=='vertical') echo '<div style="margin:10px 0;">';
	
?>&#160;
<<?php echo $tag1; ?> name="<?php echo $vars['internalname']; ?>" <?php echo $vars['js']; ?> <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> class="<?php echo $class; ?>">
<?php
			
	if (isset($vars['options_values']))
	{
		foreach($vars['options_values'] as $value => $option) {
			if (in_array($value,$vars['value'])) {
	            echo "<{$tag2}>". htmlentities($option, ENT_QUOTES, 'UTF-8') ."</{$tag2}>";
	        }
	    }
	}
	else if (isset($vars['options']))
	{
	    foreach($vars['options'] as $option) {
	    if (in_array($option,$vars['value'])) {
	            echo "<{$tag2}>". htmlentities($option, ENT_QUOTES, 'UTF-8') ."</{$tag2}>";
	        }
	    }
	}
	else if (isset($vars['query']) && !isset($vars['parameters']['through_class']))
	{
		$src=explode(".",$vars['query']['select']);
		$class=$src[0]."Peer";
		$peer=new $class();
		$data=$peer->doSelect($peer->getQuery($src[1]),false);
		
		foreach($data as $cur){
			
			$option=$cur[$vars['query']['value']];
			$text= $cur[$vars['query']['text']];
			
			if (in_array($option,$vars['value'])) {
	            echo "<{$tag2}>". htmlentities($text, ENT_QUOTES, 'UTF-8') ."</{$tag2}>";
	        }
		}
	}else if( isset($vars['assignTo']) || isset($vars['peerMethod']) ){  
	
		if (!preg_match("/[.]/",$vars['assignTo'])){
			
			$class=$vars['assignTo']."Peer";
			$peer=new $class();
			$data=$peer->doSelectAll(false); 
			
		}else if (isset($vars['peerMethod'])){
			$src=explode(".",$vars['peerMethod']['method']);
			$class=$src[0]."Peer";
			$peer=new $class();
			$methods=get_class_methods($peer);
			
			if (in_array($src[1],$methods)){ 
				if (!isset($vars['peerMethod']['value'])) $data=$peer->{$src[1]}(); 
				else $data=$peer->{$src[1]}($vars['peerMethod']['value'],$vars['peerMethod']['text']);
			}
		}
		
		if (isset($vars['peerMethod']['empty_option'])) 
			echo "<option value=\"".(Query::NULL)."\">".$vars['peerMethod']['empty_option']."</option>";
		if (isset($vars['peerMethod']['blank_option']))
			echo "<option value=\"\">".$vars['peerMethod']['blank_option']."</option>";
		//$data=$peer->{$src[1]};
		foreach($data as $cur){
			
			if (!isset($vars['peerMethod']['value']) && !isset($vars['peerMethod']['value'])){
				
				$option=reset($cur);
				$text=next($cur);
				
			}else{
				$option=$cur[$vars['peerMethod']['value']];
				$text= $cur[$vars['peerMethod']['text']];
			}
			
			if (in_array($option,$vars['value'])) {
	            echo "<{$tag2}>". htmlentities($text, ENT_QUOTES, 'UTF-8') ."</{$tag2}>";
	        }
	        
			
		}
	}
?> 
</<?php echo $tag1; ?>>
<?php if (isset($vars['align']) && $vars['align']=='vertical') echo '</div>'; ?>