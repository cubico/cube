<?php
	/*
	if (false){
	header('Content-Type: application/pdf');
	Util::noCacheHeader();	
	$pdf=Mypdf::getInstance();
	$pdf->WriteHTML($vars['body']);
	$content = $pdf->Output('', 'S');
	echo $content;
	}else echo $vars['body'];*/
ob_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
	$title=Config::get('view:metas:title');
	// Set title
	if (isset($vars['title']) && !empty($vars['title'])) $title = $vars['title'];
	
?><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" href="/css/print.css" type="text/css" />
	<script type="text/javascript" src="/js/jquery/jquery-1.4.min.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery.easing.1.3.packed.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery-ui-personalized-1.5.3.packed.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery.readonly.js"></script>
	<script type="text/javascript" src="/js/init.js"></script>
	<script type="text/javascript" src="/js/main.js"></script>
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
	$txt=ob_get_clean(); 
	
	$txt=preg_replace("/([^<]*)<label([^>]*)>([^<]*)<\/label>/", "$1<span $2 class=\"label\">$3</span>", $txt);	
		
	//echo nl2br(htmlentities($txt));die();
	header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-chache, must-revalidate, post-check=0, pre-check=0",false);
	header("Cache-Control: public");
	header('Content-Type: application/pdf');
	//header("Content-Disposition: inline");
		
	$pdf=Mypdf::getInstance('win-1252','A4-L');
	$pdf->WriteHTML($txt);
	$content= $pdf->Output('', 'S');
	echo $content;
	
?>