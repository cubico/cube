<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class MyUserUp{
	private $apps;
	private $privileges;

	private $primaria;
	private $hospital;
	private $default;
	
	public function __construct(){}
	public function setPrimaria($value){$this->primaria=$value;}
	public function setHospital($value){$this->hospital=$value;}
	public function setDefault($value){$this->default=$value;}
	public function setAppAccess($props){$this->apps=$props;}
	public function setPrivilegis($props){$this->privileges=$props;}
	
	public function getPrivilegis($app=null,$host=null){
		$ups=array();
		if ($app===null) return $this->privileges;
		else if ($app!==null && isset($this->privileges[$app])) $ups=$this->privileges[$app];
		else $ups=$this->privileges['default'];

		return $ups;
	}

	public function getApps(){
		return array_keys($this->apps);
	}

	public function getUpServeis($app=null,$host=null){
		$ups=array();
		if ($app!==null && isset($this->apps[$app])) $ups=$this->apps[$app];else $ups=$this->apps['default'];

		$res=array();
		foreach($ups as $apli=>$info){
			foreach($info as $type=>$up)
				if ($host===null || ($host!==null && $type==$host)){
					 $res[$type]=$up;
				}
		}
		return $res;
	}

	public function getDefaultUpServei($app=null,$host=null){
		$_P=$this->primaria;
		$_H=$this->hospital;
		
		/// cogemos privilegios por app, sino existe privilegios de default
		if (!isset($this->privileges[$app])) $app='default';
		$priv=$this->privileges[$app];

		//echo $app._r($priv).$host;
		/// si existen up de primaria, y no pasamos host o este es de primaria
		if (isset($priv['ES_PRIMARIA']) && ($host==$_P || $host===null)){
			
			/// si hemos pasado app y existen info de la app
			if ($app===null || ($app!==null && isset($this->apps[$app][$_P]))){
				// selected: el primero de la lista
				return current($this->apps[$app][$_P]);
			}

			$default=current($this->default);
			if ($default['primaria']) return $default;
		}

		/// si existen up de hospital, y no pasamos host o este es de hospital
		if (isset($priv['ES_HOSPITAL']) && ($host==$_H || $host===null)){
			/// si hemos pasado app y existen info de la app
			if ($app===null || ($app!==null && isset($this->apps[$app][$_H]))){
				// selected: el primero de la lista
				return current($this->apps[$app][$_H]);
			}

			$default=current($this->default);
			if (!$default['primaria']) return $default; //!primaria == hospital
		}

		
		return null;
	}
}
?>
