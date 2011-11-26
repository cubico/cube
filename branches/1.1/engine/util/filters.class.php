<?php

abstract class Filter{
	
	private static $Filter;
	private $props;
	
	function __construct($Filter=null)
	{
		self::$Filter=$Filter;
		$this->execute(self::$Filter);
	}
	
	public function getProps()
	{
		return $this->props;
	}
	
	public function setProps($props=array())
	{
		$this->props=$props;
	}
	
	public function getFilters()
	{
		return self::$Filter;
	}
	
	public function __toString() {
		return "<pre>".print_r($this,true)."</pre>";			
	}
	
	abstract public function execute($filterChain);	
}

/*
class sessionFilter extends Filter{
	public function execute($filterChain)
	{
		
		// creamos un objeto Sesion
		$b=Session::getInstance();
		
		if (!$b->is_active()){ // es la primera vez que pasamos por aquí
			// ejecutamos el contenido
			$b->init();
			// buscamos las variables de configuracion definidas
			$list=Config::getConfig();
			// las damos de alta en la sesion
			foreach($list as $k=>$v) $b->set($k,$v);
		}
		
		// siguiente filtro
		$filterChain->execute();
	}
}
*/
?>