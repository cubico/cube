<?php 
	$design=(isset($vars['mode']) && $vars['mode']=='design')?'border: 1px solid #888;':'';
	$class=empty($vars['class'])?'':$vars['class'];

if (!defined("FORMFIELD_LOG_VIEW")){ 
	define("FORMFIELD_LOG_VIEW",true); 
?>

<style>
#divlogger .column {<?php echo $design; ?>margin: 5px 10px 0 10px;overflow: hidden;float: left;display: inline;}
#divlogger .row {<?php echo $design; ?>width: 906px;margin: 0 auto;overflow: hidden;}
#divlogger .row .row {<?php echo $design; ?>margin: 0 -10px 0 -10px;width: auto;display: inline-block;}
</style>

<?php } ?>  

<div class="row <?php echo $class; ?>"><?php echo $vars['content']; ?></div>