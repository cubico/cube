<?php Viewer::setGlobalTemplate(Config::get('settings:views:global_template_print_value')); ?>
<?php /* -------------- aduserAdmuserldapForm ------------------ */ ?>
<?php ob_start(); ?>
<?php echo Session::getInstance()->getFlash('aduserAdmuserldapForm_info'); ?>

<?php echo Session::getInstance()->getFlash('aduserAdmuserldapForm'); ?>
<?php /* -------------- INIT aduserAdmuserldapForm ------------------ */ ?>
<?php /* -------------- aduserAdmuserldapForm[aduser.samaccountname] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/hidden',array (
  'internalname' => 'aduserAdmuserldapForm[aduser.samaccountname]',
  'mode' => 'edit',
  'assignTo' => 'Aduser.Nif',
'value'=>(isset($vars['values']['aduser.samaccountname'])?$vars['values']['aduser.samaccountname']:''))))); ?>

<div class="row"><?php /* -------------- aduserAdmuserldapForm[aduser.displayname] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapForm[aduser.displayname]');
								    	if (is_array($errors)) $errors=implode(', ',$errors);
								    	if ($errors!==null) { 
								    		$class='error';
								    		$errors=' : <div class="message">'.$errors.'</div>';
								    	}else{
								    		$class='formparagraph_inline';
								    		$errors='';
										} ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Nom Complet'.$errors,
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/text',array (
  'js' => 'style="width:200px;" disabled="disabled"',
  'internalname' => 'aduserAdmuserldapForm[aduser.displayname]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'edit',
  'assignTo' => 'Aduser.NomDisplay',
'value'=>(isset($vars['values']['aduser.displayname'])?$vars['values']['aduser.displayname']:''))),'class' => $class)); ?>
<?php /* -------------- aduserAdmuserldapForm[aduser.mail] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapForm[aduser.mail]');
								    	if (is_array($errors)) $errors=implode(', ',$errors);
								    	if ($errors!==null) { 
								    		$class='error';
								    		$errors=' : <div class="message">'.$errors.'</div>';
								    	}else{
								    		$class='formparagraph_inline';
								    		$errors='';
										} ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Email'.$errors,
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/text',array (
  'js' => 'style="width:200px;" disabled="disabled"',
  'internalname' => 'aduserAdmuserldapForm[aduser.mail]',
  'casesensitive' => true,
  'autolike' => true,
  'mode' => 'edit',
  'assignTo' => 'Aduser.Email',
'value'=>(isset($vars['values']['aduser.mail'])?$vars['values']['aduser.mail']:''))),'class' => $class)); ?>
<?php /* -------------- aduserAdmuserldapForm[aduser.telephonenumber] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapForm[aduser.telephonenumber]');
								    	if (is_array($errors)) $errors=implode(', ',$errors);
								    	if ($errors!==null) { 
								    		$class='error';
								    		$errors=' : <div class="message">'.$errors.'</div>';
								    	}else{
								    		$class='formparagraph_inline';
								    		$errors='';
										} ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Telefon'.$errors,
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/text',array (
  'js' => 'style="width:120px;"',
  'internalname' => 'aduserAdmuserldapForm[aduser.telephonenumber]',
  'casesensitive' => true,
  'autolike' => true,
  'mode' => 'edit',
  'assignTo' => 'Aduser.Telefon',
  'class' => 'required',
'value'=>(isset($vars['values']['aduser.telephonenumber'])?$vars['values']['aduser.telephonenumber']:''))),'class' => $class)); ?>

</div>

<div class="row"><?php /* -------------- aduserAdmuserldapForm[adgroup.grups] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Grups Intranet',
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/doublelist',array (
  'multiple' => true,
  'sort' => true,
  'filters' => true,
  'js' => 'style="width:250px;height:250px;"',
  'internalname' => 'aduserAdmuserldapForm[adgroup.grups]',
  'through_class' => 'Adgroupuser',
  'peerMethod' => 
  array (
    'method' => 'doSelectAll',
    'value' => 'samaccountname',
    'text' => 'description',
  ),
  'mode' => 'edit',
  'assignTo' => 'Adgroup',
  'parameters' => 
  array (
    'through_class' => 'Adgroupuser',
    'peerMethod' => 
    array (
      'method' => 'doSelectAll',
      'value' => 'samaccountname',
      'text' => 'description',
    ),
  ),
'value'=>(isset($vars['values']['adgroup.grups'])?$vars['values']['adgroup.grups']:''))))); ?>

</div>


<div class="row"><?php /* -------------- aduserAdmuserldapForm[__formaction__] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/hidden',array (
  'internalname' => 'aduserAdmuserldapForm[__formaction__]',
  'mode' => 'edit',
'value'=>'edit')))); ?>

</div>

<?php /* -------------- END aduserAdmuserldapForm ------------------ */ ?>

<?php $body_aduserAdmuserldapForm=ob_get_clean();

echo Viewer::view('input/form',array (
  'name' => 'aduserAdmuserldapForm',
  'internalid' => 'aduserAdmuserldapForm',
  'internalname' => 'aduserAdmuserldapForm',
  'method' => 'POST',
  'model_order' => NULL,
 'body'=>$body_aduserAdmuserldapForm,
  'action' => Route::url((isset($vars['params']['action']))?$vars['params']['action']:'')));?>

<?php Viewer::setGlobalTemplate(Config::get('settings:views:global_template')); ?>