<?php

	/**
	 * Elgg text input
	 * Displays a text input field
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
	 * @uses $vars['disabled'] If true then control is read-only
	 * @uses $vars['class'] Class override
	 */
// Utilizaremos este tipo puldowns asociados que se van recargando mediante ajax,
// a diferencia del arbol debemos guardar todos los niveles en la bd.
//echo _r($vars);	

$input=(isset($vars['input'])?$vars['input']:'pulldown');

if (isset($vars['level'])){
	$level=$vars['level'];
	$vars['level']=$level+1; // siguiente nivel
}else{
	//echo "----".$input;
	$level='';
	$vars['level']=1;
}
	
//$level=(isset($vars['level'])?$vars['level']:1);

$scriptPath=isset($vars['scriptPath'])?$vars['scriptPath']:'/';
$scriptPath=Route::url($scriptPath);

$query=(isset($vars['query'])?$vars['query']:array());

if (!isset($vars['value'])) $vars['value']=array();
if (!is_array($vars['value'])) $vars['value']=array($vars['value']);

$strippedname=strtr($vars['internalname'],"[].","___"); 


$bool=preg_match("/(.*)\[([a-zA-Z0-9\._]*)([0-9]*)\]/",$vars['internalname'],$args);

$vars['internalname']=$args[1]."[".$args[2].$level."]";
$hidden=$args[1]."[".$args[2]."]";
$padre=$args[1]."[".$args[2].($level-1)."]";

$call='';
$call2='';

//echo "categoria:"._r($vars['value'][0])."<BR>";
switch($input){
	case 'pulldown': 		
			$call = "$(\"[name='".$vars['internalname']."']\").change";
			$call2="data['value']=$(\"[name='".$vars['internalname']."']\").val();";
		break;	
	case 'checkboxes': 
	case 'radio':
		/// machacamos el atributo value de la tabla javascript data con el boton seleccionado
		$call2="data['value']=$(\"#elemento".$level." input:checked\").val();";	
		// si a un radio le llega más de un valor, te quedas con el primero!!!
		$vars['value']=$vars['value'][0];
		$call = "$(\"#elemento".$level." input\").click";//onclick="if(this.checked) 
		break;			
	default:
	    
	    $call = "$(\"#".$vars['js']['id']."\").click";
	    $call2="data['value']='#####';";
	    break;	
}
?>
<div style="border: 0px solid #555;width:<?php echo isset($vars['content_width'])?$vars['content_width']:'420px'; ?>;">
<div style="float:left">
	<script type="text/javascript">
		$(document).ready(function(){
				var data=<?php echo json_encode($vars); ?>;	
				<?php echo $call;?>(function(){	
						<?php echo $call2;?>
						$.ajax({
					  		url: '<?php echo $scriptPath;?>',
					  		type: "POST",
					  		data: data,
					  		cache: false,
					  		timeout: 60000,
					  		success: function(datax){				  
					  			var valor;
					  			if (data['value']!='') valor=data['value'];				  			
					  			else if ($("[name='<?php echo $padre; ?>']").length) 
					  				valor=$("[name='<?php echo $padre; ?>']").val();
					  			else valor='';
					  			
					  			$("[name='<?php echo $hidden;?>']").val(valor);
					  			$('#elemento<?php echo $vars['level']; ?>').html(datax);
	            			}
					  	});
				})
				.change();

			});
			
	</script> 
	<?php if ($level==1){ // prueba: si el nodo no es del nivel lo buscamos en el nivel siguiente ?>
	<input type="hidden" name="<?php echo $hidden;?>" value="<?php $vars['value']; ?>" />
	<?php } ?>
	<?php //echo $input; echo _r($vars); 
	if (is_array($vars['value']) && isset($vars['value'][0])) $vars['value']=$vars['value'][0];
	echo Viewer::view("input/{$input}",$vars); ?>
</div>
</div>
<div id="elemento<?php echo $vars['level']; ?>"><img src="/img/ajax-loader2.gif" /></div>
		  