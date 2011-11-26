<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
	$is_logged=Session::hasCredential('is_logged');
	$appName=Controller::getRoute('app');
	$appTitle=Viewer::_echo($appName); // configure i18n!
	$moduleTitle=Config::get('view:metas:title');
	// Set title
	if ($is_logged && isset($vars['title']) && !empty($vars['title']))
		$moduleTitle = $vars['title'];
	else
		$moduleTitle = Viewer::_echo(Controller::getRoute('module'));

	//if (Util::getRemoteInfo('ip')=='10.84.129.148') Session::addCredential('cubico');else Session::removeCredential('cubico');

?><head>
	<title><?php echo $appTitle.' - '.$moduleTitle; ?></title>
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
   <link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<?php echo Viewer::includeMetaJsCss(); ?>
</head>
<body id="mainbody">
<div id="boxesmodal">
 <div id="mask"></div>
 <div id="dialog" class="window">

 </div>

</div>

<div id="page_container">
<div id="page_wrapper">
<div id="layout_header"  style="margin:20px 0 0 0;">
		<div style="padding: 0 5px 0 0;background-image: url(/img/background.png);background-repeat:no-repeat;height:90px;">
			<div style="float:left;padding: 10px;">
			<a title="<?php echo Viewer::_echo("copyright"); ?>" href="https://github.com/cubico/cube" target="_blank"><img src="/img/cube.png" border="0" style="width:56px;"/></a>
		</div>
		<div style="float:left;padding:25px 0;">
			<i>&#179;Cube</i>
		</div>


			<div class="title">
				<h1><?php echo $appTitle; ?></h1>
				<h2><?php echo $moduleTitle; ?></h2>
			</div>
		</div>
		<div><?php echo Viewer::view('page_elements/topbar',array('title'=>$appTitle)); ?></div>