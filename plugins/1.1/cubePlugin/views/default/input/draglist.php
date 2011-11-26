<?php

	$dataStore=$vars['options_values'];

	$values=(isset($vars['value']['values']) && !empty($vars['value']['values']))?(array)$vars['value']['values']:array();
	
	$selecteds=array();
	foreach($values as $val) { if (isset($dataStore[$val])) $selecteds[$val]=$dataStore[$val];}

	if (isset($vars['init_values']))
	{
		$init_values=(array)$vars['init_values'];
		$indexes=array_keys(array_merge($dataStore,$init_values));
		$selecteds=array_merge($selecteds,$init_values);
	}else{
		$indexes=array_keys($dataStore);
	}

	$list=array_diff_assoc($dataStore,$selecteds);
	
	sort($indexes);
	$dataStoreIndexes=array_flip($indexes);

	$add_icon=isset($vars['add_icon'])?$vars['add_icon']:'ui-icon ui-icon-refresh';
	$add_text=isset($vars['add_text'])?$vars['add_text']:'Afegir';

	$del_icon=isset($vars['del_icon'])?$vars['del_icon']:'ui-icon ui-icon-trash';
	$del_text=isset($vars['del_text'])?$vars['del_text']:'Treure';

	$iconAfegir="<a title='{$add_text}' class='{$add_icon}'>{$add_text}</a>";
    $iconTreure="<a title='{$del_text}' class='{$del_icon}'>{$del_text}</a>";

	$dimsAfegir=array('165','20','10','20');
	if (isset($vars['add_dims']))	array_splice($dimsAfegir,0,count($vars['add_dims']),$vars['add_dims']);
	
	
	//$dimsTreure=isset($vars['del_dims'])?$vars['del_dims']:array('135','20','16');
	$dimsTreure=$dimsAfegir;

	$ajaxLoader="/img/ajax-loader2.gif";
	$strippedname=strtr($vars['internalname'],"[].","___");

	$trash_disabled=isset($vars['trash_disabled'])?$vars['trash_disabled']:true;
	$add_with_order=isset($vars['add_with_order'])?$vars['add_with_order']:true;

	//$width=isset($vars['width'])?$vars['width']:'160';
	$width=max($dimsAfegir[0],$dimsTreure[0])+25;
	$height=isset($vars['height'])?$vars['height']:'200';
	$fontsize=max($dimsAfegir[2],$dimsTreure[2]).'px';

	$filter=isset($vars['filter'])?$vars['filter']:false;

	if (isset($vars['js']))
	{
		$js='';
		if (is_array($vars['js']))
		{
			foreach($vars['js'] as $k=>$v) $js.=" {$k}=\"{$v}\" ";
		}
		else $js=$vars['js'];
	}

	 $internalnameValues=$vars['internalname'].'[values]';
	 $internalnameInit=$vars['internalname'].'[init]';
	 
	 if (!isset($vars['multiple']) || $vars['multiple']) {
		 $multi=true;
		 $internalnameValues.='[]';
		 $internalnameInit.='[]';
	 }else $multi=false;

if (!defined('DRAGLIST_COMPONENT')){ define('DRAGLIST_COMPONENT',true);?>
<script type="text/javascript" src="/js/jquery-ui/jquery-ui-1.8.13.core.js"></script>
<script type="text/javascript" src="/js/jquery-ui/jquery-ui-1.8.13.dragdropsort.js"></script>
<style type="text/css">
	.ui-state-default, .ui-widget-header .ui-state-default {
       background: #FCFCFC;
       border: 1px solid #D3D3D3;
       color: #555555;
       font-weight: normal;
    }

    .ui-state-highlight, .ui-widget-header .ui-state-highlight {
       background: #FBF9EE;
       border: 1px solid #FCEFA1;
       color: #000;
   }

   .ui-corner-tr {
       -moz-border-radius: 4px;
   }

   .ui-widget-action{
      float: right;
   }

   .ui-icon {
      background-image: url("/img/ui-icons_222222_256x240.png");
      height: 16px;
      width: 16px;
      background-repeat: no-repeat;
      display: inline;
      overflow: hidden;
	  float: right;
      text-indent: -99999px;
      text-align: left;
   }

   .ui-icon-refresh {
      background-position: -0px -192px;
   }

   .ui-icon-trash {
      background-position: -14px -192px;
   }

   .ui-widget-header {
       color: #000;
       font-weight: bold;
       padding: 0 0 15px 5px;
   }

	.ui-state-disabled {
		 background: #F7FC9C;
	}
</style>
<?php } ?>

<style type="text/css">
#sortable1<?php echo $strippedname;?>, #sortable2<?php echo $strippedname;?>{ list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px;
									background: #eee; padding: 5px; width: <?php echo $width; ?>px; height: <?php echo $height;?>px; overflow-x: hidden; overflow-y: auto;}
#sortable1<?php echo $strippedname;?> li { cursor:pointer; margin: 0 5px 1px 5px; padding: 1px 2px; font-size: <?php echo $fontsize;?>; width: <?php echo $dimsAfegir[0]; ?>px; height: <?php echo $dimsAfegir[1]; ?>px;}
#sortable2<?php echo $strippedname;?> li { cursor:pointer; margin: 0 5px 1px 5px; padding: 1px 2px; font-size: <?php echo $fontsize;?>; width: <?php echo $dimsTreure[0]; ?>px; height: <?php echo $dimsTreure[1]; ?>px;}

</style>
<script type="text/javascript" >

		var timer<?php echo $strippedname;?>;
      var interval<?php echo $strippedname;?>;

	<?php $visibleFunction=isset($vars['visible_function'])?$vars['visible_function']:('visible'.$strippedname); ?>
		
		var visible<?php echo $strippedname;?>=function($todos){
			return $todos;	
		};

      var timerFunc<?php echo $strippedname;?>=function(){
           timer<?php echo $strippedname;?>=timer<?php echo $strippedname;?>+1;
           if (timer<?php echo $strippedname;?>==1) $('#filterProcess<?php echo $strippedname;?>').show();
           else if (timer<?php echo $strippedname;?>==6) {
              //// accion
              var $todos=$('#sortable1<?php echo $strippedname;?> li');
              var $elems=<?php echo $visibleFunction;?>($todos);
              $todos.hide();
              var cerca=$('#cercador<?php echo $strippedname;?>').val().toLowerCase();
              if (cerca!=''){
                  $elems.filter(function(i) {
                     var text=$(this).find('span');
                     var toMatch = text.text().toString().toLowerCase();
                     return toMatch.indexOf(cerca) != -1;
                 }).show();
                 $('#filterActivate<?php echo $strippedname;?>').show();
               }else {
                  $elems.show();
                  $('#filterActivate<?php echo $strippedname;?>').hide();
               }
               //// fin accion
               timer<?php echo $strippedname;?>=0;
               clearInterval(interval<?php echo $strippedname;?>);
               $('#filterProcess<?php echo $strippedname;?>').hide();
           }
       }

	function updateItem<?php echo $strippedname;?>($item,type,update){
			var id=$item.find('span').attr('id');

		  switch(type){
			  case 'fromSortable2':

					$item.find( "a.ui-icon-trash" ).remove();
					$item.append("<?php echo $iconAfegir; ?>");
					$item.fadeIn(function() { $item.animate({ height: "<?php echo $dimsAfegir[1];?>px", width: "<?php echo $dimsAfegir[0];?>px" });});
					//$item.fadeIn(function() {$item.animate({ height: "<?php echo $dimsAfegir[1];?>px", width: "<?php echo $dimsAfegir[0];?>px" }).find( "img" ).animate({ height: "<?php echo $dimsAfegir[2];?>px" });});
					//$item.fadeIn(function() { $item.animate({ opacity: .5 }, 500);});
					$('[name="<?php echo $internalnameValues;?>"] option[value="'+id+'"]').remove();
					break;
			  case 'fromSortable1':
					
					$item.find( "a.ui-icon-refresh" ).remove();
					$item.append("<?php echo $iconTreure; ?>");
					$item.fadeIn(function() { $item.animate({ height: "<?php echo $dimsTreure[1];?>px", width: "<?php echo $dimsTreure[0];?>px" });});
					//$item.fadeIn(function() {$item.animate({ height: "<?php echo $dimsTreure[1];?>px", width: "<?php echo $dimsTreure[0];?>px" }).find( "img" ).animate({ height: "<?php echo $dimsTreure[2];?>px" });});
					//$item.find( "a.ui-icon-refresh" ).remove();
					// $('.action',$item).append("<?php echo $iconTreure; ?>");
					//$item.fadeIn(function() { $item.animate({opacity: 1}, 500);});
					//$('[name="<?php echo $internalnameValues;?>"]').append('<option selected="selected" value="'+id+'">'+id+'</option>'); // si no es el primero de la lista, se añade
					//updateSelect<?php echo $strippedname;?>($sortable2<?php echo $strippedname;?>);
					if (update!=undefined) updateSelect<?php echo $strippedname;?>(update);
					break;
		  }

	  }

	 function updateSelect<?php echo $strippedname;?>(sortable){
			var id,x=$('[name="<?php echo $internalnameValues;?>"]');
			x.find('option').remove();
			sortable.find('li').each(function(){
				id=$(this).find('span').attr('id');
				x.append('<option selected="selected" value="'+id+'">'+id+'</option>'); // si no es el primero de la lista, se añade
			});
	 }

	$(function() {

		var $sortable1<?php echo $strippedname;?>= $( "#sortable1<?php echo $strippedname;?>" ), $sortable2<?php echo $strippedname;?>= $( "#sortable2<?php echo $strippedname;?>" );
		
		<?php if (isset($vars['draggable']) && $vars['draggable']){ ?>
		
		$sortable1<?php echo $strippedname;?>.sortable({
		<?php if (!isset($vars['placeholder']) || $vars['placeholder']){ ?>placeholder: "ui-state-highlight",<?php } ?>
		<?php if (!isset($vars['dropOnEmpty']) || $vars['dropOnEmpty']){ ?>dropOnEmpty: true,<?php } ?>
		connectWith: "ul",
			receive: function(event, ui) {
				updateItem<?php echo $strippedname;?>(ui.item,'fromSortable2');
			}
		});

		$sortable2<?php echo $strippedname;?>.sortable({
			update: function(event, ui) {
					updateSelect<?php echo $strippedname;?>($sortable2<?php echo $strippedname;?>);
				},
			<?php if (!isset($vars['placeholder']) || $vars['placeholder']){ ?>placeholder: "ui-state-highlight",<?php } ?>
			<?php if (!isset($vars['dropOnEmpty']) || $vars['dropOnEmpty']){ ?>dropOnEmpty: true,<?php } ?>
			//items: "li:not(.ui-state-disabled)",
			<?php if (!isset($vars['allow_disabled']) || !$vars['allow_disabled']){ ?>cancel: ".ui-state-disabled",<?php } ?>
			connectWith: "ul",
			//,dropOnEmpty: false
            receive: function(event, ui) {
               updateItem<?php echo $strippedname;?>(ui.item,'fromSortable1');
				}
         });

		$( "#sortable1<?php echo $strippedname;?>, #sortable2<?php echo $strippedname;?>" ).disableSelection();
		<?php } ?>
		
		var putInOrderBy<?php echo $strippedname;?>=function($sortable,$item){
			<?php if ($add_with_order){ ?>
			var trobat=false;
			if ($sortable.children().length>0){
				$sortable.find('li').each(function(i,elem){
					if (parseInt($(elem).attr('id'))>parseInt($item.attr('id'))){
						$(this).before($item);
						trobat=true
						return false;
					}
				});
			}
			if (!trobat) <?php } ?>
				$item.appendTo($sortable);
		}

		$( ".demo<?php echo $strippedname;?> ul > li" ).click(function( event ) {
			var $item = $( this ), $target = $( event.target );

			if ( $target.is( "a.ui-icon-refresh" ) ) {
					 $item.fadeOut(function() {
						putInOrderBy<?php echo $strippedname;?>($sortable2<?php echo $strippedname;?>,$item);
						updateItem<?php echo $strippedname;?>($item,'fromSortable1',$sortable2<?php echo $strippedname;?>);
					 });
			} else if ( $target.is( "a.ui-icon-trash" ) ) {
				
				<?php if (!isset($vars['trashFunction'])): ?> 
				$item.fadeOut(function() {
						updateItem<?php echo $strippedname;?>($item,'fromSortable2');
						<?php if ($trash_disabled){ ?>if (!$item.hasClass('ui-state-disabled')){ <?php }?>
							//$item.appendTo( $sortable1<?php echo $strippedname;?> );
							putInOrderBy<?php echo $strippedname;?>($sortable1<?php echo $strippedname;?>,$item);
						<?php if ($trash_disabled){ ?>}else $item.remove();<?php }?>
				 });
				 <?php else: ?>
				   <?php echo $vars['trashFunction'];?>($item,$target);
				 <?php endif; ?>
			}

			return false;
		});

		$('#cercador<?php echo $strippedname;?>').keyup(function(){
         timer<?php echo $strippedname;?>=0;
			if (interval<?php echo $strippedname;?>!=undefined) clearInterval(interval<?php echo $strippedname;?>);
			interval<?php echo $strippedname;?> = setInterval("timerFunc<?php echo $strippedname;?>()",100);
      });
	});
</script>
<div <?php echo $js; ?>>
	<?php $str=''; foreach($selecteds as $i=>$j) $str.='<option selected="selected" value="'.$i.'">'.$i.'</option>'; ?>
	<select style="display:none;" name="<?php echo $internalnameValues;?>" <?php echo ($multi)?'multiple="multiple"':''; ?>>
	<?php echo $str; ?>
	</select>
	
	<select style="display:none;" name="<?php echo $internalnameInit;?>" <?php echo ($multi)?'multiple="multiple"':''; ?>>
	<?php echo $str; ?>
	</select>
<div><?php echo isset($vars['description'])?$vars['description']:'';?></div><br/>
<div class="demo<?php echo $strippedname;?>">
	<ul id="sortable1<?php echo $strippedname;?>">
		<?php
			//for($i=0;$i<10;$i++) echo '<li id="'.$i.'" class="ui-state-default"><span id="'.('TA-'.$i).'">Item '.$i.'</span>'.$iconAfegir.'</li>';;
			$i=0;
			foreach($list as $k=>$v){
				if (isset($dataStoreIndexes[$k])){

					echo '<li title="'.addslashes($v).'" id="'.$dataStoreIndexes[$k].'" class="ui-state-default"><span id="'.$k.'">'.
							((strlen($v)>$dimsAfegir[3])?(mb_substr($v,0,$dimsAfegir[3],'UTF-8').'...'):$v).
							'</span>'.$iconAfegir.'</li>';
					$i++;
				}
			}
		?>
	</ul>
	<div style="float:left;padding: <?php echo ($height/2);?>px 5px;"><img src="/img/icon/arrow.png" /></div>
	<ul id="sortable2<?php echo $strippedname;?>">
		<?php
			//for($i=11;$i<20;$i++) echo '<li id="'.$i.'" class="ui-state-default ui-state-disabled"><span id="'.('BR'.$i).'">Item '.$i.'</span>'.$iconTreure.'</li>';;
			$i=0;
			foreach($selecteds as $k=>$v){
				if (isset($dataStoreIndexes[$k])){

					echo '<li title="'.addslashes($v).'" id="'.$dataStoreIndexes[$k].'" class="ui-state-default"><span id="'.$k.'">'.
							((strlen($v)>$dimsTreure[3])?(mb_substr($v,0,$dimsTreure[3],'UTF-8').'...'):$v).
							'</span>'.$iconTreure.'</li>';
					$i++;
				}
			}
		?>
	</ul>
	<br clear="both" />
	<?php if ($filter!==false): ?>
	<div style="margin: 5px;width:200px;float:left;">
      <?php echo $filter; ?>
      <input type="text" id="cercador<?php echo $strippedname;?>" style="width: 80px;"/>
      <img title="El filtre està activat" id="filterActivate<?php echo $strippedname;?>" style="display:none;" src="/img/icon/exclamation_diamond.png"/>
      <img title="Cercant Aplicacions..." id="filterProcess<?php echo $strippedname;?>" style="display:none;" src="<?php echo $ajaxLoader;?>"/>
    </div>
    <?php endif; ?>
	<div id="extrafilter<?php echo $strippedname;?>"></div>
</div>
<?php unset($dataStoreIndexes); unset($dataStore); unset($selecteds); unset($list); ?>
</div>
