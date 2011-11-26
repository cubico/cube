<?php 
	
	echo 	Viewer::title('Administració de Plugins').
			Viewer::layout("two_column_left_sidebar", '', Viewer::title(Viewer::_echo('admin_plugins:plugins')) .
														Viewer::view("admin/plugins", array('installed_plugins' => $plugins)));
	
		
?>