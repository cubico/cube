<?php $str=''; if (isset($vars['js'])) foreach($vars['js'] as $attrib=>$v) { $str.="{$attrib}=\"{$v}\" "; } ?>
 
 <tr <?php echo $str; ?> <?php if (isset($vars['class']) && $vars['class']!='') echo 'class="'.$vars['class'].'"'; ?>>
<?php
	foreach($vars['body'] as $td)
	{
		echo Viewer::view('datagrid/table/td',array('body'=>$td,'td_width'=>$vars['td_width'],"class"=>$vars['class'],
													"js"=>$vars['js'],"th"=>isset($vars['th'])?$vars['th']:false));
	}
?>
</tr>