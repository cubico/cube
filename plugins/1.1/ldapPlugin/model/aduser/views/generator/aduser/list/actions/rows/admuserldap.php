<?php /* -------------- [edit] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('output/link',array (
  'title' => Viewer::_echo('button:edit'),
  'img' => '<img src="/img/icon/card__pencil.png" />',
  'internalname' => '[edit]','entity'=>$vars['values'],
  'mode' => '',
'action' => 'edit/'.$vars['params']['pks'],
'value'=>(isset($vars['values']['edit'])?$vars['values']['edit']:''))))); ?>
<?php /* -------------- [show] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('output/link',array (
  'title' => Viewer::_echo('button:show'),
  'img' => '<img src="/img/icon/card_address.png" />',
  'internalname' => '[show]','entity'=>$vars['values'],
  'mode' => '',
'action' => 'show/'.$vars['params']['pks'],
'value'=>(isset($vars['values']['show'])?$vars['values']['show']:''))))); ?>
<?php /* -------------- [activerow] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('output/confirmlink',array (
  'title' => 'Activar usuari',
  'confirm' => Viewer::_echo('grid:delete:row'),
  'img' => '<img src="/img/icon/key__plus.png" />',
  'internalname' => '[activerow]','entity'=>$vars['values'],
  'mode' => '',
'action' => 'batchactivate/'.$vars['params']['pks'],
'value'=>(isset($vars['values']['activerow'])?$vars['values']['activerow']:''))))); ?>
<?php /* -------------- [deleterow] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('output/confirmlink',array (
  'title' => 'Desactivar usuari',
  'confirm' => Viewer::_echo('grid:delete:row'),
  'img' => '<img src="/img/icon/key__minus.png" />',
  'internalname' => '[deleterow]','entity'=>$vars['values'],
  'mode' => '',
'action' => 'batchdelete/'.$vars['params']['pks'],
'value'=>(isset($vars['values']['deleterow'])?$vars['values']['deleterow']:''))))); ?>

