<?php

	/**
	 * Elgg pulldown input
	 * Displays a pulldown input field
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
	 * @uses $vars['options'] An array of strings representing the options for the pulldown field
	 * @uses $vars['options_values'] An associative array of "value" => "option" where "value" is an internal name and "option" is 
	 * 								 the value displayed on the button. Replaces $vars['options'] when defined. 
	 */
	

	$class = $vars['class'];
	if (!$class) $class = "input-pulldown";
	if (!isset($vars['value'])) $vars['value']=array();
	//if (!is_array($vars['value'])) $vars['value']=array($vars['value']);
	if (!is_array($vars['value'])) 
		$vars['value']=explode(Form::FILTER_SEPARATOR_ARRAY,$vars['value']); 
	
	$size=isset($vars['size'])?' size="'.$vars['size'].'"':'';
	$strippedname=strtr($vars['internalname'],"[].","___");
	$showfilters=(isset($vars['filters'])?$vars['filters']:false); // por defecto false 
	$sort=(isset($vars['sort'])?$vars['sort']:true);	// por defecto true
	
	if (isset($vars['align']) && $vars['align']=='horizontal') echo '<div style="margin:10px 0; float:right;">';
	
	echo Viewer::addJavascript("/js/jquery/jquery.dualListBox-1.3.min.js");
	
	//echo _r($vars);
	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
	
?>
<script language="javascript" type="text/javascript">
	$(document).ready(function(){
		
		function  markValues<?php echo $strippedname; ?>(){
			$("#[name='<?php echo $vars['internalname']; ?>[]'] option").remove();
    		$('#'+settings<?php echo $strippedname; ?>.box2View+' option').clone().appendTo("#[name='<?php echo $vars['internalname']; ?>[]']");
    		$('#'+settings<?php echo $strippedname; ?>.box2Storage+' option').clone().appendTo("#[name='<?php echo $vars['internalname']; ?>[]']");
    		if ($("#[name='<?php echo $vars['internalname']; ?>[]'] option").length>0){
    			$("#[name='<?php echo $vars['internalname']; ?>[]'] option").attr('selected','selected');
    		}
    	}
    	
		var settings<?php echo $strippedname; ?> = {
			templateTextCounter: 'Visualitzant %d de %d',			
            box1View: 'box1View<?php echo $strippedname; ?>',
            box1Storage: 'box1Storage<?php echo $strippedname; ?>',
            box1Filter: 'box1Filter<?php echo $strippedname; ?>',
            box1Clear: 'box1Clear<?php echo $strippedname; ?>',
            box1Counter: 'box1Counter<?php echo $strippedname; ?>',
            box2View: 'box2View<?php echo $strippedname; ?>',
            box2Storage: 'box2Storage<?php echo $strippedname; ?>',
            box2Filter: 'box2Filter<?php echo $strippedname; ?>',
            box2Clear: 'box2Clear<?php echo $strippedname; ?>',
            box2Counter: 'box2Counter<?php echo $strippedname; ?>',
            to1: 'to1<?php echo $strippedname; ?>',
            allTo1: 'allTo1<?php echo $strippedname; ?>',
            to2: 'to2<?php echo $strippedname; ?>',
            allTo2: 'allTo2<?php echo $strippedname; ?>',
            useFilters: <?php echo $showfilters?'true':'false'; ?>,
            useCounters: <?php echo $showfilters?'true':'false'; ?>,
            transferMode: 'move',
            useSorting: <?php echo $sort?'true, sortBy: \'text\'':'false'; ?>
        };
		
    	$.configureBoxes(settings<?php echo $strippedname; ?>);
    	
    	$('#'+settings<?php echo $strippedname; ?>.to1).click(markValues<?php echo $strippedname; ?>);
    	$('#'+settings<?php echo $strippedname; ?>.to2).click(markValues<?php echo $strippedname; ?>);
    	$('#'+settings<?php echo $strippedname; ?>.allTo1).click(markValues<?php echo $strippedname; ?>);
    	$('#'+settings<?php echo $strippedname; ?>.allTo2).click(markValues<?php echo $strippedname; ?>);
    	
    	$('#'+settings<?php echo $strippedname; ?>.box1View).dblclick(markValues<?php echo $strippedname; ?>);
    	$('#'+settings<?php echo $strippedname; ?>.box2View).dblclick(markValues<?php echo $strippedname; ?>);
    	
    	markValues<?php echo $strippedname; ?>();
    	
    	//$(".doublelist option").tipTip();
    	//$(".doublelist option").tooltip();
    });
</script>
<div>
<select style="display:none" multiple="multiple" name="<?php echo $vars['internalname']; ?>[]"></select>
<?php
	$values=array('all'=>array(),'selected'=>array());
	
	if (isset($vars['options_values']))
	{
		foreach($vars['options_values'] as $value => $option) {
	        if (!in_array($value,$vars['value'])) $values['all'][$value]=$option;
	        else $values['selected'][$value]=$option;
	    }
	}
	else if (isset($vars['options']))
	{
		foreach($vars['options'] as $value) {
	        if (!in_array($value,$vars['value'])) $values['all'][$value]=$value;
	        else $values['selected'][$value]=$value;
	    }
	}
	else if (isset($vars['query']) && !isset($vars['parameters']['through_class']))
	{
		$src=explode(".",$vars['query']['select']);
		$class=$src[0]."Peer";
		$peer=new $class();
		$data=$peer->doSelect($peer->getQuery($src[1]),false);
		
		if (!isset($vars['query']['value'])) list($qval,$qtext)=array_keys($data[0]); 
		else {$qval=$vars['query']['value'];$qtext=$vars['query']['text'];}
				
		foreach($data as $cur){
			
			$option=$cur[$qval];
			$text= $cur[$qtext];
			
			if (!in_array($option,$vars['value'])) $values['all'][$option]=$text;    
			else $values['selected'][$option]=$text;
		}
	}else if( isset($vars['assignTo']) || isset($vars['peerMethod']) ){  
	
		if (!preg_match("/[.]/",$vars['assignTo'])){
			
			$class=$vars['assignTo']."Peer";
			$peer=new $class();
			
			if (isset($vars['parameters']['query'])){
				$data=$peer->doSelect($peer->getQuery($vars['parameters']['query']),false);
				
			}else if (isset($vars['parameters']['peerMethod'])){
				$methods=get_class_methods($class);
				//echo $vars['parameters']['peerMethod'].','.$class;die();
				//if (in_array($vars['parameters']['peerMethod'],$methods))
				if (is_array($vars['parameters']['peerMethod']) && $vars['parameters']['peerMethod']['method']){
					$method=$vars['parameters']['peerMethod']['method'];
				}else
					$method=$vars['parameters']['peerMethod'];
				
				if (method_exists($class,$method)) 
					$data=$peer->{$method}();
			}else {
				$data=$peer->doSelectAll(false);
			}
			
			
		}else if (isset($vars['peerMethod'])){
			$src=explode(".",$vars['peerMethod']['method']);
			$class=$src[0]."Peer";
			$peer=new $class();
			$methods=get_class_methods($peer);
			if (in_array($src[1],$methods)){ 
				if (!isset($vars['peerMethod']['value'])) {
					$data=$peer->{$src[1]}();
				}
				else {
					$data=$peer->{$src[1]}($vars['peerMethod']['value'],$vars['peerMethod']['text']);
				}
			}
		}
		
		//echo _r($data);die();
		
		foreach($data as $cur){
			
			if (!preg_match("/[.]/",$vars['assignTo'])){
				if (!isset($vars['parameters']['value'])){
					$option=reset($cur);
					$text=next($cur);
				}else{
					$option=$cur[$vars['parameters']['value']];
					$text= $cur[$vars['parameters']['text']];	
				}
			}else if (!isset($vars['peerMethod']['value'])){
				
				$option=reset($cur);
				$text=next($cur);
				
			}else{
				$option=$cur[$vars['peerMethod']['value']];
				$text= $cur[$vars['peerMethod']['text']];
			}
			
			if (!in_array($option,$vars['value'])) $values['all'][$option]=$text;    
			else $values['selected'][$option]=$text;
		}
	}	 
	
	//echo _r($vars);
	
?>
<table class="doublelist">
<tr>
	<td>
		<?php echo Viewer::title(Viewer::_echo('form:list:all')); ?>
		<select id="box1View<?php echo $strippedname; ?>" multiple="multiple" <?php echo $js; ?>>
	    <?php 
			foreach($values['all'] as $option=>$value) {
				$value=htmlentities($value, ENT_QUOTES, 'UTF-8');
				echo "<option value=\"{$option}\" title=\"{$value}\">{$value}</option>";
			}			        
		?>
		</select>
		<?php if ($showfilters): ?>
	    <div id="box1Counter<?php echo $strippedname; ?>" class="countLabel"></div>
		<div style="padding:5px;"><?php echo Viewer::_echo('tofilter'); ?><br/><input type="text" id="box1Filter<?php echo $strippedname; ?>" />&#160;
		<button type="button" id="box1Clear<?php echo $strippedname; ?>" title="<?php echo Viewer::_echo('reset'); ?>"><img src="/img/icon/arrow_circle_135.png" /></button></div>
		<?php endif; ?>
	    <select id="box1Storage<?php echo $strippedname; ?>" <?php if (!$showfilters){?>style="display:none;"<?php } ?>></select>
	</td>
    <td style="vertical-align:middle;padding:10px;">
    	<button id="to2<?php echo $strippedname; ?>" type="button"><img src="/img/icon/control.png" /></button>
        <button id="to1<?php echo $strippedname; ?>" type="button"><img src="/img/icon/control_180.png" /></button>
        <br/>
        <button id="allTo2<?php echo $strippedname; ?>" type="button"><img src="/img/icon/control_double.png" /></button>
        <button id="allTo1<?php echo $strippedname; ?>" type="button"><img src="/img/icon/control_double_180.png" /></button>
	</td>
    <td>
    	<?php echo Viewer::title(Viewer::_echo('form:list:selection')); ?>
    	<select class="<?php echo $vars['class']; ?>" id="box2View<?php echo $strippedname; ?>" multiple="multiple" <?php echo $js; ?>>
        <?php 
			foreach($values['selected'] as $option=>$value) {
				$value=htmlentities($value, ENT_QUOTES, 'UTF-8');
				echo "<option value=\"{$option}\" title=\"{$value}\">{$value}</option>";
			}			        
		?>
		</select>
        <?php if ($showfilters): ?>
    	<div id="box2Counter<?php echo $strippedname; ?>" class="countLabel"></div>
    	<div style="padding:5px;"><?php echo Viewer::_echo('tofilter'); ?><br/><input type="text" id="box2Filter<?php echo $strippedname; ?>" />&#160;
    	<button type="button" id="box2Clear<?php echo $strippedname; ?>" title="<?php echo Viewer::_echo('reset'); ?>"><img src="/img/icon/arrow_circle_135.png" /></button></div>
    	<?php endif; ?>
		<select id="box2Storage<?php echo $strippedname; ?>"  <?php if (!$showfilters){?>style="display:none;"<?php } ?>></select>
	</td>
    </tr>
    <tr><td colspan="3">* per seleccionar més d’un element alhora, premeu la tecla Ctrl</td></tr>
    </table>
</div>
<?php if (isset($vars['align']) && $vars['align']=='horizontal') echo '</div>'; ?>
