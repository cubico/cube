<td style="width:15px;"><span style="float:left;"><?php echo Viewer::view("input/checkboxes",array("class"=>"batch_checkbox","internalname"=>"Activedir[".($vars['rownum']+$vars['offset']+1)."]","multiple"=>false,"options"=>array($vars['entity']['samaccountname']=>'')));?></span></td><td style="padding:0 2px;">
<?php echo (isset($vars['entity']['samaccountname']))?$vars['entity']['samaccountname']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['displayname']))?$vars['entity']['displayname']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['givenname']))?$vars['entity']['givenname']:'';?></td>
<?php if (Session::hasCredential('admin')){ ?><td style="padding:0 2px;">
<?php echo (isset($vars['entity']['sn']))?$vars['entity']['sn']:'';?></td>
<?php } ?><td style="padding:0 2px;">
<?php echo (isset($vars['entity']['mail']))?$vars['entity']['mail']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['telephonenumber']))?$vars['entity']['telephonenumber']:'';?></td>
<?php if (Viewer::getGlobalTemplate()!=Config::get('settings:views:global_template_print_value')){ ?><!-- actions for Activedir -->
<td class="element_list_actions">
<?php echo Viewer::view('activedir/list/actions/rows/admuserldap',array('params'=>array('pks'=>$vars['entity']['samaccountname'],'entity'=>$vars['entity'])),'especial');?>
</td> <?php } ?>