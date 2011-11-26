<?php  
	$str='';
	if ($vars['td_width']!='') 
	{
		if (array_key_exists('style',$vars['js'])) {$vars['js']['style'].=";width:".$vars['td_width'];}
		else $str='style= "width:'.$vars['td_width'].'"';
	}	
	if (isset($vars['js'])) foreach($vars['js'] as $attrib=>$v) { $str.="{$attrib}=\"{$v}\" "; }
?>

<?php $tag=(isset($vars['th']) && $vars['th'])?'th':'td'; 
	echo "<{$tag} ".((isset($vars['class']) && $vars['class']!='')?'class="'.$vars['class'].'"':'').
			" {$str} >".$vars['body']."</{$tag}>"; 