<?php	
	$render=isset($vars['render'])?$vars['render']:Viewer::_echo('column');
	$width=isset($vars['width'])?$vars['width']:'auto';
	$height=isset($vars['height'])?';height:'.$vars['height']:'';
	$class=empty($vars['class'])?'':' '.$vars['class'];
	$float='';
	if (isset($vars['label'])){  
		$label=$vars['label'];
		
		if (isset($label['template'])){
			$talign=array('m'=>'middle','t'=>'top','b'=>'bottom');
			$tposition=array('l'=>'left','r'=>'right');
			
			preg_match_all("/([:]{0,1}[a-zA-Z])([0-9]*)/i",$label['template'],$args);
			$w=0;
			foreach($args[1] as $i=>$cur){
				if (substr($cur,0,1)==':'){ //es el input
					$align=$talign[substr($cur,1)];
					$vars['js']='style="width:'.$args[2][$i].'px"';
					if ($i==1) $position='left';else $position='right';
				}else{ // es el label
					$groupw=';width: '.$args[2][$i].'px';
					$groupa=';text-align: '.$tposition[$cur];
				} 
				$w+=intval($args[2][$i]);
			}
			$width=$w+'px';
		}else{
		
			$align=isset($label['align'])?$label['align']:'middle';
			$groupw=isset($label['group_width'])?';width: '.$label['group_width']:'';
			$groupa=isset($label['group_align'])?';text-align: '.$label['group_align']:'';
			$position=isset($label['position'])?$label['position']:'left';
			//switch($position){case 'left':case 'right': $float=';float:'.$position;break;}
		}
		
		$top=($position=='top')?' style="margin: 3px 0 0 0;display:block;"':'';
		$bottom=($position=='bottom')?';margin: 3px 0 0 0;display:block;"':';';
				
		$input='<span'.$top.'>'.$render.'</span>'; 
		
		$etiq='<label style="display: inline-block; vertical-align: '.$align.$bottom.$groupw.$groupa.$float.'">'.parseText($label['title']).'</label>';
		
		switch($position){
			case 'right': $info=$input.$etiq;break;
			case 'top': $info=$etiq.$input;break;
			case 'bottom': $info=$input.$etiq;break;
			case 'left':default: $info=$etiq.$input;break;
		}
	}else{
		$info=$render;
	}
?>
<div class="column<?php echo $class; ?>" style="width:<?php echo $width.$height; ?>"><?php echo $info; ?></div>