<?php
class securityFilter extends Filter{
	protected $cfg;

	protected function testSecurity($controller,$cfg){

		$route=$controller->getRoute();

		if (isset($cfg[$route['action']])) $sec=$cfg[$route['action']];
		else if (isset($cfg['all'])) $sec=$cfg['all'];	// todas las acciones!
		else $sec=array('is_secure'=>false);

		//echo _r(Session::getCredentials());
		
		if (isset($sec['is_secure']) && $sec['is_secure']){
			$pass=false;

			if (isset($sec['credentials']) && is_array($sec['credentials']))
			{
				$s=Session::parseCredentials($sec ['credentials']);
				eval("\$pass=($s);");
			}
			
			if (!$pass)
			{
				
				// vamos a la accion executeSecurity del modulo default
				// Si hay un plugin, o app/modulo que tiene un executeSecurity se irá para allá
				if (isset($sec['redirect'])) $controller->redirect($sec['redirect']);
				else if (isset($sec['forward']))
				{
					$pr=explode("/",$sec['forward']);
					$controller->secureForward($pr[0],$pr[1]);
				}
				else {
					$controller->secureForward('default','security');
				}
				return true;
			}else {
				$vars=preg_replace("/[\r\n\t]/","",var_export($sec ['credentials'],true));
				$controller->setTestSecurity('credential',$route['action'].": ".$vars);
			}
		}else{
			$controller->setTestSecurity('simple',$route['action']);
		}
		return false;
	}

	public function execute($filterChain)
	{
		$ctrl=Controller::getInstance();
		
		if ($this->cfg===null){
			$cfg=Config::get('security','app');
			$cfg2=Config::get('security','module');
			$this->cfg=array_merge((array)$cfg,(array)$cfg2);
		}
		
		$ts=Controller::getTestSecurity();
		if ($ts===null) $this->testSecurity($ctrl,$this->cfg); 
		
		$route=$ctrl->getRoute();

		//////////////control de plugins y i18n////////////////////////////////
		
		/// registramos los modelos(!mods en elgg)
		$site=Site::getInstance();
		$actions=$site->getConfiguration('actions');
		
		////////// realizar la accion
		
		if (Route::PluginMode()===false) {
			$file=$actions['apps/'.$route['app']."/".$route['module']];
			$class=ucfirst($route['module'])."Actions";
		}
		else {
			$file=$actions['plugins/'.Route::PluginMode()."/".$route['module']];
			$class=ucfirst($route['module']).ucfirst(Route::PluginMode())."Actions";
		}
		
		if (is_file($file))
		{
			$ctrl->setControlFile($file,$class);
			
			///////// mirar si hay i18n en los plugins que estan activos para el modulo
			//echo "<br/>".$file." ".Route::PluginMode()." ".(Controller::hasForward()?'SI':'NO');
			
			if (!Controller::hasForward()){
				
				$plugins_activos_app=Config::get('plugins','app',array()); 
				/// compatibilidad con plugins:[plugin1, plugin2] -> {plugin1: on, plugin2: on}
				if (!empty($plugins_activos_app) && is_numeric(key($plugins_activos_app))){ // los plugins vienen como tabla
					$plugins_activos_app=array_fill_keys($plugins_activos_app, true);
				}
				
				$plgmod=Config::get('plugins','module',array());
				$plugins_module=array();
				if (Route::PluginMode()===false){
					if (!empty($plgmod)) {
						if (is_numeric(key($plgmod))){ // los plugins vienen como tabla
							$plugins_module=array_fill_keys($plgmod, true);
						}else 
							$plugins_module=$plgmod;
					}
				}
				else $plugins_module[Route::PluginMode()]=true; 
				
				//// plugins_activos : todos los plugins activos para añadir configuracion
				$plugins_activos=array_merge($plugins_activos_app,$plugins_module);
				Config::set('plugins',$plugins_activos);
				
				/// plugins_activos_i18n: plugins activos (diferencias) para que i18n no lo haga +1vez
				if (!empty($plugins_activos_app)) {
					$plugins_activos_i18n=array_diff($plugins_module, $plugins_activos_app);
					// incluyo el plugin en modoplugin para forzar a que se active el i18n!
					if (Route::PluginMode()!==false) $plugins_activos_i18n[Route::PluginMode()]=true;
				}
				else $plugins_activos_i18n=$plugins_module;
				
				
				
				if (!empty($plugins_activos_i18n)){	
					$default_lang=Config::get('settings:i18n:default_lang');	
					$lang_plugin=array();
					
					
					foreach($plugins_activos_i18n as $plug=>$enabled){
						$dir_i18n_plugin=CUBE_PATH_ROOT."/plugins/".$plug."/i18n";
						//$file=realpath($dir_i18n_plugin."/lang.".$default_lang.".yml");
						$fileplug=$dir_i18n_plugin."/lang.".$default_lang.".yml";
						
						//echo _r($fileplug);die();
						if (is_file($file)){
							$lang_plugin=array_merge_recursive($lang_plugin, Site::getInstance()->importFile($fileplug)); 
						}
					}
					Config::setConfig('lang_plugin',$lang_plugin);
					
					$i18n=array_merge_recursive(Config::getConfig('lang'),$lang_plugin);
					Config::setConfig('lang',$i18n);
					//echo _r(Config::getConfig('lang'));
					
					//////// añadir la config de cada plugin activo a Config con el nombre del plugin
				}	
				
				//////// config.yml en plugin
				///// Si en filtros se ejecuta 2 veces el mismo filtro, provocará que la configuración se duplique.
				// Comprobar (Modo Debug - LOG - icono campana) realmente que sólo se ejecuta una sola vez cada filtro
				
				$config_plugin=array();
				$count_plugins=array();
				foreach($plugins_activos as $plugin=>$enabled){
					if ($site->isPluginActive($plugin) && $enabled){
						$file_config_plugin=realpath(CUBE_PATH_ROOT."/plugins/".$plugin."/config.yml");
						if (is_file($file_config_plugin)){
							$config_plugin=array_merge_recursive($config_plugin,array($plugin=>Site::getInstance()->importFile($file_config_plugin)));
						}
					}
				}

				$config=Config::getConfig('module');
				
				foreach($config_plugin as $plugin=>$data){
					$env=key($data);
					$config=array_merge_recursive($config,array($env=>array($plugin=>$data[$env])));
				}
				Config::setConfig('module',$config);
			}
			
			//Log::_add(__METHOD__,"Plugin configuration for {$route['module']}: ".var_export(Config::get('plugins'),true),"plugin",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','plugin',array(
					'message' =>"Plugin configuration for {$route['module']}: ".var_export(Config::get('plugins'),true),
					'type'=>'plugin',
					'error'=>Log::SUCCESS,
					'class'=>__CLASS__,
					'method'=>__METHOD__));

			$disponibles=$site->getConfiguration('model');
			$a=Config::get('plugins');foreach($a as $k=>$w) {if (!$w) unset ($a[$k]);else $a[$k]=$k;}$rex=implode('|',$a);
			$a=array();foreach($disponibles as $k=>$w){if (preg_match("/".$rex."/",$w)) $a[]=$k;}
			
			Controller::triggerHook('debug','info',array(
						'message' =>"Available Models: ".implode(", ",$a),
						'type'=>'info',
						'error'=>Log::ERROR,
						'class'=>__CLASS__,
						'method'=>__METHOD__));

			
		}
		else throw new CubeException("'{$route['module']}' module directory does not exist of '{$route['app']}' application.", 1);	
		
		////////////////////////////////////////////
		
		//if (!$secure_app) $ctrl->testSecurity($cfg2); //seguridad en el módulo (volverá a cambiar $ctrl->getRoute()!)
				
		$filterChain->execute();
	}
}
?>