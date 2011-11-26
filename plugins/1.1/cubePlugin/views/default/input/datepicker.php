<link rel="stylesheet" type="text/css" href="/calendar/anytime.css" />
<?php
$strippedname=strtr($vars['internalname'],"[].","___"); 
if (!isset($vars['format'])) $format="%d/%m/%Y";$format=$vars['format'];


if (!function_exists('transFormatDateTime')){
	function transFormatDateTime($txt) // de formato columna a formato calendar.js
    {
        	return strtr($txt,array("%H"=>"%H","%M"=>"%i","%S"=>"%s"));
    }
    
	function transFormatDateTimeView($txt) // de formato columna a formato calendar.js
    {
    	return strtr($txt,array("%d"=>"dd","%m"=>"MM","%Y"=>"yyyy","%H"=>"HH","%M"=>"mm","%S"=>"ss"));
    }
}


//echo transFormatDate($format);die();
//echo _r($vars);
echo Viewer::addJavascript('/calendar/anytime.js');
$val='';
 $js = "cal" . $strippedname;
	if (!isset($vars['format'])) $format="%d/%m/%Y";$format=$vars['format'];
	
	if (isset($vars['default']) && $vars['mode']=='new')  
	{
		switch($vars['default']){
			case 'sysdate': //$val = time();break;
							$val = utf8_encode(strftime($format,time()));break;
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
	else {		
		//$display_format='inline';		
		//$vars['disabled']=false;
		$display_format='none';
	}
	
    if (!isset($vars['img'])) $img="/img/icon/calendar.png";else $img=$vars['img'];
?>	
<style type="text/css">
  #<?php echo $strippedname; ?> { 
  	background-image:url("<?php echo $img; ?>");
  	background-repeat: no-repeat;
	background-position: right center;  
  }
</style>
<?php
    if (isset($vars['disabled']) && $vars['disabled']) {
    	$display_format='none'; // no click
    	$vars['js'].=" disabled=\"disabled\"";$readonly=true;
    }
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

    if (isset($vars['value']) && !empty($vars['value'])) {
        $value=$vars['value'];
    if (!is_numeric($value)){
            $value=dbDriver::toTimestamp($value,$vars['format']);
        }
    }else if (isset($vars['default'])){
    	if ($vars['default']=='sysdate') $value=time();
    }else $value=null;
    
	
    $thisyear=strftime("%Y");
    if (!isset($vars['minyear']))  $minyear=intval($thisyear)-5;
    else if (preg_match("/([\+\-])([0-9]*)/",$vars['minyear'],$args)){
            eval("\$minyear=\$thisyear".$args[1].$args[2].";");
         }else $minyear=$vars['minyear'];
               
    if (!isset($vars['maxyear']))  $maxyear=intval($thisyear);
    else if (preg_match("/([\+\-])([0-9]*)/",$vars['maxyear'],$args)){
            eval("\$maxyear=\$thisyear".$args[1].$args[2].";");
    }else $maxyear=$vars['maxyear'];
               
    for($y=$minyear;$y<=$maxyear;$y++) $vars['options_values'][$y]=$y;
    if ($value!==null)  $vars['value']=strftime("%Y",$value);              
                
?>

<span id="calendar_content">
<div id="format_<?php echo $strippedname; ?>" TITLE="<?php echo Viewer::_echo('form:calendar:change_date'); ?>"  class="calendar-input-format" style="z-index:1;display:<?php echo $display_format; ?>">
	<? echo strtolower(transFormatDateTimeView($format)); ?>
</div>
<input <?php if (isset($readonly) && $readonly): ?>readonly="readonly"<?php endif; ?> <?php echo $class; ?> type="text" <?php echo $js2; ?> name="<?php echo $vars['internalname']; ?>" id="<?php echo $strippedname; ?>" value="<?php echo $val; ?>" />
</span>

<?php if (!isset($readonly) || !$readonly): ?>
<script type="text/javascript">
AnyTime.picker( "<?php echo $strippedname; ?>", 
      { 
      format: "<?php echo transFormatDateTime($format); ?>",
      //format: "%d/%m/%Y %H:%i:%s",  
      labelYear: "Anys",
      labelMonth: "Mes",
      labelHour: "Hora",
      labelMinute: "Minuts",
      labelSecond: "Segons",
      labelDayOfMonth: "Dia del mes",
      //labelDismiss: "",      
      labelTitle: "Tria la data",
      dayNames: ['Diumenge', 'Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte'],
      dayAbbreviations: ['Dg', 'Dl', 'Dt', 'Dx', 'Dj', 'Dv', 'Ds'],
      monthAbbreviations: [ 'Gen','Feb','Mar','Abr','Mai','Jun','Jul','Ago','Sep','Oct','Nov','Dec' ],
	  monthNames:  [ 'Gener','Febrer','Marc','April','Maig','Juny','Juliol','Agost','Septembre','Octubre','Novembre','Decembre' ],
      firstDOW: 1,
      earliest: new Date(<?php echo $minyear; ?>,0,1,0,0,0),
      latest: new Date(<?php echo $maxyear; ?>,11,31,23,59,59)
      //,placement: "inline" 
      } );
      
      /*$(document).ready(function(){
  			$('div.AnyTime-body').css('width','412px');
  			
  		});*/
</script>
<? endif; ?>