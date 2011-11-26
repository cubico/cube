<?php 
	$design=(isset($vars['mode']) && $vars['mode']=='design')?'border: 1px solid #888;':'';
	$class=empty($vars['class'])?'':$vars['class'];

if (!defined("FORMFIELD_VIEW")){ 
	define("FORMFIELD_VIEW",true); 
?>
<!-- 
<style>
.column {<?php echo $design; ?>margin: 5px 10px 0 10px;overflow: hidden;float: left;display: inline;}
.row {<?php echo $design; ?>width: 960px;margin: 0 auto;overflow: hidden; clear: both;}
.row .row {<?php echo $design; ?>margin: 0 -10px 0 -10px;width: auto;display: inline-block;}
</style>
 -->
<?php } ?>  

<div class="row <?php echo $class; ?>"><?php echo $vars['content']; ?></div>