<div id="elgg_topbar_container_search">
<?php if (Session::hasCredential('is_logged')){
	
	$user=MyUser::getProperties();
	
	$office=explode(";",$user->UpServeiPrincipal);
	$desc=explode(";",$user->Titulacio);
	
	//var_dump($user);
	$nom=$user->Nom;
	$info="<div class=\'userinfo\'><b>Fes clic per anar al perfil de <br/>".$user->Nom."</b></div>";
	$info.="<div class=\'userinfo\'>";
	$info.='<b>Usuari:</b> '.$user->Nif;
	$info.='<br/><b>Telèfon:</b> '.$user->Telefon;
	$info.='<br/><b>Correu:</b> '.$user->Email;
	$info.='<br/><b>Up/Servei:</b> '.(isset($office[1])?addslashes($office[1]):'-');
	$info.='<br/><b>Categoria:</b> '.(isset($desc[1])?addslashes($desc[1]):'-');

	$info.=Session::getCredential('es_metge')?' - <b>Facultatiu</b>':'';
	$info.=Session::getCredential('es_infermera')?' - <b>DUI/ATS</b>':'';
	$info.=Session::getCredential('es_administratiu')?' - <b>Administració</b>':'';

	$info.='<br/><b>Perfil:</b> '.(Session::getCredential('es_primaria')?' - PRIMARIA':'').(Session::getCredential('es_hospital')?' - HOSPITAL':'').' - ';
	//$info.='<br/><b>Centres / Serveis :</b><br/>';

	/*
	if (isset($user['centres'])){
		reset($user['centres']);
		$cur=current($user['centres']);
		do{
			$info.='<div>'.$cur.'</div><div>'.next($user['centres']).'</div><br/>';
		}while ($cur=next($user['centres']));
		
	}else $info.='Sense centres';
	//$groups=MyUser::getGroups();
	$info.='<br/><b>Grups:</b><br/>';
	reset($groups);
	$cur=current($groups);
	do{
		$info.='<div>'.$cur.'</div><div>'.next($groups).'</div><br/>';
	}while ($cur=next($groups));
	
	$info.="</div>";
	*/
}else{
	$nom="USUARI NO VALIDAT";
	$info=addslashes(sprintf(Viewer::_echo('login_info'),Viewer::_echo('login')));
}
?>
<a 	onmouseover="Tip('<?php echo $info; ?>',SHADOW, true,BORDERCOLOR,'#369',BGIMG,'/img/selected.gif',DELAY, 0,FONTSIZE,'10px',FONTCOLOR,'#000');" 
	onmouseout="UnTip()" 
	href="/admin.php/userinfo/0">
	<img src="/img/icon/user.png" />&#160;<?php echo $nom; ?></a>
</div>