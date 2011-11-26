<?php
	class DefaultCubePluginActions extends Actions
	{
		public function executeIndex($request)
		{
			$this->content=Viewer::_echo('default:module');
		}
		
		public function executeLogout($request)
		{
			MyUser::executeLogout();
			$this->forward('validation','login');
		}
		
		public function executeCreated($request)
		{
			$this->content="Página por Defecto";
			$this->setTemplate("index");
		}
		
		public function executeSecurity($request)
		{
			$this->ne='403';
			$this->setLayout(false);
			$this->content="Security!";
			$this->setTemplate("error");
			
		}
		
		public function executeConstruction($request)
		{
			$this->content=Viewer::_echo('under_construction');
			$this->setTemplate("index");
		}
		
		public function executeBlank($request)
		{
			return Viewer::NONE;
		}
		
		public function executeProxy($request)
		{
			$url=base64_decode($request->url);
			
			$urlExterna=ereg("http[s]{0,1}://",$url);
			if (!$urlExterna)
			{
				$file=rtrim($_SERVER['DOCUMENT_ROOT'],"/")."/".ltrim($url,"/");
				include_once($file);
			}
			else{
				$file=$url;
				?><base href="<?php echo $url; ?>" /><?php 
				echo file_get_contents($file); 
			} 
			
			return Viewer::NONE;
		}
		
		private function simpleCache($type,$viewname){ // css or javascript
			$sc=Config::get('settings:simple_cache');
			
			header("Content-type: text/".$type, true);
			header('Expires: ' . date('r',time() + 86400000), true);
			header("Pragma: public", true);
			header("Cache-Control: public", true);
			$view=str_replace('/','_',$viewname);
		    
			$filename = CUBE_PATH_ROOT.$sc['path'].$viewname;
			
			if ($sc['enabled'] && file_exists($filename)) $contents = file_get_contents($filename);
			else $contents = Viewer::view($viewname);
			
			header("Content-Length: " . strlen($contents));
			
			$split_output = str_split($contents, 1024);
    		foreach($split_output as $chunk) echo $chunk;
		}
		
		public function executeCss($request)
		{
			$this->setDebug(false);
			$this->simpleCache('css',$request->view);
			return Viewer::NONE;
		}
		
		public function executeJs($request)
		{
			$this->setDebug(false);
			$this->simpleCache('javascript',$request->view);
			return Viewer::NONE;
		}
		
		public function executeSpotlight($request)
		{
			if ($request->id!=null) $id=$request->id;
			else $id='spotlight';
			
			$display=$request->display;
			if ($display=='' || $display=='none' || $display=='undefined') $display='block';else $display='none';
			Session::set($id,$display);
			return Viewer::NONE;	
		}
		
		public function executeError500($request){
			$this->ne=500;
			$this->setLayout(false);
			$this->setTemplate("error");
		}
		
		public function executeError403Upload($request){
			$this->ne='403u';
			$this->setLayout(false);
			$this->setTemplate("error");
		}
		
		public function executeError403($request){
			$this->ne=403;
			$this->setLayout(false);
			$this->setTemplate("error");
		}
		
		public function executeError404($request){
			$this->ne=404;
			$this->setLayout(false);
			$this->setTemplate("error");
		}

		public function executeErrordb($request){
			$this->ne='db';
			$this->setLayout(false);
			$this->setTemplate("error");
		}

		public function executeError404Debug($request){
			$this->ne='404d';
			$this->setLayout(false);
			$this->setTemplate("error");
		}
	} 
?>