<?php /* -------------- [batch_options] ------------------ */ ?>
<table><tr><td></td><td><?php echo Viewer::view('input/pulldown',
 		array (
  'blank_option' => Viewer::_echo('form:list:withselection'),
  'options_values' => 
  array (
    'batchdelete' => Viewer::_echo('button:delete'),
  ),
  'class' => 'batch_option',
  'internalname' => '[batch_options]',
  'mode' => '',
'value'=>(isset($vars['values']['batch_options'])?$vars['values']['batch_options']:''))); ?>
</td></tr></table>
