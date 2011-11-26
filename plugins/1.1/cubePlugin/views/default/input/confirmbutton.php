<?php
	/**
	 * Create a input button
	 * Use this view for forms rather than creating a submit/reset button tag in the wild as it provides
	 * extra security which help prevent CSRF attacks.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['type'] Submit or reset, defaults to submit.
	 * @uses $vars['src'] Src of an image
	 * 
	 */

	global $CONFIG;
	
	if (isset($vars['class'])) $class = $vars['class'];
	
	if (!$class) $class = "submit_button";
	
	if (isset($vars['type'])) { $type = strtolower($vars['type']); } else { $type = 'button'; }
	/*
	switch ($type)
	{
		case 'button' : $type='button'; break;
		case 'reset' : 	$type='reset'; break;
		case 'submit':  $type = 'submit';break;
		default:
	}
	*/
	
	$value=parseText(htmlentities($vars['value'], ENT_QUOTES, 'UTF-8'));
	if (isset($vars['title'])) $title=parseText($vars['title']);else $title=$value;
	
	if (isset($vars['internalname'])) $name = $vars['internalname'];
	if (isset($vars['src'])) $src = "src=\"{$vars['src']}\"";
	else $src="";
	//if (strpos($src,$CONFIG->wwwroot)===false) $src = ""; // blank src if trying to access an offsite image.
	
	if (isset($vars['confirm'])){
		$confirm = trim($vars['confirm']);
		if (empty($confirm))
			$confirm = Viewer::_echo('question:areyousure');
		else{
			$confirm=parseText($confirm);
		}
		$onclick="if (confirm('".addslashes($confirm)."')){";
	}else $onclick="";	
	
	if (!isset($vars['entity'])) $vars['entity']=array();
	
	if (isset($vars['action']) || isset($vars['onclick'])){ 
		
		if (isset($vars['action'])) $onclick.="document.location='".$vars['url']."/".Route::parseValues($vars['action'],$vars['entity'])."'";
		else $onclick.=$vars['onclick'];
			
		$action=" onClick=\"".$onclick;
		if (isset($vars['confirm'])) $action.=";}\"";else $action.=";\"";
	}
	
	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	
	}
	
	if (isset($vars['img'])) $value='<img src="'.$vars['img'].'" />&#160;'.$value;
	
?>
<button title="<?php echo $title;?>" <?php echo $action;?> name="<?php echo $vars['internalname']; ?>" type="<?php echo $type; ?>" class="<?php echo $class; ?>" <?php echo $js; ?> <?php echo $src; ?> ><?php echo $value; ?></button>