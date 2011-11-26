
<div class="row"><?php /* -------------- [submit] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/icon/disk_black.png',
  'js' => 
  array (
    'onclick' => 'document.aduserAdmuserldapForm.submit();',
  ),
  'internalname' => '[submit]',
  'mode' => '',
'value'=>'_echo(submit)')))); ?>
<?php /* -------------- [reset] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/reset',array (
  'img' => '/img/icon/arrow_circle.png',
  'js' => 
  array (
    'onclick' => 'document.aduserAdmuserldapForm.reset();',
  ),
  'internalname' => '[reset]',
  'mode' => '',
'value'=>'_echo(reset)')))); ?>

<?php if (Controller::getInstance()->getRoute('action')!=='new'){ ?>
<?php /* -------------- [new] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/icon/wand.png',
  'type' => 'button',
  'internalname' => '[new]',
  'mode' => '',
'action' => 'new',
'value'=>'_echo(new)')))); ?>
<?php 
} //end Credentials ?>
<?php /* -------------- [list] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/icon/menu.png',
  'class' => 'green_button',
  'internalname' => '[list]',
  'mode' => '',
'action' => 'list',
'value'=>'_echo(list)')))); ?>

<?php if (Controller::getInstance()->getRoute('action')=='edit'){ ?>
<?php /* -------------- [delete] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/confirmbutton',array (
  'img' => '/img/icon/cross_circle_frame.png',
  'class' => 'red_button',
  'confirm' => Viewer::_echo('grid:delete:item'),
  'internalname' => '[delete]',
  'mode' => '',
'action' => 'delete',
'value'=>'_echo(button:delete)')))); ?>
<?php 
} //end Credentials ?>
<?php /* -------------- [print] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/icon/printer.png',
  'class' => 'orange_button',
  'js' => 
  array (
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'print\').toString());',
  ),
  'internalname' => '[print]',
  'mode' => '',
'value'=>'_echo(button:printversion)')))); ?>

</div>

