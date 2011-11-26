<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

	$options_values=array();
	if (isset($vars['assignTo'])){

		$paramModel=$vars['peerMethod'];
		if (preg_match("/[.]/",$vars['assignTo'])){
			$src=explode(".",$vars['query']['select']);
			$class=$src[0]."Peer";
			$method=$src[1];
			$paramModel['select']=$src[1];
		}
		else { // TODO: through_class
			
		}

		$peer=new $class();

		$methods=get_class_methods($peer);
		if (in_array($method,$methods)){
			if (!isset($paramModel['value'])) $data=$peer->{$method}();
			else $data=$peer->{$method}($paramModel['value'],$vars['peerMethod']['text']);
		}

		$vars['store_reader_type']='query';
		$vars['store_reader_params']=$paramModel;
		$vars['store_data_source']=$data;
		unset($vars['peerMethod']); // prevent the query logic of pulldown/checkboxes/... and normalize stores

		echo Viewer('input/store',$vars);
	}
?>
