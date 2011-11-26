<?php
ob_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
	$title=Config::get('view:metas:title');
	// Set title
	if (isset($vars['title']) && !empty($vars['title'])) $title = $vars['title'];
	
?><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style>
		th.list_header {
			background:none repeat scroll 0 0 #C2DFEF;
			color:#05354F;
			font-weight:bold;
			padding:3px;
		}
		
		th, td {
			font-weight:normal;
			text-align:left;
			vertical-align:top;			
		}
		
		.usersettings_statistics td, .admin_statistics td {
			border:1px solid #CCCCCC;
			padding:2px 4px;
		}

		.usersettings_statistics .even, .admin_statistics .even {
			background:none repeat scroll 0 0 #FFFFFF;
		}
		
		.usersettings_statistics .odd, .admin_statistics .odd {
			background:none repeat scroll 0 0 #EFEFEF;
		}

		.navigationBatchItems table, .navigationBatchItems td {
			border:0 none;
			padding:0;
		}
		
	</style>
</head>
<body>	
<div id="layout_canvas">
<?php  echo $vars['body']; ?>
<div class="clearfloat"></div>
</div><!-- /#layout_canvas -->
<?php echo "Data Creació: ".strftime('%d/%m/%Y %H:%M:%S',time()); ?>
</body>
</html>
<?php 
	$content=ob_get_clean(); 
	$content=preg_replace("/([^<]*)<label([^>]*)>([^<]*)<\/label>/", "$1<span $2 class=\"label\">$3</span>", $content);	
	//echo nl2br(htmlentities($content));die();
	
    $ctype="application/vnd.ms-excel";
      
    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
   
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
	$export_file = strtr($title,"[].,'\"çÇ ·","__________").'.xls'; 
	header('Content-Disposition: attachment; filename="'.basename($export_file).'"'); 
    //Force the download
    //$header="Content-Disposition: attachment; filename=".$filename.";";
    header($header );
    header("Content-Transfer-Encoding: binary");


	//$pdf=Mypdf::getInstance('win-1252','A4');
	//$pdf->WriteHTML($vars['body']);
	//$pdf->WriteHTML($content);
	//$txt= $pdf->Output('', 'S');
	    
    echo $content;    
    
?>