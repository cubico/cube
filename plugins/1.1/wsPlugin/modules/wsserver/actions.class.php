<?php
	class WsserverWsPluginActions extends Actions
	{
		public function executeIndex($request)
		{
			$this->setDebug(false);
			$name=$request->getParameter('name',null);
			if ($name!==null) Ws_server::init($request->server.'Plugin',$name);
			return Viewer::NONE;
		}

		public function executeTypens($request)
		{
			return Viewer::NONE;
		}


		public function executeWsdl($request){
			
			$wsdl=$request->wsdlName; // viene del modulo wsdl de cada aplicacion
			$centre=$request->centre;
			$name=$request->name;
			$this->setDebug(false);
			$conf=Config::get($request->server.'Plugin:ws_config');
			$config=$conf['ws_servers'][$name];
			$path=CUBE_PATH_ROOT.$conf['wsdl_dir'].'/'.$config['env'];
			if (realpath($path)==null) throw new WsServerException('No existeix l\'entorn '.$config['env'].' per al servidor '.$server,-3);
			$this->wsdl=realpath($path).DIRECTORY_SEPARATOR.$wsdl;
			return Viewer::XML;
		}
	} 
?>