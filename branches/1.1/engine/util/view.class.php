<?php
/////////
	class Viewer
	{
		const NONE=0;	
		const SUCCESS=1;
		const XJSON=2;
		const JSON=3;
		const XML=4;
		const PDF=5;
		const PDF_LAYOUT=6;
		const XJSONdebug=7;
		
		static private $instancia; 
		
		/*
		static public function createInstance($vars=null) {
	       if (self::$instancia == NULL) {
	          self::$instancia = new View($vars);
	       }
	   		return self::$instancia;
		}
		
		public function __construct($vars=null)
		{
			self::$vars=$vars;
		}
		*/
		
		static public function createInstance() {
	       if (self::$instancia == NULL) {
	          self::$instancia = new Viewer();
	       }
	   		return self::$instancia;
		}
		
		static public function getInstance() {
	       return self::$instancia;
		}
		
		
		public function __construct()
		{
			//echo "Creado View";
			self::$metas_js_css=array(	'httpmeta'=>null,
										'meta'=>null,							
										'stylesheets'=>null,
										'javascripts'=>null
									);
		}
		
		static private $vars=array();
		static protected function getVars()
		{
			return self::$vars;
		}
		
		static public function setVars($vars)
		{
			self::$vars=$vars;
		}
		
		static private $view;
		
		public function setViewFile($view)
		{
			self::$view=$view;	
		}
		
		public function getViewFile()
		{
			return self::$view;	
		}
		
		static $returnValue;
		static public function setViewReturnValue($value){
			self::$returnValue=$value;
		}
		
		static public function getViewReturnValue(){
			return self::$returnValue;
		}
		
		public function execute()
        {
            $viewinfo=self::getViewFile();
            if (is_array($viewinfo)){ //viene de setView
                $content=self::view('template/'.$viewinfo[0],$viewinfo[1],$viewinfo[2]);
            }else if (file_exists($viewinfo)) { //viene de setTemplate
                ob_start();
                extract(self::$vars);
                include_once $viewinfo;
                $content=ob_get_clean();
            }
           
            // IF HAS LAYOUT, THE LOGIC OF HEADERS IS DISABLED
            if (Config::get('view:has_layout'))
            {    $layout=Config::get('view:layout');
                //echo $layout;die();                   
                echo Viewer::view('layouts/'.$layout, array('body' => $content));
                //echo "template + layout!";
                //Log::_add(__METHOD__,"Render view layout '{$layout}'","view",__CLASS__,Log::SUCCESS);
                Controller::triggerHook('debug','view',array(
                    'message' =>"Render view layout '{$layout}'",
                    'type'=>'view',
                    'error'=>Log::SUCCESS,
                    'class'=>__CLASS__,
                    'method'=>__METHOD__));   
            }
            else
            {
                echo Viewer::includeMetaJsCss('javascripts');
                echo $content;
               
                //Log::_add(__METHOD__,"No Render view layout","view",__CLASS__,Log::ERROR);
                Controller::triggerHook('debug','view',array(
                    'message' =>"No Render view layout",
                    'type'=>'view',
                    'error'=>Log::ERROR,
                    'class'=>__CLASS__,
                    'method'=>__METHOD__));   
            }
           
            return "";   
        }
		
		
		static private $enabledModels;
		
		static private $enabledViews;
		
		static public function setViews($views)
		{
			self::$enabledViews=$views;
			$cont=count($views);
			//$vistas=implode(", ",array_keys($views));
			//Log::_add(__METHOD__,"Cube Library has <a style=\"cursor:pointer\" onclick=\"alert('{$vistas}');\">".$cont."</a> views and layouts","info",__CLASS__,($cont>0)?Log::SUCCESS:Log::ERROR);
			//Log::_add(__METHOD__,"Cube Library has <b>".$cont."</b> views and layouts","info",__CLASS__,($cont>0)?Log::SUCCESS:Log::ERROR);
			Controller::triggerHook('debug','info',array(
						'message' =>"Cube Library has <b>".$cont."</b> views and layouts",
						'type'=>'info',
						'error'=>($cont>0)?Log::SUCCESS:Log::ERROR,
						'class'=>__CLASS__,
						'method'=>__METHOD__));
		}
		
		static public function getView($view=null,$viewtype=null)
		{
			// si no pasamos viewtype cogemos el global (que es posible que se haya pasado por request)
			if ($viewtype===null) $viewtype=self::getGlobalTemplate();
			
			if ($view==null) return self::$enabledViews;
			
			if (isset(self::$enabledViews[$viewtype."/".$view])) 
			{
				$vista=self::$enabledViews[$viewtype."/".$view];
				
				// hay que hacerlo aquí por si hay un forward!
				//// afinamos las vista que quedaron con valor array, ahora que sé que plugins estan activos
				
				//mirar los plugins activos
				
				$plugins=Config::get('plugins');
				$site=Site::getInstance();
				if (!empty($plugins))
				{
					$candidatos=array();
					foreach($plugins as $plugin=>$enabled)
					{
						// puedo definir los plugins como una tabla [ ], o un hash "nombrePlugin: on/off"
						if (is_numeric($plugin)){$plugin=$enabled;$enabled=true;}
						if (Site::getInstance()->isPluginActive($plugin) && $enabled) $candidatos[]=$plugin;
					}

					$reg="/([^,]*)\/(plugins)\/(".implode('|',$candidatos).")\/([^\/]*)\/([^,$]*)|$/";
					unset($candidatos);
					$min=999999;$minplug=null;

					if (is_string($vista)) $vista=array($vista);
					
					foreach($vista as $cur){
						$match=preg_match($reg,$cur,$args);
						
						if (isset($args[3]))
						{
							$currentPlugin=$args[3];
							if ($currentPlugin!='cubePlugin'){
								//Log::_add(__METHOD__,"Select the view '{$view}' from <b>{$plugin}</b> plugin","plugin",__CLASS__,Log::ERROR);
								Controller::triggerHook('debug','plugin',array(
									'message' =>"Select the view '{$view}' from <b>{$plugin}</b> plugin",
									'type'=>'plugin',
									'error'=>Log::ERROR,
									'class'=>__CLASS__,
									'method'=>__METHOD__));
							}
							unset($args[0]);

							// puede ser que haya más de un plugin activo, que contenga esta vista
							// tenemos que mirar cual es más prioritario para devolver uno u otro.
							$p=$site->getPlugin($currentPlugin);
							if ($min>intval($p['order'])) {$min=intval($p['order']);$minplug=$currentPlugin;$minplugargs=implode("/",$args);}
							//echo implode("/",$args);
							//return implode("/",$args);
						}
					}
					
					reset($vista);
					
					if ($min!=999999){
						//echo $minplug;
						return $minplugargs;
					}

				}	
				
				//// no hay plugins, pero la informacion de las vistas si que está, tenemos que saltarnos todos los plugins
				/// porque no hay ninguno activo.

				// en este punto, o no hay plugins, o los que hay no estan activos, o no hay vistas
				// de esos activos, vamos cogiendo hasta encontrar los que no sean plugin 
				// en prioridad, vistas de modulo y luego vistas generales
				
				$cur=current($vista);
				$regex="/".strtr(CUBE_PATH_ROOT,array("/"=>"\/","\\"=>"\/"))."\/(plugins)\//";
				while (preg_match($regex,$cur)) {$cur=next($vista);}
				return $cur;	
				//return $vista;
			}
			return null;
		}
		
		static public function setPlugins($models)
		{
			self::$enabledModels=$models;
			$cont=count($models);
			//Log::_add(__METHOD__,"Plugins Library: ".implode(", ",array_keys($models)),"plugin",__CLASS__,($cont>0)?Log::SUCCESS:Log::ERROR);
			Controller::triggerHook('debug','plugin',array(
										'message' =>"Plugins Library: ".implode(", ",array_keys($models)),
										'type'=>'plugin',
										'error'=>($cont>0)?Log::SUCCESS:Log::ERROR,
										'class'=>__CLASS__,
										'method'=>__METHOD__));
		}
		
		/*
		static public function getPlugin($model=null)
		{
			if ($model==null) return self::$enabledModels;
			if (isset(self::$enabledModels[$model])) return self::$enabledModels[$model];
			else throw new CubeException("Plugin {$model} is not present or not enabled");
			
		}*/
		
		static public function getAllModels()
		{
			return Config::get('models');
		}
		
		static private $globalTemplate;
		
		static public function setGlobalTemplate($template)
		{
			self::$globalTemplate=$template;
			//Log::_add(__METHOD__,"Set global viewer template to '{$template}'","view",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','view',array(
										'message' =>"Set global viewer template to '{$template}'",
										'type'=>'view',
										'error'=>Log::SUCCESS,
										'class'=>__CLASS__,
										'method'=>__METHOD__));
		}
		
		static public function getGlobalTemplate()
		{
			return self::$globalTemplate;
		}
		
		/**
		 * Extends a view by adding other views to be displayed at the same time.
		 *
		 * @param string $view The view to add to.
		 * @param string $view_name The name of the view to extend
		 * @param int $priority The priority, from 0 to 1000, to add at (lowest numbers will be displayed first)
		 */
		
		static private $extensions=array();
		
		static public function extendView($view, $view_name, $priority = 501, $viewtype = '') {
			
			if (!isset(self::$extensions[$view])) {
				self::$extensions[$view][500] = "{$view}";
			}
			
			while(isset(self::$extensions[$view][$priority])) {
				$priority++;
			}
			
			self::$extensions[$view][$priority] = "{$view_name}";
			ksort(self::$extensions[$view]);
		}
		
		static public function view($view,$vars=array(),$viewtype=null)
		{
			/*
			if (Config::get("view:pagesetupdone")===null)
			{
				//Viewer::triggerEvent('pagesetup','system');
				Config::set("view:pagesetupdone",true);
			}
			*/
			//echo "<br>".$view;
			
			$bool=Viewer::viewExists($view,$viewtype);
			
			//echo "<br/>".$view."/".$viewtype._r($bool,true);
			if ($bool===false) return '';
			
			$viewFile=Viewer::getView($view,$viewtype);
			if ($viewFile===false) return '';
			
			// incializaciones
			if (!isset($vars['viewtype'])) $vars['viewtype']=Viewer::getGlobalTemplate();
			
			if (!isset($vars['class'])) $vars['class']=false;
			if (!isset($vars['js'])) $vars['js']='';
			if (!isset($vars['disabled'])) $vars['disabled']=false;
			if (!isset($vars['internalname'])){
				//$vars['internalname']='object'.microtime(true);
				$mtime = substr(str_replace(' ','',microtime()),2); 
				$vars['internalname']='object'.$mtime;
			}
			
			if (!isset($vars['disable_security'])) $vars['disable_security']=false;
			if (!isset($vars['url'])) 
			{
				$r=Controller::getInstance()->getRoute();
				$vars['url']=$r['file']."/".$r['module'];
			}
			
			// inclusion de la vista			
			
			
								
			// posibles extensiones de la vista		
			if (isset(self::$extensions[$view])) { 
				$content='';
				$viewlist = self::$extensions[$view];

				foreach($viewlist as $pri=>$w){
					if (isset(self::$enabledViews[$vars['viewtype']."/".$w])) 
					{
						$vars['file']=Viewer::getView($w,$vars['viewtype']);
						$content.=Viewer::content($vars);
					}
				}
			}else {
				$vars['file']=$viewFile;
				$content=Viewer::content($vars);
			}
			
			// activamos trigger
			//$content = Viewer::triggerHook('display',__CLASS__,array('view' => $view),$content);
		
			// Return $content
			return $content; 
		}
		
		static private function content($vars=array())
		{
			ob_start();
			include $vars['file'];
			$content = ob_get_clean();
			return $content;
		}
		
		static public function viewExists($view,$template=null){
		
			if ($template===null)$template=Viewer::getGlobalTemplate();
			$views=Site::getInstance()->getConfiguration('views');
			return isset($views[$template."/".$view]);
			
		}
		
		static public function _echo($name,$text=null)
		{
			$return=$name;
			
			if (Config::get('settings:i18n:enabled'))
			{
				$v=Config::get($name,'lang',$name);
				
				if (is_array($v)) $return=current($v); 
				else $return=$v;
			}
			else if ($text!==null) $return=$text;
			
			return $return;
		}
		
		static public function title($title='',$submenu='')
		{
			return Viewer::view('page_elements/title', array('title' => $title, 'submenu' => $submenu));
		}
		
		static public function layout($layout) {
			
			$arg = 1;
			$param_array = array();
			while ($arg < func_num_args()) {
				$param_array['area' . $arg] = func_get_arg($arg);
				$arg++;		
			}
			$bool=(Viewer::viewExists("canvas/layouts/{$layout}")!==false);
			
			if (Viewer::viewExists("canvas/layouts/{$layout}")!==false) {
				return Viewer::view("canvas/layouts/{$layout}",$param_array);
			} else {
				return Viewer::view("canvas/default",$param_array);
			}
				
		}
		
		public static function submenu($submenu='',$groupname="main",$content='')
		{
			if (!empty($submenu))
			{
				if (is_string($submenu))
				{
					/*$submenus=Viewer::view('canvas/layouts/submenu_group', array(
													'submenu' => $submenu,
													'group_name' => $groupname));*/
					return Viewer::view('page_elements/block',array('content'=>'','submenu'=>$submenu));
				}
				else if (is_array($submenu))
				{
					$menu='';
					
					foreach($submenu as $sub)
					{
						if (is_array($sub)) $menu2 = Viewer::view('canvas_header/submenu_template',$sub);
						else if ($sub=='{separator}') $menu2="<div class=\"submenu_group\" style=\"margin:0 0 7px 0;\"></div>";
						else $menu2=Viewer::view('page_elements/block',array('content'=>$sub));
						
						$menu.=$menu2;
					}
					
					$submenus=Viewer::view('canvas_header/submenu_group', array(
													'submenu' => $menu,
													'group_name' => $groupname
												));
				}
				
				$return=Viewer::view('page_elements/block',array('content'=>$content,'submenu'=>$submenus));
				
				return "<div class=\"sidebarBox\">".$return."</div>";
				
				
			}	
			return '';
		}
		
		static public function object($class_object,$vars=array(),$view='')
		{
			$class=get_class($class_object);
			
			if ($view=='') $view=strtolower($class."/".$class);
			$vars['entity']=$class_object;
			if (Viewer::viewExists($view)) return Viewer::view($view,$vars);
			 
			return '';
		}
		
		static public function list_object($viewlist,$vars=array(),$limit=false,$offset=false,$view='')
		{
			$class=substr($viewlist,0,strpos($viewlist,"/"));
			
			if ($class=='') 
			{
				$view_object=$viewlist;
				$class=ucfirst($viewlist)."Peer";	
				$viewlist.="/".$viewlist;	
			}
			else 
			{
				$view_object=$class;
				$viewlist=substr($viewlist,strpos("/",$viewlist));
				$class=ucfirst($class)."Peer";
			}
			
			if ($view!='' && strpos($view,"/")===false) $view=$view_object."/".$view;

			$obj=new $class(); 
			$vars['limit']=$limit;
			$vars['offset']=$offset;
			$vars['entity']=$obj;
			$vars['object_view']=$view;	
			
			if ($limit!==false && $offset!==false)
			{
				// si tiene definida una query count, la cogemos
				$queryCount=$obj->getQuery('count',true);
				
				if ($queryCount===null) //sino hacemos una select count de toda la vida
					$queryCount="select count(*) ".$obj->getTable()." from ".$obj->getTable();
				
				$data=$obj->doSelect($queryCount,false);
				$vars['count']=$data[0][$obj->getTable()];
				
			}
			
			$query="select * from ".$obj->getTable();
			if ($limit!==false) $query.=" limit ".$vars['limit'];
			if (isset($vars['offset'])) $query.=" offset ".$vars['offset'];
			$data=$obj->doSelect($query);
			
			if ($limit===false && $offset===false) $vars['count']=count($data);
			
			$bool=Viewer::viewExists($viewlist);
			if ($bool===false) return '';
			
			$vars['list']=$data;
			return Viewer::view($viewlist,$vars); 
		}
		
		//////////////////////////////////////
		
		static private $metas_js_css;

		static public function setMetaJsCss($type,$value){
			self::$metas_js_css[$type]=$value;
			
		}
		
		static public function getMetaJsCss($type=null){
			if ($type===null) return self::$metas_js_css;
			else if (isset(self::$metas_js_css[$type])) return self::$metas_js_css[$type];
			return null;
		}
		
		static public function includeMetaJsCss($filter=null){
			//echo _r(self::$metas_js_css);
			
			$str='';
			foreach(self::$metas_js_css as $type=>$data){
				if ($data!==null && ($filter===null || $filter==$type)){
					foreach($data as $key=>$cur){
						switch($type){
							case 'meta':	//echo "<br/>------------".$key.":  ".$cur;
											if ($key!='title') $str.='<meta name="'.$key.'" content="'.$cur.'" />'."\n";
											//else $str.="<title>{$cur}</title>\n";
											
											break; 
							case 'httpmeta'://echo "<br/>".$key." :  ".$cur;
											$str.='<meta http-equiv="'.$key.'" content="'.$cur.'" />'."\n";
											break;
							case 'stylesheets': 
											//echo "<br/>".$cur['href']." - ".$cur['media'];
											$str.='<link rel="stylesheet" href="'.$cur['href'].'" type="text/css" media="'.$cur['media'].'" />'."\n";
											break;											
							case 'javascripts': 
											$str.='<script type="text/javascript" src="'.$cur.'" ></script>'."\n";
											break;
							case 'debug': 
											$str.='<script type="text/javascript">'.$cur.'</script>'."\n";
											break;
							
						}
					}
				}
			}
			return $str;
		}
		
		static public function removeJavascripts()
		{
			if (isset(self::$metas_js_css['javascripts'])) unset(self::$metas_js_css['javascripts']); 
		}
		
		static public function addJavascript($str)
		{
			if (array_key_exists('javascripts',self::$metas_js_css)){
				if (!preg_match("/\.js$/",$str)) $str.=".js";
				//self::$metas_js_css['javascripts'][]='<script type="text/javascript" src="'.$str.'" ></script>'."\n";
				if (!in_array($str,(array)self::$metas_js_css['javascripts'])) self::$metas_js_css['javascripts'][]=$str; 
			}
		}
		
		
		
		static public function addDebugJavascript($url)
		{
			ob_start();
			?>
			$("document").ajaxError(function(event, request, settings){
			  $(this).append("<li>Error requesting page " + settings.url + "</li>");
			});

			$(document).ajaxSuccess(function(event,request, settings){
				var jsontext=request.getResponseHeader('X-JSON')
				if (jsontext!=null){
					var json=jQuery.parseJSON(jsontext);
					var id=settings.url.replace(/[/.?=& \[\]-]/g,'_');
					var id2=id.substr(0,id.indexOf('?')-1);
					
					var strh='<?php echo preg_replace("/[\t\r\n]/","\\n'+\n'",Log::headersTemplate('title2')); ?>';
					
					var pos,bgc,maxt=0;
					if (json!=null){
						$.each(json, function(k, v){
						   	if ((k%2)==0) {pos=" impar";}else {pos=" par";}
							strh+='<?php echo preg_replace("/[\t\r\n]/","\\n'+\n'",Log::itemsTemplate("'+v.time+'","'+v.method+'","'+v.type+'","'+v.message+'","'+v.error+'","'+v.pos+'")); ?>';				   	
					 		maxt=v.time;
						});
					}
					
					strh='<?php echo Viewer::view('canvas/column',array('class'=>'console','width'=>'100%','render'=>"<div style=\"padding:5px;text-align:right;\">'+settings.url+' ('+maxt+')</div>")); ?>'+strh;
	 				
					$('#divlogger #principal').after('<div id="'+id+'" style="display:none;">'+strh+'</div>');
					
					var str='<div><a title="ajax: '+settings.url+'" onClick="$(\'#divlogger #'+id+'\').toggle();"><img src="/img/clear.gif" style="width:16px;height:16px;"/></a>'+settings.url+'</div>';
					$('#divlogger div.menu .column:last').after('<?php echo Viewer::view('canvas/column',array('class'=>'logger linkh ajax','width'=>'16px','render'=>"'+str+'")); ?>');
				}
			});	
			 
			 
			<?php 
			self::$metas_js_css['debug'][]=ob_get_clean(); 
		}
		
				
		static public function addStyle($str=array())
		{
			if (!is_array($str)) $str=array("href"=>$str);
			if (!isset($str['media'])) $str['media']=(self::getGlobalTemplate()=='print')?'print, screen':'screen';
			if (isset($str['href']) && !preg_match("/\.css$/",$str['href'])) $str['href'].=".css";
			
			if (!in_array($str,(array)self::$metas_js_css['stylesheets'])) self::$metas_js_css['stylesheets'][]=$str;
		}
		
		static public function addMeta($name,$content)
		{
			//self::$metas_js_css['meta'][]='<meta name="'.$name.'" content="'.$content.'" />'."\n";
			self::$metas_js_css['meta'][$name]=$content; 
		}
		
		static public function addHttpMeta($name,$content)
		{
			//self::$metas_js_css['httpmeta'][]='<meta http-equiv="'.$name.'" content="'.$content.'" />'."\n";
			self::$metas_js_css['httpmeta'][$name]=$content; 
		}
		
		////////// layout real que queremos ejecutar (para version en pdf)
		
		static $layout;
		
		static public function setLayout($layout){
			self::$layout=$layout;
		}
		static public function getLayout(){
			return self::$layout;
		}
		
		////////////////// simple cache /////////////////////////////////
       
        static private $simplecache=array();
               
        /**
         * Registers a view to be simply cached
         *
         * Views cached in this manner must take no parameters and be login agnostic -
         * that is to say, they look the same no matter who is logged in (or logged out).
         *
         * CSS and the basic jS views are automatically cached like this.
         *
         * @param string $viewname View name
         */
            static public function registerSimplecache($viewname) {
               
                self::$simplecache[] = $viewname;               
               
            }
           
           
        /**
         * Regenerates the simple cache.
         *
         * @see elgg_view_register_simplecache
         *
         */
            static public function regenerateSimplecache() {
               
                $cachePath=realpath(CUBE_PATH_ROOT.Config::get('settings:simple_cache:path'));
                if (!file_exists($cachePath)) { @mkdir($cachePath);    }
                   
                if (count(self::$simplecache)>0) {
                    foreach(self::$simplecache as $view) {
                        $viewcontents = self::view($view);
                        //$viewname = md5(elgg_get_viewtype() . $view);
                        $viewname = $view;
                        if ($handle = fopen($cachePath . DIRECTORY_SEPARATOR. $viewname, 'w')) {
                            fwrite($handle, $viewcontents);
                            fclose($handle);
                        }
                    }
                   
                }
               
            }
           
        /**
         * Enables the simple cache.
         *
         * @see elgg_view_register_simplecache
         *
         */
           
            static public function enableSimplecache() {
               
                $enabled=Config::get('settings:simple_cache:enabled');
                if(!$enabled) {
                    Config::get('settings:simple_cache:path',true);
                    self::regenerateSimplecache();
                }
            }
           
        /**
         * Disables the simple cache.
         *
         * @see elgg_view_register_simplecache
         *
         */
           
            function disableSimplecache() {
               
                $sc=Config::get('settings:simple_cache');
               
                if($sc['enabled']) {
                    Config::get('settings:simple_cache:path',false);
                    // purge simple cache
                    if ($handle = opendir($sc['path'])) {   
                        while (false !== ($file = readdir($handle))) {
                            if ($file != "." && $file != "..") {
                                unlink($sc['path'].DIRECTORY_SEPARATOR.$file);
                            }
                        }   
                        closedir($handle);
                    }
                }
            }

		
	}
	
	
	
?>