<?php
//define ("PACKAGE_PATH",realpath(dirname(__FILE__)."/.."));
if (!defined('DIRECTORY_SEPARATOR')) define("DIRECTORY_SEPARATOR","/");
if (!defined('PATH_SEPARATOR')) define("PATH_SEPARATOR","/");

define("CUBE_PATH_ROOT",realpath($_SERVER['DOCUMENT_ROOT']."/.."));

if (isset($_SERVER['SERVER_PROTOCOL'])){ // variable no incluida en modo consola 
	$protocol=explode("/",$_SERVER['SERVER_PROTOCOL']);
	define ("CUBE_HTTP",strtolower($protocol[0]).'://'.$_SERVER['HTTP_HOST']);
}


class Package{
	private $tots;
	private static $instance;
	public $PACKAGE_PATH;
			
	static public function getInstance() {
	       
		   if (self::$instance == NULL) {
	    	 self::$instance = new Package();
	       }
       		return self::$instance;
    }
	
	public function __construct()
	{
		$this->PACKAGE_PATH=realpath(dirname(__FILE__)."/../..");
		$this->tots=null;
	}
	
	public function recursiveDirs($dir)
	{
		$dirObj = new DirectoryIterator($dir);
		foreach ($dirObj as $nombrefichero)
		{
        	if($nombrefichero->isDir() && !$nombrefichero->isDot() && substr($nombrefichero,0,1)=='.')
        	{
        		$this->tots[]=$nombrefichero->getPathname();
        		$this->recursiveDirs($nombrefichero->getPathname());
        	}
        }
	}
	
	public function getAll()
	{
		return $this->tots;
	}
	
	public function exists_files()
	{
		return $this->tots!=null;	
	}
	
	static public function getFileExtension($obj)
    {
        $Filename = $obj->GetFilename();
        $FileExtension = strrpos($Filename, ".", 1) + 1;
        if ($FileExtension != false)
            return strtolower(substr($Filename, $FileExtension, strlen($Filename) - $FileExtension));
        else
            return "";
    }
}

/// helpers

	function import($class, $dirs = null)
	{
	  	$path = str_replace('.', DIRECTORY_SEPARATOR, $class);
	    $pathArray = preg_split('/\./',$class);
		
	    if(count($pathArray)<=1) return null; 
	    
		if(end($pathArray) == "*")
	    {
			$p=Package::getInstance();
			//// directorio origen (Package.php tendria que estar en la raiz.
	    	array_pop($pathArray);
	        $path = implode(DIRECTORY_SEPARATOR,$pathArray);
	        
			/// buscar todos los directorios, para incluirlos en include_path
	        $fullPath  = $p->PACKAGE_PATH.DIRECTORY_SEPARATOR.$path;
			
			$dirObj = new DirectoryIterator($fullPath);
			
			
			$p->recursiveDirs($fullPath);
			
			if ($p->exists_files())
			{
				set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR,$p->getAll()));
			}
			
			foreach ($dirObj as $nombrefichero){
	        	
	        	if (!in_array($class,get_declared_classes()))
				{
	        		if($nombrefichero->isFile() && Package::getFileExtension($nombrefichero)=='php')
	            	{
	            		//echo "</br>".$fullPath.DIRECTORY_SEPARATOR.$nombrefichero;	
						include_once $fullPath.DIRECTORY_SEPARATOR.$nombrefichero;	
					}
	            }
	        }
			
	    }else{
	
			if (class_exists($class, false)) {
	            return;
	        }
	
			if ($dirs === null && $path != $class) {
	            $dirs = dirname($path);
	            $file = basename($path) . '.php';
	        } else {
	            $file = $class . '.php';
	        }
			
			$file=CUBE_PATH_ROOT.DIRECTORY_SEPARATOR.$dirs.DIRECTORY_SEPARATOR.$file;
			
			if (file_exists($file)) { require_once($file); }
	    }
	}


	function _r($txt,$export=false)
	{
		if ($export) return "<pre>".var_export($txt,true)."</pre>";
		return "<pre>".print_r($txt,true)."</pre>";
	}
	
	function __b(){
		//echo Date("H:i:s");
		echo utf8_encode(strftime("%H:%M:%S"));
	}

	function array_csort() 
	{  //coded by Ichier2003
   		$args = func_get_args();
		   $marray = array_shift($args);
			if (!isset($i)) $i=0;	
		   $msortline = "return(array_multisort(";
		   foreach ($args as $arg) 
		   {
       			$i++;
		       	if (is_string($arg)) 
			   	{
        		   foreach ($marray as $row) 
				   {
               			$sortarr[$i][] = $row[$arg];
		           }
       			} 
				else 
				{
		           $sortarr[$i] = $arg;
		       	}
			    $msortline .= "\$sortarr[".$i."],";
		   }
		   $msortline .= "\$marray));";

		   eval($msortline);
		   return $marray;
	}
	
	function array_msort($array, $cols)
	{
	    $colarr = array();
	    foreach ($cols as $col => $order) {
	        $colarr[$col] = array();
	        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
	    }
	    $params = array();
	    foreach ($cols as $col => $order) {
	        $params[] =& $colarr[$col];
	        $params = array_merge($params, (array)$order);
	    }
	    call_user_func_array('array_multisort', $params);
	    $ret = array();
	    $keys = array();
	    $first = true;
	    foreach ($colarr as $col => $arr) {
	        foreach ($arr as $k => $v) {
	            if ($first) { $keys[$k] = substr($k,1); }
	            $k = $keys[$k];
	            if (!isset($ret[$k])) $ret[$k] = $array[$k];
	            $ret[$k][$col] = $array[$k][$col];
	        }
	        $first = false;
	    }
	    return $ret;
	}
	
	
	function parse_urls($text) {
       
       	return preg_replace_callback('/(?<!=["\'])((ht|f)tps?:\/\/[^\s\r\n\t<>"\'\!\(\)]+)/i', 
       	create_function(
            '$matches',
            '
            	$url = $matches[1];
            	$urltext = str_replace("/", "/<wbr />", $url);
            	return "<a href=\"$url\" style=\"text-decoration:underline;\">$urltext</a>";
            '
        ), $text);
    }
	
	function autop($pee, $br = 1) {
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		// Space things out a little
		$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr)';
		$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
		$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
		$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
		if ( strpos($pee, '<object') !== false ) {
			$pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
			$pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
		}
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
		$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
		$pee = preg_replace( '|<p>|', "$1<p>", $pee );
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
		if ($br) {
			$pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'), $pee);
			$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
			$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
		}
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
		if (strpos($pee, '<pre') !== false)
			$pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'clean_pre', $pee );
		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
	
		return $pee;
	}
    
	function filter_tags($var)
	{
		return Controller::triggerHook('validate', 'input', null, $var);
	}
	
	// If magic quotes are enabled, strip slashes from all user data
	function stripslashes_recursive($var) {
		//return $var;
		return (is_array($var) ? array_map('stripslashes_recursive', $var) : stripslashes($var));
	}

	if (!function_exists('real'))
	{
		function real($path)
		{
			return str_replace("/",DIRECTORY_SEPARATOR,$path);
		}
	}
	
	function parseText($string){
		//$value = htmlentities($string, ENT_QUOTES, 'UTF-8'); // si lo descomento los tags html no van!
		$string=addslashes($string);
		$value=preg_replace("/_echo\((.*)\)/","'.Viewer::_echo('$1').'",$string);
		$value=preg_replace("/_title\((.*)\)/","'.Viewer::title('$1').'",$value);
		//eval("\$value='".addslashes($value)."';");
		eval("\$value='".$value."';");
		return $value;
	}
		
	// descomentar en linux (ojo: si se descomenta,  puede que scripts lo llame y dé errores)
	include_once dirname(__FILE__)."/cubeException.php";
	include_once dirname(__FILE__)."/yaml/sfYaml.class.php";
	include_once dirname(__FILE__)."/config.class.php";
	include_once dirname(__FILE__)."/controller.class.php";
	include_once dirname(__FILE__)."/filters.class.php";
	include_once dirname(__FILE__)."/forms.class.php";
	include_once dirname(__FILE__)."/generator.class.php";
	include_once dirname(__FILE__)."/log.class.php";
	include_once dirname(__FILE__)."/model.class.php";
	include_once dirname(__FILE__)."/request.class.php";
	include_once dirname(__FILE__)."/route.class.php";
	include_once dirname(__FILE__)."/session.class.php";
	include_once dirname(__FILE__)."/site.class.php";
	include_once dirname(__FILE__)."/view.class.php";
	include_once dirname(__FILE__)."/user.class.php";
	
	spl_autoload_register('Controller::autoload');
	
?>