<td style="width:15px;"><span style="float:left;"><?php echo Viewer::view("input/checkboxes",array("class"=>"batch_checkbox","internalname"=>"Aduser[".($vars['rownum']+$vars['offset']+1)."]","multiple"=>false,"options"=>array($vars['entity']['samaccountname']=>'')));?></span></td><td style="padding:0 2px;">
<?php echo (isset($vars['entity']['samaccountname']))?$vars['entity']['samaccountname']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['cn']))?$vars['entity']['cn']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['givenname']))?$vars['entity']['givenname']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['sn']))?$vars['entity']['sn']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['mail']))?$vars['entity']['mail']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['telephonenumber']))?$vars['entity']['telephonenumber']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['useraccountcontrol']))?$vars['entity']['useraccountcontrol']:'';?></td>
 <?php if (Viewer::getGlobalTemplate()!=Config::get('settings:views:global_template_print_value')){ ?><!-- actions for Aduser -->
<td class="element_list_actions">
<?php echo Viewer::view('aduser/list/actions/rows/admuserldap',array('values'=>$vars['entity'],'params'=>array('pks'=>$vars['entity']['samaccountname'])),$vars['viewtype']);?>
</td> <?php } ?>