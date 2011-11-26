<?php if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" "; 
		}
		else $js=$vars['js'];
	}
?>
<div <?php echo $js; ?>>
<script type="text/javascript">
var simpleTreeCollection;
$(document).ready(function(){
	simpleTreeCollection = $('.simpleTree').simpleTree({
		autoclose: true,
		drag: <?php $drag=(isset($vars['drag'])?$vars['drag']:'false'); echo $drag; ?>,
		afterClick:function(node){
			var value=$(node).attr('id');
			$('input[name="<?php echo $vars['internalname']; ?>"]').val(value);
			<?php echo isset($vars['updateFunction'])?$vars['updateFunction']."(value);":''; //alert('Loaded'); ?>
			//alert("text-"+$('span:first',node).text());
		},
		afterDblClick:function(node){
			//alert("text-"+$('span:first',node).text());
		},
		afterMove:function(destination, source, pos){
			//alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
		},
		afterAjax:function()
		{
			
		},
		animate:true
		//,docToFolderConvert:true
	});
	
	<?php if (isset($vars['value']) && !empty($vars['value'])): ?>
	$('.simpleTree li#<?php echo $vars['value']; ?> span').attr('class','active');
	$('.simpleTree li#<?php echo $vars['value']; ?>').parents('*').show();
	<?php endif; ?>
	
});
</script>
<?php
function createTree($array, $currentParent, $currLevel = 0, $prevLevel = -1) {
	$cont=0;
	foreach ($array as $categoryId => $category) {
 		if ($currentParent == $category['parent_id']) {						
 			if ($currLevel > $prevLevel) echo " <ul> "; 
 			if ($currLevel == $prevLevel) echo " </li> ";
 			echo '<li id="'.$categoryId.'"><span>'.$category['name'].'</span>';
 			if ($currLevel > $prevLevel) { $prevLevel = $currLevel; }
 			$currLevel++; 
 		 	createTree ($array, $categoryId, $currLevel, $prevLevel);
 		 	$currLevel--;
 		 	$cont++;
		}	
 	}
	if ($currLevel == $prevLevel) echo " </li>  </ul> ";
	return $cont;
}

/*
// function to create a tree based on a unordered array
function createTreeT($array, $currentParent, $currLevel = -1) {
	foreach ($array as $categoryId => $category) {	
		if ($currentParent == $category['parent_id']) {
			// print the asterisks based on the current level of nesting		
			for ($i=0;$i<$currLevel;$i++) echo "*";			
			echo $category['name']."<br />"; // print the category name
			$currLevel++;
		 	createTreeT ($array, $categoryId, $currLevel);
		 	$currLevel--;
		}	
	}
}
*/
//echo Viewer::addJavascript('/js/tree/js/jquery.simple.tree.js');
//echo Viewer::addStyle('/js/tree/css/jquery.simple.tree.css');

$arrayCategories=array();

if (isset($vars['query']['select'])) // ARBOL -> QUERY
{
	$src=explode(".",$vars['query']['select']);
	$class=$src[0]."Peer";
	$peer=new $class();
	$rsCategories=$peer->execute($src[1],array("wherenivell"=>''),false);
	$arrayCategories=array();
	
	foreach($rsCategories as $row){ 
		$arrayCategories[$row['CATEGORY_ID']] = array("parent_id" => $row['PARENT_ID'], "name" => $row['CATEGORY_LABEL']);	
	}
	$root=0;
	
}else{
	/// tengo que buscar los que tengan de padre $vars['value'] y sus siguientes hijos -> $arrayCategories 
	foreach($vars['options_values'] as $row){ 
		$arrayCategories[$row['CATEGORY_ID']] = array("parent_id" => $row['PARENT_ID'], "name" => $row['CATEGORY_LABEL']);	
	}
	$root=$vars['value'];
}

if (isset($arrayCategories) && count($arrayCategories)>0) // ARBOL -> ARRAY
{		
	?>
	<ul class="simpleTree">
	<li class="root" id='0'>
		<?php $hijos=createTree($arrayCategories, $root, (isset($vars['value'])?$vars['value']:'null'));?>		
	</li>
	</ul>
	<?php 
}

if (isset($hijos) && !$hijos) echo ('no hi han dades per crear l\'arbre');
	
?>
<input type="hidden" name="<?php echo $vars['internalname']; ?>" value="<?php echo (isset($vars['value'])?$vars['value']:''); ?>">
</div>