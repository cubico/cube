<?php $textLog=Session::hasCredential('is_logged')?'logout':'login';?>
<div id="elgg_topbar_container_right">
	<a class="<?php echo $textLog; ?>" href="<?php echo Route::url("default/logout"); ?>"><small><?php echo Viewer::_echo($textLog); ?></small></a>
</div>