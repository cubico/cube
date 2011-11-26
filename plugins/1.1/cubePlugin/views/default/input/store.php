<?php
/** 
 * Store data component
 *
 * @param string store_reader_type Type of previous reader
 * @param mixed store_reader_params Parameters of previous reader
 * @param array store_data_source Data values from previous reader
 * @param string render Render component
 */
 $paramModel=$vars['store_reader_params'];
 
 $data=isset($vars['store_data_source'])?$vars['store_data_source']:array();
 $render=isset($vars['render'])?$vars['render']:'input/pulldown';

  $result=array();

 if (isset($paramModel['blank_option'])){
	$result['']=$paramModel['blank_option'];
	unset($paramModel['blank_option']); // normalize store
 }

 if (isset($paramModel['empty_option'])){
	$result[Query::NULL]=$paramModel['empty_option'];
	unset($paramModel['empty_option']); // normalize store
 }
 
 foreach($data as $index=>$cur){
	if (!is_array($paramModel) || !isset($paramModel['value'])){
		$option=reset($cur);
		$text=next($cur);
	}else{
		$option=$cur[$paramModel['value']];
		$text= $cur[$paramModel['text']];
	}
	$result[$option]=$text; //htmlentities($text, ENT_QUOTES, 'UTF-8');
}
/// render view with params (create option_values for compatibility)
$vars['options_values']=$result;
unset($vars['store_data_source']);
unset($vars['store_reader_params']);
//echo _r($vars);
echo Viewer::view($render,$vars);
?>
