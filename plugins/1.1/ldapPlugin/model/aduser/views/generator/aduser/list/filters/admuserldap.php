<?php /* -------------- aduserAdmuserldapFormFilter ------------------ */ ?>
<?php ob_start(); ?>
<?php echo Session::getInstance()->getFlash('aduserAdmuserldapFormFilter_info'); ?>

<?php echo Session::getInstance()->getFlash('aduserAdmuserldapFormFilter'); ?>
<?php /* -------------- INIT aduserAdmuserldapFormFilter ------------------ */ ?>

<div class="row"><?php /* -------------- aduserAdmuserldapFormFilter[__filterform__samaccountname_f] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapFormFilter[__filterform__samaccountname_f]');
								    	if (is_array($errors)) $errors=implode(', ',$errors);
								    	if ($errors!==null) { 
								    		$class='error';
								    		$errors=' : <div class="message">'.$errors.'</div>';
								    	}else{
								    		$class='formparagraph_inline';
								    		$errors='';
										} ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Nif'.$errors,
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/text',array (
  'js' => 'style="width:72px;"',
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__samaccountname_f]',
  'casesensitive' => true,
  'autolike' => 'right',
  'mode' => 'filter',
  'assignTo' => 'Aduser.Nif',
'value'=>(isset($vars['values']['__filterform__samaccountname_f'])?$vars['values']['__filterform__samaccountname_f']:''))),'class' => $class)); ?>
<?php /* -------------- aduserAdmuserldapFormFilter[__filterform__cn_f] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Nom Complet',
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/text',array (
  'js' => 'style="width:200px;"',
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__cn_f]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'filter',
  'assignTo' => 'Aduser.NomComplet',
'value'=>(isset($vars['values']['__filterform__cn_f'])?$vars['values']['__filterform__cn_f']:''))))); ?>
<?php /* -------------- aduserAdmuserldapFormFilter[__filterform__givenname] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapFormFilter[__filterform__givenname]');
								    	if (is_array($errors)) $errors=implode(', ',$errors);
								    	if ($errors!==null) { 
								    		$class='error';
								    		$errors=' : <div class="message">'.$errors.'</div>';
								    	}else{
								    		$class='formparagraph_inline';
								    		$errors='';
										} ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Nom'.$errors,
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/text',array (
  'js' => 'style="width:200px;"',
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__givenname]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'filter',
  'assignTo' => 'Aduser.Nom',
'value'=>(isset($vars['values']['__filterform__givenname'])?$vars['values']['__filterform__givenname']:''))),'class' => $class)); ?>
<?php /* -------------- aduserAdmuserldapFormFilter[__filterform__sn] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapFormFilter[__filterform__sn]');
								    	if (is_array($errors)) $errors=implode(', ',$errors);
								    	if ($errors!==null) { 
								    		$class='error';
								    		$errors=' : <div class="message">'.$errors.'</div>';
								    	}else{
								    		$class='formparagraph_inline';
								    		$errors='';
										} ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Cognoms'.$errors,
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/text',array (
  'js' => 'style="width:200px;"',
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__sn]',
  'casesensitive' => false,
  'autolike' => true,
  'mode' => 'filter',
  'assignTo' => 'Aduser.Cognoms',
'value'=>(isset($vars['values']['__filterform__sn'])?$vars['values']['__filterform__sn']:''))),'class' => $class)); ?>

</div>

<div class="row"><?php /* -------------- aduserAdmuserldapFormFilter[__filterform__mail] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapFormFilter[__filterform__mail]');
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
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__mail]',
  'casesensitive' => true,
  'autolike' => true,
  'mode' => 'filter',
  'assignTo' => 'Aduser.Email',
'value'=>(isset($vars['values']['__filterform__mail'])?$vars['values']['__filterform__mail']:''))),'class' => $class)); ?>
<?php /* -------------- aduserAdmuserldapFormFilter[__filterform__telephonenumber] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapFormFilter[__filterform__telephonenumber]');
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
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__telephonenumber]',
  'casesensitive' => true,
  'autolike' => true,
  'mode' => 'filter',
  'assignTo' => 'Aduser.Telefon',
'value'=>(isset($vars['values']['__filterform__telephonenumber'])?$vars['values']['__filterform__telephonenumber']:''))),'class' => $class)); ?>
<?php /* -------------- aduserAdmuserldapFormFilter[__filterform__useraccountcontrol_f] ------------------ */ ?>
<?php 										$errors=Session::getInstance()->getFlash('aduserAdmuserldapFormFilter[__filterform__useraccountcontrol_f]');
								    	if (is_array($errors)) $errors=implode(', ',$errors);
								    	if ($errors!==null) { 
								    		$class='error';
								    		$errors=' : <div class="message">'.$errors.'</div>';
								    	}else{
								    		$class='formparagraph_inline';
								    		$errors='';
										} ?>
<?php echo Viewer::view('canvas/column',array('label'=>array (
  'title' => 'Control'.$errors,
  'position' => 'left',
  'align' => 'middle',
),	
							 					'render'=>Viewer::view('input/pulldown',array (
  'options_grid' => false,
  'blank_option' => 'Selecciona...',
  'options_values' => 
  array (
    'a' => 'Activat',
    'b' => 'Desactivat',
  ),
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__useraccountcontrol_f]',
  'mode' => 'filter',
'value'=>(isset($vars['values']['__filterform__useraccountcontrol_f'])?$vars['values']['__filterform__useraccountcontrol_f']:''))),'class' => $class)); ?>

</div>

<div class="row"><?php /* -------------- aduserAdmuserldapFormFilter[__filterform__gofilter] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/icon/funnel.png',
  'js' => 
  array (
    'onclick' => '$(this).parents(\'form\').submit();',
  ),
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__gofilter]',
  'mode' => 'filter',
'value'=>'_echo(gofilter)')))); ?>
<?php /* -------------- aduserAdmuserldapFormFilter[__filterform__clearfilters] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/icon/arrow_circle_135.png',
  'type' => 'button',
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform__clearfilters]',
  'mode' => 'filter',
'action' => 'clearfilters',
'value'=>'_echo(button:clear:filters)')))); ?>

</div>


<div class="row"><?php /* -------------- aduserAdmuserldapFormFilter[__filterform____formaction__] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/hidden',array (
  'internalname' => 'aduserAdmuserldapFormFilter[__filterform____formaction__]',
  'mode' => 'filter',
'value'=>'filters')))); ?>

</div>

<?php /* -------------- END aduserAdmuserldapFormFilter ------------------ */ ?>

<?php $body_aduserAdmuserldapFormFilter=ob_get_clean();

echo Viewer::view('input/form',array (
  'name' => 'aduserAdmuserldapFormFilter',
  'internalid' => 'aduserAdmuserldapFormFilter',
  'internalname' => 'aduserAdmuserldapFormFilter',
  'method' => 'POST',
  'model_order' => NULL,
 'body'=>$body_aduserAdmuserldapFormFilter,
  'action' => Route::url((isset($vars['params']['action']))?$vars['params']['action']:'./filter')));?>
