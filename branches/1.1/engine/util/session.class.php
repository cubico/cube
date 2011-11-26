<?php
class Session
{
	const SESSION_READY='___session_ready___';
	const SESSION_PREFIX_TAG='session';
	
	private static $instance;
	
	private static $vars;
	
	static public function getInstance() {
       
	   if (self::$instance == NULL) {
          
		  self::$instance = new Session();
       }
   		return self::$instance;
	}
	
	public function __toString()
    {
     	return "<pre>".print_r(self::$instance,true)."</pre>";	
     }
		
   static public function init ()
   {
    if ( !isset( $_SESSION [self::SESSION_READY] ) )
     {
       	session_start();
      	self::$vars=array();
		$_SESSION [self::SESSION_READY] = TRUE;
     }
     
   }
	
	public function showSessionVars()
	{
		self :: init ();
		return self::$vars;	
	}
	
   static public function set ( $fld , $val )
   {
   		self :: init ();
		self::$vars[$fld]=$val;
   		$_SESSION [self::SESSION_PREFIX_TAG."_".$fld]  = $val;
   }
   
   static public function setFlash ( $fld , $val ,$add=false)
   {
   		self :: init ();
		if ($add)
		{
			if (isset(self::$vars['__flash'][$fld])) $old=self::$vars['__flash'][$fld];else $old='';
			
			if (!is_array($old) && $old!='') 
			{	
				$old=array($old);
				$val=array_merge($old,array($val));
			}
		}
			
		self::$vars['__flash'][$fld]=$val;
   		$_SESSION ['__flash'][self::SESSION_PREFIX_TAG."_".$fld]  = $val;
		$_SESSION ['__flash_count'][self::SESSION_PREFIX_TAG."_".$fld]  = 0;
		
		
   }
   
   static public function incCountFlash($name)
   {
   		self :: init ();
		
		if (isset($_SESSION ['__flash_count'][self::SESSION_PREFIX_TAG."_".$name]))
			$_SESSION ['__flash_count'][self::SESSION_PREFIX_TAG."_".$name] =$_SESSION ['__flash_count'][self::SESSION_PREFIX_TAG."_".$name]+1;
		
		
   }
   
   static public function getCountFlash($name=null)
   {	
   		self :: init ();
		if (isset($_SESSION ['__flash_count']) && $name==null) return $_SESSION ['__flash_count'];
		
   		if (isset($_SESSION ['__flash_count'][self::SESSION_PREFIX_TAG."_".$name]))
			return $_SESSION ['__flash_count'][self::SESSION_PREFIX_TAG."_".$name];
   }
   
   
   static public function un_set ( $fld=null ) 
   {
   		self :: init ();
		if ($fld===null) {
			session_unset();
			self::$vars=array();
			$_SESSION [self::SESSION_READY] = TRUE;
		}
   		else{ unset(self::$vars[$fld]);
   			  unset( $_SESSION [self::SESSION_PREFIX_TAG."_".$fld] );
   		}
   }
   
   static public function un_setFlash ( $fld =null) 
   {
   		self :: init ();
		
		if ($fld===null){
			unset(self::$vars['__flash']);
   			unset( $_SESSION['__flash']);
			unset( $_SESSION ['__flash_count']);
		}else{
			unset(self::$vars['__flash'][$fld]);
   			unset( $_SESSION['__flash'][self::SESSION_PREFIX_TAG."_".$fld] );
			unset( $_SESSION['__flash_count'][self::SESSION_PREFIX_TAG."_".$fld] );
		}
   }
   
   static public function destroy ()
   {
   		self :: init ();
   		unset ( $_SESSION );
   		session_destroy ();
   }
   static public function get ( $fld,$value=null)
   {
   		self :: init ();
		if (isset($_SESSION [self::SESSION_PREFIX_TAG."_".$fld]))
   			return $_SESSION [self::SESSION_PREFIX_TAG."_".$fld];
		
   		if ($value!=null) return $value;	
		return null;
   }
   
   static public function getFlash ( $fld=null,$value=null)
   {
   		self :: init ();
		
		if ($fld===null)  
		{
			 if (isset($_SESSION['__flash'])) return $_SESSION['__flash'];
			 else return array();
			 
		}else if (isset($_SESSION['__flash'][self::SESSION_PREFIX_TAG."_".$fld]))
   			return $_SESSION['__flash'][self::SESSION_PREFIX_TAG."_".$fld];
		
   		if ($value!=null) return $value;	
		return null;
   }
   
   static public function is_set ( $fld ) {
   		
   		
		return isset( $_SESSION [self::SESSION_PREFIX_TAG."_".$fld] );
   }
   
   static public function is_setFlash ( $fld ) {
   		
   		
		return isset( $_SESSION['__flash'][self::SESSION_PREFIX_TAG."_".$fld] );
   }
   
   static public function is_active()
   {
   		
		return isset( $_SESSION [self::SESSION_READY] );
   }
   
   static public function restoreFlashVars()
	{
		if (self::is_active()) 
		{
			$flash=self::getFlash();
			if ($flash!==null)
			{
				foreach($flash as $name=>$cur)
				{
					$name=substr($name,strpos($name,"_")+1);
					
					self::incCountFlash($name);
					if (self::getCountFlash($name)>1) self::un_setFlash($name); 
					
				}
				
			}
			//echo _r($flash);
			//Session::un_setFlash();
		}
		//echo _r(Session::getCountFlash(),true);
	}
	
	static public function hasCredential($fld)
	{
		
		return (isset($_SESSION['__credentials'][self::SESSION_PREFIX_TAG."_".$fld]));
	}
	
	
	static public function addCredential ($fld)
	{
		self::setCredential($fld,true);
	}
	
	static public function setCredential ($fld,$value=true)
   	{
   		
   		self::$vars['__credentials'][$fld]=$value;
   		
		$_SESSION['__credentials'][self::SESSION_PREFIX_TAG."_".$fld]=$value;
	}
	
	static public function getCredential ($fld)
   	{
   		self :: init ();
   		if (isset($_SESSION['__credentials'][self::SESSION_PREFIX_TAG."_".$fld]))
   			return $_SESSION['__credentials'][self::SESSION_PREFIX_TAG."_".$fld];

   		return null;
	}
	
	static public function getCredentials ()
   	{
   		self :: init ();
   		
   		if (isset($_SESSION['__credentials'])) 
   		{
   			$a=array();
   			foreach($_SESSION['__credentials'] as $cred=>$value){
   				$a[preg_replace("/^".self::SESSION_PREFIX_TAG."_(.*)$/","$1",$cred)]=$value;
   			}
   			return $a;
   		}
   		return null;
   	}
	
	static public function removeCredential($fld)
	{
		if (self::hasCredential($fld))
		{
			unset(self::$vars['__credentials'][$fld]);
   			unset( $_SESSION['__credentials'][self::SESSION_PREFIX_TAG."_".$fld] );	
		}
	
	}
	
	static public function removeAllCredentials()
	{
		unset(self::$vars['__credentials']);
   		unset($_SESSION['__credentials']);	
	}
	
	static public function parseCredentials($elem,$type='&&',&$str=''){
		$str='';
		foreach($elem as $i=>$v){
			if ($i>0) $str.=" {$type} ";	
			
			if (is_array($v)){
				$str.="(".self::parseCredentials($v,($type=='&&')?'||':'&&').")";
			}else{
				//preg_match("/^([!]{0,1})(.*)/",$v,$args);
				//$str.="{$args[1]}Session::hasCredential('{$args[2]}')";
				
				preg_match("/^([-!]{0,1})([:]{0,1})(.*)/",$v,$args);
				// miro el modo (edit, new, show, ...)
				if ($args[2]==':'){
					if ($args[1]=='-') $args[1]='!';
					$str.="Controller::getInstance()->getRoute('action'){$args[1]}=='{$args[3]}'";
				}else if ($args[1]=='-' || $args[1]=='!') $str.="!Session::hasCredential('{$args[3]}')";
                else $str.="Session::hasCredential('{$args[3]}')";

			}
		}
		return $str;
	}
	
} 
?>