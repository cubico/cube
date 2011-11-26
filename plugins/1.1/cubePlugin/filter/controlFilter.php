<?php
class controlFilter extends Filter{
	private $ajaxRequest;
	
	public function execute($filterChain)
	{
			$ctrl=Controller::getInstance();
			$globalTemp=Viewer::getGlobalTemplate();
			
			if (preg_match("/^(pdf|xls)/",$globalTemp)) {
				Viewer::setGlobalTemplate('print');
				Config::set('view:layout',$globalTemp);
				Actions::setDebug(false);
			}

			// si es una llamada Ajax deshabilitamos el modo debug
			if (Controller::isAjaxRequest()){
            	$this->ajaxRequest=true; 
            	Actions::setDebug(false);		// debug false!
			}else{	// si no es una llamada Ajax incluimos los scripts!
				//// add javascripts
				$c_site=Config::get("view:javascripts","site");
				$c_app=Config::get("view:javascripts","app");
				$c_mod=Config::get("view:javascripts","module");
				$c_all=array_unique(array_merge((array)$c_site,(array)$c_app,(array)$c_mod));
				$js=array();
				foreach($c_all as $cur) Viewer::addJavascript($cur);
				
			}
            
			$request=Request::getInstance();
			
			$route=$ctrl->getRoute();
			
			$controlFile=$ctrl->getControlFile();

			if ($controlFile===null){ // no se ha activado el filtro de seguridad
				$site=Site::getInstance();
				$actions=$site->getConfiguration('actions');

				if (Route::PluginMode()===false) {
					$file=$actions['apps/'.$route['app']."/".$route['module']];
					$class=ucfirst($route['module'])."Actions";
				}
				else {
					$file=$actions['plugins/'.Route::PluginMode()."/".$route['module']];
					$class=ucfirst($route['module']).ucfirst(Route::PluginMode())."Actions";
				}

				if (is_file($file)){
					$ctrl->setControlFile($file,$class);
					$controlFile=array("file"=>$file,"class"=>$class);
				}else
					throw new CubeException("'{$route['module']}' module directory does not exist of '{$route['app']}' application.", 1);
			}

			$file=$controlFile['file'];
			$class=$controlFile['class'];
			$templates=Site::getInstance()->getConfiguration('templates');
			
			include_once $file;
			
			$b=new $class();
			$methods=get_class_methods($class);
			$method="execute".ucfirst($route['action']);
			//Log::_add(__METHOD__,"Execute action ".$route['action'],"log",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','log',array(
					'message' =>"Execute action ".$route['action'],
					'type'=>'log',
					'error'=>Log::SUCCESS,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
			
			if (in_array($method,$methods)) {
				
				ob_start();
				$ret=$b->{$method}($request);
				
			}
			else throw new CubeException("'{$route['action']}' action not exists in '{$route['module']}' module of '{$route['app']}' application.", 1);	
			
			//////// comprobar si el acceso era seguro para guardarlo en base de datos
			
			$security=Controller::getTestSecurity();
			
			if (!empty($security) && !Controller::hasForward()){
				Controller::triggerHook('log','access',array(
							'message' =>$security['msg'],
							'error'=>Log::SUCCESS,
							'class'=>__CLASS__,
							'method'=>__METHOD__));
			}
					
			// mirar //
			
			if ($ret===null) 
			{
				if (Controller::hasForward()) $ret=Viewer::NONE;
				else {
					$ret=Viewer::SUCCESS;
					
					if (Actions::getDebug())
						Viewer::addDebugJavascript(Route::url(Config::get("settings:views:ajax_debug_action")));
						
				}
			}
			
			if ($ret!=Viewer::NONE)
			{
				
				if ($ret==Viewer::SUCCESS){	// != de cabeceras JSON, XML, PDF, ...
					
					//// add stylesheets
					$c_user=Viewer::getMetaJsCss('stylesheets');	
					Viewer::setMetaJsCss('stylesheets',null);
					
					$c_site=Config::get("view:stylesheets","site");
					$c_app=Config::get("view:stylesheets","app");
					$c_mod=Config::get("view:stylesheets","module");
					
					$c_all=array_unique(array_merge((array)$c_site,(array)$c_app,(array)$c_mod,(array)$c_user));
					$css=array();
					foreach($c_all as $cur) {
						Viewer::addStyle(is_array($cur)?$cur:array('href'=>$cur));
					}
					
					$userHttpmeta=array_keys((array)Viewer::getMetaJsCss('httpmeta'));
					$userMeta=array_keys((array)Viewer::getMetaJsCss('meta'));
					
					$hm_site=Config::get("view:http_metas");
					foreach($hm_site as $key=>$val){
						if (!in_array($key,$userHttpmeta)) Viewer::addHttpMeta($key,$val);
					}
					
					$hm_site=Config::get("view:metas");
					foreach($hm_site as $key=>$val) {
						if (!in_array($key,$userMeta))  Viewer::addMeta($key,$val);
					}
					
				}else {
					Actions::setDebug(false);
					Config::set('view:has_layout',false);
					Viewer::removeJavascripts();
				}
				
				$temp=$b->getTemplate();
				$temp2=$b->getView();
               
                if ($temp2!==null) // hay vista (setView)
                {
                    Viewer::getInstance()->setViewFile($temp2);
                    Controller::triggerHook('debug','view',array(
                            'message' =>"Execute view template/<b>".$temp2[0]."</b>",
                            'type'=>'view',
                            'error'=>Log::SUCCESS,
                            'class'=>__CLASS__,
                            'method'=>__METHOD__));
                   
                    $filterChain->execute();
                }
                else if ($temp!==false) // hay template (setTemplate)
				{
					$mod=$b->getTemplateModule();
					if ($mod!==null) $route['module']=$mod; 
					
					Viewer::getInstance()->setVars(get_object_vars($b));
					//Viewer::createInstance(get_object_vars($b));
					if (!isset($route['view'])) $view=$route['action'];else $view=$route['view'];
				 	
					if ($temp!==null) $view=$temp;
					
					if (Route::PluginMode()===false) $ruta='apps/'.$route['app']."/".$route['module']."/".$view;
					else $ruta='plugins/'.Route::PluginMode()."/".$route['module']."/".$view;
					
					if (array_key_exists($ruta,$templates))
					{
						$file=$templates[$ruta];
						
						//Log::_add(__METHOD__,"Execute template <b>".$view."</b> from module <b>".$route['module']."</b>","view",__CLASS__,Log::SUCCESS);
						Controller::triggerHook('debug','view',array(
							'message' =>"Execute template <b>".$view."</b> from module <b>".$route['module']."</b>",
							'type'=>'view',
							'error'=>Log::SUCCESS,
							'class'=>__CLASS__,
							'method'=>__METHOD__));
						Viewer::getInstance()->setViewFile($file);
						$filterChain->execute();	// ejecutará el filtro de la vista.
					}
					else throw new CubeException("View '{$view}' does not exist of '{$route['app']}' application.", 1);
				}else {
					if ($ret==Viewer::SUCCESS) echo Viewer::includeMetaJsCss('javascripts');
					$filterChain->endFilterChain()->execute();
				}
			}else{    
				if (!Controller::hasForward() && Actions::getDebug()) echo Viewer::includeMetaJsCss('javascripts');
				$filterChain->endFilterChain()->execute();
			}
            
            $content=ob_get_clean();
	        
            if (in_array($_SERVER['HTTP_HOST'],Config::get('settings:secureServers')))
				header("Framework: CUBE-".$ctrl->getSecret());

			if ($this->ajaxRequest && Actions::getDebug()) {
				$he=Log::plainText(Log::get());
				header("X-JSON: ".$he);
			}else if (Viewer::getGlobalTemplate()=='pdf') {
				$ret=Viewer::PDF_LAYOUT;
				//Viewer::setGlobalTemplate('default');
			}
			 	
			switch($ret){
            	case Viewer::PDF:	header('Content-Type: application/pdf');
		 							Util::noCacheHeader();
            						echo $content;
		 							break;
				case Viewer::XJSON:	eval("\$c=$content;");
									header('X-JSON: ('.json_encode($c).')');
									break;
				case Viewer::JSON:	header('Content-type: application/x-json');
									eval("\$c=$content;");
									echo json_encode($c);
									break;
				case Viewer::XML:	header ("Content-type: text/xml; charset: utf-8");
									echo $content;
									//echo htmlentities($content);
									break;
				default:			
									//echo "-----------------".$ret;
									//echo Config::get('view:layout');
									//echo _r($request->get());
									echo $content;
									break;
			}
	}
}
?>