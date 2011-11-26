<?php
	/**
	 * Display a page in an embedded window
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] Source of the page
	 * 
	 */

if (!isset($vars['entity'])) $vars['entity']=array();
if (isset($vars['action'])) $vars['value']=$vars['url']."/".Route::parseValues($vars['action'],$vars['entity']);
$js=(isset($vars['js'])?$vars['js']:'');

$data=isset($vars['data'])?$vars['data']:array();
$dataType=isset($vars['dataType'])?$vars['dataType']:'html';
$success=isset($vars['success'])?$vars['success']:'';
?>
<script type="text/javascript">
	$('[name="<?php echo $vars['internalname']; ?>"]').ready(function() {
		
		$.ajax({
			  type: 'POST',
			  url: '<?php echo $vars['value']; ?>',
			  data: <?php echo json_encode($data); ?>,
			  success: function(res){
				  var $res=$('[name="<?php echo $vars['internalname']; ?>"]');
				  $res.html(res);
				  var $sections=$res.find('[id^=section]').attr('id','_section');
				  $sections.find('h3').parent('a').removeAttr('href');
				  <?php echo $success;?>
			  },
			  dataType: '<?php echo $dataType;?>'
			});
				
	});
</script>
<div style="padding:4px;font-size:1.2em !important;" name="<?php echo $vars['internalname']; ?>" <?php echo $js; ?>></div>