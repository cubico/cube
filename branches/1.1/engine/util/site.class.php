<?php
class Site
{
	static private $config;
	static private $instancia;
	static private $settings;
	
	static public function getInstance($config=null) {
		if (self::$instancia == NULL) {
        	self::$instancia = new Site($config);
       }
   		return self::$instancia;
	}
	
	static private $app;
	static private $debug;
	static private $cache;
	
	public function __construct($config=array())
	{
		
		if (isset($config['app'])) self::$app=$config['app'];
		//else throw new Exception('Application Name is required',-1);
		if (isset($config['debug'])) self::$debug=$config['debug'];else self::$debug=false;
		if (isset($config['cache'])) self::$cache=$config['cache'];else self::$cache=true;
		
		self::$settings=array("app"=>self::$app,"debug"=>self::$debug,"cache"=>self::$cache);
		
		
		if (self::$debug){ 
			
			// habilitar todos los eventos de debug
			Controller::registerHook("debug","all",array("Log","add"),true); //true: el primero
		}
	}
	
	public function isDebugMode(){ return self::$debug;}
	public function getApp(){ return self::$app;}
	
	static public function recursiveDirs($dir,$filter="(.*)",&$tots=array())
	{
		if (is_dir($dir))
		{
			$dirObj = new DirectoryIterator($dir);
			foreach ($dirObj as $nombrefichero)
			{
				if (substr($nombrefichero,0,1)!='.') // && substr($nombrefichero,0,1)!='_')
	        	{
					if($nombrefichero->isDir() && !$nombrefichero->isDot())
	        		{
	        			self::recursiveDirs($nombrefichero->getPathname(),$filter,$tots);
	        		}
	        		else if ($nombrefichero->isFile()) 
	        		{
	        			$name=realpath($nombrefichero->getPathname());
	        			if (preg_match("/".$filter."/",str_replace("\\","/",$name),$args)) $tots[]=$name;
	        		}
	        	}
	        }
		}
        return $tots;
	}
	
	public function getConfiguration($name=null)
	{
		if ($name===null) return self::$config;
		else if (isset(self::$config[$name])) return self::$config[$name];
		return null;
	}
	
	public function getPlugin($plugin)
	{
		if (isset(self::$config['plugins'][$plugin])) return self::$config['plugins'][$plugin];
		return null;
	}
	
	public function isPluginActive($plugin=null)
	{
		return (isset(self::$config['plugins'][$plugin]['active']) && self::$config['plugins'][$plugin]['active']);
	}
	
	// getSitePluginList: only for administration purpose	
	static public function getSitePluginList()
	{
		$plugs=array();
		$plugins=self::recursiveDirs(CUBE_PATH_ROOT.'/plugins/',"plugins\/(.*)Plugin\/manifest.yml$");
		
		if (count($plugins)>0)
		{
			// todos los plugins
			foreach($plugins as $plugin)
			{
				$plugin=str_replace(DIRECTORY_SEPARATOR,"/",$plugin);
				preg_match("/\/plugins\/([^\/]*)\/(.*)$/",$plugin,$args);
				$plugs[$args[1]]=array(null,$args[0]);
			}
		}	
		
		//echo _r(self::$pluginlist,true);
		if (self::$pluginlist==null) self::$pluginlist=self::$config['plugins'];
		
		foreach(self::$pluginlist as $plugin=>$attrib){
			$plugs[$plugin][0]=$attrib['order'];
		}
		$max=count(self::$pluginlist)*10;
		
		foreach($plugs as $k=>$cur) { 
			if ($cur[0]==null)
			{ 
				//$plugs[$k]=array('active'=>false,'order'=>$max,'manifest'=>Spyc::YAMLLoad(realpath(CUBE_PATH_ROOT.$cur[1])));
				$plugs[$k]=array('active'=>false,'order'=>$max,'manifest'=>sfYaml::load(realpath(CUBE_PATH_ROOT.$cur[1])));
				$max+=10;
			}
			else
				$plugs[$k]=self::$pluginlist[$k];
			
		}
		return array_csort($plugs,'order',SORT_ASC);
		
	}
	
	private static $pluginlist;
	
	static public function setInstalledPluginList(&$list=array())
	{
		$list=array_csort($list,'order',SORT_ASC);
		//echo _r($list2);
				
		$pluginsFile=(CUBE_PATH_ROOT."/engine/plugins.yml");
		
		
		$content="plugins:\n";
		$cont=10;
		foreach($list as $plugin=>$data)
		{
			$active=$data['active']?'on':'off';
			$order=$cont;
			$list2[$plugin]['order']=$cont;
			
			$content.="  {$plugin}:\n    active: {$active}\n    order: {$order}\n    manifest:\n";
			foreach($data['manifest'] as $atrib => $text)
			{
				$content.="      {$atrib}: {$text}\n";
			}
			
			$cont+=10;
		}
		$loaded=file_put_contents($pluginsFile, $content);
		
		try{@chmod($pluginsFile,0777);}catch(CubeException $e){} // si no puede, no pasa nada.
		
		//Log::_add(__METHOD__,"Create plugins settings file","save",__CLASS__,($loaded!==false)?Log::SUCCESS:Log::NONE);
		Controller::triggerHook('debug','save',array(
						'message' =>"Create plugins settings file",
						'type'=>'save',
						'error'=>($loaded!==false)?Log::SUCCESS:Log::NONE,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
		
		$cacheFile=realpath(CUBE_PATH_ROOT."/cache/engine/plugins.php");
		$app=self::$app;
		$settingsFile=realpath(CUBE_PATH_ROOT."/cache/apps/{$app}/settings.php");
		if (file_exists($cacheFile)) unlink($cacheFile);
		if (file_exists($settingsFile)) unlink($settingsFile);
		//echo _r($list2);
		self::$pluginlist=$list;
		return $list;
	}
	
	private function getPluginManifest($dir,&$tots=array()){
		if (is_dir($dir))
		{
			$dirObj = new DirectoryIterator($dir);
			foreach ($dirObj as $nombrefichero)
			{
				$name=realpath($nombrefichero->getPathname());
				// es un directorio
	        	if (substr($nombrefichero,0,1)!='.' && $nombrefichero->isDir() && 
	        		preg_match("/plugins\/(.*)Plugin$/",str_replace("\\","/",$name),$args) &&
	        		file_exists($name.DIRECTORY_SEPARATOR."manifest.yml")) 
	        			$tots[]=$name.DIRECTORY_SEPARATOR."manifest.yml";
	        }
		}
        return $tots;
	}
	
	private function readSiteFiles()
	{	
		$config=array();
		$enabledMods=array();
		$enabledPlugins=array();
		$enabledViews=array();
		
		/// directorio /model y /model de aplicacion (modelos) 
		
		$directories = array(	//CUBE_PATH_ROOT.'/model/',							
								//CUBE_PATH_ROOT.'/views/',
								CUBE_PATH_ROOT.'/apps/'.self::$app."/model/",
								CUBE_PATH_ROOT.'/apps/'.self::$app."/views/",
								CUBE_PATH_ROOT.'/apps/'.self::$app."/modules/",
								);
		
		$plugins=self::getPluginManifest(CUBE_PATH_ROOT.'/plugins/');								
		
		if (count($plugins)>0)
		{
			// todos los plugins
			foreach($plugins as $plugin)
			{
				preg_match("/\/plugins\/([^\/]*)\/(.*)$/",str_replace(DIRECTORY_SEPARATOR,"/",$plugin),$args);
				
				$directories[]=CUBE_PATH_ROOT.'/plugins/'.$args[1].'/model/';
				$directories[]=CUBE_PATH_ROOT.'/plugins/'.$args[1].'/views/';
				$directories[]=CUBE_PATH_ROOT.'/plugins/'.$args[1].'/modules/';
				// i18n se mira en controller.class.php y no hace falta aquí.
				$enabledMods[$args[1]]=$plugin;
			}
		}
		
		//// para todos los plugins que hemos encontrado (enabledMods) crear otro fichero /engine/plugins.yml
		//// que leeremos cuando queramos administrar la lista
		$pluginsFileAux=CUBE_PATH_ROOT.DIRECTORY_SEPARATOR."engine".DIRECTORY_SEPARATOR."plugins.yml";
		$pluginsFile=realpath($pluginsFileAux);
		if (file_exists($pluginsFile)){ //existe
			$list=Site::getInstance()->importFile($pluginsFile);
			//echo _r($list);
		}else{
			$list=array('plugins'=>array());
			$cont=10;
			$content="plugins:\n";
			foreach($enabledMods as $plugin=>$file)
			{
				//$manifest=Spyc::YAMLLoad(realpath($file));
				$manifest=sfYaml::load(realpath($file));
				if ($plugin=='cubePlugin') $active=true;else $active=false;
				$list['plugins'][$plugin]=array('active'=>$active,'order'=>$cont,'manifest'=>$manifest);
				$content.="  {$plugin}:\n    active: ".($active?'on':'off')."\n    order: {$cont}\n    manifest:\n";
				foreach($manifest as $atrib => $text)
				{
					$content.="      {$atrib}: {$text}\n";
				}
				$cont+=10;
			}
			$loaded=file_put_contents($pluginsFileAux, $content);
			@chmod($pluginsFileAux,0777);
			//Log::_add(__METHOD__,"Create plugins settings file","save",__CLASS__,($loaded!==false)?(Log::SUCCESS):(Log::ERROR));
			Controller::triggerHook('debug','save',array(
						'message' =>"Create plugins settings file",
						'type'=>'save',
						'error'=>($loaded!==false)?(Log::SUCCESS):(Log::ERROR),
						'class'=>__CLASS__,
						'method'=>__METHOD__));
			
		}
		$allPlugins=$list['plugins'];
		$allPlugins=array_csort($allPlugins,'order',SORT_ASC); 
		$allPluginsIndexes=array_keys($allPlugins);
		//echo _r($allPlugins);
		//echo $max;die();
		//// fin creacion fichero /engine/plugins.yml
		
		foreach ($directories as $directory) 
		{
			//echo "<br/>".$directory;
			$config2=self::recursiveDirs($directory,"(.*)");
			$config=array_merge($config2,$config);
			//echo _r($config2);
		}
		
		
		$modelClasses=array();
		$appModules=array();
		$appTemplates=array();
		$appGenerators=array();
		$modelViewClass=array();
		
		foreach($config as $file)
		{
			$file=str_replace(DIRECTORY_SEPARATOR,"/",$file); //url de windows!
			//echo "<br/>$file";
			
			$filtro="/(.*)\/views\/([^\.]*).php$/"; //vistas
			if (preg_match($filtro,$file,$args))
			{
				if (!isset($enabledViews[$args[2]])) $enabledViews[$args[2]]=$file;
				else 
				{
					if (!is_array($enabledViews[$args[2]])) $aux=array($enabledViews[$args[2]],$file);
					else $aux=array_merge($enabledViews[$args[2]],array($file));
					$enabledViews[$args[2]]=$aux;	
				}
			}
			
			$filtro="/(.*)\/object\/([^\.]*).php$/"; //modelos --> ../config.yml
			if (preg_match($filtro,$file,$args))
			{
				$force=false;
				$filtro2="/([^\/]*)\/([^\/]*)\/(views)\/([^\/]*)\/([^\/]*)\/object\/([^\/]*)\/([^\.]*).php$/"; //modelos --> ../config.yml
				if (preg_match($filtro2,$file,$args2)) // es un objeto
				{
					$modelViewClass[$args2[2]."/".$args2[7]]=ucfirst($args2[2]);
				}
				else //es un modelo
				{
					// si el modelo proviene de un plugin, mirar el estado del plugin, si está activo, podemos añadir el modelo a la lista
					if (preg_match("/\/plugins\/([^\/]*)\/(.*)$/",$file,$argsplug)) {
						$active=isset($allPlugins[$argsplug[1]])?$allPlugins[$argsplug[1]]['active']:false;
						if (isset($modelClasses[$args[2]])){ // si ya ha una clase guardada, mirar si el plugin es más prioritario que el que hay guardado
							preg_match("/\/plugins\/([^\/]*)\/(.*)$/",$modelClasses[$args[2]],$storedplug);
							$force=(array_search($argsplug[1],$allPluginsIndexes)<array_search($storedplug[1],$allPluginsIndexes));
						}
					}
					else $active=true; // si es un modelo que no viene de un plugin lo añadimos sin más.
					
					// sólo añadimos el primer modelo que encontramos 
					// (p.ej, sólo nos guardamos el model más prioritario (cubePlugin, NPlugin,..) 	
					if ($force || ($active && !isset($modelClasses[$args[2]]))) $modelClasses[$args[2]]=$file; 
					//else if (!is_array($modelClasses[$args[2]])) $modelClasses[$args[2]]=array($modelClasses[$args[2]],$file); 
					//else array_push($modelClasses[$args[2]],$file);
				}
			}
			
			
			$filtro="/([^\/]*)\/([^\/]*)\/modules\/([^\/]*)\/templates\/([^\.]*).php$/"; //templates
			if (preg_match($filtro,$file,$args))
			{
				//echo _r($args,true);
				$appTemplates[$args[1]."/".$args[2]."/".$args[3]."/".$args[4]]=$file;
			}
			
			$filtro="/([^\/]*)\/([^\/]*)\/modules\/([^\/]*)\/actions\.class/"; //actions
			
			if (preg_match($filtro,$file,$args))
			{
				//echo _r($args,true);
				$appModules[$args[1]."/".$args[2]."/".$args[3]]=$file;
			}
			
			$filtro="/(apps|plugins|model)\/(.*)\/generators\/([^\.]*).yml$/"; //generators
			if (preg_match($filtro,$file,$args))
			{
				//echo _r($args,true);
				$appGenerators[$args[1]."/".$args[2]."/".$args[3]]=$file;
			}
			
		}		
		
		$siteconf=array();
		$siteconf['plugins']=$allPlugins;
		$siteconf['generators']=$appGenerators;
		$siteconf['views']=$enabledViews;
		$siteconf['model']=$modelClasses;
		$siteconf['modelclass']=$modelViewClass;
		$siteconf['templates']=$appTemplates;
		$siteconf['actions']=$appModules;
		$siteconf['project']=self::$settings;
		
		//echo _r($siteconf['model']);
		
		return $siteconf;
	}
	
	static public function inCache($file)
	{
		// si estamos en debug y no queremos cache, devolvemos false
		// si no estamos en debug, o tenemos activada la cache, seguimos
		if (self::$cache===false) return array(false,false);
		
		$file=str_replace(DIRECTORY_SEPARATOR,"/",$file);
		$root=str_replace(DIRECTORY_SEPARATOR,"/",CUBE_PATH_ROOT);
		
		$original=substr($file,strlen($root));
		$php=preg_replace("/([^\.]*).yml$/","$1.php",$original);
		
		$filecache=str_replace("/",DIRECTORY_SEPARATOR,CUBE_PATH_ROOT."/cache".$php);
		
		$dirs=explode("/",$php);
		$dir=CUBE_PATH_ROOT.DIRECTORY_SEPARATOR."cache";			
		
		//echo _r($dirs)." ".$dir;
		// creamos los directorios que hagan falta
		for ($i=0;$i<(count($dirs)-1);$i++)
		{
			$dir.=$dirs[$i].DIRECTORY_SEPARATOR;
			//echo "<br/> crear ".$dir;
			if (!is_dir($dir)) {mkdir($dir);@chmod($dir,0777);}
			//else echo "--> NO";
		}
		$cache=$filecache; //realpath($filecache);
		//echo " -->".$cache." ".$filecache;
		return array(is_file($cache),$filecache);
		
	}
		
	public function readConfiguration()
	{
		$cache=str_replace(DIRECTORY_SEPARATOR,"/",CUBE_PATH_ROOT)."/apps/".self::$app."/settings.php";
		$incache=self::inCache($cache);
		//var_dump($incache);die();		
		if ($incache[0]===false){
			$data=self::readSiteFiles();
			//Log::_add(__METHOD__,"Read <b>".self::$app."</b> project configuration directories.","load",__CLASS__,Log::ERROR);
			Controller::triggerHook('debug','load',array(
						'message' =>"Read <b>".self::$app."</b> project configuration directories.",
						'type'=>'load',
						'error'=>Log::ERROR,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
			$load=false;
		}
		else{
			//Log::_add(__METHOD__,"Load <b>".self::$app."</b> project configuration from cache","cache",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','cache',array(
						'message' =>"Load <b>".self::$app."</b> project configuration from cache",
						'type'=>'cache',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
			include $incache[1];
			$load=true;
		}
		
		// creamos el fichero en cache
		if (($incache[0]===false && $incache[1]!==false))
		{
			$content='<?php '."\n\n".'$data='.var_export($data,true).';'."\n\n";
			file_put_contents($incache[1], $content);
			//Log::_add(__METHOD__,"Write <b>".self::$app."</b> project configuration settings to cache","save",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','save',array(
						'message' =>"Write <b>".self::$app."</b> project configuration settings to cache",
						'type'=>'save',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
		}
		
		self::$config=$data;
		return $data;		
	}
	
	public function importFile($file) 
	{
		$incache=self::inCache($file);
		
		if ($incache[0]!==false) // si existe
		{ 
			$fileChanged = (filemtime($file)>filemtime($incache[1]));
			
			if (!$fileChanged)
			{
				//echo "<br/>".$file." ".$incache[1]." ".var_export($fileChanged,true);
				include $incache[1];
				//Log::_add(__METHOD__,"Load data '{$file}' from cache","cache",__CLASS__,Log::SUCCESS);
				Controller::triggerHook('debug','cache',array(
						'message' =>"Load data '{$file}' from cache",
						'type'=>'cache',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
				$load=true;
				return $data;
			}
		}
		
		if (is_file($file)){ //es un fichero válido
			
			//$data=Spyc::YAMLLoad($file); // cargamos los datos
			$data=sfYaml::load($file);
			$content='<?php '."\n\n".'$data='.var_export($data,true).'; ?>';
			//Log::_add(__METHOD__,"Load YML '{$file}'","load",__CLASS__,Log::ERROR);
			Controller::triggerHook('debug','load',array(
						'message' =>"Load YML '{$file}'",
						'type'=>'load',
						'error'=>Log::ERROR,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
			
			// creamos el fichero en cache
			if (isset($fileChanged) || $incache[1]!==false)
			{
				$loaded=file_put_contents($incache[1], $content);
				//Log::_add(__METHOD__,"Write data '{$file}' to cache","save",__CLASS__,($loaded!==false)?Log::SUCCESS:Log::NONE);
				Controller::triggerHook('debug','save',array(
						'message' =>"Write data '{$file}' to cache",
						'type'=>'save',
						'error'=>($loaded!==false)?Log::SUCCESS:Log::NONE,
						'class'=>__CLASS__,
						'method'=>__METHOD__));							
				//htmlentities($content);
			}
			return $data;
		}
		
		return array();
		//else throw new CubeException("No se puede importar el archivo", 1);
		
	}
	
	static public function setLocaleConfig($i18n=null){  // TODO: generar la zona horaria segun configuracion del ordenador!
		
		if (isset($i18n['default_timezone'])) date_default_timezone_set($i18n['default_timezone']);  
		
		if (isset($i18n['default_locale'])){
			$loc=$i18n['default_locale'];
			setlocale(LC_ALL, $loc[0],$loc[1],$loc[2],$loc[3]);
		}
	}

	
	static private $models=array();
	 
	public static function modelLoaded($model)
	{
		return (isset(self::$models[$model]));
	}
	
	public static function setModelDir($model,$url)
	{
		self::$models[$model]=$url;
	}
	
	static public function getDirModel($name=null)
	{
		if ($name==null) return self::$models;
		else if (isset(self::$models[$name])) return self::$models[$name];
		throw new CubeException("Model ".$name." not exist",2);
	}
}
?>