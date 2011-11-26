<?php
$hidden='style="display:none;"';
$wrappedform="aaaa"; // formulario de busqueda
?>
<!--
<div class="contentWrapper">
<div id="logbrowserSearchform" <?php echo $hidden; ?>><?php echo $wrappedform; ?></div>
		<p>
			<a href="#" onclick="$('#logbrowserSearchform').toggle()"><?php echo Viewer::_echo('datagrid:search'); ?></a>
		</p>
</div>
-->
<?php 
$maxcolumns=0; 
?>
<div class="admin_statistics">
    <?php if (isset($vars['title'])) echo Viewer::title($vars['title']); 
	
	$pagination=(isset($vars['pagination']) && is_array($vars['pagination']));
	if ($pagination)
	{
		$offset=intval($vars['pagination']['offset']);
	
		$nav=Viewer::view('navigation/pagination',array(	'baseurl' => Route::url(),
														'offset' => $offset,
														'count' => count($vars['data']),
														'limit' => $vars['pagination']['limit']));
		echo $nav;
	}
	
	?>
	<table>
    	<tr>
			<?php foreach ($vars['headers'] as $i=>$header):?>
			<th <?php echo ($i==-1)?'class="column_one"':''; ?> style="background:#E4E4E4;padding:0 5px;"><b><?php echo $header;?></b></th>
			<?php $maxcolumns++; endforeach; ?>
		</tr>
        <?php 
		
		if ($pagination) $max=min(array($offset+$vars['pagination']['limit'],count($vars['data'])));
		else {$offset=0;$max=count($vars['data']);}
		
		for($j=$offset;$j<$max;$j++){
			
			$item=$vars['data'][$j]; 
			
		?>
		<tr class="<?php echo (($j%2)==0)?'odd':'even';?>">
			<?php $col=current($item);?>
			<?php for ($i=0;$i<$maxcolumns;$i++): ?>
				<td <?php echo ($i==-1)?'class="column_one"':''; ?> style="padding:0 5px;"><?php echo ($col)?$col:''; $col=next($item);?></td>
			<?php endfor; ?>
		</tr>
	<?php } ?>

    </table> 
	<?php if ($pagination) echo $nav; ?>
</div>  