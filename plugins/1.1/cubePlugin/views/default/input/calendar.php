<script language="JavaScript" src="/js/CalendarPopup.js"></script>
<?php 

	/**
	 * Elgg calendar input
	 * Displays a calendar input field
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
	 * 
	 */
	if (!isset($vars['format'])) $format="%d/%m/%Y";$format=$vars['format'];
    $strippedname=strtr($vars['internalname'],"[].","___");
           
    if (!function_exists('transFormatDate')){
    	
    	function transFormatDate($txt) // de formato columna a formato calendar.js
        {
        	return strtr($txt,array("%d"=>"dd","%m"=>"MM","%Y"=>"yyyy","%H"=>"HH","%M"=>"mm","%S"=>"ss"));
        }
        
        
    	
        echo <<< END
<script type="text/javascript">
		
		function openCalendarInfo(name,format,transformat){
        	
			var cal=eval('cal'+name);
        	var input=document.getElementById(name);
        	$(input).removeAttr('disabled').css('background','fff');
        	cal.select(input,'anchor'+name,transformat);
        	$("#format_"+name).hide(); 
        	return false;	
        }
</script>
END;
        
    }
    
    //$strippedname = sanitise_string($vars['internalname']);
    
    $js = "cal" . $strippedname;
	//if (!isset($vars['format'])) $format="%d/%m/%Y";$format=$vars['format'];
	//echo _r($vars);
   
    $format=isset($vars['format'])?$vars['format']:"%d/%m/%Y";
    $extendReturnFunction=isset($vars['extendReturnFunction'])?($vars['extendReturnFunction'].'($(\'#'.$strippedname.'\'),{y:y,m:m,d:d});'):'';
	$errormessage=isset($vars['errorMessage'])?$vars['errorMessage']:Viewer::_echo('error:incorrect_date');
    
	if (isset($vars['default']) && empty($vars['value']) && $vars['mode']!='filter')
	{
		switch($vars['default']){
			case 'sysdate':
			case 'now': 
						$vars['value'] = utf8_encode(strftime($format,time()));break;
			default:
						$vars['value']=$vars['default'];break;
		}
	}
		
	if (!empty($vars['value'])) 
	{
		if (is_numeric($vars['value'])) $val = utf8_encode(strftime($format,$vars['value'])); //$val = date($format,$vars['value']);
		else $val=$vars['value'];
		$display_format='none';
		//$vars['disabled']=false;
	}
	else {$val='';
		$display_format='inline';		
		//$vars['disabled']=false;
	}
	
    if (!isset($vars['img'])) $img="/img/icon/calendar.png";else $img=$vars['img'];
	
    if (isset($vars['disabled']) && $vars['disabled']) {
    	$display_format='none'; // no click
    	if (is_array($vars['js'])) $vars['js']['disabled']='disabled';
    	else $vars['js'].=" disabled=\"disabled\"";
    	$readonly=true;
    }
    else {
    	$readonly=false;
    }
    
    if (isset($vars['readonly']) && $vars['readonly']) {$vars['js'].=" readonly=\"readonly\"";$readonly=true;}
	if (isset($vars['class']) && !empty($vars['class'])) $class = 'class="'.$vars['class'].'"';else $class="class=\"input-text\"";
	
	if (isset($vars['js']) && $vars['js']!=null)
	{
		$js2='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) {
				$js2.=" {$k}=\"{$v}\" "; 
			}
		}
		else $js2=$vars['js'];
	}else $js2='style="width:80px;"';
	
	
?>
<script language="JavaScript">

	function returnFunctionDefault<?php echo $strippedname; ?>(y,m,d)
	{
			if (y!=undefined){
				var dt = new Date(y,m-1,d,0,0,0);
				var v = formatDate(dt,'<?php echo transFormatDate($format); ?>')
				this.CP_targetInput.value = v;
				var name="<?php echo $strippedname;?>";
				$('#'+this.CP_targetInput.id).focus();
			}
									
			<?php echo $extendReturnFunction;?>
			
	}

	var cal<?php echo $strippedname; ?> = new CalendarPopup();
	
	$(document).ready(function(){
		$('#format_<?php echo $strippedname; ?>').click(function(){
			$('#<?php echo $strippedname; ?>').focus();
		});
		
		<?php $retFunc=isset($vars['returnFunction'])?$vars['returnFunction']:'returnFunctionDefault'.$strippedname; ?>

		$('#<?php echo $strippedname; ?>').focus(function(){
				$(this).removeAttr('disabled').css('background','#fff');
				$('#format_<?php echo $strippedname; ?>').hide();
		});
			
		$('#<?php echo $strippedname; ?>').blur(function(){
			
			if ($(this).val()==''){
				$(this).attr('disabled','disabled').css('background','#eee');
				$('#format_<?php echo $strippedname; ?>').css('display','inline');
			}else{				
				var date=getDateFromFormat(this.value,'<? echo transFormatDate($format); ?>');								
				if (date==0) { 
					alert("<?php echo addslashes($errormessage); ?>"); 
					$(this).attr('disabled','disabled').val('').css('background','#eee');
					$('#format_<?php echo $strippedname; ?>').css('display','inline');
				}
			}
			<?php echo $retFunc; ?>();
		}).blur();

		$('#anchor<?php echo $strippedname; ?>').click(function(){
			openCalendarInfo('<?php echo $strippedname; ?>','<?php echo $format; ?>','<? echo transFormatDate($format); ?>');
		});

		// al escoger una fecha en el calendario se ejecuta la funcion de retorno por defecto.
		// O bien, la returnFunction definida por el usuario, reemplaza a la default 
		// si no hay función de substitución y hay de extensión esta se ejecutará despues de la de por defecto 		
		cal<?php echo $strippedname; ?>.setReturnFunction('<?php echo $retFunc; ?>');
	});
</script>
<span id="calendar_content">
<div id="format_<?php echo $strippedname; ?>" class="calendar-input-format" style="z-index:1;display:<?php echo $display_format; ?>">
	<? echo strtolower(transFormatDate($format)); ?>
</div>
<input <?php echo $class; ?> type="text" <?php echo $js2; ?> name="<?php echo $vars['internalname']; ?>" id="<?php echo $strippedname; ?>" value="<?php echo $val; ?>" />
<?php if (!isset($readonly) || !$readonly): ?>
<a TITLE="<?php echo Viewer::_echo('form:calendar:change_date'); ?>"  NAME="anchor<?php echo $strippedname; ?>" 
   ID="anchor<?php echo $strippedname; ?>" class="buttonAction" ><img src="<?php echo $img; ?>" /></a> 
<? endif; ?>
</span>