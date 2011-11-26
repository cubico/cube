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
	$strippedname=strtr($vars['internalname'],"[].","___");
    $format=isset($vars['format'])?$vars['format']:"%d/%m/%Y";
    
	if (!isset($vars['img'])) $img="/img/icon/calendar.png";else $img=$vars['img'];
	
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
		
  
    if (!function_exists('transFormatDate')){
    	function transFormatDate($txt) // de formato columna a formato calendar.js
        {
        	return strtr($txt,array("%d"=>"dd","%m"=>"MM","%Y"=>"yyyy","%H"=>"HH","%M"=>"mm","%S"=>"ss"));
        }
    }

    if (!defined('CalendarViewJqueryV2')){
    	define('CalendarViewJqueryV2',true);
        echo <<< END
<script language="JavaScript" src="/js/CalendarPopup.js"></script>
<script type="text/javascript">
	$(document).ready(function(){	
		jQuery.fn.enableCalendar=function(){
		  	var args = arguments[0] || {}; // It's your object of arguments
			var input = $(this[0]) // It's your element
			input.prev().css('z-index',-1);
    		input.css('z-index',1);
    		input.attr('disabled',false);
    		if (args.date) input.val(args.date);
			input.focus();
		};
		
		jQuery.fn.disableCalendar=function(){
			var args = arguments[0] || {}; // It's your object of arguments  	
			var input = $(this[0]) // It's your element
			input.prev().css('z-index',1);
    		input.css('z-index',-1);
    		input.attr('disabled',true);
    		if (args.date) input.val(args.date);
			else input.val('');
		};
	});
</script>
END;
    }
	
   	$transformdate=transFormatDate($format);	
    $tradate=strtolower($transformdate);
    $readonly=(isset($vars['disabled']) && $vars['disabled'])?true:false;
    
    if (isset($vars['readonly']) && $vars['readonly']) {$clickevent='';$readonly=true;}
    else $clickevent="$('#format_{$strippedname}').click(function () { $(this).next().enableCalendar(); });";
        
        
    if (!empty($vars['value'])) 
	{
		if (is_numeric($vars['value'])) $val = utf8_encode(strftime($format,$vars['value'])); //$val = date($format,$vars['value']);
		else $val=$vars['value'];
	}
	else {$val='';}
	
	if ($val!='' && !$readonly){
		$enableCalendar="$(\"[name='{$vars['internalname']}']\").enableCalendar({date:'{$val}'});";
	}
	else
		$enableCalendar="$(\"[name='{$vars['internalname']}']\").disableCalendar({date:'{$val}'});";
	
	echo <<<EOF
<script type="text/javascript">    
		function funccal{$strippedname}(year,month,day){
			var str='{$transformdate}';
			str=str.replace('dd',str_pad(day, 2, '0', 'STR_PAD_LEFT'))
			str=str.replace('MM',str_pad(month, 2, '0', 'STR_PAD_LEFT'));
			str=str.replace('yyyy',year);
			str=str.replace('HH','00')
			str=str.replace('mm','00')
			str=str.replace('ss','00')
			$("[name='{$vars['internalname']}']").enableCalendar({date:str});
		}

    	$(document).ready(function(){
    		var cal{$strippedname}= new CalendarPopup();
        	cal{$strippedname}.setReturnFunction('funccal{$strippedname}');
        	
    		$('#anchor{$strippedname}').click(function(){
    			var input=$(this).prev();
    			input.attr('disabled',false);
    			cal{$strippedname}.select(input.get(0),'anchor{$strippedname}','{$transformdate}');
    		});
    		
    		$("[name='{$vars['internalname']}']").blur(function(event){
    			var format='{$tradate}';
    			this.value=this.value.substr(0,format.length);
				
				if (getDateFromFormat(this.value,'$transformdate')==0){ 
					if (this.value!='') alert('El format ha de ser {$tradate}'); 
					$(this).disableCalendar();
				}
			});
    		
    		$("[name='{$vars['internalname']}']").keyup(function(event){
				//String.fromCharCode(event.keyCode)
    			var format='$tradate';
				var pos=this.value.length;
				var separador=format.substr(pos,1);
				if (format.length>pos && separador.match(/[dmyhs]/)==null) this.value+=separador;
				else this.value=this.value.substr(0,format.length);
			});
    		
			{$clickevent}
			{$enableCalendar}
    	});
</script>
EOF;
    
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
<span id="calendar_content<?php echo $strippedname; ?>">
<div id="format_<?php echo $strippedname; ?>" class="calendar-input-format" style="z-index:1;">
	<? echo strtolower(transFormatDate($format)); ?>
</div>
<input <?php echo $class; ?> <?php echo $js2; ?> type="text" id="<?php echo $strippedname; ?>" name="<?php echo $vars['internalname']; ?>"  />
<?php if (!$readonly): ?>
<a TITLE="<?php echo Viewer::_echo('form:calendar:change_date'); ?>"  NAME="anchor<?php echo $strippedname; ?>" 
   ID="anchor<?php echo $strippedname; ?>" class="buttonAction" ><img src="<?php echo $img; ?>" /></a>
<? endif; ?> 
</span>