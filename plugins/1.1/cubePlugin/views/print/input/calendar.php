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

    
    if (!function_exists('transFormatDate')){
        echo <<< END
<script language="JavaScript" src="/js/CalendarPopup.js"></script>
<script type="text/javascript">
		
		function isDate(obj,format,msg) {
			if (obj.value!=''){
				var date=getDateFromFormat(obj.value,format);
				if (date==0) { alert(msg); obj.value='';return false;}
			}
			return true;
		}

		function openCalendarInfo(name,format,transformat){
        	
		var cal=eval('cal'+name);
        	var input=document.getElementById(name);
        	$(input).attr('disabled','');
        	cal.select(input,'anchor'+name,transformat);
        	$("#format_"+name).hide();        	
        	return false;
        }
</script>
END;
        function transFormatDate($txt) // de formato columna a formato calendar.js
        {
        	return strtr($txt,array("%d"=>"dd","%m"=>"MM","%Y"=>"yyyy","%H"=>"HH","%M"=>"mm","%S"=>"ss"));
        }
        
        
    }
    
    //$strippedname = sanitise_string($vars['internalname']);
    $strippedname=strtr($vars['internalname'],"[].","___");
    $js = "cal" . $strippedname;
	//if (!isset($vars['format'])) $format="%d/%m/%Y";$format=$vars['format'];
	$format=isset($vars['format'])?$vars['format']:"%d/%m/%Y";
	
	if (isset($vars['default']) && $vars['mode']=='new')  
	{
		switch($vars['default']){
			case 'sysdate':
			case 'now': 
						$vars['value'] = utf8_encode(strftime($format));break;
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
	
    if (isset($vars['disabled']) && $vars['disabled']) {$vars['js'].=" disabled=\"disabled\"";$readonly=true;}
    else $readonly=false;
    
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
	
	if ($display_format=='inline'){
    	$js2.=" disabled=\"disabled\"";
	}
	
?>
<span id="calendar_content">
<span  <?php echo $class; ?> <?php echo $js2; ?> name="<?php echo $vars['internalname']; ?>"><?php echo htmlentities($val, ENT_QUOTES, 'UTF-8'); ?></span>
</span>