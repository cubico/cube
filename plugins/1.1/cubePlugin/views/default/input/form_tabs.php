<?php
	/**
	 * Create a form for data submission.
	 * Use this view for forms rather than creating a form tag in the wild as it provides
	 * extra security which help prevent CSRF attacks.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['body'] The body of the form (made up of other input/xxx views and html
	 * @uses $vars['method'] Method (default POST)
	 * @uses $vars['enctype'] How the form is encoded, default blank
	 * @uses $vars['action'] URL of the action being called
	 * 
	 */

	if (isset($vars['internalid'])) { $id = $vars['internalid']; } else { $id = ''; }
	if (isset($vars['internalname'])) { $name = $vars['internalname']; } else { $name = ''; }
	$body = $vars['body'];
	$action = $vars['action'];
	if (isset($vars['enctype'])) { $enctype = $vars['enctype']; } else { $enctype = ''; }
	if (isset($vars['method'])) { $method = $vars['method']; } else { $method = 'POST'; }

	// Generate a security header
	$security_header = "";
	if ($vars['disable_security']!=true)
	{
		$ts = time();
		$token = Controller::generateActionToken($ts);
		$security_header = Viewer::view('input/hidden', array('internalname' => '___token', 'value' => $token));
		$security_header .= Viewer::view('input/hidden', array('internalname' => '___ts', 'value' => $ts));
	}
?>
<form <?php if ($id) { ?>id="<?php echo $id; ?>" <?php } ?> <?php if ($name) { ?>name="<?php echo $name; ?>" <?php } ?> action="<?php echo $action; ?>" method="<?php echo $method; ?>" <?php if ($enctype!="") echo "enctype=\"$enctype\""; ?>>
<?php echo $security_header; ?>

<link rel="stylesheet" href="/css/tabs.css" type="text/css" media="screen">
<script type="text/javascript">	
	$(document).ready(function(){
	var count=0;
	var tabs='<ul class="tabs">';
	var div_tabs='';
	var title='';
	var activo=0; // default
	var error='';
	var prueba='';
	$(".user_settings").each(function(){
	 	title=$(this).children().children().html();
	 	$(this).children().children().remove('h3');	 	       		
		tabs+='<li><a href="#" id="tabs'+count+'" title="content_'+count+'" class="tab">'+title+'</a></li>';
       	div_tabs+='<div style="display: none;" id="content_'+count+'" class="content">'+$("#section"+count).html()+'</div>';       
        count++;
    });    
    tabs+='</ul>';

	$('.reportedcontent_content').each(
		function ( intindex ){		
		if ($(this).attr("class")=='reportedcontent_content active_report'){					
			if (error=='') error = $(this).html(); 	// recojemos el mensaje errores en el formulario
			activo=1; // la primera pestaña donde hay errores=> parent 
			//prueba=$(this).parent().html();
			//alert(prueba);
			
		} 			
	});
  	  	
   	$("#tabbed_area").append('<div class="reportedcontent_content archived_report_blue">'+$("#tabs_content .reportedcontent_content").html()+'</div><br>');
    if (error!='') $("#tabbed_area").append('<div class="reportedcontent_content active_report">'+error+'</div><br>');
	$("#tabbed_area").append(tabs);			// pestañas
	$("#tabbed_area").append(div_tabs);		// contenido pestañas
	//$("#tabs_content").html(''); 			// borramos el formulario original 
	
	$("a.tab").click(function () {
		$(".active").removeClass("active");			
		$(this).addClass("active");
		$(".content").slideUp();
		var content_show = $(this).attr("title");
		$("#"+content_show).slideDown();
	});
		
	//$("#content_"+activo).slideDown();
	$("#tabs"+activo).click();	
});
</script>

<div id="tabbed_box_1" class="tabbed_box"><div class="tabbed_area" id="tabbed_area"></div></div>
<div id="tabs_content" style="display: none;"> <?php echo $body; ?></div>
</form>