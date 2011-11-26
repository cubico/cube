<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
	$title=Config::get('view:metas:title');
	// Set title
	if (isset($vars['title']) && !empty($vars['title'])) $title = $vars['title'];
	
?><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $title; ?></title>
	<?php  
		echo Viewer::includeMetaJsCss();
	?>
</head>
<body>	
<div id="layout_canvas">
<?php  echo $vars['body']; ?>
<div class="clearfloat"></div>
</div><!-- /#layout_canvas -->
<?php //echo Viewer::view('page_elements/footer', $vars); ?>
<?php echo "Data Creació: ".strftime('%d/%m/%Y %H:%M:%S',time()); ?>
</body>
</html>