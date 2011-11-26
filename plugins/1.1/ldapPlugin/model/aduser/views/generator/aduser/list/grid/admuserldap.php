				<?php $routing=Controller::getInstance()->getRoute(); ?>
				<script type="text/javascript">
					var executeAction=function(action,domElem,elem){
						var checks=$('#batch_formAduser').serialize()
						var ok=true;
						
						if (eval("typeof(confirmReturn"+domElem.attr('id')+")")!=='undefined')
							ok=eval("confirmReturn"+domElem.attr('id'));
													
						if (ok){ 
							if (checks.length>0){
								var form=$('#aduserAdmuserldapFormFilter');
								// set the href to the action form
								var url='<?php echo $routing["file"]."/".$routing["module"]."/"; ?>';
								
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
								alert('<?php echo addslashes(Viewer::_echo("error:notselection")); ?>');
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
				<div class="admin_statistics">
				<?php 
					$numVisibleCols=9;
					
					if (!isset($vars['viewtype_element'])) $vars['viewtype_element']=$vars['viewtype'];
					
					if (isset($vars['title'])) echo Viewer::title($vars['title']); 
					
					echo Session::getInstance()->getFlash('aduserAdmuserldapForm_info');
					
					$pagination=true;
					if (isset($vars['pagination']['view_options'])){
						if (!is_array($vars['pagination']['view_options'])) $pagination=$vars['pagination']['view_options'];
						else if (isset($vars['pagination']['view_options']['active'])) $pagination=$vars['pagination']['view_options']['active'];
					}
					
					$sorting=array("js"=>"class=\"sorting\"","value"=>Viewer::_echo("button:clear:sort"),"action"=>$vars['clearsort']);
					
					$nav_batch= Viewer::view('aduser/list/actions/batch/admuserldap',array('class'=>'batch_option'),$vars['viewtype']);
				?>	
								<div class="navigationBatchItems"><?php echo $nav_batch; ?></div>
								<?php		
					if ($pagination)
					{
						$offset=intval($vars['pagination']['offset']);
						
						$nav=	Viewer::view('navigation/pagination',array('action'=>'filter', 
																		'offset' => $offset,
																		'filterName'=>'aduserAdmuserldapFormFilter',
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
					
										
					$grid_params=array( 																													   );
					
					?>
					<script type="text/javascript">
						 
						$(document).ready(function(){
							// search the output links of the list headers to change action
							$('.list_action_link').click(function(){
								// get the filter form
								var form=$('#aduserAdmuserldapFormFilter');
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
										<form style="" id="batch_formAduser">
					<table style="">
				    	<tr>
							<?php echo Viewer::view('aduser/list/headers/admuserldap',array('sort'=>$vars['sort']),$vars['viewtype_element']); ?>
						</tr>
				        <?php 
				        	$maxdata=count($vars['data']);
				        	if ($maxdata>0){
				            for($j=0;$j<$maxdata;$j++): ?>
							<?php $item=$vars['data'][$j]; //echo Viewer::object("aduser",$vars['viewtype']); 
																
								if (isset($vars['selected']['aduser.samaccountname'])
&& $vars['selected']['aduser.samaccountname']==$item['samaccountname']
													)
									$class="selected";
								else 
									$class=(($j%2)==0)?'odd':'even'; 
							?>
							
							<tr class="<?php echo $class; ?>">
							<?php echo Viewer::view("aduser/list/element/admuserldap",
											array(	'entity'=>$item, 'viewtype'=>$vars['viewtype'], 'rownum'=>$j,
													'offset'=>$offset, 'params'=>$grid_params), 
											$vars['viewtype_element']);?>
											
							</tr>
						<?php endfor; } ?>
						<?php if ($vars['pagination']['limit']==null):?>
						<tr style="background:#E4E4E4;padding:0 5px;">
							<td colspan="<?php echo $numVisibleCols; ?>" style="text-align:right"><?php echo Viewer::_echo('total').": ".count($vars['data'])." ".Viewer::_echo('rows');?></td>
						</tr>
						<?php endif; ?>
					</table>
				    </form>
				    <div  style="margin: 10px 0;">
									    	<div class="navigationBatchItems"><?php echo $nav_batch; ?></div>
										<?php 
						if ($pagination) echo $nav;else echo '<div class="pagination"></div>';  
						//echo Viewer::view('aduser/list/actions/buttons/admuserldap',array(),$vars['viewtype']); 
					?>
					</div>
					<div style="clear:both;"></div>
				</div>
				  
				