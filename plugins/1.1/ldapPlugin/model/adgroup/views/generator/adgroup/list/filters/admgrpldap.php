<?php /* -------------- adgroupAdmgrpldapFormFilter ------------------ */ ?>
<?php ob_start(); ?>
<?php echo Session::getInstance()->getFlash('adgroupAdmgrpldapFormFilter_info'); ?>

<?php echo Session::getInstance()->getFlash('adgroupAdmgrpldapFormFilter'); ?>
<?php /* -------------- INIT adgroupAdmgrpldapFormFilter ------------------ */ ?>
<?php /* -------------- adgroupAdmgrpldapFormFilter[__filterform__samaccountname] ------------------ */ ?>
<?php $errors=Session::getInstance()->getFlash('adgroupAdmgrpldapFormFilter[__filterform__samaccountname]');?>
<?php if (is_array($errors)) $errors=implode(", ",$errors);?>
<?php if ($errors!==null) { $class='class="reportedcontent_content active_report"'; $errors=" : ".$errors; }else $class='class="formparagraph"';?>
 <table <?php echo $class; ?> >
	<tr><td><?php echo "<label>Id</label>"; ?></td><td><?php echo $errors.""."&#160;".Viewer::view('input/text',
		array (
  'js' => 'style="width:72px;"',
  'internalname' => 'adgroupAdmgrpldapFormFilter[__filterform__samaccountname]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'filter',
  'assignTo' => 'Adgroup.Id',
'value'=>(isset($vars['values']['__filterform__samaccountname'])?$vars['values']['__filterform__samaccountname']:'')));?>&#160;<br/>
</td></tr></table>
<?php /* -------------- adgroupAdmgrpldapFormFilter[__filterform__description] ------------------ */ ?>
<?php $errors=Session::getInstance()->getFlash('adgroupAdmgrpldapFormFilter[__filterform__description]');?>
<?php if (is_array($errors)) $errors=implode(", ",$errors);?>
<?php if ($errors!==null) { $class='class="reportedcontent_content active_report"'; $errors=" : ".$errors; }else $class='class="formparagraph"';?>
 <table <?php echo $class; ?> >
	<tr><td><?php echo "<label>Descrip</label>"; ?></td><td><?php echo $errors.""."&#160;".Viewer::view('input/text',
		array (
  'js' => 'style="width:72px;"',
  'internalname' => 'adgroupAdmgrpldapFormFilter[__filterform__description]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'filter',
  'assignTo' => 'Adgroup.Descrip',
'value'=>(isset($vars['values']['__filterform__description'])?$vars['values']['__filterform__description']:'')));?>&#160;<br/>
</td></tr></table>
<?php /* -------------- adgroupAdmgrpldapFormFilter[__filterform__info] ------------------ */ ?>
<?php $errors=Session::getInstance()->getFlash('adgroupAdmgrpldapFormFilter[__filterform__info]');?>
<?php if (is_array($errors)) $errors=implode(", ",$errors);?>
<?php if ($errors!==null) { $class='class="reportedcontent_content active_report"'; $errors=" : ".$errors; }else $class='class="formparagraph"';?>
 <table <?php echo $class; ?> >
	<tr><td><?php echo "<label>Info</label>"; ?></td><td><?php echo $errors.""."&#160;".Viewer::view('input/text',
		array (
  'js' => 'style="width:72px;"',
  'internalname' => 'adgroupAdmgrpldapFormFilter[__filterform__info]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'filter',
  'assignTo' => 'Adgroup.Info',
'value'=>(isset($vars['values']['__filterform__info'])?$vars['values']['__filterform__info']:'')));?>&#160;<br/>
</td></tr></table>

<table class="formtable"><tr>
<td><?php /* -------------- adgroupAdmgrpldapFormFilter[__filterform__gofilter] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/icon/funnel.png',
  'type' => 'submit',
  'internalname' => 'adgroupAdmgrpldapFormFilter[__filterform__gofilter]',
  'mode' => 'filter',
'value'=>'_echo(gofilter)')); ?><br/>
</td></tr></table>
</td>
<td><?php /* -------------- adgroupAdmgrpldapFormFilter[__filterform__clearfilters] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/icon/arrow_circle_135.png',
  'type' => 'button',
  'internalname' => 'adgroupAdmgrpldapFormFilter[__filterform__clearfilters]',
  'mode' => 'filter',
'action' => 'clearfilters',
'value'=>'_echo(button:clear:filters)')); ?><br/>
</td></tr></table>
</td>
</tr></table>

<?php /* -------------- adgroupAdmgrpldapFormFilter[__filterform____formaction__] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/hidden',
 		array (
  'internalname' => 'adgroupAdmgrpldapFormFilter[__filterform____formaction__]',
  'mode' => 'filter',
'value'=>'filters')); ?><br/>
</td></tr></table><?php /* -------------- END adgroupAdmgrpldapFormFilter ------------------ */ ?>

<?php $body_adgroupAdmgrpldapFormFilter=ob_get_clean();

echo Viewer::view('input/form',array (
  'name' => 'adgroupAdmgrpldapFormFilter',
  'internalid' => 'adgroupAdmgrpldapFormFilter',
  'internalname' => 'adgroupAdmgrpldapFormFilter',
  'method' => 'POST',
  'model_order' => NULL,
 'body'=>$body_adgroupAdmgrpldapFormFilter,
  'action' => Route::url((isset($vars['params']['action']))?$vars['params']['action']:'./filter')));?>
