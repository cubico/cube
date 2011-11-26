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
									array(	"action"=>'filter?sort=LDAP_USERS.samaccountname&sort_type='.nextSorting('LDAP_USERS.samaccountname',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Nif"))); ?>
							</th>
<th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_USERS.cn&sort_type='.nextSorting('LDAP_USERS.cn',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Nom Complet"))); ?>
							</th>
<th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_USERS.givenname&sort_type='.nextSorting('LDAP_USERS.givenname',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Nom"))); ?>
							</th>
<th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_USERS.sn&sort_type='.nextSorting('LDAP_USERS.sn',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Cognoms"))); ?>
							</th>
<th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_USERS.mail&sort_type='.nextSorting('LDAP_USERS.mail',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Email"))); ?>
							</th>
<th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_USERS.telephonenumber&sort_type='.nextSorting('LDAP_USERS.telephonenumber',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Telefon"))); ?>
							</th>
<th class="list_header" style="">
	<?php echo Viewer::view("output/link",
									array(	"action"=>'filter?sort=LDAP_USERS.useraccountcontrol&sort_type='.nextSorting('LDAP_USERS.useraccountcontrol',$img,$vars),
											"img"=>$img,
											"class"=>"list_action_link",
											"value"=>Viewer::_echo("Control"))); ?>
							</th>
 <?php if (Viewer::getGlobalTemplate()!=Config::get('settings:views:global_template_print_value')){ ?><!-- actions for Aduser -->
<th class="list_header" style="">
<b><?php echo Viewer::_echo("actions"); ?></b>
</th> <?php } ?>