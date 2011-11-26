<?php
class renderFilter extends Filter{
	public function execute($filterChain)
	{
		if (!Controller::hasForward())
		{
			//echo "render!";
			// ultimo filtro filtro. No hay $filterChain->execute();
			//Log::_add(__METHOD__,"Execution successfully.","info",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','info',array(
					'message' =>"Execution successfully.",
					'type'=>'info',
					'error'=>Log::SUCCESS,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
			
				
			///// variables flash
			$old=Session::getFlash("__log__");
			if (is_array($old)) $info=array_merge($old,Log::get());else $info=Log::get();
			
			$cfg=Config::getConfig('project');
			//$cfg=Site::getInstance()->getConfiguration('project');
			
			if ($cfg['debug']) Log::plainText($info);
			Controller::activeErrorHandler(false,'errorHandlerView');
		}
	}
}
?>