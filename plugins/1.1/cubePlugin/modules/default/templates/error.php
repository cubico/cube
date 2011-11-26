<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
	<meta http-equiv="Content-Language" content="en-us">
	<title><?php echo Viewer::_echo('error'.$ne.':title'); ?></title>
		<style type="text/css">
		body.validacio_intranet {
			background: #111 url(<?php echo Config::get('errorpages:imagespath'); ?>login_bg.jpg) 0px -30px repeat-x;
			font-family: Helvetica, Arial, sans-serif;
			font-size: 12px;
		}
		
		.validacio_intranet a img {
			border: none;
		}
		
		.validacio_intranet #main_cont {
			margin: 100px auto;
			width: 649px;
			height: 500px;
			background: url(<?php echo Config::get('errorpages:imagespath'); ?>login_page_bg.png) 0px 0px no-repeat;
		}
		
		.validacio_intranet #top_cont {
			width: 600px;
			margin: 0 auto;
			position: relative;
			text-align: center;
		}
		
		.validacio_intranet #top_cont a {
			/*background: url(<?php echo Config::get('errorpages:imagespath'); ?>yc_logo2.png) 0px 0px no-repeat;*/
			width: 300px; /*208px;*/
			height: 60px;
			display: block;
			margin: 0 auto;
			position: relative;
		}
		
		.validacio_intranet #center {
			width: 528px;
			margin: 0 auto;
			/*position: relative;*/
		}
		
		.validacio_intranet #body_top {
			background: url(<?php echo Config::get('errorpages:imagespath').substr($ne,0,3); ?>_body_top.png) 0px 0px no-repeat;
			width: 500px;
			padding: 0 14px;
			height: 88px;
			float: left;
			position: relative;
		}
		
		.validacio_intranet #content {
			display: inline;
			width: 460px;
			height: 300px;
			padding: 0 34px;
			float: left;
			/*position: relative;*/
			background: url(<?php echo Config::get('errorpages:imagespath'); ?>error_body_bg.png) 0px 0px no-repeat;
		}
		
		.validacio_intranet #body_bottom {
			clear: both;
			float: left;
			position: relative;
			width: 500px;
			padding: 0 14px;
			height: 24px;
			background: url(<?php echo Config::get('errorpages:imagespath'); ?>pw_body_bottom.png) 0px 0px no-repeat;
		}
		
		.validacio_intranet #message {
			width: 300px;
			display: inline;
			float: left;
			position: relative;
			margin-top: 25px;
		}
		
		.validacio_intranet #message_main {
			font-size: 36px;
			float: left;
			position: relative;
			clear: both;
			font-weight: bold;
			color: #363636;
			width: 300px;
		}
		
		.validacio_intranet #message_text {
			font-size: 20px;
			float: left;
			position: relative;
			clear: both;
			font-weight: bold;
			color: #726C5A;
			width: 300px;
		}
		
		.validacio_intranet #face {
			width: 140px;
			margin: 0 10px;
			float: left;
			position: relative;
		}
		
		.validacio_intranet .try {
			float: left;
			position: relative;
			font-size: 12px;
			clear: both;
			color: #363636;
			margin-left: 25px;
			margin-bottom: 2px;
			margin-top: 10px;
			font-weight: bold;
			width: 400px;
		}
		
		.validacio_intranet #or {
			font-weight: bold;
			font-size: 20px;
			width: 460px;
			float: left;
			clear: both;
			margin: 15px 0;
			position: relative;
			text-align: center;
			color: #777;
		}
		
		.validacio_intranet form, ul {
			float: left;
			position: relative;
			clear: both;
			display: inline;
			margin: 0;
			padding: 0;
		}
		
		.validacio_intranet #form_cont {
			float: left;
			position: relative;
			clear: both;
			width: 460px;
		}
		
		.validacio_intranet input.enviar_btn {
			border: 0;
			background: url(<?php echo Config::get('errorpages:imagespath'); ?>enviar_btn.jpg) 0px 0px no-repeat;
			cursor: pointer;
			width: 88px;
			height: 35px;
			position: absolute;
			bottom: -3px;
		}
		
		.validacio_intranet input#search {
			width: 150px;
			font-size: 12px;
			font-weight: bold;
			color: #555;
			border: 1px solid #b4b4b4;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
			height: 17px;
			padding: 9px 0 1px 3px;
			background: #fff url(<?php echo Config::get('errorpages:imagespath'); ?>/input_text_bg.jpg) 0px 0px repeat-x;
			float: left;
			position: relative;
			margin-left: 25px;
		}
		
		.validacio_intranet ul {
			list-style-type: none;
			width: 468px;
			margin-left: -4px;
		}
		
		.validacio_intranet li {
			float: left;
			display: inline;
			position: relative;
			width: 92px;
		}
		
		.validacio_intranet li a {
			display: block;
			padding: 0px 5px;
			width: 82px;
			text-align: center;
			text-decoration: none;
			color: #555;
			text-transform: uppercase;
			font-weight: bold;
			font-size: 10px;
			height: 30px;
			line-height: 30px;
		}
		
		.validacio_intranet li.dub a {
			line-height: 15px;
		}
		
		.validacio_intranet li a:hover {
			color: #777;
		}
		
		.validacio_intranet li.vr {
			width: 1px;
			height: 30px;
			border-left: 1px solid #000;
			background: #fff;
			opacity: .25;
			filter:alpha(opacity=25);
		}
		
		* html .validacio_intranet #body_bottom {
			background: transparent none;
			filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo Config::get('errorpages:imagespath'); ?>pw_body_bottom.png',sizingMethod='crop');
		}
		
		* html .validacio_intranet #content {
			background: transparent none;
			filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo Config::get('errorpages:imagespath'); ?>error_body_bg.png',sizingMethod='crop');
		}
		
		* html .validacio_intranet #body_top {
			background: transparent none;
			filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo Config::get('errorpages:imagespath').substr($ne,0,3); ?>_body_top.png',sizingMethod='crop');
		}
		
		* html .validacio_intranet #main_cont {
			background: transparent none;
			filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo Config::get('errorpages:imagespath'); ?>login_page_bg.png',sizingMethod='crop');
		}
		
		* html .validacio_intranet #top_cont a {
			background: transparent none;
			filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo Config::get('errorpages:imagespath'); ?>yc_logo2.png',sizingMethod='crop');
		}

	</style>
	
	<!--[if IE 6]>

		<style>

			.validacio_intranet #content {

				display: inline

			}

		</style>

	<![endif]-->
	

</head><body class="validacio_intranet">
	<div id="main_cont">
		<div id="top_cont">
			<a href="#"></a>
		</div>
		<div id="center">
			<div id="body_top"></div>
			<div id="content">
				<div id="face"><img src="<?php echo Config::get('errorpages:icon:e'.$ne); ?>" alt="sorry face"></div>
				<div id="message">
					<div id="message_main"><?php Viewer::_echo('noprivileges'); ?></div>
					<div id="message_text"><?php echo sprintf(Viewer::_echo('myip'),Util::getRemoteInfo('ip')); ?></div>
				</div>
				<div id="form_cont"><?php echo Viewer::_echo('error'.$ne.':description'); ?></div>
				<div style="color:#f00;"><?php echo Session::getFlash('cube_system_error',''); ?></div>
				<div id="or"><?php echo Viewer::_echo('erroroptions'); ?></div>  
				<ul style="position: relative; left: 40px;">
					<?php foreach(Config::get('errorpages:options') as $i=>$option): ?>
					<?php if ($i!=0) {?><li class="vr"></li><?php } ?>
					<li class="dub"><a href="<?php echo $option['link']; ?>"><?php echo $option['text']; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div id="body_bottom"></div>
		</div>
	</div>
</body></html>