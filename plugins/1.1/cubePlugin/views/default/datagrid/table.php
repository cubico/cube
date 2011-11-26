<?php 
$width=(isset($vars['th']['td_width'])?'style="width:'.$vars['th']['td_width'].'"':'');
?>
<div id="customise_page_view">
<table style="border:1px solid #CCCCCC;" <?php foreach($vars['table']['js'] as $attrib=>$value) { echo "\"{$attrib}\"=\"{$value}\" "; } ?>>
<!-- headers -->
<thead>
 <?php
 	foreach($vars['th']['data'] as $i=>$j) 
		$vars['th']['data'][$i]='<h2 '.$width.'>'.
				$vars['th']['data'][$i].'</h2>';
				
 	if (isset($vars['th']['js'])) $js=$vars['th']['js'];else $js=array();
	echo Viewer::view('datagrid/table/tr',array("body"=>$vars['th']['data'],"class"=>$vars['class'],"js"=>$js,"th"=>true,"td_width"=>''));
?>	
</thead>
<!-- end headers -->
<!-- data -->
<tbody>
<?php
	foreach($vars['td']['data'] as $tr)
	{
		if (isset($vars['td']['class'])) $class=$vars['td']['class'];else $class='';
		if (isset($vars['td']['js'])) $js=$vars['td']['js'];else $js=array();
		echo Viewer::view('datagrid/table/tr',array("body"=>$tr,"class"=>$class,"js"=>$js,"td_width"=>($width!='')?$vars['th']['td_width']:''));
	}
  ?>
<!-- end data -->
</tbody>  
</table>
</div>

