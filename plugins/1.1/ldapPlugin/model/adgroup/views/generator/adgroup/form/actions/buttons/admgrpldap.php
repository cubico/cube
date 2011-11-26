
<table class="formtable"><tr>
<td><?php /* -------------- [submit] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/icon/disk_black.png',
  'js' => 
  array (
    'onclick' => 'document.adgroupAdmgrpldapForm.submit();',
  ),
  'internalname' => '[submit]',
  'mode' => '',
'value'=>'_echo(submit)')); ?><br/>
</td></tr></table>
</td>
<td><?php /* -------------- [reset] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/reset',
 		array (
  'img' => '/img/icon/arrow_circle.png',
  'js' => 
  array (
    'onclick' => 'document.adgroupAdmgrpldapForm.reset();',
  ),
  'internalname' => '[reset]',
  'mode' => '',
'value'=>'_echo(reset)')); ?><br/>
</td></tr></table>
</td>
<td>
<?php if (Controller::getInstance()->getRoute('action')!=='new'){ ?>
<?php /* -------------- [new] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/icon/wand.png',
  'type' => 'button',
  'internalname' => '[new]',
  'mode' => '',
'action' => 'new',
'value'=>'_echo(new)')); ?><br/>
</td></tr></table><?php 
} //end Credentials ?>

</td>
<td><?php /* -------------- [list] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/icon/menu.png',
  'class' => 'green_button',
  'internalname' => '[list]',
  'mode' => '',
'action' => 'list',
'value'=>'_echo(list)')); ?><br/>
</td></tr></table>
</td>
<td>
<?php if (Controller::getInstance()->getRoute('action')=='edit'){ ?>
<?php /* -------------- [delete] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/confirmbutton',
 		array (
  'img' => '/img/icon/cross_circle_frame.png',
  'class' => 'red_button',
  'confirm' => Viewer::_echo('grid:delete:item'),
  'internalname' => '[delete]',
  'mode' => '',
'action' => 'delete',
'value'=>'_echo(button:delete)')); ?><br/>
</td></tr></table><?php 
} //end Credentials ?>

</td>
<td><?php /* -------------- [print] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/icon/printer.png',
  'class' => 'orange_button',
  'js' => 
  array (
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'print\').toString());',
  ),
  'internalname' => '[print]',
  'mode' => '',
'value'=>'_echo(button:printversion)')); ?><br/>
</td></tr></table>
</td>
<td><?php /* -------------- [pdfform] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/crystal/16x16/mimetypes/pdf-document.png',
  'class' => 'orange_button',
  'js' => 
  array (
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'pdf\').toString());',
  ),
  'internalname' => '[pdfform]',
  'mode' => '',
'value'=>'_echo(button:pdfversion-form)')); ?><br/>
</td></tr></table>
</td>
</tr></table>

