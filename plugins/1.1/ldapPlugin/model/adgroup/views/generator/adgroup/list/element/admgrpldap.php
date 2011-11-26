<td style="width:15px;"><span style="float:left;"><?php echo Viewer::view("input/checkboxes",array("class"=>"batch_checkbox","internalname"=>"Adgroup[".($vars['rownum']+$vars['offset']+1)."]","multiple"=>false,"options"=>array($vars['entity']['samaccountname']=>'')));?></span></td><td style="padding:0 2px;">
<?php echo (isset($vars['entity']['samaccountname']))?$vars['entity']['samaccountname']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['description']))?$vars['entity']['description']:'';?></td>
<td style="padding:0 2px;">
<?php echo (isset($vars['entity']['info']))?$vars['entity']['info']:'';?></td>
 <?php if (Viewer::getGlobalTemplate()!=Config::get('settings:views:global_template_print_value')){ ?><!-- actions for Adgroup -->
<td class="element_list_actions">
<?php echo Viewer::view('adgroup/list/actions/rows/admgrpldap',array('values'=>$vars['entity'],'params'=>array('pks'=>$vars['entity']['samaccountname'])),$vars['viewtype']);?>
</td> <?php } ?>