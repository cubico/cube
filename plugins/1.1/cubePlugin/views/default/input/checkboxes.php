<?php
	$strippedname=strtr($vars['internalname'],"[].,","___");
	$empty_value=isset($vars['empty_option'])?$vars['empty_option']:Query::NULL;
?>
<script type="text/javascript">
	var functionCheckBoxActivate<?php echo $strippedname;?>=function(e,i,v){
		var value=($(e).attr('checked'))?v:'<?php echo $empty_value;?>';
		$('[name="<?php echo $vars['internalname'];?>'+i+'"]').val(value);
	};
</script>
<span>
	<span>
<?php
	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
	
	$multi=isset($vars['multiple'])?$vars['multiple']:true; // por defecto es multiple
	
	if ($multi && !is_array($vars['value'])) 
		$vars['value']=explode(Form::FILTER_SEPARATOR_ARRAY,$vars['value']);
	
	$class = $vars['class'];
	if (!$class) $class = "input-checkboxes";
	if (isset($vars['query'])) // si pasamos query, creamos el options que despues leeremos
	{
		$vars['options']=array();
		
		$src=explode(".",$vars['query']['select']);
		$class=$src[0]."Peer";
		$peer=new $class();
		$data=$peer->doSelect($peer->getQuery($src[1]),false);
		
		foreach($data as $cur){
			
			$option=$cur[$vars['query']['value']];
			$text= $cur[$vars['query']['text']];
			
			$vars['options'][$option]=$text;
		}
	}else if (isset($vars['peerMethod'])){
		$paramModel=$vars['peerMethod'];
		$src=explode(".",$paramModel['method']);
		$class=$src[0]."Peer";
		$peer=new $class();
		$methods=get_class_methods($peer);
			
		if (in_array($src[1],$methods)){ 			
			if (!isset($paramModel['value'])) $data=$peer->{$src[1]}(); 
			else $data=$peer->{$src[1]}($paramModel['value'],$vars['peerMethod']['text']);
		}
		
		foreach($data as $cur){
			
			if (!is_array($paramModel) || !isset($paramModel['value'])){
				$option=reset($cur);
				$text=next($cur);
			}else{
				$option=$cur[$paramModel['value']];
				$text= $cur[$paramModel['text']];
			}
			
			$vars['options'][$option]=$text;
		}
	}
	
	//$vars['align']='vertical';
	if (isset($vars['align']) && $vars['align']=='vertical') {$vertical=true;echo "<br/>";}
	
	if (!isset($vars['value'])) $vars['value']='';
		
	foreach($vars['options'] as $option => $label) {
        //if (!in_array($option,$vars['value'])) {
        if (is_array($vars['value'])) {

			$valarray = $vars['value'];
        	$valarray = array_map('strtolower', $valarray);
        	if (!in_array(strtolower($option),$valarray)) $selected = "";
	      else $selected = "checked = \"checked\"";
	     } else {
				if (strtolower($option) != strtolower($vars['value'])) $selected = "";
				else $selected = "checked = \"checked\"";
        }

		  $labelint = (int) $label;
		  if ("{$label}" == "{$labelint}") $label = $option;
		  $value=parseText(htmlentities($label, ENT_QUOTES, 'UTF-8'));


		  $option=htmlentities($option, ENT_QUOTES, 'UTF-8');
		  $valor=($selected=="")?$empty_value:$option;

		  $disabled = "";
        if ($vars['disabled']) $disabled = ' disabled="yes" '; 
        if ($multi) $index="[{$option}]";else $index="";

		  echo "<input id=\"{$vars['internalname']}{$index}\" type=\"checkbox\" $disabled $js {$selected} class=\"$class\" onclick=\"functionCheckBoxActivate{$strippedname}(this,'{$index}','{$option}');\"/>{$value}";
        echo "<input type=\"hidden\" $disabled name=\"{$vars['internalname']}{$index}\" value=\"".$valor."\" />";
        echo "</span>".(isset($vertical)?"<br/>":"")."<span>";
        
        
    }
?></span></span>