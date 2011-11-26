
<?php if (Controller::getInstance()->getRoute('action')!=='new'){ ?>
<?php 
} //end Credentials ?>
<?php $menu_data=array (
  0 => '',
  1 => 
  array (
    'selected' => false,
    'onclick' => 'window.open(window.location.pathname+$.query.set(\'viewer\',\'print\').toString());',
    'label' => Viewer::_echo('button:printversion'),
  ),
);
echo Viewer::submenu($menu_data,'actions');
?>
