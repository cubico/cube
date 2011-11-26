<?php
   	
   	
    if (!function_exists('timestamp')){
        function timestamp($time,$format){
            return time();   
        }
    }
   
    if (!function_exists('monthvals')){
        function monthvals(){
            //$months=array(null);
            for($i=1;$i<=12;$i++) $months[$i]=utf8_encode(strftime("%B",mktime(0,0,0,$i,1,1)));
            return $months;
        }
    }
    
    if (isset($vars['value']) && !empty($vars['value'])) {
        $value=$vars['value'];
    	if (!is_numeric($value)){
            $value=dbDriver::toTimestamp($value,$vars['format']);
        }
    }else if (isset($vars['default'])){
    	if ($vars['default']=='sysdate') $value=time();
    }else $value=null;
   
    $vars['align']='horizontal';
    $name=$vars['internalname'];
    //echo _r($vars); //.strftime("%d/%m/%Y %H:%M:%S",time());
    
    switch($vars['type']){
        case 'selection':
       			$vars['options_values']=array();
                // hour
                if (isset($vars['inchour'])) $inch=$vars['inchour'];else $inch=1;
                //$vars['options_values']=array(null);
                for ($i=0;$i<24;$i+=$inch) {
                	$j=str_pad($i, 2, "0", STR_PAD_LEFT);
                	$vars['options_values'][$j]=$j;
                }
                if ($value!==null) $vars['value']=intval(strftime("%H",$value));
                $vars['internalname']=$name."[hour]";
                $hours=Viewer::view("input/pulldown",$vars);
               	$vars['options_values']=array();
                // minute
                if (isset($vars['incminute'])) $incm=$vars['incminute'];else $incm=1;
                //$vars['options_values']=array(null);
                for ($i=0;$i<60;$i+=$incm){
                	$j=str_pad($i, 2, "0", STR_PAD_LEFT);
                	$vars['options_values'][$j]=$j;
                }
                if ($value!==null) $vars['value']=intval(strftime("%M",$value));
                $vars['internalname']=$name."[minute]";
                $minutes=Viewer::view("input/pulldown",$vars);
                
                $vars['options_values']=array();
                // seconds
                if (isset($vars['incsecond'])) $incs=$vars['incsecond'];else $incs=1;
                //$vars['options_values']=array(null);
                for ($i=0;$i<60;$i+=$incs){
                	$j=str_pad($i, 2, "0", STR_PAD_LEFT);
                	$vars['options_values'][$j]=$j;
                }
                if ($value!==null) $vars['value']=intval(strftime("%S",$value));
                $vars['internalname']=$name."[second]";
                $seconds=Viewer::view("input/pulldown",$vars);

                
                // days
                //$vars['options_values']=array_merge(array(null),range(1, 31));
                $vars['options_values']=range(0, 31);
                unset($vars['options_values'][0]);
                if ($value!==null) $vars['value']=intval(strftime("%d",$value));
                
                $vars['internalname']=$name."[day]";
                $days=Viewer::view("input/pulldown",$vars);
                unset($vars['options_values']);
                //months
               
                $vars['options_values']=monthvals();
                if ($value!==null)  $vars['value']=intval(strftime("%m",$value));
                $vars['internalname']=$name."[month]";
                $months=Viewer::view("input/pulldown",$vars);
                
                $vars['options_values']=array();
                
                // years
                //$vars['options_values']=array(null);
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
                
                if (isset($vars['year_order']) && $vars['year_order']='desc')
                	$vars['options_values']=array_reverse($vars['options_values'],true);

                if ($value!==null)  $vars['value']=strftime("%Y",$value);
				
                
                $vars['internalname']=$name."[year]";
                $years=Viewer::view("input/pulldown",$vars);
               	
                   
                // template

                $template=preg_replace("/%year%/i",$years,$vars['template']);
                $template=preg_replace("/%month%/i",$months,$template);
                $template=preg_replace("/%day%/i",$days,$template);
                $template=preg_replace("/%hour%/i",$hours,$template);
                $template=preg_replace("/%minute%/i",$minutes,$template);
                $template=preg_replace("/%second%/i",$seconds,$template);
                echo $template;
                
                break;
        case 'calendar':   
        default:
                echo Viewer::view("input/calendar",$vars);
                break;
    }
?>