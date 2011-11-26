<?php
	
	if (!isset($vars['confirm'])) $confirm = htmlentities(Viewer::_echo('question:areyousure'), ENT_QUOTES, 'UTF-8');
	else //$confirm=htmlentities(parseText(addslashes($vars['confirm'])), ENT_QUOTES, 'UTF-8');
	$confirm=parseText(addslashes($vars['confirm']));
		
	if (!isset($vars['entity'])) $vars['entity']=array();
		
if (isset($vars['action'])) $vars['href']=$vars['url']."/".Route::parseValues($vars['action'],$vars['entity']);
if (isset($vars['img'])) {$img=$vars['img'];}else {$img="";}
if (isset($vars['value'])) $value=$vars['value'];else if (!isset($vars['img'])) $value="LINK";
if (isset($vars['class']) && !empty($vars['class'])) $class='class="'.$vars['class'].'"';else $class="";

if (!isset($value)) $value='';
$value=parseText($value); // 
if (isset($vars['title'])) $title=parseText($vars['title']);else $title=$value;

$value=htmlentities($value, ENT_QUOTES, 'UTF-8');

	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	
	}
/*<div style="position:absolute;top:100px;left:100px;"><?php echo _r($vars); ?>	
?>
<a <?php echo $class; ?> onclick="return confirm('<?php echo addslashes($confirm); ?>');" title="<?php echo $title; ?>" href="<?php echo isset($vars['href'])?$vars['href']:'#'; ?>" 
<?php echo isset($vars['target'])?" target=\"".$vars['target']."\"":''; ?> 
<?php echo $js;?>><?php echo $img;?><?php echo $value; ?></a>
*/
	$strippedname=strtr($vars['internalname'].microtime(),"[]., ","_____");
?>

<script type="text/javascript">
var mensaje<?php echo $strippedname;?> = unescape("<?php echo $confirm; ?>");
var confirmReturn<?php echo $strippedname;?> = false;
</script>
<a id="<?php echo $strippedname;?>" <?php echo $class; ?> onclick="return confirmReturn<?php echo $strippedname;?> = confirm(mensaje<?php echo $strippedname;?>);" title="<?php echo $title; ?>"
href="<?php echo isset($vars['href'])?$vars['href']:'#'; ?>" <?php echo $js;?>
<?php echo isset($vars['target'])?" target=\"".$vars['target']."\"":''; ?>><?php echo $img;?><?php echo $value; ?>
</a>