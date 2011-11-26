<?php /* -------------- [edit] ------------------ */ ?>
<?php echo Viewer::view('output/link',
 		array (
  'title' => Viewer::_echo('button:edit'),
  'action' => 'edit/'.$vars['params']['pks'],
  'img' => '<img src="/img/icon/card__pencil.png" />',
  'internalname' => '[edit]','entity'=>$vars['params']['entity'],
  'mode' => '',
  'value'=>(isset($vars['values']['edit']))?$vars['values']['edit']:'')); ?>
<?php /* -------------- [show] ------------------ */ ?>
<?php echo Viewer::view('output/link',
 		array (
  'title' => Viewer::_echo('button:show'),
  'action' => 'show/'.$vars['params']['pks'],
  'img' => '<img src="/img/icon/card_address.png" />',
  'internalname' => '[show]','entity'=>$vars['params']['entity'],
  'mode' => '',
  'value'=>(isset($vars['values']['show']))?$vars['values']['show']:'')); 
 		
	if (isset($vars['params']['entity']['useraccountcontrol'])){
		
		$control=Ldap::get_account_control($vars['params']['entity']['useraccountcontrol']);
		if (in_array('ACCOUNTDISABLE',$control)){
		
			echo Viewer::view('output/confirmlink',
			 		array (
			  'title' => 'Activar usuari: '.implode(", ",$control),
			  'action' => 'batchactivate/'.$vars['params']['pks'],
			  'confirm' => Viewer::_echo('grid:delete:row'),
			  'img' => '<img src="/img/icon/key__plus.png" />',
			  'internalname' => '[activerow]','entity'=>$vars['params']['entity'],
			  'mode' => '',
			  'value'=>(isset($vars['values']['activerow']))?$vars['values']['activerow']:'')); 
		}else{
				 echo Viewer::view('output/confirmlink',
			 		array (
			  'title' => 'Desactivar usuari: '.implode(", ",$control),
			  'action' => 'batchdelete/'.$vars['params']['pks'],
			  'confirm' => Viewer::_echo('grid:delete:row'),
			  'img' => '<img src="/img/icon/key__minus.png" />',
			  'internalname' => '[deleterow]','entity'=>$vars['params']['entity'],
			  'mode' => '',
			  'value'=>(isset($vars['values']['deleterow']))?$vars['values']['deleterow']:'')); 
		}
	}		 		?>
