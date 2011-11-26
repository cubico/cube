<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__).'/../../../lib/wsdlgenerator/WSDLCreator.php';

class Ws_server{

	static public function init($server,$name){
		$config=Config::get($server.':ws_config:ws_servers:'.$name);
		
		if ($config!==null){
			eval("\$version=".$config['soap_version'].";");
			$server =  new SoapServer(null, array(	'uri'=>$config['uri'],'soap_version' =>$version));
			$server->setClass($config['class']);
			$server->handle();
		}else throw new WsServerException('No existeix configuració al servidor '.$server.' pel webservice '.$name,-2);
		
	}
	
	static public function generate($server,$name,&$wsdl){
		$conf=Config::get($server.':ws_config');
		$config=$conf['ws_servers'][$name];
		
		if ($config!==null){
			//// existe la clase?
			$models=Site::getInstance()->getConfiguration('model');
			$class=$config['class'];
			if (!isset($models[$class]))	throw new WsServerException('Clase no trobada',-1);

			//// generar el wsdl
			//$uri=$config['uri']."/server.php/wsserver/".$name;
			$uri=$config['uri']."/{$name}.php/server";
			
			$test = new WSDLCreator($name, $uri);
			$test->includeMethodsDocumentation(false);
			$test->setClassesGeneralURL($uri);
			$test->addFile($models[$class]);
			$test->addURLToClass($class,$uri);			
			//$test->addFile($models[$class.'_input']);
			$test->addURLToClass($class.'_input',$uri.'/input');
			//$test->addURLToTypens($class.'_input', $uri.'/input');

			$path=CUBE_PATH_ROOT.$conf['wsdl_dir'].'/'.$config['env'];
			
			$wsdlname=isset($config['wsdl'])?$config['wsdl']:'wsdl'.time();
			
			if (realpath($path)==null) throw new WsServerException('No existeix l\'entorn '.$config['env'].' per al servidor '.$server,-3);
			$wsdl=realpath($path).DIRECTORY_SEPARATOR.$wsdlname.'.wsdl';
			
			// This method is used in case you have a complex type and the PHP code for that type is not in the included files.
			// In this case you define the URL for this type.
			$test->addURLToTypens("XMLCreator", $uri);

			$test->ignoreMethod(array($class=>"__construct"));
			$test->ignoreMethod(array($class.'_input'=>"__construct"));
			//$test->ignoreMethod(array($class.'_input'=>"getPacient"));
			
			return $test; //->getWSDL();
		}else throw new WsServerException('No existeix configuració al servidor '.$server.' pel webservice '.$name,-2);
		return null;
	}

}
?>