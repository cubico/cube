<?php
	define("CUBE_GENERATOR","cubeGenerator");
	
	 function delete_directory($dirname,$bool=false) {
	     if (is_dir($dirname) && substr($dirname,0,1)!='.')
	         $dir_handle = opendir($dirname);
	     if (!$dir_handle)
	         return false;
	     while($file = readdir($dir_handle)) {
	         if (substr($file,0,1)!='.') {
	             if (!is_dir($dirname."/".$file))
	                 unlink($dirname."/".$file);
	             else
	                 delete_directory($dirname.'/'.$file,true);          
	         }
	     }
	     closedir($dir_handle);
	     if ($bool) rmdir($dirname);
	     return true;
	 }
	// emulacion de teclado en linea de comandos
	 function read() {
    	$fp1=fopen("php://stdin", "r");
    	$input=fgets($fp1, 255);
    	fclose($fp1);
	    return $input;
	}
	
	function versionUpgrade($generator,$gendir){
		
		if (file_exists($gendir)){
			$config=sfYaml::load($gendir);
			if ($config[$generator]['class']!='cubeGenerator') return true;
		}else return true;
			
		return false;
	}
	
	function real($path)
	{
		return str_replace("/",DIRECTORY_SEPARATOR,$path);
	}
	
	function getConfig($ROOT){
		include_once real($ROOT."/engine/util/package.class.php"); //dbDriver
		$config=sfYaml::load(real($ROOT."/engine/config.yml"));
		
		// databases.yml
		$databases=real($ROOT."/engine/databases.yml");
		if (realpath($databases)!==false){
			$config2=sfYaml::load($databases);
			
			foreach($config2 as $env=>$data){
				foreach($data as $connection=>$info){
					$config[$env]['database'][$connection]=$info;
				}
			}
		}
		return $config;
	}
	
	
	function execute($argv=array(),$internal=false)
	{
		$argc=count($argv);
		
		
	/// if no pass parameters to script
	if ($argc==2 || $argv[2]=='help') {
		$scriptOption='help';
	}else{
		$scriptName=$argv[1]; // nombre del script de ejecucion
		$scriptOption=$argv[2]; // opcion de menu
		
	}
	
	
	//// execution by parameter
	$script='';
	$root=realpath(".");
	$ROOT=trim($root);
	
	if ($scriptOption=='cc')
	{
		echo "Deleting Cache Content\n";
		error_reporting(~E_ALL);
		delete_directory(realpath($ROOT."/cache"));
		error_reporting(E_ALL);
		return 1;
	}
	error_reporting(E_ALL);
	//////// get params
	$app=null;$plugin=null;$module=null;$model=null;$generator=null;$db=null;$appmodel=null;$project=null;$menu=null;$table=null;$pluginmodel=null;

	foreach($argv as $param)
	{
		if (preg_match("/^-project=(.*)$/",$param,$args)) 	$project=$args[1];
		if (preg_match("/^-menu=(.*)$/",$param,$args)) 		$menu=$args[1];
		
		if (preg_match("/^-plugin=(.*)$/",$param,$args)) 	$plugin=$args[1];
		if (preg_match("/^-app=(.*)$/",$param,$args)) 		$app=$args[1];
		if (preg_match("/^-(mod|module)=(.*)$/",$param,$args)) 		$module=$args[2]; // compatibility with 1.0
		if (preg_match("/^-schema=(.*)$/",$param,$args)) 	$schema=$args[1];
		if (preg_match("/^-table=(.*)$/",$param,$args)) 	$table=$args[1];
		if (preg_match("/^-model=(.*)$/",$param,$args)) 	$model=strtolower($args[1]);
		if (preg_match("/^-(gen|generator)=(.*)$/",$param,$args))	$generator=$args[2]; // compatibility with 1.0
		if (preg_match("/^-db=(.*)$/",$param,$args))				$db=$args[1];
		if (preg_match("/^-appmodel=(.*)$/",$param,$args)) 	$appmodel=$args[1];
		if (preg_match("/^-pluginmodel=(.*)$/",$param,$args)) $pluginmodel=$args[1];
	}
	
	$is_plugin=false;
	$rootDir=$ROOT.'/apps/';

	//echo "1: {$app}, {$module}, {$model}, {$generator}, {$db}, {$appmodel}, {$plugin}, {$pluginmodel}\n";

	if ($pluginmodel!==null){
		$pluginmodel=preg_replace("/Plugin$/i","",$pluginmodel);
		$appmodel=$pluginmodel.'Plugin';
		$rootDir=$ROOT.'/plugins/';
		$is_plugin=true;
	}

	if ($plugin!==null){
		$plugin=preg_replace("/Plugin$/i","",$plugin);
		$app=$plugin.'Plugin';
		$rootDir=$ROOT.'/plugins/';
		$is_plugin=true;
	}

	//echo $scriptOption.": app:{$app}, {$module}, model:{$model}, {$generator}, {$db}, appmodel:{$appmodel}, plugin:{$plugin}, pluginmodel:{$pluginmodel}\n";

	$AppText=($is_plugin)?'Plugin':'Application';

	$viewHelp=false;
	switch($scriptOption)
	{
		case 'plugin':
		case 'app':
					if (empty($plugin) && $scriptOption=='plugin') {	echo "--> ERROR: Plugin name not found\n"; return 2; }
					else if (empty($app) || (!empty($app) && $plugin!==null) && $scriptOption=='app') {	echo "--> ERROR: Application name not found\n"; return 2; }
						
					$dirapps=real($rootDir);if (!is_dir($dirapps)){`mkdir {$dirapps}`;chmod($dirapps,0777);}  // create /apps if not exist
						
					echo "Creating New {$AppText} '".$app."'\n";
					$dir=real($rootDir.$app);
					
					if (!$internal) 
					{
						if (is_dir($dir)) {echo "--> ERROR: {$AppText} '{$app}' exists!\n";return 0;}
					
						/// directorios
						if ($is_plugin) $dirs=array("","/modules","/model");
						else $dirs=array("","/i18n","/model","/modules","/views");

						
						foreach($dirs as $cur) 
						{
							$path=real($dir.$cur);
							`mkdir {$path}`;
							chmod($path,0777);
						}

						$config=getConfig($ROOT);
						
						if (!$is_plugin){
							// fichero de lenguaje
							$lang=$config['all']['settings']['i18n']['default_lang'];
							file_put_contents(real($dir."/i18n/lang.{$lang}.yml"), "all:\n  default:\n    module: Default Module");
							chmod(real($dir."/i18n/lang.{$lang}.yml"),0666);
						}else{
							// manifest
							$i18n=$config['all']['settings']['i18n'];
							Site::setLocaleConfig($i18n);

							$versionCube=strftime("%Y%m%d",time()).str_pad(round($config['all']['settings']['version']), 2, '0', STR_PAD_LEFT);
							file_put_contents(real($dir."/manifest.yml"), "author:\nversion: 0.0\ndescription:\nwebsite:\ncopyright: (C) ".strftime("%Y",time())."\nlicense: GNU Public License version 2\ncube_version: {$versionCube}");
							chmod(real($dir."/manifest.yml"),0666);
						}
					}
					else if (!is_dir($dir)) {echo "--> ERROR: {$AppText} '{$app}' not exists!\n";return 0;}
					//// fichero de configuracion
					
					
/*					
					$content="all:
  routing:
    homepage:
      url:   /
      param: { module: default, action: index }
    default_index:
      url:   /:module
      param: { action: index }
    default:
      url:   /:module/:action/*
  settings:
    i18n:
      default_lang: xx_xx
      enabled: on
    enabled_modules: [default]
  view:
    http_metas:
      content-type: text/html; charset=UTF-8
    metas:
      title:        $app
      description:  cube project
      keywords:     cube, project
      language:     xx
      robots:       index, follow
    #stylesheets:    []
    #javascripts:    []
    has_layout:     on
    layout:         default
  plugins:
  security:
    all:
      is_secure: off
";
*/
		$inline=5;

		if (!$is_plugin){
		$content=array ('all' =>
  				array ('routing' => 
    				array (	'homepage' =>array ('url' => '/','param' => 
        										array (	'module' => 'default',
          												'action' => 'index')),
      						'default_index' =>array ('url' => '/:module','param' => 
        										array ('action' => 'index')),
      						'default' => array ('url' => '/:module/:action/*')),
    					'settings' =>array ('i18n' =>array (
        					'default_lang' => 'ca_es',
        					'enabled' => true),
      					'enabled_modules' =>array ('default')),
    					'view' =>array ('http_metas' =>array ('content-type' => 'text/html; charset=UTF-8'),
      									'metas' =>array (
										        'title' => $app,
										        'description' => 'cube project',
										        'keywords' => 'cube, project',
										        'language' => 'ca',
										        'robots' => 'index, follow',
										      ),
      									'has_layout' => true,
      									'layout' => 'default'),
    					'plugins' => array('cubePlugin'),
    					'security' => array ('all' =>array ('is_secure' => false))
  						));			
		}else{
			$content=array ('all' => array('security' => array ('all' =>array ('is_secure' => false))));
		}
  		include_once real($ROOT."/engine/util/yaml/sfYaml.class.php"); //yaml de symfony
  		//include_once real($ROOT."/engine/util/yml.class.php"); //spyc
  		
  		
		if ($internal && !$is_plugin)	// si es interno , es que estamos en 'admin' y tenemos que hacer un routing con las pk
		{
			if ($appmodel!=null) $dirmodel=real($rootDir.$appmodel."/model/".$model);
			else {echo "--> ERROR: {$AppText} name not found\n"; return 0;}
			
			//$data=Spyc::YAMLLoad(real($dirmodel."/schema.yml"));
			$data=sfYaml::load(real($dirmodel."/schema.yml"));
			
			$pks=array();
			foreach($data['columns'] as $key=>$value)
			{
				if (isset($value['pk']) && $value['pk']===true) $pks[]=$model.".".$key;
			}
			
			if (file_exists(real($dir."/config.yml"))){ // si ya existe fichero de configuracion lo cogemos
				//$content=Spyc::YAMLLoad(real($dir."/config.yml"));
				$content=sfYaml::load(real($dir."/config.yml"));
				//unlink(real($dir."/config.yml"));
			}	
			
			if (isset($content[0]) && $content[0]=='--') unset($content[0]); //borramos ---
			
			if (isset($content['all']['routing'])){
				$modrouting=array($module=>array("url"=>"/:module/:action/:".implode("/:",$pks),
												 "param"=>array("module"=>$module))
								  );
				$content['all']['routing']=array_merge($modrouting,$content['all']['routing']);
			}
			
			
			
			if ($project!==null)
			{
				// generamos archivo admin
				$ga=real($dir."/genadmin.yml");
				if (!file_exists($ga)){
					$cadmin=array("admin"=>
								array(
									$module=>"-app={$app} -mod={$module} -generator={$generator} -model={$model} -appmodel={$appmodel}"
									//,"fin"=>""
								 ));
								 
					//$genadmin=Spyc::YAMLDump($cadmin);
					$genadmin=sfYaml::dump($cadmin,$inline);
					file_put_contents($ga,$genadmin);
					chmod($ga,0666);
				}else{
					//$co_genadmin=Spyc::YAMLLoad($ga);
					$co_genadmin=sfYaml::load($ga);
					if (isset($co_genadmin[0]) && $co_genadmin[0]=='--') unset($co_genadmin[0]); //borramos ---
					if (!isset($co_genadmin['admin'][$module])){
						$a=array();
						$a[$module]="-app={$app} -mod={$module} -generator={$generator} -model={$model} -appmodel={$appmodel}";
						$a=array_merge_recursive(array("admin"=>$a),$co_genadmin);
						//$genadmin=Spyc::YAMLDump($a);
						$genadmin=sfYaml::dump($a,$inline);
						file_put_contents($ga,$genadmin);
						chmod($ga,0666);
					}
				}
			}
		}

		$configApp=real($dir."/config.yml");
		if (!$is_plugin || ($is_plugin && !file_exists($configApp))){
			//$datos=Spyc::YAMLDump($content);
			$datos=sfYaml::dump($content,$inline);
			file_put_contents($configApp,$datos);
			chmod($configApp,0666);
		}

		if (!$internal && !$is_plugin)
		{
			
		$content="<?php
	include_once dirname(__FILE__).\"/../engine/util/package.class.php\";
	import(\"engine.util.*\");
	
	\$site=Site::getInstance(array('app'=>'{$app}','debug'=>false));
	\$conf=\$site->readConfiguration();
	Controller::createInstance(\$conf)->init();
?>";

						file_put_contents(real($ROOT."/web/{$app}.php"),$content);
						chmod(real($ROOT."/web/{$app}.php"),0666);
						$content="<?php
	if (!in_array(@\$_SERVER['REMOTE_ADDR'], array('127.0.0.1'))) die('You are not allowed to access this file.');
	
	include_once dirname(__FILE__).\"/../engine/util/package.class.php\";
	import(\"engine.util.*\");
	
	\$site=Site::getInstance(array('app'=>'{$app}','debug'=>true,'cache'=>false));
	\$conf=\$site->readConfiguration();
	Controller::createInstance(\$conf)->init();
?>";
						file_put_contents(real($ROOT."/web/{$app}_dev.php"),$content);
						chmod($ROOT."/web/{$app}_dev.php",0666);
		}
		
		
					break;
		case 'mod':
		case 'module': // compatibility with 1.0
		
					include_once real($ROOT."/engine/util/yaml/sfYaml.class.php"); //yaml de symfony
			
					if ($module===null) {echo "--> ERROR: Module name?\n"; return 0;}
					if ($app===null) {echo "--> ERROR: {$AppText} name?\n"; return 0;}
					echo "Creating New Module '".$module."' for {$AppText} '".$app."'\n";
					
					$dir=real($rootDir.$app);
					if (!is_dir($dir)) {echo "--> ERROR: {$AppText} '{$app}' not exists!\n"; return 0;}
					
					$dir=real($rootDir.$app."/modules/".$module);
					if (is_dir($dir)) {echo "--> ERROR: Module '{$module}' exists!\n"; return 2;}
					
					if ($appmodel!=null) $dirgen=real($rootDir.$appmodel."/model/".$model);
					else $dirgen=real($rootDir.$app."/model/".$model); // si no pasamos appmodel , creamos el modelo en la misma aplicación
					
		//if (!$internal || (!file_exists(real($dir."/generators/{$generator}.yml")) && $internal))
		$gendir=real($dirgen."/generators/{$generator}.yml");
		$version=versionUpgrade($generator,$gendir);
		
		if (CUBE_GENERATOR!='cubeGenerator' && ($version || !$internal)){ // versiones que no vayan con templates y si con vistas
			
				$dirs=array("","/views","/views/default","/views/default/template"); 
					
				foreach($dirs as $cur) 
				{
					$path=real($dir.$cur);
					
					`mkdir {$path}`;
					chmod($path,0777);
				}
				
				if (!$internal){
				
			$content="<?php
	class ".ucfirst($module).ucfirst(($is_plugin)?$app:'')."Actions extends Actions
	{
		public function executeIndex(\$request)
		{
			\$this->setView('index',array('content'=>Viewer::_echo('default:module').' ".ucfirst($module)."'),'default');
		}
	} 
?>";				
			file_put_contents(real($dir."/actions.class.php"),$content);
			chmod(real($dir."/actions.class.php"),0666);
							
			file_put_contents(real($dir."/views/default/template/index.php"),"<?php echo \$vars['content']; ?>");
			chmod(real($dir."/views/default/template/index.php"),0666);
			
			}
		}else{
				
			$dirs=array("","/templates"); //cube 1.0
					
				foreach($dirs as $cur) 
				{
					$path=real($dir.$cur);
					
					`mkdir {$path}`;
					chmod($path,0777);
				}

			if (!$internal){
			// fichero de accion por defecto
				$content="<?php
	class ".ucfirst($module).ucfirst(($is_plugin)?$app:'')."Actions extends Actions
	{
		public function executeIndex(\$request)
		{
			\$this->content=Viewer::_echo('default:module').' ".ucfirst($module)."';
		}
	} 
?>";				
		
				file_put_contents(real($dir."/actions.class.php"),$content);
				chmod(real($dir."/actions.class.php"),0666);
						
				file_put_contents(real($dir."/templates/index.php"),"<?php echo \$content; ?>");
				chmod(real($dir."/templates/index.php"),0666);
			}
		
		}
		
	  	$dirc=real($dir."/config.yml");
      	if (!file_exists($dirc)){
      		$content="all:
  settings:
    i18n:
      default_lang: ca_es
      enabled: on
  view:
    http_metas:
      content-type: text/html; charset=UTF-8
    metas:
      title:        {$module}
      description:  cube project
      keywords:     cube, project
      language:     ca
      robots:       index, follow
    #stylesheets:    []
    #javascripts:    []
    has_layout:     on
    layout:         default
  plugins:
  security:
    all:
      is_secure: off
";	
			file_put_contents($dirc,$content);
			chmod($dirc,0666);
      	}				
	
		
		break;
		
		case 'schema': // si es internal se crea en el make.	
				if ($model===null) {echo "--> ERROR: Model name not found\n"; return 0;}
				if ($db===null) {echo "--> ERROR: Database name not found\n"; return 0;}
				
				if ($table===null) {
					//echo "--> ERROR: Table name not found\n"; return 0;
					$table=strtoupper($model);
				}
	 
				if ($appmodel!=null) $dir=real($rootDir.$appmodel."/model/".$model);
				else {echo "--> SCHEMA ERROR: appmodel option must be activaded.\n"; return 0;}
					
				if (is_dir($dir)) {echo "--> WARNING: Model '{$model}' exists. \n";}
				else echo "Creating New Schema '".$model."' in '{$dir}'\n";
				
				`mkdir {$dir}`;
				@chmod($dir,0777);
					
				if ($db!==null) {	// si pasamos db creamos el schema.
				/// existe schema?
					if (!file_exists(real($dir."/schema.yml")))
					{
						include_once real($ROOT."/engine/util/package.class.php"); //dbDriver
						
						//$config=Spyc::YAMLLoad(real($ROOT."/engine/config.yml"));
						//$config=sfYaml::load(real($ROOT."/engine/config.yml"));
						$config=getConfig($ROOT);
						//echo "\n".$data['database'];
						if (!isset($config['all']['database'][$db])) { echo "Database ".$db." not found."; return 0;}
						
						$db2=$config['all']['database'][$db];
						
						include_once real($ROOT."/engine/".str_replace(".","/",$db2['package']).".php");
						include_once real($ROOT."/plugins/cubePlugin/model/util/object/Util.php");
						
						$dr=db::create($db2,$model);
						$info=$dr->extractSchemaInfo($table,$dir.'/..',$ROOT);
						
						foreach($info as $k=>$v){ if (isset($v['pk'])) {$count=$k;break;}}
						$columns=implode(", ",array_keys($info));
						$genadmin=sfYaml::dump(array('columns'=>$info),2);

						$content="table: ".$table."
class: ".ucfirst($model)."
database: ".$db."
  #column: {type: , phpname: , params: { format: %d/%m/%Y %H:%M:%S, null: , default: , comment:  }, fk: , pk: , autonumeric: , sequence: }
{$genadmin}querys: 
  #queryName: query {table} {sequence}
  count: \"select count({$count}) {count} from {table}\"
  doSelectTable: \"select {$columns} from {table}\"";
  					file_put_contents(real($dir."/schema.yml"),$content);
					@chmod(real($dir."/schema.yml"),0666);
					}else{echo "--> ERROR: Schema '{$model}' exists. Not created.\n";}
				}
				break;
		case 'object':
				if ($model===null) {echo "--> ERROR: Model name not found\n"; return 0;} 
					
					/// ./cube model [model] ([app]|[])
					
				if ($appmodel!=null) $dir=real($rootDir.$appmodel."/model/".$model);
				else {echo "--> OBJECT ERROR: appmodel option must be activaded.\n"; return 0;}
					
				if (is_dir($dir)) {echo "--> WARNING: Model '{$model}' exists. \n";}
				else echo "Creating New Model '".$model."' in '{$dir}'\n";
					///// crea directorios
				$dirs=array("","/object");
					
				foreach($dirs as $cur) 
				{
					$path=real($dir.$cur);
					`mkdir {$path}`;
					@chmod($path,0777);
				}
					
				if ($db!==null) {	// si pasamos db creamos el schema.
					
					// existe objeto clase?
					if (!file_exists(real($dir."/object/".ucfirst($model).".php"))){
						$content="<?php
class ".ucfirst($model)." extends Cube{
	// // Executed at Cube::delete() method
	public function logicDelete(){	
		// \$this->
		//return true;	// not execute save method (ex. condition credentials) 
	}

	// Executed at Cube::save() method
	public function logicInsert(){
		// \$this->
	}
	
	// Executed at Cube::getObjectFilter() method
	public function logicFilter(){
		// \$this->
	}
		
	public function __construct(){
		\$this->setLogic(false);	// logic delete 
	}
}
?>";
						file_put_contents(real($dir."/object/".ucfirst($model).".php"),$content);
						@chmod(real($dir."/object/".ucfirst($model).".php"),0666);
					}
					
					// existe objeto peer?
					if (!file_exists(real($dir."/object/".ucfirst($model)."Peer.php"))){
						$content="<?php
class ".ucfirst($model)."Peer extends CubePeer{
	public function configure()
	{
	
	}
	
}
?>";
						file_put_contents(real($dir."/object/".ucfirst($model)."Peer.php"),$content);
						@chmod(real($dir."/object/".ucfirst($model)."Peer.php"),0666);
					}
					
					if (file_exists(real($dir."/schema.yml"))){
						/// crear los peerMethods de las fk de la clase
						include_once real($ROOT."/engine/util/package.class.php"); //dbDriver 
						
						/// se supone que ahora en el schema, que ya existe, estan los datos buenos de la BD
						$data=sfYaml::load(real($dir."/schema.yml"));
						
						//$config=Spyc::YAMLLoad(real($ROOT."/engine/config.yml"));
						//$config=sfYaml::load(real($ROOT."/engine/config.yml"));
						$config=getConfig($ROOT);
						//echo "\n".$data['database'];
						if (!isset($config['all']['database'][$data['database']])) { echo "Database ".$data['database']." not found."; return 0;}
						
						$db=$config['all']['database'][$data['database']];
						
						include_once real($ROOT."/engine/".str_replace(".","/",$db['package']).".php");
						
						$driver=$db['driver'];
						$dr=new $driver(null);
						$file=real($dir."/object/".ucfirst($model)."Peer.php");
						
						$info=$dr->generatePeerMethodsFK(ucfirst($model),$data['columns'],$dir.'/..',$file,$ROOT);
							
					}else{ 
						echo "Schema ".real($dir."/schema.yml")." not found."; return 0;
					} 
					
					
				}else{	// creamos la clase (que no extiende de Cube!)

						// existe objeto clase?	
						if (!file_exists(real($dir."/object/".ucfirst($model).".php"))){
					$content="<?php
class ".ucfirst($model)."{
	
}
?>";
							file_put_contents(real($dir."/object/".ucfirst($model).".php"),$content);
							@chmod(real($dir."/object/".ucfirst($model).".php"),0666);
						}
				}
					break;
		case 'generate': // compatibility with 1.0
		case 'gen':
					if ($model===null) {echo "--> ERROR: Model name?\n"; return 0;}
					if ($generator===null) {echo "--> ERROR: Generator name?\n"; return 0;}
					
					/// ./cube model [model] ([app]|[])
					
					if ($appmodel!=null) $dir=real($rootDir.$appmodel."/model/".$model);
					else $dir=real($rootDir.$app."/model/".$model);
					
					if (!is_dir($dir)) {echo "--> ERROR: Model '{$model}' not exists!\n"; return 0;}
					echo "Creating New Generator '{$generator}' of '".$model."'\n-- {$dir} --\n";
					///// crea directorios
					$dirs=array("/generators");
					
					foreach($dirs as $cur) 
					{
						$path=real($dir.$cur);
						if (!file_exists($path))
						{
							//echo $path;
							`mkdir {$path}`;
							@chmod($path,0777);
						}
					}
					
					///// leer schema.yml y crear generator.yml
					include_once real($ROOT."/engine/util/cubeException.php"); //cubeException
					//include_once real($ROOT."/engine/util/controller.class.php"); //cubeException
					include_once real($ROOT."/engine/util/yaml/sfYaml.class.php"); //yaml de symfony
					//include_once real($ROOT."/engine/util/yml.class.php"); //spyc
					include_once real($ROOT."/engine/util/model.class.php"); //dbDriver 
					
					include_once real($ROOT."/engine/util/site.class.php"); //locale
					 
					//$data=Spyc::YAMLLoad(real($dir."/schema.yml"));
					$data=sfYaml::load(real($dir."/schema.yml"));
					
					//$config=Spyc::YAMLLoad(real($ROOT."/engine/config.yml"));
					//$config=sfYaml::load(real($ROOT."/engine/config.yml"));
					$config=getConfig($ROOT);
					//echo "\n".$data['database'];
					if (!isset($config['all']['database'][$data['database']])) { echo "Database ".$data['database']." not found."; return 0;}
					
					$db=$config['all']['database'][$data['database']];
					
					include_once real($ROOT."/engine/".str_replace(".","/",$db['package']).".php");
					Site::setLocaleConfig($config['all']['settings']['i18n']);
					
					$driver=$db['driver'];
					$dr=new $driver(null);
					$pks=array();
					$info=$dr->generate(ucfirst($model),$data['columns'],$pks);
					if (!empty($pks)) $pks='/:'.implode('/:',$pks);else $pks='';
					//echo _r($pks);die();
					$render='';
					$keys=array_keys($data['columns']);
					foreach($keys as $key)
					{
						$render.="\n        {{$key}}";
					}
					
					$render_grid=preg_replace("/{([^\}]*)}/","{\$$1}",$render);
					
					$render_actions="\n        |{submit}|{reset}|{new}|{list}|{delete}|{print}|{pdfform}|{previous}|{next}|";
					$render_actions_row="\n        {edit}.{show}.{deleterow}";
					$render_actions_menus="\n        -{*new}-{*list}-{*print}-{*pdfform}
        -{*submit}-{*reset}-
        -{*delete}-";
					
					$generatorClass=CUBE_GENERATOR;
					
					$content="{$generator}:
  class: {$generatorClass}
  package: engine.drivers.generators
  param:
    layout: generator
  fields:".$info."
    gofilter:
      view: [input/button, {img: /img/icon/funnel.png, value: _echo(gofilter), js: {onclick: $(this).parents('form').submit();}}]  
      credentials:
    clearfilters:
      view: [input/button, {img: /img/icon/arrow_circle_135.png, type: button, value: _echo(button:clear:filters), action: clearfilters}]
  actions:  
    submit:
      view: [input/button, {img: /img/icon/disk_black.png, value: _echo(submit), js: {onclick: $('form:first').submit();}}]
      credentials:  
    reset:
      view: [input/reset, {img: /img/icon/arrow_circle.png, value: _echo(reset), js: {onclick: $('form:first')[0].reset();}}]
      credentials:
    new:
      view: [input/button, {img: /img/icon/wand.png, type: button, value: _echo(new), action: new }]
      credentials: [-:new]
    list:
      view: [input/button, {img: /img/icon/menu.png, class: green_button, value: _echo(list), action: list}]
      credentials:
    print: 
      view: [input/button, {img: /img/icon/printer.png, class: orange_button, value: _echo(button:printversion), js: {onclick: \"window.open(window.location.pathname+$.query.set('viewer','print').toString());\" }}]
      credentials:
    pdflist:
      view: [input/button, {img: /img/crystal/16x16/mimetypes/pdf-document.png, class: orange_button, value: _echo(button:pdfversion-list), js: {onclick: \"window.open(window.location.pathname+$.query.set('viewer','pdf-l').toString());\" }}]
      credentials:
    xlslist:
      view: [input/button, {img: /img/icon/document_excel.png, class: orange_button, value: _echo(button:xlsversion-list), js: {onclick: \"window.open(window.location.pathname+$.query.set('viewer','xls').toString());\" }}]
      credentials:
    pdfform:
      view: [input/button, {img: /img/crystal/16x16/mimetypes/pdf-document.png, class: orange_button, value: _echo(button:pdfversion-form), js: {onclick: \"window.open(window.location.pathname+$.query.set('viewer','pdf').toString());\" }}]
      credentials:      
    openfilters: 
      view: [input/button, {img: /img/icon/magnifier.png, class: green_button, value: _echo(button:open:filters), js: {onclick: \"openFilters();\"}}]
    edit: 
      view: [output/link, {title: _echo(button:edit), action: edit, img: <img src=\"/img/icon/card__pencil.png\" />}]
      credentials:
    show: 
      view: [output/link, {title: _echo(button:show), action: show, img: <img src=\"/img/icon/card_address.png\" />}]
      credentials:
    deleterow: 
      view: [output/confirmlink, {title: _echo(button:delete), action: delete, confirm: _echo(grid:delete:row), img: <img src=\"/img/icon/cross.png\" />}]
      credentials: 
    delete: 
      view: [input/confirmbutton, {img: /img/icon/cross_circle_frame.png, class: red_button, action: delete{$pks}, confirm: _echo(grid:delete:item), value: _echo(button:delete) }]
      credentials: [:edit]
    batch_options:
      view: [input/pulldown, {blank_option: _echo(form:list:withselection), options_values: {batchdelete: _echo(button:delete)}}]
      credentials:
    next:
      view: [input/button, {img: /img/icon/arrow_skip.png, class: purple_button, action: edit/:next, value: '', title: _echo(next) }]
      credentials: [:edit, has_next]
    previous:
      view: [input/button, {img: /img/icon/arrow_skip_180.png, class: purple_button, value: '', action: edit/:previous, title: _echo(previous) }]
      credentials: [:edit, has_previous]
  form:    
    ajax: false
    ajax_validators: false
    layout: one_column
    render: 
      new: |".$render."
      edit: |".$render."
    actions: 
      buttons: |".$render_actions."
      menus: |".$render_actions_menus."
    params:
      name: ".$model.ucfirst($generator)."Form
      action:
      #method: POST
      #enctype: text/html 
    model_order: 
      #new:  []
      #edit: []      
  list:
    ajax: false
    layout: one_column
    width: [15px, *]
    #scroll: {x: off, y: 300px auto}
    render: 
      grid: |".$render_grid."
      object: |".$render."
    batch_actions: |
      {batch_options}
    actions: 
      buttons: |
        |{new}|{openfilters}|{print}|{pdflist}|{xlslist}|
      menus: |
        -{*new}-{*print}-
      rows: |".$render_actions_row."
    # layout_filter: menu/list
    layout_filter: list
    filters: |".$render."
        |{gofilter}|{clearfilters}|
    pagination: {active: on, pulldown: true, min: 10, max: 100, inc: 10}
    show_numbers: false
    default_query: true
    default_sort:
    default_filter:
    query: doSelectTable
    ";
					if (!$internal || (!file_exists(real($dir."/generators/{$generator}.yml")) && $internal))
					{
						file_put_contents(real($dir."/generators/{$generator}.yml"),$content);
						@chmod(real($dir."/generators/{$generator}.yml"),0666);
					}
					break;
		
		case 'make':
					error_reporting(~E_ALL);
					
					if ($model===null) { echo "--> ERROR: Model name?\n"; return 0;}
					if ($generator===null) {echo "--> ERROR: Generator name?\n"; return 0;}
					//$view=$generator;
					$view=$model;
					
					if ($appmodel!=null) $dirmodel=real($rootDir.$appmodel."/model/".$model);
					else $dirmodel=real($rootDir.$app."/model/".$model);
					
					include_once real($ROOT."/engine/util/package.class.php"); //spyc
					import("engine.util.*");
					$route=new Route();
					Request::createInstance(array());
					
					$file=real($dirmodel."/generators/".$generator.".yml");
					
					//////////////////////////
					//$data=Spyc::YAMLLoad($file);
					$data=sfYaml::load($file);
					//var_dump($data);die();
					
					if (!isset($data[$generator])) {echo "--> ERROR: Not found generator '{$generator}\n{$file}'"; return 0;}
					
					$mtime = substr(str_replace(' ','',microtime()),2);
					//$mtime=microtime();
					
					$params=array(	'name'=>'object'.$mtime,
							'method'=>'POST',
							'enctype'=>'text/html',
							'action'=>'');
					
					if (is_array($data[$generator]['form']['params'])) 
						$params=array_merge($params,$data[$generator]['form']['params']);
					
					$data1=$data[$generator]['fields'];
					
					if (isset($data[$generator]['form']['model_order'])) $modelorder=$data[$generator]['form']['model_order'];
					else $modelorder=null;
					
					///////////// list
					
					$genClass=$data[$generator]['class'];
					include_once real($ROOT."/".str_replace(".","/",$data[$generator]['package'])."/".$genClass.".php");
					
					
					$form=new Form($params['name'],$params['action'],$params['method'],$params['enctype'],$modelorder);

					if (isset($params['tabs']) && $params['tabs']) $input = 'input/form_tabs'; else  $input = 'input/form';	// manu				
					$root=new FormElement(array(	'type'=>'form',
													'params'=>array(
														'name'=>$params['name'],
														'view'=>array($input,array(
																'name'=>$params['name'],
																'internalid'=>$params['name'],
																'internalname'=>$params['name'],
																'method'=>$params['method'],
																'action'=>$params['action'],
																'model_order'=>$modelorder,
																'ajax'=>$data[$generator]['form']['ajax']
													))
											)),'FormElement');
					
					$root->setName($params['name']);
					
					$root_new=clone $root;
					$form_new=clone $form;
					$form_new->setRoot($root_new);
					
					$root_edit=clone $root;
					$form_edit=clone $form;
					$form_edit->setRoot($root_edit);
					
					
					$root_filters=new FormElement(array(	'type'=>'form',
													'params'=>array(
														'name'=>$params['name']."Filter",
														'view'=>array('input/form',array(
																'name'=>$params['name']."Filter",
																'internalid'=>$params['name']."Filter",
																'internalname'=>$params['name']."Filter",
																'method'=>"POST",
																'action'=>'./filter',
																'model_order'=>null,
																'ajax'=>$data[$generator]['list']['ajax']))
											)),'FormElement');
					
					//echo _r($root_filters);
					$root_filters->setName($params['name']."Filter");
					
					$form_filters=new Form($params['name']."Filter","filter","POST");
					$form_filters->setRoot($root_filters);
					
					////////////// actions buttons - form ///////////
					$ra=clone $root;
					$fa=clone $form;
					$fa->setRoot($ra);
					
					$rend=$data[$generator]['form']['actions']['buttons'];
					$rows=explode("\n",$rend);
					$data3=array();
					foreach($rows as $row){Form::renderElement($row,$data3);}
					
					// pongo grupo para que cuando haga firstChild, me pille el grupo, no el form
					$datos=array("__group_actions"=>$data3);
					$fa->setRender($datos);
					$body_actions='';
					$fa->setFields($data[$generator]['actions']);
					$ra=$fa->render();
					
					$ra->firstChild()->render($body_actions);
					
					//////////// actions menus - form /////////////////
					
					$ra2=clone $root;
					$fa2=clone $form;
					$fa2->setRoot($ra2);
					
					$rend2=$data[$generator]['form']['actions']['menus'];
					$rows=explode("\n",$rend2);
					$data32=array();
					
					foreach($rows as $row){Form::renderElement($row,$data32);}
					// pongo grupo para que cuando haga firstChild, me pille el grupo, no el form
					$datos=array("__group_actions"=>$data32);
					$fa2->setRender($datos);
					$body_actions2='';
					$fa2->setFields($data[$generator]['actions']);
					$ra2=$fa2->render();
					$ra2->firstChild()->render($body_actions2);

					//////////// actions menus - list /////////////////
					
					$ra3=clone $root;
					$fa3=clone $form;
					$fa3->setRoot($ra3);
					
					$rend3=$data[$generator]['list']['actions']['menus'];
					$rows=explode("\n",$rend3);
					$data33=array();
					
					foreach($rows as $row){Form::renderElement($row,$data33);}
					// pongo grupo para que cuando haga firstChild, me pille el grupo, no el form
					$datos=array("__group_actions"=>$data33);
					$fa3->setRender($datos);
					$body_actions3='';
					$fa3->setFields($data[$generator]['actions']);
					$ra3=$fa3->render();
					$ra3->firstChild()->render($body_actions3);
					
					//////////// actions buttons - list /////////////////
					
					$ra4=clone $root;
					$fa4=clone $form;
					$fa4->setRoot($ra4);
					
					$rend4=$data[$generator]['list']['actions']['buttons'];
					$rows=explode("\n",$rend4);
					$data34=array();
					
					foreach($rows as $row){Form::renderElement($row,$data34);}
					// pongo grupo para que cuando haga firstChild, me pille el grupo, no el form
					$datos=array("__group_actions"=>$data34);
					$fa4->setRender($datos);
					$body_actions4='';
					$fa4->setFields($data[$generator]['actions']);
					$ra4=$fa4->render();
					$ra4->firstChild()->render($body_actions4);
					
					//////////// actions rows - list /////////////////
					
					$ra5=clone $root;
					$fa5=clone $form;
					$fa5->setRoot($ra5);
					
					$rend5=$data[$generator]['list']['actions']['rows'];
					$rows=explode("\n",$rend5);
					$data35=array();
					
					foreach($rows as $row){Form::renderElement($row,$data35);}
					// pongo grupo para que cuando haga firstChild, me pille el grupo, no el form
					$datos=array("__group_actions"=>$data35);
					$fa5->setRender($datos);
					$body_actions5='';
					$fa5->setFields($data[$generator]['actions']);
					$ra5=$fa5->render();
					$ra5->firstChild()->render($body_actions5);
					
					$body_actions5=preg_replace("/'internalname' => '(.*)',/","'internalname' => '$1','entity'=>\$vars['values'],",$body_actions5);
					$body_actions5=preg_replace("/'action' => '(.*)',/","'action' => '$1/'.\$vars['params']['pks'],",$body_actions5);
					//////////// actions object - list /////////////////
					
					$ra6=clone $root;
					$fa6=clone $form;
					$fa6->setRoot($ra6);
					
					$rend6=$data[$generator]['list']['render']['object'];
					$rows=explode("\n",$rend6);
					$data36=array();
					
					foreach($rows as $row){Form::renderElement($row,$data36);}
					// pongo grupo para que cuando haga firstChild, me pille el grupo, no el form
					$datos=array("__group_actions"=>$data36);
					$fa6->setRender($datos);
					$body_actions6='';
					$fa6->setFields($data[$generator]['actions']);
					$ra6=$fa6->render();
					$ra6->firstChild()->render($body_actions6);
					
					//////////// actions batch_actions - list /////////////////
					
					$ra61=clone $root;
					$fa61=clone $form;
					$fa61->setRoot($ra61);
					
					$rend61=$data[$generator]['list']['batch_actions'];
					
					$rows=explode("\n",$rend61);
					$data361=array();
					
					foreach($rows as $row){Form::renderElement($row,$data361);}
					// pongo grupo para que cuando haga firstChild, me pille el grupo, no el form
					$datos=array("__group_actions"=>$data361);
					$fa61->setRender($datos);
					$body_actions61='';
					$ac=$data[$generator]['actions'];
					
					foreach($ac as $current=>$datac){
						$ac[$current]['view'][1]['class']='batch_option';
					}
					
					$fa61->setFields($ac);
					$ra61=$fa61->render();
					$ra61->firstChild()->render($body_actions61);
					//$body_actions61=preg_replace("/'mode' => '',/","'class' => 'batch_option',",$body_actions61);
										
					/////////// filters /////////////
					//$rend7=$data[$generator]['form']['render']['edit']; //$data[$generator]['list']['filters'];
					$rend7=$data[$generator]['list']['filters'];
					$rows=explode("\n",$rend7);
					$data7=array();
					foreach($rows as $row){Form::renderElement($row,$data7);}
					$data7['__row_end']="{__formaction__}";
					//echo _r($data7);die();
					
					$form_filters->setRender($data7); //true: son filtros!
					
					$body='';
					
					$data1['__formaction__']=array('view'=>array('input/hidden',array('value'=>'filters')));
					/*
					$data1['gofilter']=array('view'=>array("input/button", array("type"=>"submit", "value"=>"_echo(gofilter)")),"credentials"=>""); 
					
					$data1['clearfilters']=array('view'=>array(	"input/button", 
														array(	"type"=>"button", 
																"value"=>"_echo(button:clear:filters)", 
																"action"=>"clearfilters")),
												"credentials"=>"");
					*/
					
					$form_filters->setFields($data1);
					$root_filters=$form_filters->render("filter");
										
					$root_filters->render($body);
					
					$body_filters=$body;
					
					/////////////// edit ///////////
					$rend=$data[$generator]['form']['render']['edit'];
					$rows=explode("\n",$rend);
					$data3=array();
					foreach($rows as $row){Form::renderElement($row,$data3);}
					$data3['__row_end']="{__formaction__}";
					$form_edit->setRender($data3);
					$body='';
					$data1['__formaction__']=array('view'=>array('input/hidden',array('value'=>"edit")));
					$form_edit->setFields($data1);
					$root_edit=$form_edit->render("edit");
					$root_edit->render($body);
					$body_edit=$body;
					$body_show="<?php Viewer::setGlobalTemplate(Config::get('settings:views:global_template_print_value')); ?>\n".$body."\n<?php Viewer::setGlobalTemplate(Config::get('settings:views:global_template')); ?>";
					/////////////// new ///////////
					if (isset($data[$generator]['form']['render']['new']))
					{
						$rend=$data[$generator]['form']['render']['new'];
						$rows=explode("\n",$rend);
						$data2=array();
						foreach($rows as $row){Form::renderElement($row,$data2);}
						$data2[]="{__formaction__}";
						$form_new->setRender($data2);
						$body='';
						$data1['__formaction__']=array('view'=>array('input/hidden',array('value'=>"new")));
						$form_new->setFields($data1);
						$root_new=$form_new->render("new");
						$root_new->render($body);
						$body_new=$body;
					}
					else 
					{
						$root_new=$root_edit;
						$body_new=$body_edit;
					}
					
					$dirs=array("/views","/views/generator","/views/generator/".$view,
								"/views/generator/".$view."/form","/views/generator/".$view."/object",
								"/views/generator/".$view."/form/new","/views/generator/".$view."/form/edit",
								"/views/generator/".$view."/form/show",
								"/views/generator/".$view."/object/new","/views/generator/".$view."/object/edit",
								"/views/generator/".$view."/object/filters","/views/generator/".$view."/object/fields",
								"/views/generator/".$view."/form/validators",			
								"/views/generator/".$view."/form/actions",
								"/views/generator/".$view."/form/actions/buttons",
								"/views/generator/".$view."/form/actions/menus",
								"/views/generator/".$view."/list",
								"/views/generator/".$view."/list/actions/",
								"/views/generator/".$view."/list/actions/buttons",
								"/views/generator/".$view."/list/actions/menus",
								"/views/generator/".$view."/list/actions/rows",
								"/views/generator/".$view."/list/actions/batch",
								"/views/generator/".$view."/list/element",
								"/views/generator/".$view."/list/grid",
								"/views/generator/".$view."/list/headers",
								"/views/generator/".$view."/list/filters"
								);
					
					foreach($dirs as $cur) 
					{
						$path=real($dirmodel.$cur);
						if (!file_exists($path))
						{
							//echo $path;
							`mkdir {$path}`;
							@chmod($path,0777);
						}
					}
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/form/new/{$generator}.php"),$body_new);
					@chmod(real($dirmodel."/views/generator/{$view}/form/new/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/form/edit/{$generator}.php"),$body_edit);
					@chmod(real($dirmodel."/views/generator/{$view}/form/edit/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/form/show/{$generator}.php"),$body_show);
					@chmod(real($dirmodel."/views/generator/{$view}/form/edit/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/form/actions/buttons/{$generator}.php"),$body_actions);
					@chmod(real($dirmodel."/views/generator/{$view}/form/actions/buttons/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/form/actions/menus/{$generator}.php"),$body_actions2);
					@chmod(real($dirmodel."/views/generator/{$view}/form/actions/menus/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/object/new/{$generator}.php"),base64_encode(serialize($root_new)));
					@chmod(real($dirmodel."/views/generator/{$view}/object/new/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/object/edit/{$generator}.php"),base64_encode(serialize($root_edit)));
					@chmod(real($dirmodel."/views/generator/{$view}/object/edit/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/object/filters/{$generator}.php"),base64_encode(serialize($root_filters)));
					@chmod(real($dirmodel."/views/generator/{$view}/object/filters/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/object/fields/{$generator}.php"),base64_encode(serialize($data1)));
					@chmod(real($dirmodel."/views/generator/{$view}/object/fields/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/list/actions/menus/{$generator}.php"),$body_actions3);
					@chmod(real($dirmodel."/views/generator/{$view}/list/actions/menus/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/list/actions/buttons/{$generator}.php"),$body_actions4);
					@chmod(real($dirmodel."/views/generator/{$view}/list/actions/buttons/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/list/actions/rows/{$generator}.php"),$body_actions5);
					@chmod(real($dirmodel."/views/generator/{$view}/list/actions/rows/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/list/actions/batch/{$generator}.php"),$body_actions61);
					@chmod(real($dirmodel."/views/generator/{$view}/list/actions/batch/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/list/filters/{$generator}.php"),$body_filters);
					@chmod(real($dirmodel."/views/generator/{$view}/list/filters/{$generator}.php"),0666);
					
					file_put_contents(real($dirmodel."/views/generator/{$view}/{$view}.php"),$body_actions6);
					@chmod(real($dirmodel."/views/generator/{$view}/{$view}.php"),0666);
					
					
					error_reporting(E_ALL);
					
					// instancia del generador definido en generator.yml
					$gen=new $genClass(array(	"model"=>$model,"generator"=>$generator,"appmodel"=>$appmodel,"module"=>$module,
														"app"=>$app,"plugin"=>$plugin,"pluginmodel"=>$pluginmodel),$ROOT,$dirmodel);
					
					// creamos las vistas grid
					$gen->generateGrid();
					
					// creamos las acciones para administrar las vistas form y list
 					if ($internal) $gen->generateActions($is_plugin);
 					
					$gen->generateValidators(real($dirmodel."/views/generator/{$view}/form/validators/{$generator}.php"));
					///////////////////
					echo "Make New View '{$view}' from model {$model}\n{$file}\n-- dirmodel: {$dirmodel} --\n";
					//echo var_export($kk,true);
					//echo var_export($root,true);
					
					break;
		case 'menu':
					if ($app!==null){
					include_once real($ROOT."/engine/util/yaml/sfYaml.class.php"); //yaml de symfony	
					//include_once real($ROOT."/engine/util/yml.class.php"); //spyc
						$ga=real($rootDir.$app."/genadmin.yml");
						if (file_exists($ga)){
							//$c=Spyc::YAMLLoad($ga);
							$c=sfYaml::load($ga);
							if (isset($c[0]) && $c[0]=='--') unset($c[0]); //borramos ---
							if (isset($c['admin']['fin']) && $c['admin']['fin']==null) unset($c['admin']['fin']); //borramos ---
							do{
								$cont=1;
								echo "\n\n-- Regenerate Admin for {$AppText} $app --\n";
								$opciones=array(0);
								foreach($c['admin'] as $k=>$v){
									echo "\t{$cont}: '{$k}'\n";
									preg_match_all("/([^-$])*-(app|mod|model|appmodel|generator)=([^ ]*)/",$v,$args);
									foreach($args[2] as $k2=>$v2){
										echo "\t\t{$v2}: {$args[3][$k2]}\n";
									}
									$opciones[]=$scriptName." admin {$v}";
									$cont++;
								}
								echo "\t0: exit\nOption (0-".($cont-1).")?";
								
								$char = read();
								$num=intval(trim($char));
								if ($num>0 && $num<$cont) {
									echo `{$opciones[intval($char)]}`;
								}
							}while ($num!=0);
							return 0;
							
						}else echo "--> ERROR: File '{$ga}' for {$AppText} '{$app}' No found ";
						break;
					}
					break;			
		case 'admin':
					if ($plugin===null && $app===null) 		{echo "--> ERROR: Application/Plugin name?\n"; return 0;}
					else if ($plugin===null) {$option='app';}
					else $option='plugin';


					if ($module===null) 	{echo "--> ERROR: Module name?\n"; return 0;}
					if ($model===null) 		{echo "--> ERROR: Model name?\n"; return 0;}
					if ($generator===null) 	{echo "--> ERROR: Generator name?\n"; return 0;}
					
					/// ./cube model [model] ([app]|[])
					
					$dir=real($rootDir.$app."/modules/".$module);

					if ($pluginmodel===null && $appmodel===null) {echo "--> ADMIN ERROR: appmodel option must be activaded.\n"; return 0;}
					else if ($pluginmodel===null) {$option2='-appmodel';}
					else $option2='-pluginmodel';

					$dirmodel=real($rootDir.$appmodel."/model/".$model);

					echo "Creating New Admin '{$module}' of '{$app}' from model {$model}\n{$dir}\n-- dirmodel: {$dirmodel} --\n";
					
					///// TODO: regenera el fichero config, con la informacion del nuevo routing
					
					/// crea aplicacion si no existe o cambia parámetros de routing, y añade actions.auto.php
					$res=execute(array($argv[0],$scriptName,$option,"-{$option}={$app}","-model={$model}","{$option2}={$appmodel}","-mod={$module}","-gen={$generator}","-project={$app}"),true);
					
					// crea modulo de aplicacion
					if ($res!=0) 
						$res=execute(array($argv[0],$scriptName,"module","-mod={$module}","-{$option}={$app}","-gen={$generator}","-model={$model}","{$option2}={$appmodel}"),true);
					
					/// generador
					if ($res!=0) 
						$res=execute(array($argv[0],$scriptName,"gen","-model={$model}","-gen={$generator}","{$option2}={$appmodel}","-mod={$module}"),true);
					
					// vistas del modelo
					if ($res!=0) 
						$res=execute(array($argv[0],$scriptName,"make","-model={$model}","-gen={$generator}","{$option2}={$appmodel}","-mod={$module}","{$option2}={$app}"),true);
									
					break;
		case 'version':
				include_once real($ROOT."/engine/util/package.class.php"); //dbDriver
				//$config=sfYaml::load(real($ROOT."/engine/config.yml"));
				$config=getConfig($ROOT);
				echo " --- CUBE FRAMEWORK v.".$config['all']['settings']['version']." ---\nMonotonic Team - toni@monotonic.es\n";
				break;
		case 'help':
		default:
					$viewHelp=true;
	}
	
	if ($viewHelp)
	{
		echo 	"Usage: ./cube [options]\n".
				"  Options: \n".
				"  -------------------------------------------------CLEAR CACHE---\n".
				"  cc\n".
				"  ---------------------------------------------CURRENT VERSION---\n".
				"  version\n".
				"  ------------------------------------------CREATE APPLICATION---\n".
				"  app -app=APP\n".
				"  -----------------------------------------------CREATE PLUGIN---\n".
				"  plugin -plugin=PLUGIN\n".
				"  -----------------------------------------------CREATE MODULE---\n".
				"  module -mod=MODULE -app=APP|PLUGIN\n".
				"  -----------------------------------------------CREATE SCHEMA---\n".
		      "  schema -model=MODEL -db=DATABASE -(app|plugin)model=APP|PLUGIN\n\t\t[-table=TABLE] : if null table=upper(model)\n".
				"  ------------------------------------------------CREATE MODEL---\n".
				"  object -model=MODEL -db=DATABASE -(app|plugin)model=APP|PLUGIN\n\t\t(app/plugin model, db: engine/config.yml)\n".
				"  --------------------------------------------CREATE GENERATOR---\n".
				"  gen -model=MODEL -gen=GENERATOR -appmodel=APP|PLUGIN\n\t\t(app/plugin model, parse schema.yml)\n".
				"  -----------------------------------CREATE VIEWS OF GENERATOR---\n".
				"  make -model=MODEL -gen=GENERATOR -(app|plugin)model=APP|PLUGIN\n\t\t(app/plugin model, parse generator.yml)\n".
				"  -----------------------------------CREATE ADMIN OF GENERATOR---\n".
				"  admin -app -mod -model -gen -(app|plugin)model|PLUGIN\n\t\t(app/plugin model, =mod+generate+make+actions)\n".
				"  -------------------------------------------------SHOW ADMINS---\n".
				"  menu -app=APP: menu with previous generate administrations\n\t\t\n".
				"  ---------------------------------------------------------------\n";
		return 0;
	}
		
		return 1;
	}
	//print_r($argv);
	execute($argv);
?>