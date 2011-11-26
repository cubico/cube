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
		
	
	?><div class="pagination"><?php
// si la paginacion está activa	
if (isset($vars['view_options']['active']) && $vars['view_options']['active']){	
	if (isset($vars['view_options']['pulldown']) && $vars['view_options']['pulldown'])  {
		$min=(isset($vars['view_options']['min']))?$vars['view_options']['min']:10;
		$max=(isset($vars['view_options']['max']))?$vars['view_options']['max']:100;
		$inc=(isset($vars['view_options']['inc']))?$vars['view_options']['inc']:10;
		?><div style="float:right;">
		<span style="float:left;"><?php echo Viewer::_echo("grid:elems_per_page"); ?></span>
		<?php 
				if ($limit==$count) echo "&#160;".Viewer::_echo("allelements");
				else {
					for ($i=$min;$i<=$max;$i+=$inc)	if ($limit==$i) echo "&#160;".$i;
				}
			?>	
		
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
		
		echo "<a href=\"{$prevurl}&viewer=print\" class=\"pagination_previous\">&laquo; ". Viewer::_echo("previous") ."</a> ";
		
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
				echo " <a href=\"{$counturl}&viewer=print\" class=\"pagination_number\">{$i}</a> ";
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
		
		echo " <a href=\"{$nexturl}&viewer=print\" class=\"pagination_next\">" . Viewer::_echo("next") . " &raquo;</a>";
	}
  } // end of pagination check if statement
	
}    

?>
<br class="clearfloat" />
</div>