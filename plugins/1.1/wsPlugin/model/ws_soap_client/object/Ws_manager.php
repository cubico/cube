<?php
/* 
 *  factory
 */

class Ws_manager {

	protected $props;

	public function __toString()
	{
		return "<pre>".print_r($this,true)."</pre>";
	}
	
	public static function getManager($manager,$props=null){
     	try{
			if (!preg_match("/ClientManager$/",$manager))  $manager.='ClientManager';
			//echo _r(get_declared_classes());
			//echo _r(Site::getInstance()->getConfiguration());
			if (!class_exists($manager)) throw new CubeException("Manager '".$manager."' is not loaded", 1);
			$obj=new $manager($props);
			Controller::triggerHook('debug','model',array(
							'message' =>"Run WS_Manager <b>{$manager}</b> with properties: ".var_export($props,true),
							'type'=>'model',
							'error'=>Log::SUCCESS,
							'class'=>__CLASS__,
							'method'=>__METHOD__));
			return $obj; 
     	}catch(CubeException $e){echo $e;}
	}
 }

?>
