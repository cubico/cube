<?php
	
	class PluginsCubePluginActions extends Actions
	{
		public function executeIndex($request)
		{
			$plugs=array();
			$this->plugins=Site::getSitePluginList();
		}
		
		public function executeReorder($request)
		{
			$plugin=$request->plugin;
			
			$plugs=Site::getSitePluginList();
			$plugs[$request->plugin]['order']=$request->order;
			$this->plugins=Site::setInstalledPluginList($plugs);
			//$this->setTemplate("index");
			$this->redirect("plugins/index");
		}		
		
		public function executeDisable($request)
		{
			$plugin=$request->plugin;
			
			$plugs=Site::getSitePluginList();
			$plugs[$request->plugin]['active']=false;
			$this->plugins=Site::setInstalledPluginList($plugs);
			$this->redirect("plugins/index");
		}
		
		public function executeEnable($request)
		{
			$plugin=$request->plugin;
			$plugs=array();
			// supongo que se debe comprobar si el token es correcto.			
			$plugs=Site::getSitePluginList();
			$plugs[$request->plugin]['active']=true;
			
			$this->plugins=Site::setInstalledPluginList($plugs);
			$this->redirect("plugins/index");
		}
		
		public function executeDisableall($request)
		{
			$plugin=$request->plugin;
			
			$plugs=Site::getSitePluginList();
			foreach($plugs as $plugin=>$data)
				$plugs[$plugin]['active']=false;
			$this->plugins=Site::setInstalledPluginList($plugs);
			$this->redirect("plugins/index");
		}
		
		public function executeEnableall($request)
		{
			$plugin=$request->plugin;
			
			$plugs=Site::getSitePluginList();
			foreach($plugs as $plugin=>$data)
				$plugs[$plugin]['active']=true;
			$this->plugins=Site::setInstalledPluginList($plugs);
			$this->redirect("plugins/index");
		}
	} 
?>