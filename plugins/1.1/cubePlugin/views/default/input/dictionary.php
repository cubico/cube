<?php 
	$strippedname=strtr($vars['internalname'],"[].,","___");
	if (isset($vars['dictionary'])){ 
	
		$ajax=Route::url($vars['dictionary']);
		echo Viewer::addJavascript('/js/jquery.spellchecker.js');
		echo Viewer::addStyle('/css/spellchecker.css');
?>
	<div>
		<span class="loading" style="float:right;padding: 0.5em 8px;display: none;font-size: small;">carregant..</span>		
		<?php echo Viewer::view("output/link",array(
					'value'=>'',
					'img'=>'/img/crystal/16x16/actions/spellcheck.png',
					'internalname'=>'check-textarea'.$strippedname,
					'js'=>'style="float:right"'
					)); ?>
		<?php echo Viewer::view("input/".$vars['input'],$vars); ?>
	</div>
	
	<script type="text/javascript">

		// check the spelling on a textarea
		$("[name=check-textarea<?php echo $strippedname; ?>]").click(function(e){
			e.preventDefault();
			
			$(".loading").show();

			$("[name='<?php echo $vars['internalname']; ?>']")
			.spellchecker({
				url: '<?php echo $ajax; ?>',	
				suggestBoxPosition: "above"
			})
			.spellchecker("check", function(result){

				// spell checker has finished checking words
				$(".loading").hide();

				// if result is true then there are no badly spelt words
				if (result) {
					alert('<?php echo isset($vars['errorMessage'])?$vars['errorMessage']:Viewer::_echo('error:notfound'); ?>');
				}
			});
			
		});
		
		/*  you can ignore this; if document is viewed via subversion in google code then re-direct to demo page
		if (/jquery-spellchecker\.googlecode\.com/.test(window.location.hostname) && /svn/.test(window.location)) {
			window.location = 'http://spellchecker.jquery.badsyntax.co.uk/';
		}*/
	</script>
<?php }else echo Viewer::view("input/".$vars['input'],$vars); ?>