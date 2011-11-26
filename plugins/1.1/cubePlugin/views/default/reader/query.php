<?php
/* 
 * Store with query
 *
 * @param mixed assignTo Model+Column or Model that assign value on database
 * @param string/array query Parameters of query database. select or [select, text, value]
 *
 */
	
	// sacar los values, por ejemplo, con la trougth_class --- implementar reader!!!!!!!
	$options_values=array();
	
	$paramModel=$vars['query'];
	if (!is_array($paramModel) || !isset($paramModel['select'])){
		$paramModel=array('select'=>$paramModel);
	}
	
	$src=explode(".",$paramModel['select']);
	$class=$src[0]."Peer";
	$method=isset($src[1])?$src[1]:'doSelectAll';
	//if (preg_match("/[.]/",$vars['assignTo'])){}else { /* through_class*/	$class=$vars['assignTo']."Peer";	}
	$peer=new $class();
	$data=$peer->doSelect($peer->getQuery($method),false);
		
	$vars['store_reader_type']='query';
	$vars['store_reader_params']=$paramModel;
	$vars['store_data_source']=$data;
	unset($vars['query']); // prevent the query logic of pulldown/checkboxes/... and normalize stores
		
	echo Viewer::view('input/store',$vars);
?>
