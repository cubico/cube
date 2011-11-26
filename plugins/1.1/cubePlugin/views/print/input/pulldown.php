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
	if (!is_array($vars['value'])) {
		if ($vars['value']=='') $vars['value']=array();
		else $vars['value']=array($vars['value']);
	}
	if (isset($vars['multiple']) && $vars['multiple']){
		$tag1='ul';$tag2='li';
	}else{
		$tag1='span';$tag2='span';
	}
	
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
		$paramModel=$vars['query'];
		
		if (is_array($paramModel)) {
			$src=explode(".",$vars['query']['select']);
			$class=$src[0]."Peer";
			$select=$src[1];
		}else{
			$select=$paramModel;
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
		
		/*if (is_array($paramModel) && empty($vars['value'])){
			if (isset($paramModel['blank_option']))
				echo "<{$tag2}>".$paramModel['blank_option']."</{$tag2}>";
						
			if (isset($paramModel['empty_option'])) 
				echo "<{$tag2}>".$paramModel['empty_option']."</{$tag2}>";
		}*/
		
		foreach($data as $cur){
			
			if (!is_array($paramModel) || !isset($paramModel['value'])){
				$option=reset($cur);
				$text=next($cur);
			}else{
				$option=$cur[$paramModel['value']];
				$text= $cur[$paramModel['text']];
			}
			
			if (in_array($option,$vars['value'])) {
	            echo "<{$tag2}>". htmlentities($text, ENT_QUOTES, 'UTF-8') ."</{$tag2}>";
	        }
		}
	}
	
?> 
</<?php echo $tag1; ?>>
<?php if (isset($vars['align']) && $vars['align']=='vertical') echo '</div>'; ?>