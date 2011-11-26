<span>
	<span style="font-size:1.0em;">
<?php	

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
	}
	else if (isset($vars['peerMethod'])){
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
	foreach($vars['options'] as $option => $label) {
        //if (!in_array($option,$vars['value'])) {
        if (!isset($vars['value'])) $img="/img/icon/checkbox_uncheck.png";
                
        else if (is_array($vars['value'])) {
        	$valarray = $vars['value'];
        	$valarray = array_map('strtolower', $valarray);
        	if (!in_array(strtolower($option),$valarray)) {
	            $img="/img/icon/checkbox_uncheck.png";
	        } else {
	        	$img="/img/icon/checkbox.png";
	        }
        } else {
	    	if (strtolower($option) != strtolower($vars['value'])) {
	            $img="/img/icon/checkbox_uncheck.png";
	        } else {
	            $img="/img/icon/checkbox.png";
	        }
        }
        $labelint = (int) $label;
        if ("{$label}" == "{$labelint}") {
        	$label = $option;
        }
        
        $value=parseText(htmlentities($label, ENT_QUOTES, 'UTF-8'));
		
        echo '<img class="'.$class.'" src="'.CUBE_HTTP.$img.'" />'.$value;
        
        echo "</span>".(isset($vertical)?"<br/>":"")."<span>";
        
        
    }
?></span></span>