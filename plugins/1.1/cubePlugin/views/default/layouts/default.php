<?php

	// Set title
	if (!isset($vars['title']))
	$vars['title'] = Viewer::_echo(Config::get('view:metas:title'));
	$vars['url']=Route::url("/");
	
?>

<?php echo Viewer::view('page_elements/header', $vars); ?>
<!-- main contents -->
    
<!-- canvas -->
<div id="layout_canvas">

<?php  echo $vars['body']; ?>

<div class="clearfloat"></div>
</div><!-- /#layout_canvas -->

<?php /* if (Session::hasCredential('is_logged')){?>
	 <?php echo Viewer::view('page_elements/spotlight', $vars);  ?>
<?php } */ ?>

<!-- footer -->
<?php echo Viewer::view('page_elements/footer', $vars); ?>
