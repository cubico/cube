<?php
class viewFilter extends Filter{
	public function execute($filterChain)
	{
		$b=Viewer::getInstance();
		if ($b!==null) 
		{
			// restauramos el Errorhandler, para que los errores no sean excepciones
			Controller::activeErrorHandler(false);

			$project=Config::getConfig('project');
			if ($project['debug']) Controller::activeErrorHandler(true,'errorHandlerView');
			else Controller::activeErrorHandler(true,'errorHandlerNoDebug');
			
			$b->execute();
			 
		}
		
		// siguiente filtro
		$filterChain->execute();
	}
}
?>