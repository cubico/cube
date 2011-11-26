<?php

	$class = $vars['class'];
	if (!$class) $class = "input-pulldown";
	if (!isset($vars['value'])) $vars['value']='';

	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
	
	if (isset($vars['align']) && $vars['align']=='vertical') echo '<div style="margin:10px 0;">';
	
?>&#160;
<div name="<?php echo $vars['internalname']; ?>" <?php echo $vars['js']; ?> class="<?php echo $class; ?>">
<?php
	$template=isset($vars['template'])?$vars['template']:null;
	
	

	if (!isset($vars['column'])){
		echo isset($vars['value'])?$vars['value']:'';
	}else{
		if (is_array($vars['column'])) $columna=$vars['column'][0];
		else $columna=$vars['column'];
		 
		$src=explode(".",$columna); 
		$class=$src[0]."Peer";
		
		$peer=new $class();
		if (!empty($vars['value']))
		{
			$data=$peer->retrieveByColumns(array($src[1]=>array("value"=>$vars['value'])));		
			if (count($data)>0) {
				$template=preg_replace("/(\%\%)([^(\%\%)]*)(\%\%)/","'.\$data[0]->$2.'",$template);
				eval("\$str='$template';");	
				echo $str;	
				//echo $cursos[0]->{$vars[];
				//echo "<{$tag2}>". htmlentities($text, ENT_QUOTES, 'UTF-8') ."</{$tag2}>";
			}
		}
	}
?> 
</div>
<?php if (isset($vars['align']) && $vars['align']=='vertical') echo '</div>'; ?>