<?php 
$is_logged=MyUser::isLogged();
$textLog=$is_logged?'logout':'login';
$props=MyUser::getProperties(); 

if ($is_logged): ?>
<div style="float:right;margin: 3px 130px;color:#A1DFF4;text-align:right;" title="<?php echo $props->CubUsrId; ?>"><?php echo $props->CubUsrName;?></div>
<?php endif; ?>
<div id="elgg_topbar_container_right">
	<a class="<?php echo $textLog; ?>" href="<?php echo Route::url("default/logout"); ?>"><small><?php echo Viewer::_echo($textLog); ?></small></a>
</div>