<?php

class Route
{
	private $route=array();
	private $filters;
	const DOLAR='__DOLAR__';
	
	public function __construct()
	{
		$this->forward=null;
	}
	
	static public function parseValues($url,$values=array()){
		$count=0;
		// parse request
		$url=self::parseRequest($url);
		
		/// parse values
		$url2=preg_replace("/_field\(([^\)]*)\)|:([^\/?$&]*)/","'.(isset(\$values['$1$2'])?\$values['$1$2']:'').'",$url,-1,$count);
		if ($count>0) eval("\$url='".$url2."';");
		return $url;	
	}
	
	static public function parseRequest($url){
		$count=0;
		$r=Request::getInstance();
		$url2=preg_replace("/_field\(([^\)]*)\)|:([^\/?$&]*)/","'.\$r->getParameter('$1$2',':$1$2').'",$url,-1,$count);
		if ($count>0) eval("\$url='".$url2."';");
		return $url;
	}
   
	
	static public function url($url=null)
	{
		if ($url===null || $url=='') return $_SERVER['PHP_SELF'];
		else if (preg_match("/^[\.]\//",$url))
		{
			$r=Controller::getInstance()->getRoute();
			if (isset($r['options']['module'])) return $r['file']."/".$r['options']['module'];
			
			return preg_replace("/^[\.]\/(.*)/",$r['file']."/".$r['module']."/$1",$url); 
			
		}
		else if (preg_match("/^\//",$url)){
			if ($url=='/') return "/";
			return Controller::getInstance()->getRoute('file');
		}
		
		if (preg_match('#^[a-z][a-z0-9\+.\-]*\://#i', $url)) return $url;
		else
		{
			if (substr($url,0,1)!="/") 
			{
				$file=Controller::getInstance()->getRoute('file');
				return $file."/".$url;	
			}
		}
		
		
		
		return $url;
	} 
		
	public function parseRoute($url,$route=array())
	{
		$ok=false;
		$cfg=Config::getInstance();
		$req=Request::getInstance();
		$routing=$cfg->get('routing','app');
		// TODO: hay que mirar el routing de plugin 
		$str="";
		
		$regs=array();
		$args=array();
		$vars=array();
		$values=array();
		
		$url_modif=urldecode(str_replace('$',self::DOLAR,$url)); // caso especial urldecode no codifica el signo $
		
		foreach($routing as $k=>$cur)
		{
			$url2=preg_replace("/[\/][\*]/","(.*)",$cur['url']);
			$nueva=str_replace("/","\/",$url2);
			
			preg_match_all("/:([^\/?$]*)/",$url2,$args);
			
			foreach($args[1] as $variable){
				
				if (isset($cur['requirements'][$variable])){ 
					$nueva=preg_replace("/:".$variable."/",'('.$cur['requirements'][$variable].')',$nueva);
				}
				
				if (isset($cur['param'][$variable])) $nueva=preg_replace("/:".$variable."/",'('.$cur['param'][$variable].')',$nueva);
				else 
					$nueva=preg_replace("/:(module|action|view|app)/i",'([^\/]*)',$nueva);
					
			}
			$nueva=str_replace("$",self::DOLAR,$nueva);
			$nueva=preg_replace("/:([\w.]*)/i",'([^\/$]*)',$nueva);
			
			
			$exp="/^".$nueva."[\/]{0,1}$/";
						
			//echo '<br/>'.$k.' --> '.urldecode($url).' --> '.$url2. '<br/><b>--> '.$exp.'</b>';
			
			if (preg_match($exp,$url_modif,$values)) {
				
				//echo "  SIIIIIII!!!!";
				$vars=preg_split("/(\/[:])|(\(\.\*\))/",$url2);
				//echo _r($vars)._r($values);die();
				
				$xx=array_combine($vars,$values);
				array_shift($xx);
				if (isset($cur['param'])) $xx=array_merge($cur['param'],$xx);
				
				//echo _r($xx);
				$str="Activate routing '".$k."' : '".$cur['url']."' for route '".$url."'";
				//echo $str;
				
				foreach($xx as $k=>$v){
					$v=str_replace(self::DOLAR,'$',$v);
					if (!preg_match("/^(module|action|view|app)$/",$k)) 
					{
						$req->{$k}=$v;
						$_REQUEST[$k]=$v;
					}
					else $route[$k]=$v;
				}	
				
				Controller::triggerHook('debug','route',array(
					'message' =>$str,
					'type'=>'route',
					'error'=>Log::SUCCESS,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
				
				$ok=true;
				break;
			}
		}	
				
		if (!$ok) {
			//Log::_add(__METHOD__,"No routing Expression found for '".$url."'","route",__CLASS__,Log::ERROR);
			Controller::triggerHook('debug','route',array(
						'message' =>"No routing Expression found for '".$url."'",
						'type'=>'route',
						'error'=>Log::ERROR,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
		}
		else
		{
			if (!isset($route['view'])) $route['view']=$route['action'];
			else if (empty($route['view'])) throw new CubeException("Route Parameter 'view' has required: ".$str,-1);
		}
		
		if (isset($routing[$k]['options'])) $route['options']=$routing[$k]['options'];
		
		//echo _r($route);echo _r($_REQUEST);die();
		$this->route=$route;
		$vars=  "<b>Routing vars</b>: ".var_export($route,true)."<br/>".
				"<b>Request vars</b>: ".var_export($req->get(),true)."<br/>";
		
		//Log::_add(__METHOD__,$vars,"route",__CLASS__,Log::SUCCESS);
		Controller::triggerHook('debug','route',array(
					'message' =>$vars,
					'type'=>'route',
					'error'=>Log::SUCCESS,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
	}
	
	public function get($name=null)
	{
		if ($name===null) return $this->route;
		if (isset($this->route[$name])) return $this->route[$name];
		return null; 
	}
	
	static private $pluginMode;
	
	static public function setPluginMode($plugin){ self::$pluginMode=$plugin;}
	
	static public function PluginMode(){ return self::$pluginMode;}
	
	static private function fusionFilters($x=array(),$y=array()){
           
        if (count($y)==0) return $x;
       $r=array();
        $ultimaOk=-1;
        for ($i=0;$i<count($y);$i++){
            $pos=array_search($y[$i],$x);
            if ($pos!==false){
                for($j=$ultimaOk+1;$j<$pos;$j++) {
                 if (array_search($x[$j],$r)===false) $r[]=$x[$j];
                }
                if (array_search($y[$i],$r)===false) $r[]=$y[$i];
                $ultimaOk=$pos;
            }else if (array_search($y[$i],$r)===false) $r[]=$y[$i];   
        }
       
        return $r;
    }
    
	static public function execute($current=array())
	{
		try{
			self::$pluginMode=false;
			$cfg=Config::getInstance();
			$dir=CUBE_PATH_ROOT."/apps/".$current['app']."/modules/".$current['module'];
			
			
			$pl=Site::getInstance()->getConfiguration('plugins');
			//echo _r($pl);
			
			if (!is_dir($dir))
			{
				$enabled_modules=$cfg->get('settings:enabled_modules');
				
				if ($enabled_modules!=null)
				{
					$plugin=null;
					$actions=Site::getInstance()->getConfiguration('actions'); // acciones registradas (de apps y de pluglins)
					$i=0;
					$ok=false;
					$maxplugin=array(9999,null);
					do
					{
						$module=$current['module'];
						
						if ($module==$enabled_modules[$i])
						{
							foreach ($actions as $action=>$file) // para cada accion
							{
								preg_match("/plugins\/([^\/]*)\/{$module}/",$action,$args);  // mirar si hay algun de plugin
								
								if (isset($args[1]) && $pl[$args[1]]['active'])  // devuelve algo y el plugin está activo
								{
									/// (comportamiento para vistas) si el plugin tiene menos orden, es que es más prioritario. Lo guardamos en maxplugin 
									/// (comportamiento para modulos) si un plugin no tiene una accion definida, pasa a la siguiente accion
 										
									include_once $file;
									$class=ucfirst($current['module']).ucfirst($args[1])."Actions";
										
									$b=new $class();
									$methods=get_class_methods($class);
									$method="execute".ucfirst($current['action']);
									
									if (in_array($method,$methods) && $maxplugin[0]>$pl[$args[1]]['order']) 
									{
										$maxplugin=array($pl[$args[1]]['order'],$args[1]);
									}
									else $actionNotExists=true; 
									// hemos encontrado algo! 
									$ok=true; 
									
									//break;
								}
							}	
						}
						
						$plugin=$maxplugin[1]; // en maxplugin tendremos el plugin habilitado para la aplicación, más prioritario que esta activo.
						$i++;
					}while (!$ok && $i<count($enabled_modules));
					
					if ($ok) 
					{
						//echo _r(Site::getInstance()->getConfiguration());
						
						$dir=CUBE_PATH_ROOT."/plugins/".$plugin."/modules/".$current['module'];
						self::$pluginMode=$plugin;
						
						//Controller::getInstance()->setRoute($current);
					}
					//Log::_add(__METHOD__,"Execute action ".$current['action'],"log",__CLASS__,Log::SUCCESS);
					Controller::triggerHook('debug','log',array(
						'message' =>"Execute action ".$current['action'],
						'type'=>'log',
						'error'=>Log::SUCCESS,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
				}		
			}
			
						
			if (is_dir($dir)){
               
                $cfg->setConfig('module',null);
               
                $data0=Site::getInstance()->importFile(realpath($dir."/config.yml")); // core
                $cfg->importData($data0,'module');
               
                $filters_site=Config::get('filters','site');
                $filters_app=Config::get('filters','app');
                $filters_module=Config::get('filters','module');
					
                // miramos el orden final de ejecucion de los filtros
                $r=self::fusionFilters(array_keys((array)$filters_site),array_keys((array)$filters_app));
                $r=self::fusionFilters($r,array_keys((array) $filters_module));

					 // asignamos valores a los filtros
                $filters=array();
                foreach($r as $cur){
                    // asignar site
                    if (isset($filters_site[$cur])) $filters[$cur]=$filters_site[$cur];
                    // asignar app (mirando el tipo de dato)
                    if (isset($filters_app[$cur])){
                        if (!is_array($filters_app[$cur])) $filters[$cur]['enabled']=$filters_app[$cur];
                        else {
									$ff=isset($filters[$cur])?$filters[$cur]:array();
									$filters[$cur]=array_merge($ff,$filters_app[$cur]);
								}
					}
					if (isset($filters_module[$cur])){
						if (!is_array($filters_module[$cur])) $filters[$cur]['enabled']=$filters_module[$cur];
						else if (isset($filters[$cur])) $filters[$cur]=array_merge($filters[$cur],$filters_module[$cur]);
						else $filters[$cur]=$filters_module[$cur];
                    }
                }
					 
                $cfg->set('filters',$filters);
                
				//////// cambiar la ruta si hay un plugin activo (para que no pete el modulo de la aplicacion)
				///////  recuerda: los plugins son genericos, y sus módulos deben estar "publicados" para esa aplicacion
				
				
				$plugins=$cfg->get('plugins');
				$plugins2=array();
				
				if (!empty($plugins))
				{
					//// OJO: me pueden pasar los plugins como un array o un hash con enable/disable!
					/// hago la transformacion
					//echo _r($plugins,true);
					foreach($plugins as $plug=>$enabled){
						if (is_numeric($plug)){$plug=$enabled;$enabled=true;}
						$plugins2[$plug]=$enabled; 
					}
					//echo _r($plugins2,true);				
					//// miro los que realmente esta Habilitados
					$enabledSitePlugins=Site::getInstance()->getConfiguration('plugins');
					
					//// en $p tendré los que he activado para el modulo, y estan habilitados!
					$p=array();
					foreach($enabledSitePlugins as $plugin=>$data){
						if ($data['active'] && isset($plugins2[$plugin])) $p[$plugin]=$plugins2[$plugin]; 
					}
					
					//echo _r($p);
					$cfg->set('plugins',$p);
				}
					
			}
			else 
			{
				/*
				// miramos si tiene un plugin activo!
				$actions=Site::getInstance()->getConfiguration('actions');
				//$file=$actions['apps/'.$current['app']."/".$current['module']];
				echo _r($actions);
				
				foreach ($actions as $action=>$file)
				{
					echo "<br/>".$action;
					preg_match("/plugins\/([^\/]*)\/(.*)/",$action,$args);
					if (isset($args[1]))
					{
						self::$posibles[$args[1]]=$action;
					}
				}
				*/				
				
				/*
				$no_error=true;
				if (!is_file($file)) 
				{
					reset($p);
					$item=current($p);
					do{
						$file=$actions['plugins/'.key($p).'/'.$route['module']];
						if (is_file($file)) {$no_error=true;$module=key($k);break;}
					}while ($item=next($p));
					
					$no_error=false;
				}
				else {
					$module=$route['module'];
				}
				
				echo _r($plugins);
				*/
				//$current['view']='asd';
				//Controller::getInstance()->setRoute($current);
				
				//if (Site::getInstance()->isDebugMode()) 
				$filters=Config::get('filters');
				unset($filters['view']);
				unset($filters['control']);
				unset($filters['security']);
				
				
					
				if (Site::getInstance()->isDebugMode()){
					if (isset($actionNotExists)) throw new CubeException("Action '".$current['action']. "' of module '".$current['module']."' does not exist");
					else throw new CubeException("Module ".$current['module']." does not exist");
					//throw new CubeException("Module ".$current['module']." does not exist");
				}else{
					$c=Controller::getInstance();
					$r=$c->getRoute();
					$c->forward("default","error404Debug");
					//echo $r['file'];
				}	
			}
		}catch(Exception $e) {echo $e;}			
		
		return $filters;
		
	}
	
	// Route is stored in Controller Instance. This is a "soft link". See controller.class.php 
	static public function getRoute($name=null){
		return Controller::getInstance()->getRoute($name);
	}
}	
?>