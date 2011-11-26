			<?php   if (!function_exists('nextSorting')){
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
			?>
			<th class="list_header" style="width:15px;"></th><th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_GROUPS.samaccountname&sort_type='.nextSorting('LDAP_GROUPS.samaccountname',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Id"))); ?>
							</th>
<th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_GROUPS.description&sort_type='.nextSorting('LDAP_GROUPS.description',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Descrip"))); ?>
							</th>
<th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_GROUPS.info&sort_type='.nextSorting('LDAP_GROUPS.info',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Info"))); ?>
							</th>
 <?php if (Viewer::getGlobalTemplate()!=Config::get('settings:views:global_template_print_value')){ ?><!-- actions for Adgroup -->
<th class="list_header" style="">
<b><?php echo Viewer::_echo("actions"); ?></b>
</th> <?php } ?>