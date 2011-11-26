
<div class="row">
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
<?php /* -------------- [openfilters] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/icon/magnifier.png',
  'class' => 'green_button',
  'js' => 
  array (
    'onclick' => 'openFilters();',
  ),
  'internalname' => '[openfilters]',
  'mode' => '',
'value'=>'_echo(button:open:filters)')))); ?>
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
<?php /* -------------- [pdflist] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/crystal/16x16/mimetypes/pdf-document.png',
  'class' => 'orange_button',
  'js' => 
  array (
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'pdf-l\').toString());',
  ),
  'internalname' => '[pdflist]',
  'mode' => '',
'value'=>'_echo(button:pdfversion-list)')))); ?>
<?php /* -------------- [xlslist] ------------------ */ ?>
<?php echo Viewer::view('canvas/column',array(	
							 					'render'=>Viewer::view('input/button',array (
  'img' => '/img/icon/document_excel.png',
  'class' => 'orange_button',
  'js' => 
  array (
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'xls\').toString());',
  ),
  'internalname' => '[xlslist]',
  'mode' => '',
'value'=>'_echo(button:xlsversion-list)')))); ?>

</div>

