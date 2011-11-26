
<table class="formtable"><tr>
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
<td><?php /* -------------- [openfilters] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/icon/magnifier.png',
  'class' => 'green_button',
  'js' => 
  array (
    'onclick' => 'openFilters();',
  ),
  'internalname' => '[openfilters]',
  'mode' => '',
'value'=>'_echo(button:open:filters)')); ?><br/>
</td></tr></table>
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
<td><?php /* -------------- [pdflist] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/crystal/16x16/mimetypes/pdf-document.png',
  'class' => 'orange_button',
  'js' => 
  array (
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'pdf-l\').toString());',
  ),
  'internalname' => '[pdflist]',
  'mode' => '',
'value'=>'_echo(button:pdfversion-list)')); ?><br/>
</td></tr></table>
</td>
<td><?php /* -------------- [xlslist] ------------------ */ ?>
<table><tr><td></td><td>&#160;<?php echo Viewer::view('input/button',
 		array (
  'img' => '/img/icon/document_excel.png',
  'class' => 'orange_button',
  'js' => 
  array (
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'xls\').toString());',
  ),
  'internalname' => '[xlslist]',
  'mode' => '',
'value'=>'_echo(button:xlsversion-list)')); ?><br/>
</td></tr></table>
</td>
</tr></table>

