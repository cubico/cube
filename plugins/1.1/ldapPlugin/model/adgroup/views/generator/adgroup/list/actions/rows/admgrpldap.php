<?php /* -------------- [edit] ------------------ */ ?>
<?php echo Viewer::view('output/link',
 		array (
  'title' => Viewer::_echo('button:edit'),
  'img' => '<img src="/img/icon/card__pencil.png" />',
  'internalname' => '[edit]','entity'=>$vars['values'],
  'mode' => '',
'action' => 'edit/'.$vars['params']['pks'],
'value'=>(isset($vars['values']['edit'])?$vars['values']['edit']:''))); ?>
<?php /* -------------- [show] ------------------ */ ?>
<?php echo Viewer::view('output/link',
 		array (
  'title' => Viewer::_echo('button:show'),
  'img' => '<img src="/img/icon/card_address.png" />',
  'internalname' => '[show]','entity'=>$vars['values'],
  'mode' => '',
'action' => 'show/'.$vars['params']['pks'],
'value'=>(isset($vars['values']['show'])?$vars['values']['show']:''))); ?>
<?php /* -------------- [deleterow] ------------------ */ ?>
<?php echo Viewer::view('output/confirmlink',
 		array (
  'title' => Viewer::_echo('button:delete'),
  'confirm' => Viewer::_echo('grid:delete:row'),
  'img' => '<img src="/img/icon/cross.png" />',
  'internalname' => '[deleterow]','entity'=>$vars['values'],
  'mode' => '',
'action' => 'delete/'.$vars['params']['pks'],
'value'=>(isset($vars['values']['deleterow'])?$vars['values']['deleterow']:''))); ?>

