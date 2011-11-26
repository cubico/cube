<?php
class Actions
{
    private $view;
   
    public function setView($view,$props=array(),$viewtype=null)
    {
        $this->view=array($view,$props,$viewtype);
    }
   
    public function getView()
    {
        return $this->view;
    }

    private $template;
	private $templateModule;
	
	public function setTemplate($template,$module=null)
	{
		$this->template=$template;
		$this->templateModule=$module;
	}
	
	public function getTemplate()
	{
		return $this->template;
	}
	
	public function getTemplateModule()
	{
		return isset($this->templateModule)?$this->templateModule:null;
	}
	
	public function setLayout($layout)
	{
		if ($layout===false) Config::set('view:has_layout',false);
		else
		{ 
			Config::set('view:has_layout',true);
			Config::set('view:layout',$layout);
		}
	}
	
	public function forward($module,$action,$view=null)
	{
		Controller::getInstance()->forward($module,$action,$view);
	}
	
	public function redirect($url,$redirectUrl=false)
	{
		$this->setTemplate(false);
		Controller::getInstance()->redirect($url,$redirectUrl);
	}
	
	static public function setDebug($bool){
		$a=Config::getConfig("project");
		$a['debug']=$bool;
		Config::setConfig("project",$a);
	}
	
	static public function getDebug(){
		$a=Config::getConfig("project");
		return $a['debug'];
	}
	
	public function executeDefault()
	{
		echo "Module Default"; 
		return Viewer::NONE;
	}
	
	
}
	
class Controller{
	private static $instancia; 	// context
	private static $cfg;
	private static $filters;
	
	
	static public function autoload($class_name) {
		
		//echo $class_name;
		
		//if (file_exists($class_name . '.php')){include_once $class_name . '.php';}else 
		if (preg_match("/Auto(.*)Actions/",$class_name,$args)) //realmente no es class_name sino el generador
		{
			$route=Controller::getInstance()->getRoute();
			$actions=Site::getInstance()->getConfiguration('actions');
			
			if (Route::PluginMode()===false) $file=$actions['apps/'.$route['app']."/".$route['module']];
			else $file=$actions['plugins/'.Route::PluginMode()."/".$route['module']];
	
			$file=preg_replace("/actions.class.php$/","actions.".strtolower($args[1]).".php",$file);
			include_once $file;
		}
		else
		{
			$conf=Site::getInstance()->getConfiguration('model');
			
			if (isset($conf[$class_name]) && file_exists($conf[$class_name]))
			{
				$class=preg_replace("/(.*)Peer/","$1",$class_name);
				
				if (!class_exists($class))
				{
					$configDir=realpath(dirname($conf[$class])."/.."); //estan en /object!
					$data3=Site::getInstance()->importFile($configDir."/schema.yml"); // core
					//echo $class.": "._r($data3,true);
					if ($data3!==null) Config::getInstance()->set('model:'.strtolower($class),$data3,'model');
					//echo _r(Config::getInstance()->getConfig('all'));
					
					if (file_exists($conf[$class])) 
					{ include_once $conf[$class];}
									
					if (isset($conf[$class."Peer"]) && file_exists($conf[$class."Peer"])) 	
					{ include_once $conf[$class."Peer"];}
					
					
				}
				//else echo "Ya existe ".$class;
			}
		}
	}
	/////////////
	
	public function forward($module,$action,$view=null)
	{
		$r=$this->getRoute();
		$r['module']=$module;
		$r['action']=$action;
		if ($view==null) $r['view']=$action; else $r['view']=$view;
		$this->setRoute($r);
		$this->setForward(true);
		
		//Log::_add(__METHOD__,,"forward",__CLASS__,Log::ERROR);
		Controller::triggerHook('debug','forward',array(
					'message' =>"Forward to '{$module}/{$action}/{$view}'",
					'type'=>'forward',
					'error'=>Log::ERROR,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
	}
	
	////// guardar el tipo de acceso (se lo pasamos de securityFilter a controlFilter)
	static private $testSecurityMode;
	
	static public function resetTestSecurity(){
		self::$testSecurityMode=null;
	}

	static public function getTestSecurity(){
		//return empty(self::$testSecurityMode)?null:self::$testSecurityMode;
		return  self::$testSecurityMode;
	}
	
	static public function setTestSecurity($type,$msg=''){
		self::$testSecurityMode=array("type"=>$type,"msg"=>$msg);
	}
	
	/*public function testSecurity($cfg){
		
		$route=$this->getRoute();
								
		if (isset($cfg[$route['action']])) $sec=$cfg[$route['action']];
		else if (isset($cfg['all'])) $sec=$cfg['all'];	// todas las acciones!
		else $sec=array('is_secure'=>false);
		
		
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
				if (isset($sec['redirect'])) $this->redirect($sec['redirect']);
				else if (isset($sec['forward'])) 
				{
					$pr=explode("/",$sec['forward']);
					$this->secureForward($pr[0],$pr[1]);
				}
				else {
					$this->secureForward('default','security');
					
				}
				return true;
			}else {
				$vars=preg_replace("/[\r\n\t]/","",var_export($sec ['credentials'],true));
				$this->setTestSecurity('credential',$route['action'].": ".$vars);					
			}
		}else 
			$this->setTestSecurity('simple',$route['action']);
			
			
		return false;
	}*/
	
	////// guardar el fichero y la clase de acceso (se lo pasamos de securityFilter a controlFilter)
	static private $controlFile;
	
	static public function getControlFile(){
		//return empty(self::$testSecurityMode)?null:self::$testSecurityMode;
		return  self::$controlFile;
	}
	
	static public function setControlFile($file,$class){
		self::$controlFile=array("file"=>$file,"class"=>$class);
	}
	
	public function secureForward($module,$action,$view=null)
	{
		$r=$this->getRoute();
		$r['module']=$module;
		$r['action']=$action;
		if ($view==null) $r['view']=$action; else $r['view']=$view;
		Route::execute($r);
		$this->setRoute($r);
		//echo _r($r);
		//Log::_add(__METHOD__,"Secure Forward to '{$module}/{$action}/{$view}'","forward",__CLASS__,Log::ERROR);
		Controller::triggerHook('debug','forward',array(
					'message' =>"Secure Forward to '{$module}/{$action}/{$view}'",
					'type'=>'forward',
					'error'=>true,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
	}
	
	public function redirect($url,$directUrl=false)
	{
		if (!$directUrl) $url=Route::url($url);
		//echo var_export($directUrl).__METHOD__.$url;die();
		//Log::_add(__METHOD__,"Redirect to '{$url}'","forward",__CLASS__,Log::ERROR);
		Controller::triggerHook('debug','forward',array(
					'message' =>"Redirect to '{$url}'",
					'type'=>'forward',
					'error'=>Log::ERROR,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
		Session::setFlash("__log__",Log::get());
		header("Location: ".$url);
		
		/// control de variables flash (solo una página de sesion)
		Session::restoreFlashVars();die(); /// si trae problemas, se puede quitar la siguiente linea, inclusive die()!
				
		return Viewer::NONE;	
	}
	
	///////////
	
	static public function createInstance($config=null) {
       if (self::$instancia == NULL) {
          
		  self::$instancia = new Controller($config);
       }
   		return self::$instancia;
	}

	public function __construct($config)
	{
		
		self::$cfg=Config::createInstance($config['project']);
		self::activeErrorHandler();
		self::$secret = md5(rand().microtime());
	}
	
	static public function getInstance() {
      	return self::$instancia;
	}
	
	static public function activeErrorHandler($bool=true,$handler='errorHandler')
	{
		if ($bool) set_error_handler(array('Controller',$handler));
		else restore_error_handler();
		//Log::_add(__METHOD__,"Changes Error handler '{$handler}' to ".($bool?'ON':'OFF'),"log",__CLASS__,Log::SUCCESS);
		Controller::triggerHook('debug','log',array(
					'message' =>"Changes Error handler '{$handler}' to ".($bool?'ON':'OFF'),
					'type'=>'log',
					'error'=>Log::SUCCESS,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
	}
	
	////////// errorHandlers
	
	public static function errorHandler($errno, $errstr, $errfile, $errline) {
		throw new CubeException($errstr, $errno);
	}

	public static function errorHandlerView($errno, $errstr, $errfile, $errline) {
		echo "<p>".strtoupper("<b>(Error {$errno}) {$errstr} </b>")."<br/>(Line {$errline}) '{$errfile}'</p>";
		//die();
	}
	
	public static function errorHandlerNoDebug($errno, $errstr, $errfile, $errline) {
		//echo "<h1>".LANG_ERROR_ERROR_HANDLER."</h1>";
		//echo "<p>Error: {$errstr} ({$errno}): Line {$errline} of '{$errfile}'</p>";
		//die();
	}
	
	//////////// fin errorHandlers
	
	public function __destruct()
	{
		//restore_error_handler();
	}
	
	public function getConfiguration()
	{
		return self::$cfg;
	}

	static function endFilterChain(){
		end(self::$filters);
		return self::$instancia;
	}

	public function execute()
	{
		$valor=current(self::$filters);
		$class=key(self::$filters)."Filter";
		//echo _r(self::$filters);
		if (key(self::$filters)!=null) // si no es el ultimo
		{
			
			//echo "<br/>".$class.": ";var_dump($valor);
			next(self::$filters);
			
			if (is_array($valor))
			{
				if (isset($valor['class'])) $class=$valor['class'];
				if (!$valor['enabled']) return self::execute();
				if (isset($valor['package'])) 
				{ 
					//echo $valor['package'];die();
					import($valor['package']);
				}
			}else if (!$valor) return self::execute();
			
			if (class_exists($class)) 
			{
				return new $class(self::$instancia);
			}
			else {throw new CubeException($class." not exist", "5");}
			
		}				
		return null;
	}
	static private $route;
	
	static public function getRoute($name=null)
	{
		if ($name==null) return self::$route;
		else if (isset(self::$route[$name])) return self::$route[$name];
		return null;
	}
	
	static public function setRoute($route)
	{
		self::$route=$route;
	}
	
	static private $forward;
	
	static public function setForward($bool)
	{
		self::$forward=$bool;
	}
	
	static public function hasForward()
	{
		return (self::$forward);
	}
	
	static private $secret;
	
	static public function getSecret() {return self::$secret;}
	
	static public function generateActionToken($timestamp)
    {
        // Get input values
        $site_secret = self::getSecret();
        
        // Current session id
        $session_id = session_id();
        
        // Get user agent
        $ua = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'HTTP_USER_AGENT';
        
        // Session token
        $st = implode("_",Config::getConfig('project','site'));
        
        if (($site_secret) && ($session_id))
        	return md5($site_secret.$timestamp.$session_id.$ua.$st);
        
        return false;
    }
       
	static public function isAjaxRequest(){
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest');
	}
    
	
	public function init()
	{
		
		$cfg=self::$instancia->getConfiguration();
		
		$params=$cfg->getConfig('project');
				
		//Log::_add(__METHOD__,"<b>Cache is ".($params['cache']?'ON':'OFF')."</b>","log",__CLASS__,($params['cache']?Log::SUCCESS:Log::ERROR));
		Controller::triggerHook('debug','log',array(
					'message' =>"<b>Cache is ".($params['cache']?'ON':'OFF')."</b>",
					'error'=>$params['cache']?Log::SUCCESS:Log::ERROR,
					'class'=>__CLASS__,
					'method'=>__METHOD__));

		$site=Site::getInstance();
		try{
			$data0=$site->importFile(realpath(CUBE_PATH_ROOT."/engine/config.yml")); // core
			
			// databases.yml
			$databases=realpath(CUBE_PATH_ROOT."/engine/databases.yml");
			if ($databases!==false){
				$dataDB=$site->importFile($databases); // core
				foreach($dataDB as $env=>$data){
					foreach($data as $connection=>$info){
						$data0[$env]['database'][$connection]=$info;
					}
				}
			}
			
			$cfg->importData($data0,'site');
			
			$dir=CUBE_PATH_ROOT."/apps/".$params['app'];
			if (is_dir($dir) || $params['app']=='__script__')
			{
				$data1=$site->importFile(realpath($dir."/config.yml")); // core
				$cfg->importData($data1,'app');
			}
			else throw new CubeException("Application ".$params['app']." does not exist");
			
			$supportI18NSite=Config::get('settings:i18n:cube_lang','site');
						
			///// filtros (init, ..., render)
			$filterDefPack=Config::get('settings:filters:default_packages');
			foreach((array)$filterDefPack as $cur)	import($cur);
			
			$plugins_activos=Config::get('plugins');
			
			// Logs
			Config::set('settings:logs:enabled',$params['debug'],'site');
			
			// soporte para lenguaje (si solo se especifica en app!!!!)
			$supportI18N=Config::get('settings:i18n','app');
			//$supportI18N=Config::get('settings:i18n');
			if (!isset($supportI18N)) $configI18N='site';else $configI18N='app';
			
			$enableI18N=Config::get('settings:i18n:enabled',$configI18N,false);
			
			if ($enableI18N)
			{
				$default_lang=Config::get('settings:i18n:default_lang',$configI18N);
				
				$dir_i18n=$dir."/i18n";
				$file=realpath($dir_i18n."/lang.".$default_lang.".yml");
				
				if (is_file($file))
				{
					$lang=$site->importFile(realpath($file)); // core
					$cfg->importData($lang,'lang');
					
					//Log::_add(__METHOD__,"Get i18n data from ".$default_lang,"log",__CLASS__,Log::SUCCESS);
					Controller::triggerHook('debug','log',array(
						'message' =>"Get i18n data from ".$default_lang,
						'type'=>'log',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
				}
				else throw new CubeException("Lang file ".$file." not Found",-1);
				
				
				///////// mirar si hay i18n en los plugins que estan activos para la aplicacion
				$lang_plugin=array();
				
				if (!empty($plugins_activos)){	
					foreach($plugins_activos as $plugin){
						if ($site->isPluginActive($plugin)){
							// i18n
							$dir_i18n_plugin=CUBE_PATH_ROOT."/plugins/".$plugin."/i18n";
							$file=realpath($dir_i18n_plugin."/lang.".$default_lang.".yml");
							if (is_file($file)){
								$lang_plugin=array_merge_recursive($lang_plugin,$site->importFile($file));
							}
						}
					}
				}
				
			}
			else 
			{
				Config::setConfig('lang',null);
			}
			
			
			// guardamos en i18n = i18n de App + i18n Plugin
			$i18n=array_merge_recursive($cfg->getConfig('lang'),$lang_plugin);
			$cfg->setConfig('lang',$i18n);
			////// fin soporte i18n
			
			//////// config.yml en plugin
			$config_plugin=array();
			// compatibilidad con plugins:[plugin1, plugin2] -> {plugin1: on, plugin2: on}
			
			
			if (!empty($plugins_activos) && is_numeric(key($plugins_activos))){ // los plugins vienen como tabla
				$plugins_activos=array_fill_keys($plugins_activos, true);
			}
						
			if (!empty($plugins_activos)){	
				foreach($plugins_activos as $plugin=>$enabled){
					if ($site->isPluginActive($plugin) && $enabled){
						$file_config_plugin=realpath(CUBE_PATH_ROOT."/plugins/".$plugin."/config.yml");
						if (is_file($file_config_plugin)){
							$config_plugin=array_merge_recursive($config_plugin,array($plugin=>$site->importFile($file_config_plugin)));
						}
					}
				}
			}
			$config=$cfg->getConfig('app');
			
			foreach($config_plugin as $plugin=>$data){
				if ($site->isPluginActive($plugin)){
					$env=key($data);
					$config=array_merge_recursive($config,array($env=>array($plugin=>$data[$env])));
				}
			}
			
			$cfg->setConfig('app',$config);
			////// fin config.yml (aunque se mira despues para el módulo)
			
			
			// guardamos Request en la Clase
			unset($_REQUEST['qfRoute']);
			
			/// si está activado , pasa las variables escapando comillas, y entonces se las volvemos a quitar
			if (get_magic_quotes_gpc()) {
				$_GET = stripslashes_recursive($_GET);
				$_POST = stripslashes_recursive($_POST);
				$_COOKIE = stripslashes_recursive($_COOKIE);
				$_REQUEST = stripslashes_recursive($_REQUEST);
			}
	
			$r=Request::createInstance($_REQUEST);
			
			// extraemos de la configuracion la aplicacion que se ejecuta
			$cfg1=Config::getInstance();
			$project=$cfg1->getConfig('project');
			
			// extraemos la uri que se ejecuta
			$uri=explode("?",$_SERVER['REQUEST_URI']);
			
			//$url=preg_replace("/^(\/[^.]*.php)/","",$uri[0]);
			if ($uri[0]=='/') {$url='/';$args=array("/","/index.php");}
			else if (preg_match('/^\/[^\.]*$/',$uri[0])) {$url=$uri[0];$args=array($uri[0],'/index.php');}
			else  
			{
				/// si es una pagina redirigida (404, 403, ...) no utilizes uri, sino PHP_SELF!
				if (!isset($_SERVER['REDIRECT_STATUS']))
					preg_match("/^(\/[^.]*.php)[\/]{0,1}(.*)/",$uri[0],$args);	
				else
					preg_match("/^(\/[^.]*.php)[\/]{0,1}(.*)/",$_SERVER['PHP_SELF'],$args);
				
				if ($args[2]=="") $url="/";else {$url="/".$args[2];} //{$url="/".rtrim($args[2],"/")."/";}
			}
			
			// creamos ruta inicial
			$ctrl=Controller::getInstance();
			$route=new Route();
			$route->parseRoute($url,array("app"=>$project['app'],"file"=>$args[1]));
			$ctrl->setRoute($route->get());
			
			/// views
            $global_template_param=Config::get('settings:views:global_template_param','site');
            $global_template=$r->getParameter($global_template_param,Config::get('settings:views:global_template'));
			
            $v=Viewer::createInstance();
			
			$v->setGlobalTemplate($global_template);
			$v->setViews($site->getConfiguration('views'));
			
			$plugins=$site->getConfiguration('plugins');
			$enabledPlugins=array();
			foreach($plugins as $plugin=>$data)
			{
				if ($data['active']) $enabledPlugins[$plugin]=$data;
			}
			
			
			$v->setPlugins($enabledPlugins);
			
			//Log::_add(__METHOD__,"Active Models: ".implode(", ",array_keys($site->getConfiguration('model'))),"info",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','info',array(
						'message' =>"Active Models: ".implode(", ",array_keys($site->getConfiguration('model'))),
						'type'=>'info',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));	
			
			///// ejecucion
			do{
				$flash=array_keys(Session::getInstance()->getFlash());
				//Log::_add(__METHOD__,"Flash Vars: ".implode(", ",$flash),"info",__CLASS__,Log::SUCCESS);
				Controller::triggerHook('debug','info',array(
						'message' =>"Flash Vars: ".implode(", ",$flash),
						'type'=>'info',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));	
				
				Controller::setForward(false);
				self::$filters=$route->execute($ctrl->getRoute());
				
				$infofilters=array();foreach(self::$filters as $filter=>$data){ if ($data['enabled']) $infofilters[]=$filter;}
				//Log::_add(__METHOD__,"Execution Filters: ".implode(", ",$infofilters),"log",__CLASS__,Log::SUCCESS);
				//Log::_add(__METHOD__,"Enabled Modules: ".implode(", ",Config::get('settings:enabled_modules')),"plugin",__CLASS__,Log::SUCCESS);
				
				Controller::triggerHook('debug','log',array(
						'message' =>"Execution Filters: ".implode(", ",$infofilters),
						'type'=>'log',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));	
				
				Controller::triggerHook('debug','route',array(
						'message' =>"Enabled modules configuration: ".implode(", ",Config::get('settings:enabled_modules')),
						'type'=>'plugin',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));	
				
				self::execute();
				$global_template=$r->getParameter($global_template_param,Config::get('settings:views:global_template'));
				$v->setGlobalTemplate($global_template);
				
			}while (Controller::hasForward());
			
			/// control de variables flash (solo una página de sesion)
			Session::restoreFlashVars();
			
		
		}catch(Exception $e) {echo $e;}			
	}
	
	private static $hooks;

	static public function hasRegisterHook($hook, $entity_type) {
		return isset(self::$hooks[$hook][$entity_type]);
	}
	
	static public function unregisterHook($hook, $entity_type, $function) {
		if (self::hasRegisterHook($hook, $entity_type)){
			foreach(self::$hooks[$hook][$entity_type] as $key => $hook_function) {
				
				if (is_array($function)) $func=implode("::",$function);
				else $func=$function;
				
				if ($hook_function == $func) {
					unset(self::$hooks[$hook][$entity_type][$key]);
				}
			}
			//Log::add2(__METHOD__,"Unregister trigger {$hook}.{$entity_type} succesfully","trigger",__CLASS__,Log::SUCCESS);
			
			Controller::triggerHook('debug','trigger',array(
						'message' =>"<b>Unregister trigger {$hook}.{$entity_type} succesfully</b>",
						'type'=>'trigger',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
				
			return true;
		}
		//Log::add2(__METHOD__,"Not unregister trigger {$hook}.{$entity_type}","trigger",__CLASS__,Log::ERROR);
		Controller::triggerHook('debug','trigger',array(
						'message' =>"<b>Not unregister trigger {$hook}.{$entity_type}</b>",
						'type'=>'trigger',
						'error'=>Log::ERROR,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
		return false;
    }
    
    static private $debugTime;
    
    static public function getDebugTime(){
    	return self::$debugTime; 
    }
	
    static public function setDebugTime($time){
    	self::$debugTime=$time; 
    }
    
    
	static public function registerHook($hook, $entity_type, $function, $priority = 500,$first=false) {
		
		//$entity_type=ucfirst($entity_type);
		
		if (!isset(self::$hooks)) {
			self::$hooks = array();
		} else if (!isset(self::$hooks[$hook]) && !empty($hook)) {
			self::$hooks[$hook] = array();
		} else if (!isset(self::$hooks[$hook][$entity_type]) && !empty($entity_type)) {
			self::$hooks[$hook][$entity_type] = array();
		}
		
		if (is_array($function)) // es un metodo de una clase
			$callable=is_callable($function,true,$function);
		else	
			$callable=is_callable($function);
		
		//echo _r($function);die();
			
		if (!empty($hook) && !empty($entity_type) && $callable) {
			$priority = (int) $priority;
			if ($priority < 0) $priority = 0;
			while (isset(self::$hooks[$hook][$entity_type][$priority])) {
				$priority++;
			}
			self::$hooks[$hook][$entity_type][$priority] = $function;
			ksort(self::$hooks[$hook][$entity_type]);
			
			//Log::_add(__METHOD__,"<b>Trigger {$hook}.{$entity_type} registered with priority {$priority}</b>","trigger",__CLASS__,Log::SUCCESS);
			if (!$first) //excepcion del primer trigger
				Controller::triggerHook('debug','trigger',array(
						'message' =>"<b>Trigger {$hook}.{$entity_type} registered with priority {$priority}</b>",
						'type'=>'trigger',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
				
			return true;
		} else {
			//Log::_add(__METHOD__,"Trigger {$hook}.{$entity_type} NOT registered","trigger",__CLASS__,Log::ERROR);
			if (!$first) //excepcion del primer trigger
				Controller::triggerHook('debug','trigger',array(
						'message' =>"Trigger {$hook}.{$entity_type} NOT registered",
						'type'=>'trigger',
						'error'=>Log::ERROR,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
			return false;
		}
		
	}
	
	static public function triggerHook($hook, $entity_type, $params = null, $returnvalue = null) {
		
		// Hooks HOOK - ENTITY
		if (!empty(self::$hooks[$hook][$entity_type]) && is_array(self::$hooks[$hook][$entity_type])) {
			foreach(self::$hooks[$hook][$entity_type] as $hookfunction) {
				
				if (preg_match("/::/",$hookfunction)) {
					$temp_return_value=call_user_func_array(explode("::",$hookfunction),array($hook, $entity_type, $returnvalue, $params));
				}
				//else $temp_return_value = $hookfunction($hook, $entity_type, $returnvalue, $params);
				if (!is_null($temp_return_value)) $returnvalue = $temp_return_value;
			}
		}
		
		// Hooks ALL - ENTITY
		if (!empty(self::$hooks['all'][$entity_type]) && is_array(self::$hooks['all'][$entity_type])) {
			foreach(self::$hooks['all'][$entity_type] as $hookfunction) {
				
				if (preg_match("/::/",$hookfunction)) {
					$temp_return_value=call_user_func_array(explode("::",$hookfunction),array($hook, $entity_type, $returnvalue, $params));
				}
				//else $temp_return_value = $hookfunction($hook, $entity_type, $returnvalue, $params);
				if (!is_null($temp_return_value)) $returnvalue = $temp_return_value;
			}
		}
		
		// Hooks HOOK - ALL
		if (!empty(self::$hooks[$hook]['all']) && is_array(self::$hooks[$hook]['all'])) {
			foreach(self::$hooks[$hook]['all'] as $hookfunction) {
				
				if (preg_match("/::/",$hookfunction)) {
					$temp_return_value=call_user_func_array(explode("::",$hookfunction),array($hook, $entity_type, $returnvalue, $params));
				}
				//else $temp_return_value = $hookfunction($hook, $entity_type, $returnvalue, $params);
				if (!is_null($temp_return_value)) $returnvalue = $temp_return_value;
			}
		}
		
		// Hooks ALL - ALL
		if (!empty(self::$hooks['all']['all']) && is_array(self::$hooks['all']['all'])) {
			foreach(self::$hooks['all']['all'] as $hookfunction) {
				
				if (preg_match("/::/",$hookfunction)) {
					$temp_return_value=call_user_func_array(explode("::",$hookfunction),array($hook, $entity_type, $returnvalue, $params));
				}
				//else $temp_return_value = $hookfunction($hook, $entity_type, $returnvalue, $params);
				if (!is_null($temp_return_value)) $returnvalue = $temp_return_value;
			}
		}
			
		return $returnvalue;
	}
	
}	


?>