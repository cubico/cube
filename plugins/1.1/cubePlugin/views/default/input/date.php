<?php if (!defined('DATE_COMPONENT_VIEW')){ define('DATE_COMPONENT_VIEW',true); ?>
<script type="text/javascript" src="/js/jquery-ui/jquery-ui-1.8.13.core.js"></script>
<script type="text/javascript" src="/js/jquery-ui/jquery-ui-1.8.13.datepicker.js"></script>
<link rel="stylesheet" href="/css/ui-lightness/jquery-ui-1.8.13.custom.css" />
<?php 
	if (!function_exists('transFormatDateFunction')){
		function transFormatDateFunction($txt) // de formato columna a formato calendar.js
		{
			return strtr($txt,array("%d"=>"dd","%m"=>"mm","%Y"=>"yy","%H"=>"HH","%M"=>"mm","%S"=>"ss"));
		}
		
		function transFormatTemplateFunction($txt) // de formato columna a formato calendar.js
		{
			return strtr($txt,array("%d"=>"dd","%m"=>"mm","%Y"=>"yyyy","%H"=>"HH","%M"=>"mm","%S"=>"ss"));
		}
	}
} 

$format=!isset($vars['format'])?"%d/%m/%Y":$vars['format'];
$lang=explode('_',Config::get('settings:i18n:default_lang'));
$strippedname=strtr($vars['internalname'],"[].","___");
$transformat=transFormatDateFunction($format);
$value=isset($vars['value'])?$vars['value']:'';
$errormessage=isset($vars['errorMessage'])?$vars['errorMessage']:Viewer::_echo('error:incorrect_date');

//$value="10/10/2010";
?>
<script type="text/javascript">
	$(document).ready(function() {
		$.datepicker.setDefaults( $.datepicker.regional[ "<?php echo $lang[0]; ?>" ] );
		var $datepicker<?php echo $strippedname;?>=$( "#<?php echo $strippedname;?>");
		
		
		$datepicker<?php echo $strippedname;?>.datepicker({
												showOn: "button",
												buttonImage: "/img/icon/calendar.png",
												buttonImageOnly: true,
												<?php if (isset($vars['returnFunction'])): ?>
												onClose: function(dateText, inst) {
													<?php echo $vars['returnFunction']; ?>
												},<?php endif; ?>
												dateFormat: '<?php echo $transformat; ?>'
												//,appendText: '<?php echo transFormatTemplateFunction($format); ?>'
												});
		var width=(parseInt($datepicker<?php echo $strippedname;?>.css('width'))+100)+'px';
		
		var $dateformat<?php echo $strippedname;?>=$('<span style="position:relative;float:right;margin:0 0 0 -'+width+
														';"><?php echo transFormatTemplateFunction($format); ?></span>')

		////$dateformat<?php echo $strippedname;?>.css('position','relative').css('left','-'+$datepicker<?php echo $strippedname;?>.css('width'));
		
		$dateformat<?php echo $strippedname;?>.click(function(){
			$('#<?php echo $strippedname; ?>').focus();
		});

		$('#<?php echo $strippedname; ?>').focus(function(){
			$(this).removeAttr('disabled').css('background','#fff');
			$dateformat<?php echo $strippedname;?>.hide();
		});
		
		
		<?php if (!empty($value)):?>$dateformat<?php echo $strippedname;?>.hide();<?php endif;?>

		$('#<?php echo $strippedname; ?>').blur(function(){
			if ($(this).val()==''){
				$(this).attr('disabled','disabled').css('background','#eee');
				$dateformat<?php echo $strippedname;?>.show();
			}else{
				// mirar si valor coincide con template del format!
				var date; //=getDateFromFormat(this.value,'<? echo $transformat; ?>');
				if (date==0) { 
					alert("<?php echo addslashes($errormessage); ?>"); 
					$(this).attr('disabled','disabled').val('').css('background','#eee');
					$dateformat<?php echo $strippedname;?>.show();
				}
			}
		}).blur();

		$datepicker<?php echo $strippedname;?>.before($dateformat<?php echo $strippedname;?>);
		
	});
</script>
<input type="text" value="<?php echo $value;?>" name="<?php echo $vars['internalname']; ?>" id="<?php echo $strippedname;?>"/>