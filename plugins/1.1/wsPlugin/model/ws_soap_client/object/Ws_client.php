<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 abstract class Ws_client{
	private $config;
	private $class;
	private $client;
	private $env;
	protected $columns;

	/**
	 * get input parameters to call webservice
	 * @param object/array $params Parameters needed for call webservices
	 * @return array $par Parameters parsed for call webservices
	 */
	abstract public function prepare($params=array());

	/**
	 * get output data from webservice and prepare it for result
	 * @param array $result Data result from webservices
	 * @return array $res Data query of Webservice
	 */
	abstract public function output($result,$params=array());

	/**
	 * get output data from webservice and filter it
	 * @param array $list Data result from webservices
	 * @return array $res Data filtered
	 */
	protected function filter($value,$criteria=array()){
		$cond=true;

		if (is_array($criteria)) $cond=in_array($value,$criteria);
		else $cond=($value==$criteria);

		return $cond;
	}

	public function __construct($config=array(),$env=null){
		$this->config=$config;
		$this->class=get_class($this);
		$cfgWs=$config['ws_clients'][strtolower($this->class)];
		
		/// rewrite enviroment
		$enviroment=($env===null)?$cfgWs['env']:$env;
		$this->env=$enviroment;
		
		$wsdl=$this->getWsFile();
		ini_set('soap.wsdl_cache_enabled', $config['wsdl_cache_enabled']);

		try{
			$this->client=new SoapClient($wsdl, array(
				'login' => $config['wsdl_env'][$enviroment]['login'],
				'password'=> $config['wsdl_env'][$enviroment]['password'],
				'soap_version'=>SOAP_1_2,
				'trace'=>1,
				'encoding'=>$config['wsdl_encoding'],
				'proxy_port'=>'8091'
			));

			Controller::triggerHook('debug','model',array(
							'message' =>"Connect to soapClient <b>{$this->class}</b> in <b>{$enviroment}</b> enviroment.<br/>WSDL: <b>".str_replace(DIRECTORY_SEPARATOR,' /',$wsdl).'</b>',
							'type'=>'model',
							'error'=>Log::SUCCESS,
							'class'=>__CLASS__,
							'method'=>__METHOD__));

		}catch(SoapFault $e){
			throw new WsClientException($e->getMessage(),$e->getCode());
		}
	}

	public function getConfig(){
		return $this->config;
	}
	
	protected function getClient(){
		return $this->client;
	}

	private function getWsFile(&$operation=null){
		$cfgWs=$this->config['ws_clients'][strtolower($this->class)];
		$operation=$cfgWs['operation'];
		// get webservice from directory with rewrited enviroment
		$temp=CUBE_PATH_ROOT.'/'.$this->config['wsdl_dir'].'/'.$this->env.'/'.$cfgWs['wsdl'].'.wsdl';
		$wsdl=realpath($temp);
		if ($wsdl===false) throw new WsClientException('El fitxer de webservice no existeix.<br/><small>'.$temp.'</small>',-1);
		return $wsdl;
	}

	public function execute($params=array()){
		try{
			
			$operation=$this->config['ws_clients'][strtolower($this->class)]['operation'];
			$result=call_user_func_array(array($this->client,$operation), array($this->prepare($params)));
			Controller::triggerHook('debug','model',array(
								'message' =>"Execute webservice <b>{$this->class}</b> with operation <b>{$operation}</b><br/>Parameters: ".var_export($params,true),
								'type'=>'model',
								'error'=>Log::SUCCESS,
								'class'=>__CLASS__,
								'method'=>__METHOD__));

			$data=$this->output($result,$params);
		}catch(SoapFault $e){
			
			throw new WsClientException($e->getMessage().'<br/>'.htmlentities($this->client->__getLastResponse()), $e->getCode());
		}
		return $this->_filterData($data,$params);
	}

	private function _filterData($list,$params=array()){
		
		$columns=isset($params['columns'])?(array)$params['columns']:array();
		$criteria=isset($params['filters'])?(array)$params['filters']:array();
		
		if (!empty($columns) || !empty($criteria)){
			/// crear array y filtar por los campos que hemos pasado en params['filters']
			if (!is_array($list)) $list=array($list);
			$list2=array();

			foreach($list as $k=>$v){

				$condition=true;
				if (!empty($criteria)){
					foreach($criteria as $type=>$info){
						if (property_exists($v,$type)) {
							$condition&=$this->filter($v->{$type},$info);
						}
					}
				}

				if ($condition){
					$item=array();
					if (!empty($columns)) {
						foreach($columns as $param) {
							eval("\$ok=isset(\$v->{$param});");
							if ($ok) $item[$param]=$v->{$param};
						}
						$list2[]=$item;
					}else $list2[]=$v;
				}
			}
			return $list2;
		}
		return $list;
	}
	
	public function objectToArray($d) {
		if (is_object($d)) $d = get_object_vars($d);
		if (is_array($d)) return array_map(array($this,'objectToArray'), $d);
		else return $d;
	}
 }
?>
