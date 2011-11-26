<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
	$vars['store_reader_type']='array';
	$vars['store_reader_params']=array();
	$vars['store_data_source']=$vars['data'];
	unset($vars['data']); // prevent the query logic of pulldown/checkboxes/... and normalize stores

	echo Viewer('input/store',$vars);
?>
