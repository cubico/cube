<?php
class Config
	{
		static private $config;
		static private $instancia;
		
		static public function createInstance($config=null) {
	       if (self::$instancia == NULL) {
	          self::$instancia = new Config($config);
	       }
	   		return self::$instancia;
		}
		
		static public function getInstance() {
	       return self::$instancia;
    	}
		
		public function __construct($config)
		{
			if (!isset($config['cache'])) $config['cache']=true; //si no pasamos parámetro cache, por defecto activada
			self::$config['project']=$config;
		}
		
		static public function getConfig($name=null)
		{
			if ($name==null) return self::$config;
			if (isset(self::$config[$name])) return self::$config[$name];
			return null;
		}
		
		static public function setConfig($name,$value)
		{
			self::$config[$name]=$value;
		}
		
		static public function get($name,$prefix='all',$defaultValue=null)
		{
			///// con el nuevo lector de sfyaml, los valores no llevan implicito un addslashes!
			$name=addslashes($name);
			////////////////////////////////
			
			$loop=false;
			if ($prefix=='all') // tengo que mirar por modulo-app-engine, hasta encontrarlo 
			//if (true)
			{
				$prefixes=array("module","app","site");
				$loop=true;
				$prefix=current($prefixes);
					
				do{
					$route="'{$prefix}']['all']['".str_replace(":","']['",$name)."'";
					eval("\$ok=isset(self::\$config[$route]);");
					$prefix=next($prefixes);
				}while (!$ok && $prefix);
				//echo "<br/>".$route;
			}
			else 
			{
				$route="'{$prefix}']['all']['".str_replace(":","']['",$name)."'";
				//echo _r($route);
				eval("\$ok=isset(self::\$config[$route]);");	
			}
			
			if ($ok) 
			{
				eval("\$val=self::\$config[$route];");
				return $val;	
			}
			else if ($defaultValue!==null) return $defaultValue;
			
			return null;
			
			
		}
		
		static public function set($name,$value,$prefix='module')
		{
			$route="'{$prefix}']['all']['".str_replace(":","']['",$name)."'";
			eval("self::\$config[$route]=\$value;");
		}
		
		
		static public function importData($data,$type='all')
		{
			if (!isset(self::$config[$type])) self::$config[$type]=$data;
			else self::$config[$type]=array_merge_recursive(self::$config[$type],$data);
		}
		
		
		static public function parseVars($value,$parent="var",$separator='_',&$a=array())
		{
			if (is_array($value))
			{
				foreach($value as $k=>$v)
				{
					self::parseVars($v,$parent.$separator.$k,$separator,$a);
					
				}
			}
			else $a[$parent]=$value;
			return $a;
		}
		
		public function fusion($arrays)
		{
			return self::unsetConfig($arrays);
		}
		
		private function unsetConfig($arrays,&$target = array()) {
		    
			if (is_array($arrays))
			{
				$cont=0;
				foreach ($arrays as $k=>$item) {
			    	
					if (is_array($item)) {
				    	
						if (is_numeric(key($item))) // hay mas de una opcion
						{
							do{$a=current($item);}while (next($item));
							$target[$k]=$a;
							
							//do{
							//	$target[$k]=current($item);
							//}while ($target[$k]==='=' && next($item));
							
						}
						else
						{
						   	$target[$k]=self::unsetConfig($item);
						}
						   
				    } else  { $target[$k] =$item;}
					
			    }
		    }
			return $target;
			
		}
	}
?>