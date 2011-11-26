<?php
	/**
	 * Elgg pagination
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008
	 * @link http://elgg.org/
	 * 
	 */
		
	if (!isset($vars['offset'])) {
		$offset = 0;
	} else {
		$offset = $vars['offset'];
	}
	if (!isset($vars['limit'])) {
		//$limit = 10;
		return 0;
	} else {
		$limit = $vars['limit'];
	}
	if (!isset($vars['count'])) {
		$count = 0;
	} else {
		$count = $vars['count'];
	}
	if (!isset($vars['word'])) {
		$word = "offset";
	} else {
		$word = $vars['word'];
	}
	if (isset($vars['nonefound'])) {
		$nonefound = $vars['nonefound'];
	} else {
		$nonefound = true;
	}
	
	$totalpages = ceil($count / $limit);
	$currentpage = ceil($offset / $limit) + 1;
	
	if (!isset($vars['action']))
		$baseurl = preg_replace('/[\&\?]'.$word.'\=[0-9]*/',"",$vars['baseurl']);
	else{ 
		if (!isset($vars['entity'])) $vars['entity']=array();	
		$baseurl=$vars['url']."/".Route::parseValues($vars['action'],$vars['entity']);
	}	
	
	$strippedname=strtr($vars['internalname'],"[].,","____"); 

	//echo _r($vars);
	//echo _r(Request::getInstance()->get());
	$n=isset($vars['filterName'])?'#'.$vars['filterName']:'form';
?>
<div class="pagination">
<?php 

if (!defined($strippedname."Func")) {
 	define($strippedname."Func",1);	
?>
<script type="text/javascript">
			// search the output links of the list headers to change action
			var <?php echo $strippedname ?>Func=function(limit){
				var form=$('<?php echo $n; ?>');
				// get the href 
				//var href='<?php echo $baseurl; ?>?limit=' +$('#<?php echo $strippedname ?>').val();
				var href='<?php echo $baseurl; ?>?limit=' +limit;
				// set the href to the action form
				form.attr('action',href);
				// submit filter form
				form.submit();
			};
			
</script>
<?php } ?>

<?php
// si la paginacion está activa	
if (isset($vars['view_options']['active']) && $vars['view_options']['active']){	
	if (isset($vars['view_options']['pulldown']) && $vars['view_options']['pulldown'])  {
		$min=(isset($vars['view_options']['min']))?$vars['view_options']['min']:10;
		$max=(isset($vars['view_options']['max']))?$vars['view_options']['max']:100;
		$inc=(isset($vars['view_options']['inc']))?$vars['view_options']['inc']:10;
		?><div style="float:right;padding: 0 10px;">
		<span style="float:left;"><?php echo Viewer::_echo("grid:elems_per_page"); ?></span>
		<select id="<?php echo $strippedname; ?>" class="pagination_next" onChange="<?php echo $strippedname ?>Func(this.value);">
		<?php for ($i=$min;$i<=$max;$i+=$inc):?>		
			<option value="<?php echo $i ?>" <?php echo ($limit==$i)?"selected=\"selected\"":"" ?>><?php echo $i; ?></option>
		<?php endfor; ?>	
			<option <?php echo ($limit==$count)?"selected=\"selected\"":"" ?> value="<?php echo $count; ?>"><?php echo Viewer::_echo("allelements"); ?></option>
		</select>
		
			</div>
	<?php 
	} 
		
	//only display if there is content to paginate through or if we already have an offset
	if (($count > $limit || $offset > 0) /*&& get_context() != 'widget'*/) {
?>
<?php  
	
	if ($offset > 0) {
		
		$prevoffset = $offset - $limit;
		if ($prevoffset < 0) $prevoffset = 0;
		
		$prevurl = $baseurl;
		if (substr_count($baseurl,'?')) {
			$prevurl .= "&{$word}=" . $prevoffset;
		} else {
			$prevurl .= "?{$word}=" . $prevoffset;
		}
		
		echo "<a href=\"{$prevurl}\" class=\"pagination_previous\">&laquo; ". Viewer::_echo("previous") ."</a> ";
		
	}
	
	if ($offset > 0 || $offset < ($count - $limit)) {
		
		$currentpage = round($offset / $limit) + 1;
		$allpages = ceil($count / $limit);
		
		$i = 1;
		$pagesarray = array();
		while ($i <= $allpages && $i <= 4) {
			$pagesarray[] = $i;
			$i++;
		}
		$i = $currentpage - 2;
		while ($i <= $allpages && $i <= ($currentpage + 2)) {
			if ($i > 0 && !in_array($i,$pagesarray))
				$pagesarray[] = $i;
			$i++;
		}
		$i = $allpages - 3;
		while ($i <= $allpages) {
			if ($i > 0 && !in_array($i,$pagesarray))
				$pagesarray[] = $i;
			$i++;
		}
		
		sort($pagesarray);
		
		$prev = 0;
		foreach($pagesarray as $i) {
		
			if (($i - $prev) > 1) {
				
				echo "<span class=\"pagination_more\">...</span>";
				
			}
			
			$counturl = $baseurl;
			$curoffset = (($i - 1) * $limit);
			if (substr_count($baseurl,'?')) {
				$counturl .= "&{$word}=" . $curoffset;
			} else {
				$counturl .= "?{$word}=" . $curoffset;
			}
			if ($curoffset != $offset) {
				echo " <a href=\"{$counturl}\" class=\"pagination_number\">{$i}</a> ";
			} else {
				echo "<span class=\"pagination_currentpage\"> {$i} </span>";
			}
			$prev = $i;
		
		}

	}
	
	if ($offset < ($count - $limit)) {
		
		$nextoffset = $offset + $limit;
		if ($nextoffset >= $count) $nextoffset--;
		
		$nexturl = $baseurl;
		if (substr_count($baseurl,'?')) {
			$nexturl .= "&{$word}=" . $nextoffset;
		} else {
			$nexturl .= "?{$word}=" . $nextoffset;
		}
		
		echo " <a href=\"{$nexturl}\" class=\"pagination_next\">" . Viewer::_echo("next") . " &raquo;</a>";
	}
	
		
		if (isset($vars['with_sorting'])){
			echo Viewer::view('output/link',$vars['with_sorting']);
		
		}
		?>	  
<?php
    } // end of pagination check if statement
	else if (isset($vars['with_sorting'])){
		?>
		
		<span class="pagination"><?php 
    	echo Viewer::view('output/link',$vars['with_sorting']);
    	?></span><?php 
	}
}    
else if (isset($vars['with_sorting'])){
		?>
		
		<span class="pagination"><?php 
    	echo Viewer::view('output/link',$vars['with_sorting']);
    	?></span><?php 
	
}
?>
<br class="clearfloat" />
</div>