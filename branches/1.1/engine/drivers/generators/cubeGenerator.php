<?php
	class cubeGenerator extends Generator
	{
		/////// MODEL GENERATORS (grid)
		
		// generateListGrid, -> model, generator, pks 
		// generateListElement -> model, generator, pks, columns
		// generateListHeaders -> functionNextSort, columns
		// generateObject -> columns --> ya lo hace en scripts.php
		// generateObjectList ->
			
		private function searchModelColumn($models,$columnName){
			foreach($models as $model=>$info){
				$id=array_search($columnName,$info['columns']);
				if ($id!==false) return array("pos"=>$id,"model"=>$info);
			}
			return null;
		}
		
		private function replaceWidth($widths,$string,$num){
			if (isset($widths[$num]) && $widths[$num]!='*'){ 
			 	return str_replace('{width}','width:'.$widths[$num].';',$string);
			}
			return str_replace('{width}','',$string);
		}
		
		public function generateGrid()
		{
			ob_start();$n=$this->generateListElement();$content=ob_get_clean();
			$content=preg_replace("/\[\?php/","<?php",$content);
			$content=preg_replace("/\?\]/","?>",$content);
			//echo $content;
			file_put_contents(real($this->dir."/views/generator/".$this->model."/list/element/".$this->generator.".php"),$content);
			chmod(real($this->dir."/views/generator/".$this->model."/list/element/".$this->generator.".php"),0666);
			
			ob_start();$this->generateListHeaders();$content=ob_get_clean();
			$content=preg_replace("/\[\?php/","<?php",$content);
			$content=preg_replace("/\?\]/","?>",$content);
			//echo $content;
			file_put_contents(real($this->dir."/views/generator/".$this->model."/list/headers/".$this->generator.".php"),$content);
			chmod(real($this->dir."/views/generator/".$this->model."/list/headers/".$this->generator.".php"),0666);
						
			ob_start();$this->generateListGrid($n);$content=ob_get_clean();
			$content=preg_replace("/\[\?php/","<?php",$content);
			$content=preg_replace("/\?\]/","?>",$content);
			//echo $content;
			file_put_contents(real($this->dir."/views/generator/".$this->model."/list/grid/".$this->generator.".php"),$content);
			chmod(real($this->dir."/views/generator/".$this->model."/list/grid/".$this->generator.".php"),0666);
		}
		
		public function generateTemplates($dir){
			
			$templateList="/templates/list.php";
			$templateForm="/templates/form.php";
			
			if (!file_exists(real($dir.$templateList)))
			{
				ob_start();
				$this->generateListTemplate();
				$contentList=ob_get_clean();
				$contentList=preg_replace("/\[\?php/","<?php",$contentList);
				$contentList=preg_replace("/\?\]/","?>",$contentList);
				
				file_put_contents(real($dir.$templateList),$contentList);
				chmod(real($dir.$templateList),0666);
			}
			
			if (!file_exists(real($dir.$templateForm)))
			{
				ob_start();
				$this->generateFormTemplate();
				$contentForm=ob_get_clean();
				$contentForm=preg_replace("/\[\?php/","<?php",$contentForm);
				$contentForm=preg_replace("/\?\]/","?>",$contentForm);
				
				file_put_contents(real($dir.$templateForm),$contentForm);
				chmod(real($dir.$templateForm),0666);
			}
		}
		
                public function generateValidators($url){
                    if ($this->ajax_validators && !empty($this->validators)):
                    ob_start();?><script type="text/javascript" src="/js/jquery/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
       
       $("form[name='<?php echo $this->formName; ?>']").validate({
          rules: {
              <?php 
                    $val2=array();
                    $messages=array();
                    foreach($this->validators as $key=>$value){
                        $str="'".$this->formName."[{$key}]' : {\n\t\t\t";
                        $val=array();
                        foreach($value as $validator=>$valor) {
                            if (is_array($valor)) {$valor=$valor[0];$messages[$key][$validator]=$valor[1];}
                            else $messages[$key][$validator]="<?php echo addslashes(Viewer::_echo('validator:{$validator}')); ?>";
                            $val[]="'{$validator}': ".var_export($valor,true);
                        }
                        $str.=implode("\n\t\t\t,",$val);
                        $str.="}";
                        $val2[]=$str;
                    } 
                    echo implode(",\n\t\t",$val2);
                    ?>
              
          },
          messages:{
              <?php 
                    $val2=array();
                    
                    foreach($messages as $key=>$value){
                        $str="'".$this->formName."[{$key}]' : {\n\t\t\t";
                        $val=array();
                        $messages=array();
                        foreach($value as $v=>$valor) {
                            $val[]="'{$v}': '{$valor}'";
                        }
                        $str.=implode("\n\t\t\t,",$val);
                        $str.="}";
                        $val2[]=$str;
                    } 
                    echo implode(",\n\t\t",$val2);
                    ?>
          }
       }); 
    });
</script>

                    <?php
                    $content=ob_get_clean();
                    file_put_contents($url,$content);
                    chmod($url,0666);
                    endif;
                }
                
		public function generateActions($is_plugin)
		{
			$rootDir=($is_plugin)?'/plugins/':'/apps/';
			$app=($this->app!==null)?$this->app:$this->appmodel;

			$dir=real($this->ROOT.$rootDir.$app."/modules/".$this->module);
			
			$generator=$this->generator;
			$module=$this->module;
			
			ob_start();$this->generateMethods(($is_plugin)?$app:null);$content=ob_get_clean();
			$content=preg_replace("/\[\?php/","<?php",$content);
			$content=preg_replace("/\?\]/","?>",$content);
			
			file_put_contents(real($dir."/actions.{$generator}.php"),$content);
			chmod(real($dir."/actions.{$generator}.php"),0666);
			
			if (!file_exists(real($dir."/actions.class.php")))
			{
				$sufix=$is_plugin?ucfirst($app):'';

				ob_start();
				echo "<?php\n".
				"class ".ucfirst($module).$sufix."Actions extends Auto".ucfirst($generator).$sufix."Actions\n".
				"{\n".
				"\tpublic function executeIndex(\$request)\n".
				"\t{\n\t\t\$this->forward('".$module."','list');\n\t}\n}\n?>";
				$content=ob_get_clean();
				
				file_put_contents(real($dir."/actions.class.php"),$content);
				chmod(real($dir."/actions.class.php"),0666);
			}
			
			$this->generateTemplates($dir);
		}
		
		public function generateListElement($headers=false)
		{
			$refmodel=$this->models[$this->referenceClass];
			$widths=$this->widths;
			$visibleCols=0;
								
			if ($headers){
				$open_td='<th class="list_header" style="{width}">';
				$close_td="</th>";
				$this->nextSorting();
				
				$array_shift=false;
				if (	(isset($this->show_numbers) && !empty($this->show_numbers)) ||
						(isset($this->batch_actions) && !empty($this->batch_actions))){ 
					echo $this->replaceWidth($widths,$open_td,0).$close_td;
					array_shift($widths);
				}
				
			}
			else {
				$open_td='<td style="padding:0 2px;{width}">';
				$close_td="</td>";
				$array_shift=false; 
				$strpks=array();
				if (!empty($refmodel['pks'])) {
					foreach($refmodel['pks'] as $pk){ $strpks[]="\$vars['entity']['{$pk}']";}
					$params="array('values'=>\$vars['entity'],'params'=>array('pks'=>".implode(".\"/\".",$strpks)."))";
				}else {$params="array('values'=>array(), 'params'=>array())";}
			
				if (isset($this->batch_actions) && !empty($this->batch_actions)){
					$bb='<span style="float:left;">'.
					'[?php echo Viewer::view("input/checkboxes",'.
					'array("class"=>"batch_checkbox",'.
					//'"internalname"=>"'.$this->referenceClass.'",'.
					'"internalname"=>"'.$this->referenceClass.'[".($vars[\'rownum\']+$vars[\'offset\']+1)."]","multiple"=>false,'.
					'"options"=>array('.implode(".'/'.",$strpks).'=>\'\')'.
					'));?]</span>';
					
					//$bb='<input type="checkbox" class="batch_checkbox" name="'.$this->referenceClass.'[[?php echo $vars[\'rownum\']?]]" value="[?php echo '.implode(".'/'.",$strpks).'; ?]" /></div>';
				}else $bb='';
					
				if (isset($this->show_numbers) && !empty($this->show_numbers)){ 
					$array_shift=true;
					echo $this->replaceWidth($widths,'<td class="show_number" style="{width}">',0).'<div>'.$bb.'<span>[?php echo str_pad($vars[\'rownum\']+$vars[\'offset\']+1,6, "0", STR_PAD_LEFT); ?]</span></div></td>';
					$visibleCols++;
				}else if ($bb!='') {
					$array_shift=true;
					echo $this->replaceWidth($widths,'<td style="{width}">'.$bb.'</td>',0);
					$visibleCols++;
				}
			}
			
			
			
			foreach($this->columns as $numcolumn=>$column) // todas las columnas del render!
			{
			 	$tmp=$this->searchModelColumn($this->models,$column);
			 	$model=$tmp['model'];
				$credentials=false;
				if (isset($this->credentials[$column]))
			 	{
			 		$todas_crenden=Session::parseCredentials($this->credentials[$column]);
	 				?>[?php if (<?php echo $todas_crenden; ?>){ ?]<?php
			 		$credentials=true;
			 	}
			 	
			 	//echo "\n...... $column .....\n";
			 	//echo _r($model);
				if (!is_array($column)) 
			 	{
			 		if ($array_shift) {array_shift($widths);$array_shift=false;}
			 		echo $this->replaceWidth($widths,$open_td,$numcolumn)."\n";
			 		$visibleCols++;
			 		//echo "\n".$model['types'][$column];
		 		  	//if (in_array($column,$model['columns'])) //existe en el modelo de la base de datos
		 		  	//$id=array_search($column,$model['columns']);
					if ($model!=null && isset($model['types'][$column]))
		 			{
			 			
		 				if ($headers){
			 				
			 				switch($model['types'][$column]['type'])
				 			{
				 				case 'text': 	// los campos text no se deberian ordenar
				 								// ademas, los campos long en oracle no se pueden ordenar.
				 			?>[?php echo Viewer::_echo("<?php echo $model['phpnames'][$tmp['pos']]; ?>");?]<?php		
				 				break; 
								default:
									
						
									$labelcol=isset($this->labels[$column])?$this->labels[$column]:$column;
							
								 ?>	[?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=<?php echo $model['table'].".".$column; ?>&sort_type='.nextSorting('<?php echo $model['table'].".".$column; ?>',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("<?php echo $labelcol; ?>"))); ?]
							<?php
								break;
							}
						}else {
							switch($model['types'][$column]['type'])
				 			{
				 				case 'date':
				 					if (isset($this->options['format'][$model['table'].".".$column])){
				 						?>[?php if ($vars['entity']['<?php echo $column; ?>']!=null)<?php 
										?>echo utf8_encode(strftime('<?php echo $this->options['format'][$model['table'].".".$column]; ?>',$vars['entity']['<?php echo $column; ?>']));?]<?php
				 					}else if ($model['types'][$column]['format']!==null){
				 						?>[?php if ($vars['entity']['<?php echo $column; ?>']!=null)<?php  
										?>echo utf8_encode(strftime('<?php echo $model['types'][$column]['format'] ?>',$vars['entity']['<?php echo $column; ?>']);?]<?php 
									}else{
										?>[?php echo $vars['entity']['<?php echo $column; ?>'];?]<?php 
									}
				 					break;
								default:
				 					if (isset($this->options['options'][$model['table'].".".$column])){
										?>[?php if ($vars['entity']['<?php echo $column; ?>']!=null) echo $vars['params']['options']['<?php echo $model['table'].".".$column; ?>'][$vars['entity']['<?php echo $column?>']];?]<?php
									}else if (isset($this->options['options_values'][$model['table'].".".$column])){
										?>[?php if ($vars['entity']['<?php echo $column; ?>']!=null) echo $vars['params']['options_values']['<?php echo $model['table'].".".$column; ?>'][$vars['entity']['<?php echo $column?>']];?]<?php
									}else{
										?>[?php echo (isset($vars['entity']['<?php echo $column; ?>']))?$vars['entity']['<?php echo $column; ?>']:'';?]<?php
									}
				 					
				 					break; 
									
							}
						}
					  }else{
					  	if ($headers){
					  		$labelcol=isset($this->labels[$column])?$this->labels[$column]:$column;	
					  	
					  		?>[?php /* echo Viewer::_echo("<?php echo $labelcol; ?>"); */ ?]<?php
					  		?>[?php $label=Viewer::_echo("<?php echo $labelcol; ?>");
				 					nextSorting('<?php echo $column; ?>',$img,$vars);
				 					echo Viewer::view("output/text",array("img"=>$img,"value"=>$label));
				 			?]<?php
					  		 
					  	}else{
			  				if (isset($this->options['options'][$column])){
								?>[?php if ($vars['entity']['<?php echo $column; ?>']!=null) echo $vars['params']['options']['<?php echo $column; ?>'][$vars['entity']['<?php echo $column?>']];?]<?php
							}else if (isset($this->options['options_values'][$model['table'].".".$column])){
								?>[?php if ($vars['entity']['<?php echo $column; ?>']!=null) echo $vars['params']['options_values']['<?php echo $column; ?>'][$vars['entity']['<?php echo $column?>']];?]<?php
							}else{
								?>[?php echo $vars['entity']['<?php echo $column; ?>'];?]<?php
							}
						}
					  }
					 echo $close_td."\n"; 
				}
				
				if ($credentials) { ?>[?php } ?]<?php }
				
			}
			
			if (isset($this->row_actions) && count($this->row_actions)>0){
			// si estamos en el template PRINT no queremos visualizar las acciones
			?> [?php if (Viewer::getGlobalTemplate()!=Config::get('settings:views:global_template_print_value')){ ?]<?php
			?><!-- actions for <?php echo $this->referenceClass; ?> --><?php echo "\n";
				if ($headers){ 
					echo $this->replaceWidth($widths,$open_td,-1)."\n";
					?><b>[?php echo Viewer::_echo("actions"); ?]</b><?php
					echo "\n".$close_td; 
				}else { 
					?><td class="element_list_actions"><?php echo "\n";
					/*$strpks=array();
					if (!empty($refmodel['pks'])) {
						foreach($refmodel['pks'] as $pk){ $strpks[]="\$vars['entity']['{$pk}']";}
						$params="array('params'=>".implode(".\"/\".",$strpks).")";
					}else {$params="array('params'=>array())";}*/
					 
					?>[?php echo Viewer::view('<?php echo $this->model; ?>/list/actions/rows/<?php echo $this->generator; ?>',<?php 
					echo $params; ?>,$vars['viewtype']);?]<?php
					echo "\n";?></td><?php 
					$visibleCols++;	
				}
				
			?> [?php } ?]<?php 	 
			}
			return $visibleCols;
		}
		
		public function nextSorting()
		{
			?>
			[?php   if (!function_exists('nextSorting')){
						function nextSorting($col,&$img='',$vars)
						{
							if (isset($vars['sort'][$col]))
							{
								switch($vars['sort'][$col])
								{
									case 'asc': 	$img='<img src="/img/icon/sort_alphabet.png" />';return 'desc';
									case 'desc': 	$img='<img src="/img/icon/sort_alphabet_descending.png" />';return '';
									case '': 		$img='';return 'asc'; 
								}
							}
							else {$img='';return 'asc';}
						}
					}
					
					$img='';
			?]
			<?php 
		}
		
		public function generateListHeaders()
		{
			$this->generateListElement(true);
		}
		
		public function generateListGrid($visibleCols)
		{
			$batch_options=$this->batch_actions;
			
			if (!empty($batch_options)){
				//$all_batch_options='';
				
				?>
				[?php $routing=Controller::getInstance()->getRoute(); ?]
				<script type="text/javascript">
					var executeAction=function(action,domElem,elem){
						var checks=$('#batch_form<?php echo $this->referenceClass; ?> [value!="<?php echo Query::NULL; ?>"]').serialize();
						var ok=true;
						
						if (eval("typeof(confirmReturn"+domElem.attr('id')+")")!=='undefined')
							ok=eval("confirmReturn"+domElem.attr('id'));
													
						if (ok){ 
							if (checks.length>0){
								var form=$('#<?php echo $this->formName."Filter"; ?>');
								// set the href to the action form
								var url='[?php echo $routing["file"]."/".$routing["module"]."/"; ?]';
								
								var re = new RegExp(url);
  								if (action.match(re)) url=action+'?'+checks;
  								else url+=action+'?'+checks;
								
								url=unescape(url)
								
								if (domElem.attr('target')!='') form.attr('target',domElem.attr('target'));
								
								form.attr('action',url);
								// remove href attribute
								$(this).removeAttr('href');
								// submit filter form
								form.submit();
							}else{
								alert('[?php echo addslashes(Viewer::_echo("error:notselection")); ?]');
								if (elem) elem.val("");
							}
						}
					}
					
					$(document).ready(function(){
						$('.batch_option').each(function(i,domElem){
							var elem=$(domElem);
							
							if (elem.is('select')) {	// es seleccionable
								elem.removeAttr('onChange');
								elem.change(function(){
									executeAction(elem.val(),elem,elem);
								});
							}else if (elem.is('input') || elem.is('button')){	// es boton
								elem.removeAttr('onClick');
								elem.click(function(){
									executeAction(elembutton.val(),elem);
								});
							}else if (elem.is('a')){	// es link
								var href=elem.attr('href');
								
								if (href.length==0) href=elem.attr('onclick');
								else elem.removeAttr('href');
								if (href.length==0) elem.removeAttr('onclick');
								
								elem.click(function(){
									executeAction(href,elem);
								});
							}
						});
						
						$('.navigationBatchItems').children().each(function(i,domElem){
							var elem=$(domElem);
							elem.attr('class','navigationBatchItem');
						});	
					});
				</script>
				<?php
			}//else $all_batch_options='';

			?><div class="admin_statistics">
				[?php 
					$numVisibleCols=<?php echo $visibleCols; ?>;
					
					if (!isset($vars['viewtype_element'])) $vars['viewtype_element']=$vars['viewtype'];
					
					if (isset($vars['title'])) echo Viewer::title($vars['title']); 
					
					echo Session::getInstance()->getFlash('<?php echo $this->model.ucfirst($this->generator)."Form_info"; ?>');
					
					$pagination=true;
					if (isset($vars['pagination']['view_options'])){
						if (!is_array($vars['pagination']['view_options'])) $pagination=$vars['pagination']['view_options'];
						else if (isset($vars['pagination']['view_options']['active'])) $pagination=$vars['pagination']['view_options']['active'];
					}
					
					$sorting=array("js"=>"class=\"sorting\"","value"=>Viewer::_echo("button:clear:sort"),"action"=>$vars['clearsort']);
					
					$nav_batch= Viewer::view('<?php echo $this->model; ?>/list/actions/batch/<?php echo $this->generator; ?>',array('class'=>'batch_option'),$vars['viewtype']);
				?]	
				<?php if (!empty($batch_options)): ?>
				<div class="navigationBatchItems">[?php echo $nav_batch; ?]</div>
				<?php endif; ?>
				[?php		
					if ($pagination)
					{
						$offset=intval($vars['pagination']['offset']);
						
						$nav=	Viewer::view('navigation/pagination',array('action'=>'filter', 
																		'offset' => $offset,
																		'filterName'=>'<?php echo $this->formName."Filter"; ?>',
																		'count' => $vars['pagination']['count'],
																		'limit' => $vars['pagination']['limit'],
																		'view_options'=>$vars['pagination']['view_options'],
																		'with_sorting'=>$sorting));
						
						echo $nav;
					}
					else {
						echo "<div class=\"pagination\">".Viewer::view('output/link',$sorting)."</div>";
						$offset=null;
					}
					
					<?php 
						if (isset($this->options['format'])){
							$value=var_export($this->options['format'],true);
							$value=preg_replace("/_echo\((.*)\)/","'.Viewer::_echo('$1').'",$value);
							$value=preg_replace("/_title\((.*)\)/","'.Viewer::title('$1').'",$value);
							//eval("\$opts={$value};");
							$opts3=$value;
						}
						
						$opts=array();
						if (isset($this->options['options'])){
							$value=var_export($this->options['options'],true);
							$value=preg_replace("/_echo\((.*)\)/","'.Viewer::_echo('$1').'",$value);
							$value=preg_replace("/_title\((.*)\)/","'.Viewer::title('$1').'",$value);
							//eval("\$opts={$value};");
							$opts=$value;
						}
						
						$opts2=array();
						if (isset($this->options['options_values'])){
							$value=var_export($this->options['options_values'],true);
							$value=preg_replace("/_echo\((.*)\)/","'.Viewer::_echo('$1').'",$value);
							$value=preg_replace("/_title\((.*)\)/","'.Viewer::title('$1').'",$value);
							//eval("\$opts={$value};");
							$opts2=$value;
						}
					
					//var_dump($this->options);
					?>
					
					$grid_params=array( <?php if (isset($this->options['options'])) { ?> 'options'=><?php echo $opts.","; }?>
										<?php if (isset($this->options['options_values'])) { ?> 'options_values'=><?php echo $opts2.","; }?>
										<?php if (isset($this->options['format'])) { ?> 'formats'=><?php echo $opts3; }?>
									   );
					
					?]
					<script type="text/javascript">
						 
						$(document).ready(function(){
							// search the output links of the list headers to change action
							$('.list_action_link').click(function(){
								// get the filter form
								var form=$('#<?php echo $this->formName."Filter"; ?>');
								// get the href 
								var href=$(this).attr('href');
								// set the href to the action form
								form.attr('action',href);
								// remove href attribute
								$(this).removeAttr('href');
								// submit filter form
								form.submit();
							});
						});
					</script>
					<?php 
						if (!empty($this->widths) && !in_array('*',$this->widths)){
							$width_grid=array_sum(array_map('intval',$this->widths));
							$width_grid_style='width:'.$width_grid.'px;overflow-x:auto;';
						}else $width_grid_style='';
						
						$style='style="';
						if (isset($this->scroll['x'])){
							$scx='';
							$scrollx=explode(" ",$this->scroll['x']);
							$s1=intval($scrollx[0]);
							if ($s1!=0){
								$scx='width:'.$scrollx[0].';overflow-x:';
								if (isset($scrollx[1])) $scx.=$scrollx[1];else $scx.='auto;';
							}else if (is_bool($scrollx[0]) && $scrollx[0]){
								$scx='overflow-x:scroll;';
							}
							$style.=$scx;	
						}else $style.=$width_grid_style;
						
						if (isset($this->scroll['y'])){
							$scrolly=explode(" ",$this->scroll['y']);
							$s1=intval($scrolly[0]);
							$scy='';
							if ($s1!=0){
								$scy='height:'.$scrolly[0].';overflow-y:';
								if (isset($scrolly[1])) $scy.=$scrolly[1];else $scy.='auto;';
							}else if (is_bool($scrolly[0]) && $scrolly[0]){
								$scy='overflow-y:scroll;';
							}
							$style.=$scy;
						}
						$style.='"';
					
					?>
					<form <?php echo $style; ?> id="batch_form<?php echo $this->referenceClass; ?>">
					<table style="<?php echo $width_grid_style; ?>">
				    	<tr>
							[?php echo Viewer::view('<?php echo $this->model; ?>/list/headers/<?php echo $this->generator; ?>',array('sort'=>$vars['sort']),$vars['viewtype_element']); ?]
						</tr>
				        [?php 
				        	$maxdata=count($vars['data']);
				        	if ($maxdata>0){
				            for($j=0;$j<?php echo "<"; ?>$maxdata;$j++): ?]
							[?php $item=$vars['data'][$j]; //echo Viewer::object("<?php echo $this->model; ?>",$vars['viewtype']); 
								<?php 
									$model=$this->models[$this->referenceClass]; // el model=clase
									$condi=array();
									if (isset($model['pks'])){
										foreach($model['pks'] as $pk)
										$condi[]="\$vars['selected']['".$this->model.".".$pk."']==\$item['".$pk."']
													";
										$conditions="\n&& ".implode(" && ",$condi).")\n";
									}else $conditions=")\n";
								?>
								
								if (isset($vars['selected']['<?php echo $this->model.".".$model['pks'][0]; ?>'])<?php echo $conditions; ?>
									$class="selected";
								else 
									$class=(($j%2)==0)?'odd':'even'; 
							?]
							
							<tr class="[?php echo $class; ?]">
							[?php echo Viewer::view("<?php echo $this->model; ?>/list/element/<?php echo $this->generator; ?>",
											array(	'entity'=>$item, 'viewtype'=>$vars['viewtype'], 'rownum'=>$j,
													'offset'=>$offset, 'params'=>$grid_params), 
											$vars['viewtype_element']);?]
											
							</tr>
						[?php endfor; } ?]
						[?php if ($vars['pagination']['limit']==null):?]
						<tr style="background:#E4E4E4;padding:0 5px;">
							<td colspan="[?php echo $numVisibleCols; ?]" style="text-align:right">[?php echo Viewer::_echo('total').": ".count($vars['data'])." ".Viewer::_echo('rows');?]</td>
						</tr>
						[?php endif; ?]
					</table>
				    </form>
				    <div  style="margin: 10px 0;">
					<?php if (!empty($batch_options)): ?>
				    	<div class="navigationBatchItems">[?php echo $nav_batch; ?]</div>
					<?php endif; ?>
					[?php 
						if ($pagination) echo $nav;else echo '<div class="pagination"></div>';  
						//echo Viewer::view('<?php echo $this->model; ?>/list/actions/buttons/<?php echo $this->generator; ?>',array(),$vars['viewtype']); 
					?]
					</div>
					<div style="clear:both;"></div>
				</div>
				  
				<?php
		}
		
		/////// MODULE GENERATORS
		public function generateFormTemplate()
		{
			?>[?php
			$formView='<div class="contentWrapper">'.$form.'</div>';
			
			switch($titleMode){
				case "show":	$title=Viewer::_echo('form:element:show');break;
				case "new": 	$title=<?php echo $this->titleNew;?>;break;
				case "edit":	$request=Request::getInstance();
							<?php	
								$model=$this->models[$this->referenceClass]; // el model=clase
								$condi=array();
								$str='';
								foreach($model['pks'] as $pk) {
									$str.=".' '.\$request->getParameter('".$this->model.".".$pk."')";
								}
							?> $title=<?php echo $this->titleEdit.$str; ?>;
							break;
			}
			
			$actions='<div class="minicontentWrapperActions">'.$actions.'</div>';
			
			?]<?php
			switch($this->layoutForm){
				case 'two_column_left_sidebar': ?>
			[?php
				echo Viewer::layout("two_column_left_sidebar",$menus,Viewer::title($title).$formView.$actions);
			?]
			<?php
				break;
				case 'one_column': 
				default: ?> 
			[?php
				echo Viewer::layout("one_column",Viewer::title($title).$actions.$formView.$actions);
			?]
			<?php
			}
		}
		
		public function generateListTemplate()
		{
			?>[?php
				if (!empty($filters)){
				$search=Viewer::view("canvas_header/form_filters",
									array(	"content"=>$filters,
											"default_display"=>(!isset($params['showgrid']) || !$params['showgrid'])?'block':'none',
											"id"=>Controller::getInstance()->getRoute('module')));
				}else $search='';
				
				if (!isset($params['showgrid']) || $params['showgrid'])
					$content=Viewer::view("<?php echo $this->model; ?>/list/grid/<?php echo $this->generator; ?>",$params,$params["viewtype"]);
				else $content='';
				
				$buttons='<div class="minicontentWrapperActions">'.$buttons.'</div>';
				$title=Viewer::title(<?php echo $this->titleList; ?>);
				?]<?php
				
			switch($this->layoutList){
				case 'two_column_left_sidebar': 
				?>[?php	echo Viewer::layout("two_column_left_sidebar", $menus,$title.$buttons.$search.$content); ?]<?php
				break;
				case 'one_column': 
				default: ?>[?php echo Viewer::layout("<?php echo $this->layoutList;?>", $title.$buttons.$search.$content.$buttons); ?]
				<?php
				break; 
			}
			?>
			<script type="text/javascript">
				function openFilters(){
					var targetContent=$('#<?php echo $this->module;?>.collapsable_box');
					$.post('[?php echo Route::url("default/spotlight") ?]?id=<?php echo $this->module;?>&display='+targetContent.css('display'));		
					
					if (targetContent.css('display') == 'none') targetContent.show('fast');
					else targetContent.hide('fast');
				}
			</script>
			<?php 
			
		}
		
		
		public function generateMethods($plugin=null)
		{
			$sufix=($plugin===null)?'':ucfirst($plugin);

?>[?php
	class Auto<?php echo ucfirst($this->generator).$sufix;?>Actions extends Actions
	{	
		protected $VIEWTYPE='<?php echo $this->viewtype; ?>';
		protected $VIEWTYPE_ELEMENT='<?php echo $this->viewtype; ?>';
							
		protected function getLimit(){
			$pager=Session::get("<?php echo $this->generator; ?>.pager");
			$limit=Request::getInstance()->limit;
			if ($limit!=null){
				if ($limit==0) $limit=null;
				$this->setPager(array("limit"=>$limit));
			}else if (isset($pager['limit'])) {
				$limit=$pager['limit'];
			}else $limit=<?php if (isset($this->pagination['active']) && $this->pagination['active']) 
				echo (isset($this->pagination['min'])?$this->pagination['min']:10); else echo "null"; ?>;
			
			return $limit;
		}
		
		protected function getData($request)
		{
			//sort 
			if ($request->sort) $this->setSort($request->sort,$request->sort_type);
			else {
				if (!Session::is_set('<?php echo $this->generator; ?>.sort')) $this->defaultSort(); // por defecto
			}
			// filters
			if (!Session::is_set('<?php echo $this->generator; ?>.filters')){
				$this->clearFilters(); // por defecto
			}
			
			// pager
    		$this->pager = $this->getPager();
    		
    		return $this->buildCriteria();
    	}
		
		protected $default_query=<?php echo ($this->default_query)?'true':'false'; ?>;
		
		//////// PAGER /////////////
		
		protected function getPager(){
		
			$limit=$this->getLimit();
					
			if ($limit!=null)
			{	
				// valores del generador 
				if (!Session::is_set("<?php echo $this->generator; ?>.pager")){
					$this->setPager(array("limit"=>$limit,"offset"=>0,"page"=>null));
				}else if (Request::getInstance()->offset!=null){
					$this->setPager(array("limit"=>$limit,"page"=>null,"offset"=>Request::getInstance()->offset));
				}else if (Request::getInstance()->page!=null){
					$this->setPager(array("limit"=>$limit,"page"=>Request::getInstance()->page-1,"offset"=>Request::getInstance()->page*$limit));
				}
			}
			else if (Session::is_set("<?php echo $this->generator; ?>.pager")) Session::un_set("<?php echo $this->generator; ?>.pager");
			
			$pager=Session::get("<?php echo $this->generator; ?>.pager");
			
			if (!isset($pager['limit'])) {$pager['limit']=$limit;$pager['offset']=0;$pager['page']=0;}
			if ($limit!=$pager['limit']) $this->setPager(array("limit"=>$limit));
			
			return $pager;
		}
		
		protected function setPager($pager=array())
		{
			$old=Session::get("<?php echo $this->generator; ?>.pager");
			if (is_array($old)) $pager=array_merge($old,$pager);
			Session::set("<?php echo $this->generator; ?>.pager",$pager);
		}
		
		protected function getCount(){
			return Session::get("<?php echo $this->generator; ?>.count");
		}
		
		protected function setCount($count=array())
		{
			Session::set("<?php echo $this->generator; ?>.count",$count);
			
		}
		
		///////// SORT /////////////
		
		protected function setSort($column=null,$mode='asc')
		{
			if ($column!=null)
			{
				$sort=$this->getSort();
				$sort[$column]=$mode;
				Session::set('<?php echo $this->generator; ?>.sort', $sort);
			}
		}
		
		protected function defaultSort()
		{
			<?php if ($this->defaultSort!==null){ 
					foreach($this->defaultSort as $sort){ 
			?>$this->setSort(<?php echo $sort; ?>);<?php }	} ?>
			
		}
		
		protected function getSort(){
			return Session::get('<?php echo $this->generator; ?>.sort');
		}
		
		protected function clearSort(){
			
			Session::un_set('<?php echo $this->generator; ?>.sort');
			$this->setPager(array("offset"=>0,"page"=>null));
			//Session::set('<?php echo $this->generator; ?>.sort',array());
			$this->defaultSort();
		}
		
		///////// FILTERS /////////////
		protected $filtersform;
		
		public function executeFilter($request){
            Session::un_set('<?php echo $this->generator; ?>.count');
            $this->setPager(array("offset"=>0,"page"=>null));
            //$this->executeList($request);$this->setTemplate("list");
            <?php if ($this->ajax_list): ?>$request->ajax=true;<?php endif; ?>
            $this->forward("<?php echo $this->module;?>","list");
        }
		
		protected function extraFilters(){}
		
		protected function defaultFilters(){<?php 
			$filters=array();
			$refmodel=$this->models[$this->referenceClass];
			//echo "/* "._r($this)." */";
			
			if (isset($this->options['default'])){
				foreach($this->options['default'] as $key2=>$value){
					$key=explode(".",$key2);
					// si la columna está en filters
					if (in_array($key[1],array_keys($refmodel['filters']))){
						// si es de tipo date y tiene un valor de sydate (en generador!)
						if ($refmodel['types'][$key[1]]['type']=='date' && $value=='sysdate'){
							$value="strftime('".$this->options['format'][$key2]."',time())";
						}else	$value="'{$value}'";
						if (!empty($value)) $filters[]="'{$key[1]}',".$value;
					}
				}
			}
			
			if ($this->defaultFilter!==null) $filters=array_merge_recursive($filters,$this->defaultFilter); 
			foreach($filters as $filter):?>
			
			$this->addFilter(<?php echo $filter; ?>);
			<?php endforeach; ?>
			
		}
		
		protected function getFilters(){
		   return $this->filtersform;
		}
		
		protected function getFiltersFromSession(){
		   $r=Session::get('<?php echo $this->generator; ?>.filters');
		   return (is_array($r)?$r:array());
		}
		
		protected function updateFilters(){
			// recuperamos campos de formulario
			$b=Request::getInstance()->getFormVars('<?php echo $this->model; ?>/<?php echo $this->generator; ?>','Filter');
			
			// si hay algo un filterform (defaultFilters!) se lo asignamos a f
			if (empty($this->filtersform)) $f=array();else $f=$this->filtersform;
			
			if (empty($b)) { // si no hay datos de formulario -> sesion + defaultFilters
				$this->filtersform=array_merge($this->getFiltersFromSession(),$f);
				$this->extraFilters(); // añadimos extraFilters: filtersform = default + sesion + extra
			}
			else{ // si hay datos en el formulario -> form + defaultFilters (actualiza valores form)
				$this->filtersform=array_merge($f,$b);
				Session::un_set('<?php echo $this->generator; ?>.filters'); // borramos de sesion lo que hubiera
				$this->extraFilters(); // añadimos extraFilters: filtersform = default + form + extra
				// subimos a sesion filtersform (nuevos filtros
				Session::set('<?php echo $this->generator; ?>.filters', $this->filtersform);
			}
			
		}
		
		protected function getFilter($column){
			
			$filters=$this->filtersform;
			return (isset($filters[Form::FILTER_PREFIX.$column])?$filters[Form::FILTER_PREFIX.$column]:null);
		}
		
		protected function addFilter($column=null,$value='',$criteria=null){
			if ($column!=null)
			{
				if ($criteria==null) $this->filtersform[Form::FILTER_PREFIX.$column]=$value;
				else {
					if (!is_array($value)) $value=array($value); 
					$value[]=$criteria;
					$this->filtersform[Form::FILTER_PREFIX.$column]=$value;
				}
			}
		}
		
		protected function clearFilters(){
			
			Session::un_set('<?php echo $this->generator; ?>.filters');
			$this->setPager(array("offset"=>0,"page"=>null));
			$this->filtersform=array();
			$this->defaultFilters();
			$this->updateFilters();
			
		}
		
		protected function peerMethod($model=null,$filters=null)
		{
			if ($model==null) $model=new <?php echo $this->referenceClass; ?>Peer();
			return $model->bindQueryFilters("<?php echo $this->query;?>",$filters);
		}
		
		protected function countMethod($model=null,$filters=null)
		{
			$select=$this->peerMethod($model,$filters);	
			<?php if (($this->query)): ?> 
			return $model->doCount("select count(*) {count} from ({$select}) sub1");
			<?php else: ?>
			return $model->doCount($select);
			<?php endif; ?>
		}
		
		protected function buildCriteria()
		{
			$this->updateFilters();
			
			/// extrameos el objeto formulario de los filtros
			$formfilter=Request::getInstance()->getFormObject('<?php echo $this->model; ?>/filters/<?php echo $this->generator; ?>',$this->VIEWTYPE);
			// añadimos los filtros que hemos creado
			$form=$formfilter->addFilters($this->getFilters());
			/// sacamos los objetos de modelo del formulario
			$objects=$form->getModelObjects(true);
			// creamos el string de filtros (.. and ..) que pasaremos a la select
			$filtersWhere=$form->filterForm($objects);
						
			if (isset($this->pager))
			{
				$limit=$this->pager['limit'];
				$offset=$this->pager['offset'];
				$page=$this->pager['page'];
			}else 
			{
				$limit=null;$offset=null;$page=null;
			}
			
			if ($this->default_query || Session::is_set("<?php echo $this->generator; ?>.filters")){
			
			$q=new <?php echo $this->referenceClass; ?>Peer();
			$q->configure();
			<?php if (isset($this->pagination['active']) && $this->pagination['active']){ 
			?>
			$a=$this->countMethod($q,$filtersWhere);
            <?php } 
            ?>
            $data=$q->	select($this->peerMethod($q,$filtersWhere))->
							limit($limit)->
							offset($offset)->
							page($page)->
							//sort('column1','asc','column2','desc',...)->
							sortCriteria($this->getSort())->
							exec(false);
			<?php if (isset($this->pagination['active']) && $this->pagination['active']){ 
			?>
			$this->setCount($a);
            <?php }else{ 
			?>	
			$this->setCount(count($data));
			<?php } ?>
			}else { $this->setCount(0);$data=array();}
			return $data;  
			
		}
		
		//////////// actions EXECUTE 
		
		public function executeClearsort(){
			$this->clearSort();
			$this->forward("<?php echo $this->module;?>","list");
		}
		
		public function executeClearfilters(){
			$this->clearFilters();
			Session::un_set('admder.count');
			$this->forward("<?php echo $this->module;?>","list");
		}
		
		public function executeList($request)
		{
			$data=$this->getData($request);<?php 
			$strPKS='$cur[\''.implode('\'].\'/\'.$cur[\'',$this->models[ucfirst($this->model)]['pks']).'\']'; ?>

			$ids=array();foreach($data as $cur){$ids[]=<?php echo $strPKS; ?>;}
			Session::set("ids:<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$ids);
						
			$count=$this->getCount();
			
			if ($count>0){
				$success=str_replace("{elements}",$count,Viewer::_echo('listcount:success'));
				$ok=$success;
			}else $ok=Viewer::_echo("listcount:noelements");
			
			$request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$ok,true,'reportedcontent_content archived_report');
			
			$pager=$this->getPager();
			
			$this->params=array(
							"pagination"=>array("count"=>$count,
												"page"=>$pager['page'],
												"limit"=>$pager['limit'],
												"offset"=>$pager['offset'],
												"view_options"=><?php echo var_export($this->pagination,true); ?>
												),
							"data"=>$data,
							"showgrid"=>($this->default_query || Session::is_set("<?php echo $this->generator; ?>.filters")),
							"selected"=>$request->getSelectedItem("<?php echo $this->model; ?>/<?php echo $this->generator; ?>"),
							"mode"=>"list",
							"sort"=>$this->getSort(),
							"clearsort"=>"clearsort",												
							"viewtype"=>$this->VIEWTYPE,
							"viewtype_element"=>$this->VIEWTYPE_ELEMENT
						);
			
			///////////
			if (!$request->ajax){
			$this->filters=Viewer::view('<?php echo $this->model; ?>/list/filters/<?php echo $this->generator; ?>',array("values"=>$this->getFilters()),$this->VIEWTYPE);
			<?php switch($this->layoutFilter){
				case 'menu': ?>$filters=$this->filters;<?php break;
				case 'list':
				default: ?>$filters=''; 
			<?php } ?>	
			
			$this->menus=Viewer::view('<?php echo $this->model; ?>/list/actions/menus/<?php echo $this->generator; ?>',array(),$this->VIEWTYPE).
						"<div id=\"owner_block\">".$filters."</div>";
			
			$this->buttons=Viewer::view('<?php echo $this->model; ?>/list/actions/buttons/<?php echo $this->generator; ?>',array(),$this->VIEWTYPE);
                        }else{
                            $content=Viewer::view("<?php echo $this->model; ?>/list/grid/<?php echo $this->generator; ?>",$this->params,$this->VIEWTYPE);
                            echo json_encode(array('error'=>0,'count'=>$count,'content'=>$content));
                            $this->setTemplate(false);
                        }
		}
		
		public function executeNew($request)
		{
			$ok=Viewer::_echo('form:infomessage');
			$request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$ok,true,'reportedcontent_content archived_report_blue');
			
			<?php if ($this->ajax_form): ?>$vars=array();
			<?php else: ?>
			$vars=$request->getFormVars("<?php echo $this->model; ?>/<?php echo $this->generator; ?>");
			<?php endif; ?>
			$this->titleMode="new";
			$this->form=<?php if ($this->ajax_validators): ?>Viewer::view("<?php echo $this->model; ?>/form/validators/<?php echo $this->generator; ?>",array(),$this->VIEWTYPE).<?php endif; 
                        ?>Viewer::view("<?php echo $this->model; ?>/form/new/<?php echo $this->generator; ?>",
				array(	"values"=>$vars,
						"mode"=>"new",
						"viewtype"=>$this->VIEWTYPE,
						"params"=>array("action"=>"<?php echo $this->module; ?>/create")),$this->VIEWTYPE);
			
			
			$this->actions=Viewer::view('<?php echo $this->model; ?>/form/actions/buttons/<?php echo $this->generator; ?>',array("values"=>$vars),$this->VIEWTYPE);
			$this->menus=Viewer::view("<?php echo $this->model; ?>/form/actions/menus/<?php echo $this->generator; ?>",array("values"=>$vars),$this->VIEWTYPE);
			$this->setTemplate("form");
			
			
		}

		protected function nextPrevious(&$vars){
			$ids=Session::get("ids:<?php echo $this->model; ?>/<?php echo $this->generator; ?>");<?php 
					$strPKS=array();
					$pks=$this->models[ucfirst($this->model)]['pks'];
					foreach($pks as $pk) $strPKS[]='$vars[\''.$this->model.'.'.$pk.'\']';?>

			if (count($ids)>0){
				$order=array_search(<?php echo implode('.\'/\'.',$strPKS); ?>,$ids);

				$previous=$ids[max(0,$order-1)];
				$next=$ids[min(count($ids)-1,$order+1)];

				if ($order>0) Session::addCredential('has_previous');
				else Session::removeCredential ('has_previous');

				if ($order<(count($ids)-1)) Session::addCredential('has_next');
				else Session::removeCredential('has_next');

				$vars['previous']=$previous;
				$vars['next']=$next;
			}else{
                            Session::removeCredential('has_previous');
                            Session::removeCredential('has_next');
                        }
		}

		public function executeEdit($request)
		{
			$ok=Viewer::_echo('form:infomessage');
			$request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$ok,true,'reportedcontent_content archived_report_blue');
			
			$vars=$request->getRouteObjectVars("<?php echo $this->model; ?>/<?php echo $this->generator; ?>","edit");
			$this->nextPrevious($vars);

			$this->titleMode="edit";
			$this->form=<?php if ($this->ajax_validators): ?>Viewer::view("<?php echo $this->model; ?>/form/validators/<?php echo $this->generator; ?>",array(),$this->VIEWTYPE).<?php endif; 
                        ?>Viewer::view("<?php echo $this->model; ?>/form/edit/<?php echo $this->generator; ?>",
				array(	"values"=>$vars,
						"mode"=>"edit",
						"viewtype"=>$this->VIEWTYPE,
						"params"=>array("action"=>"<?php echo $this->module; ?>/update")),$this->VIEWTYPE);
			
			$this->actions=Viewer::view('<?php echo $this->model; ?>/form/actions/buttons/<?php echo $this->generator; ?>',array("values"=>$vars),$this->VIEWTYPE);
			$this->menus=Viewer::view("<?php echo $this->model; ?>/form/actions/menus/<?php echo $this->generator; ?>",array("values"=>$vars),$this->VIEWTYPE);	
			
			$this->setTemplate("form");
			
		}
		
		public function executeShow($request)
		{
			$vars=$request->getRouteObjectVars("<?php echo $this->model; ?>/<?php echo $this->generator; ?>","edit");
			
			$this->titleMode="show";
			$this->form=Viewer::view("<?php echo $this->model; ?>/form/show/<?php echo $this->generator; ?>",
				array(	"values"=>$vars,
						"mode"=>"show",
						"viewtype"=>$this->VIEWTYPE,
						"params"=>array("action"=>"<?php echo $this->module; ?>/update")),$this->VIEWTYPE);
			
			$this->actions=Viewer::view('<?php echo $this->model; ?>/form/actions/buttons/<?php echo $this->generator; ?>',array(),$this->VIEWTYPE);
			$this->menus=Viewer::view("<?php echo $this->model; ?>/form/actions/menus/<?php echo $this->generator; ?>",array("values"=>$vars),$this->VIEWTYPE);	
			
			$this->setTemplate("form");
			
		}
		
		public function executeDelete($request)
		{
			$vars=$request->getRouteObjectVars("<?php echo $this->model; ?>/<?php echo $this->generator; ?>");
			$q=new <?php echo $this->referenceClass; ?>Peer();
			$pks=$q->extractPK($q->getColumns());
			$php=$q->getPHPNames();
			
			
			$q2=new <?php echo $this->referenceClass; ?>();
			foreach($pks as $pk) $q2->{$php[$pk]}=$vars['<?php echo $this->model; ?>.'.$pk];
			
			$error=false;
			if (!($q2->delete($error))){
				$request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$error,false,"reportedcontent_content active_report");
				$error=true;
			}
			
			if (!$error){
				$ok=Viewer::_echo("form:deletesuccess");
				$request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$ok,false,"reportedcontent_content archived_report");
			}
			
			$request->setFormVars("<?php echo $this->model; ?>/<?php echo $this->generator; ?>");
			
			$this->redirectSave();
		}
		
		protected function redirectSave($action='<?php echo $this->module; ?>/list',$pks=array(),$message=null,$vars=array(),$errores=array(),$formName=null,$redirect=null){
			$sufix=empty($pks)?'':('/'.implode('/',$pks));
		    // hay errores
		    $errors=array();
		    <?php if ($this->ajax_form): ?>                                        
			if (!empty($vars)){
				foreach ($vars as $i=>$field){ 
					if (isset($errores[$formName.'['.$i.']'])){                             
						$errors[]=array('name'=>$i,'val'=>$errores[$formName.'['.$i.']']);                                                        
					}                         
				}                                         
			}

		    if (empty($errors)) $params=array('error'=>0,'message'=>$message);
		    else $params=array('error'=>1,'message'=>$message,'errors'=>$errors);                            

		    if (!$redirect) $params['redirect'] = Route::url($action.$sufix);

		    echo json_encode($params);
		    exit;
			<?php else: ?>
		    $session=Session::getInstance();
		    if (!empty($vars)){
				foreach ($vars as $i=>$field){ 
					if ($session->is_setFlash("{$formName}[{$i}]")){ 
						$errors[]=array('name'=>$i,'val'=>$session->getFlash("{$formName}[{$i}]"));
						$session->un_setFlash("{$formName}[{$i}]");
					}
				}  
			}
		    $this->redirect($action.$sufix);
		    <?php endif; ?>
		}
		                
		protected function saveForm($request,$action='new',$redirectIfOk=null,$gen=null,$vars=null){
			if ($gen==null) $gen=$action;
			if ($redirectIfOk==null) $redirectIfOk=$action;
			$form=$request->getFormObject('<?php echo $this->model; ?>/'.$gen.'/<?php echo $this->generator; ?>',$this->VIEWTYPE);
			if ($vars===null) $vars=$request->getFormVars('<?php echo $this->model; ?>/<?php echo $this->generator; ?>'); 
			$form->bind($vars);
			$error=Viewer::_echo("form:someerrors");
			if ($form->isValid($errores))
			{
				$error=false;
				$pks=array();
				$objects=$form->getModelObjects();
				$error=$form->saveForm('<?php echo $this->referenceClass; ?>',$objects,$pks,($action=='new'));

				if (!$error)
				{
					$ok=Viewer::_echo("form:savesuccess");
					$request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$ok,false,"reportedcontent_content archived_report"<?php if ($this->ajax_form): ?>,($redirectIfOk==$action)<?php endif;?>);
					$request->setFormVars("<?php echo $this->model; ?>/<?php echo $this->generator; ?>");
					$this->redirectSave('<?php echo $this->module; ?>/'.$redirectIfOk,$pks,$ok,array(),array(),null<?php if ($this->ajax_form): ?>,($redirectIfOk==$action)<?php endif;?>);
				}
			}

			$request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$error,false,"reportedcontent_content active_report");
			$request->setFormVars("<?php echo $this->model; ?>/<?php echo $this->generator; ?>");
			$this->redirectSave('<?php echo $this->module; ?>/'.$action,null,$error,$vars,$errores,$form->getName());
                        
		}
                
		public function executeCreate($request){
				$this->saveForm($request,'new','edit');
				return Viewer::NONE;
		}
		
		public function executeUpdate($request){
				$this->saveForm($request,'edit');
				return Viewer::NONE;
		}
                                
                public function executeBatchdelete($request){
                        
			Controller::unregisterHook("log","access",array("Logger","execute"));
                        $this->setDebug(false);
                                          
                        <?php 
                            $class=ucfirst($this->model);
                            $pks=$this->models[$class]['pks']; 
                        ?>    
                            
                        if ($request->getParameter('<?php echo $class;?>')!=null){ 
                            $ids = $request->getParameter('<?php echo $class;?>'); // array ids url
                        
                            $act = new <?php echo $class;?>();
                            $errors=array();
                            foreach ($ids as $id)
                            {
                                <?php
                                if (count($pks)>1){ 
                                    ?>$vpk=explode('/',$id);<?php
                                    foreach($pks as $k=>$pk){
                                        $index=array_search($pk,$this->models[$class]['columns']);
                                        echo '$act->'.$this->models[$class]['phpnames'][$index].'=$vpk['.$k.'];';
                                    }
                                }else{ 
                                    $index=array_search($pk,$this->models[$class]['columns']);
                                    echo '$act->'.$this->models[$class]['phpnames'][0].'=$id;';
                                } 
                                ?>
                                $error=false;$act->delete($error);
                                if ($error!==false) $errors[]=$error;
                             }   
			}
                        
                        if (count($errors)==0)
                        {
                            $ok=Viewer::_echo("form:batchdeletesuccess");
                            $request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",$ok,false,"reportedcontent_content archived_report");
                        }else{
                            $request->setInfo("<?php echo $this->model; ?>/<?php echo $this->generator; ?>",implode('<br/>',$errors),false,"reportedcontent_content active_report");
                        }
                        $request->setFormVars("<?php echo $this->model; ?>/<?php echo $this->generator; ?>");
                        $this->redirectSave('<?php echo $this->module; ?>/list');
                }
                
                
		public function executePreviewform($request)
		{
			$f2=Form::load("<?php echo $this->model; ?>/<?php echo $this->generator; ?>","model");
			$this->form=$f2->render();
			$this->title="Preview Form";
			$this->menus='<div style="margin:10px;">'.Viewer::_echo('preview:info').'</div>';
			$this->actions="Actions";
			$this->setTemplate("form");
		}
		
	}
?]<?php
		}
	}
?>