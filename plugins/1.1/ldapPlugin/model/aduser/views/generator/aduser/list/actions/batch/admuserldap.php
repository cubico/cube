<?php /* -------------- [batch_options] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/pulldown',array (
  'blank_option' => Viewer::_echo('form:list:withselection'),
  'options_values' => 
  array (
    'batchdelete' => 'Desactivar usuari',
    'batchactivate' => 'Activar usuari',
  ),
  'class' => 'batch_option',
  'internalname' => '[batch_options]',
  'mode' => '',
'value'=>(isset($vars['values']['batch_options'])?$vars['values']['batch_options']:''))))); ?>

