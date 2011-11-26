<?php
	class GenerateWsPluginActions extends Actions
	{
		public function executeIndex($request){
			$sid=$request->sid;
			$wsdlFile=null;
			$server=new Ws_server();
			$res=$server->generate($request->server.'Plugin',$sid,$wsdlFile);
			
			switch($sid){
				case 'funca':  $res->addURLToClass('XMLCreator',CUBE_HTTP.'/server.php/wsserver/'.$request->server.'/'.$sid.'/xml');break;
				default:			break;
			}

			$res->createWSDL();
			$res->saveWSDL($wsdlFile, true);
			$wsdl=$res->getWSDL();
			//$wsdl->printWSDL(true);
			//return Viewer::NONE;

			$this->wsdl=$wsdl;
			if ($request->wsdl!==null) {$this->setDebug(false);$this->setTemplate('wsdl');}
			else {
				$this->setTemplate('wsdl');
				//return Viewer::XML;
			}
		}
	} 
?>