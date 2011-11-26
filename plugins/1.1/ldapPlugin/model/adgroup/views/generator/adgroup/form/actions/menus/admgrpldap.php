
<?php if (Controller::getInstance()->getRoute('action')!=='new'){ ?>
<?php 
} //end Credentials ?>
<?php $menu_data=array (
  0 => '',
  1 => 
  array (
    'selected' => false,
    'action' => 'list',
    'label' => Viewer::_echo('list'),
  ),
  2 => 
  array (
    'selected' => false,
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'print\').toString());',
    'label' => Viewer::_echo('button:printversion'),
  ),
  3 => 
  array (
    'selected' => false,
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'pdf\').toString());',
    'label' => Viewer::_echo('button:pdfversion-form'),
  ),
);
echo Viewer::submenu($menu_data,'actions');
?><?php $menu_data=array (
  0 => 
  array (
    'selected' => false,
    'onclick' => 'document.adgroupAdmgrpldapForm.submit();',
    'label' => Viewer::_echo('submit'),
  ),
  1 => 
  array (
    'selected' => false,
    'onclick' => 'document.adgroupAdmgrpldapForm.reset();',
    'label' => Viewer::_echo('reset'),
  ),
);
echo Viewer::submenu($menu_data,'actions');
?>
<?php if (Controller::getInstance()->getRoute('action')=='edit'){ ?>
<?php 
} //end Credentials ?>
<?php $menu_data=array (
  0 => '',
);
echo Viewer::submenu($menu_data,'actions');
?>
