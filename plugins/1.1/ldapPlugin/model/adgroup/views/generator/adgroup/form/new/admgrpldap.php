<?php /* -------------- adgroupAdmgrpldapForm ------------------ */ ?>
<?php ob_start(); ?>
<?php echo Session::getInstance()->getFlash('adgroupAdmgrpldapForm_info'); ?>

<?php echo Session::getInstance()->getFlash('adgroupAdmgrpldapForm'); ?>
<?php /* -------------- INIT adgroupAdmgrpldapForm ------------------ */ ?>
<?php /* -------------- adgroupAdmgrpldapForm[adgroup.samaccountname] ------------------ */ ?>
<?php $errors=Session::getInstance()->getFlash('adgroupAdmgrpldapForm[adgroup.samaccountname]');?>
<?php if (is_array($errors)) $errors=implode(", ",$errors);?>
<?php if ($errors!==null) { $class='class="reportedcontent_content active_report"'; $errors=" : ".$errors; }else $class='class="formparagraph"';?>
 <table <?php echo $class; ?> >
	<tr><td><?php echo "<label>Id</label>"; ?></td><td><?php echo $errors.""."&#160;".Viewer::view('input/text',
		array (
  'js' => 'style="width:72px;"',
  'internalname' => 'adgroupAdmgrpldapForm[adgroup.samaccountname]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'new',
  'assignTo' => 'Adgroup.Id',
  'class' => 'required',
'value'=>(isset($vars['values']['adgroup.samaccountname'])?$vars['values']['adgroup.samaccountname']:'')));?>&#160;<br/>
</td></tr></table>
<?php /* -------------- adgroupAdmgrpldapForm[adgroup.description] ------------------ */ ?>
<?php $errors=Session::getInstance()->getFlash('adgroupAdmgrpldapForm[adgroup.description]');?>
<?php if (is_array($errors)) $errors=implode(", ",$errors);?>
<?php if ($errors!==null) { $class='class="reportedcontent_content active_report"'; $errors=" : ".$errors; }else $class='class="formparagraph"';?>
 <table <?php echo $class; ?> >
	<tr><td><?php echo "<label>Descrip</label>"; ?></td><td><?php echo $errors.""."&#160;".Viewer::view('input/text',
		array (
  'js' => 'style="width:72px;"',
  'internalname' => 'adgroupAdmgrpldapForm[adgroup.description]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'new',
  'assignTo' => 'Adgroup.Descrip',
  'class' => 'required',
'value'=>(isset($vars['values']['adgroup.description'])?$vars['values']['adgroup.description']:'')));?>&#160;<br/>
</td></tr></table>
<?php /* -------------- adgroupAdmgrpldapForm[adgroup.info] ------------------ */ ?>
<?php $errors=Session::getInstance()->getFlash('adgroupAdmgrpldapForm[adgroup.info]');?>
<?php if (is_array($errors)) $errors=implode(", ",$errors);?>
<?php if ($errors!==null) { $class='class="reportedcontent_content active_report"'; $errors=" : ".$errors; }else $class='class="formparagraph"';?>
 <table <?php echo $class; ?> >
	<tr><td><?php echo "<label>Info</label>"; ?></td><td><?php echo $errors.""."&#160;".Viewer::view('input/text',
		array (
  'js' => 'style="width:72px;"',
  'internalname' => 'adgroupAdmgrpldapForm[adgroup.info]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'new',
  'assignTo' => 'Adgroup.Info',
  'class' => 'required',
'value'=>(isset($vars['values']['adgroup.info'])?$vars['values']['adgroup.info']:'')));?>&#160;<br/>
</td></tr></table>

<?php /* -------------- adgroupAdmgrpldapForm[__formaction__] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/hidden',
 		array (
  'internalname' => 'adgroupAdmgrpldapForm[__formaction__]',
  'mode' => 'new',
'value'=>'new')); ?><br/>
</td></tr></table><?php /* -------------- END adgroupAdmgrpldapForm ------------------ */ ?>

<?php $body_adgroupAdmgrpldapForm=ob_get_clean();

echo Viewer::view('input/form',array (
  'name' => 'adgroupAdmgrpldapForm',
  'internalid' => 'adgroupAdmgrpldapForm',
  'internalname' => 'adgroupAdmgrpldapForm',
  'method' => 'POST',
  'model_order' => NULL,
 'body'=>$body_adgroupAdmgrpldapForm,
  'action' => Route::url((isset($vars['params']['action']))?$vars['params']['action']:'')));?>
