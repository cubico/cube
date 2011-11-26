<?php
class CubeException extends Exception {
    	// Redefine the exception so message isn't optional
	    public function __construct($message, $code = 0) {
	        // some code
	    	
	        // make sure everything is assigned properly
	        parent::__construct($message, $code);
	    }
	
	    // custom string representation of object */
	    public function __toString() {
	    	
			if (!Site::getInstance()->isDebugMode()) {echo "<b>Error a la plana</b>: {$this->message}";die();} 
	    
	    	$str="<div style=\"width:100%;text-align:center;margin: 40px 40px 40px 40px\"><div style=\"width:800px;text-align:left;padding:10px;background-color:#DDDDDD;\">".
				"<h2>".__CLASS__ . ": [{$this->code}] {$this->message}</h2><h3>{$this->file} (line {$this->line})</h3><br/><h4>Stack:</h4>";
						
			$t=$this->getTrace();
			
			foreach($t as $k=>$v)
			{
				
				$str.='<div style="text-align:left;font-size:10pt;font-weight:bold;padding:5px;background-color:#EEEEEE;">'.$k.': '.(isset($v['file'])?$v['file']:"");
					$str.='<div style="text-align:left;font-size:10pt;font-weight:normal;padding:10px;color:#ff0000">';
				
					if (isset($v['line'])) $str.='Line: '.$v['line'].',';
					if (isset($v['function']))$str.='Function: '.$v['function'].',';
					if (isset($v['args'][1])) $str.=" <b>".$v['args'][1]."</b>";
					if (isset($v['args'][2])) $str.="<br/>".$v['args'][2];
					
					
					//$str.='Class: '.$v['class'].',';
					//$str.='Type: '.$v['type'];
					
					if (isset($v['args']))
					{
						$str.='<div style="cursor:pointer;text-align:left;font-size:10pt;color:#555555" onClick="document.getElementById(\'args'.$k.'\').style.display=(document.getElementById(\'args'.$k.'\').style.display==\'block\')?\'none\':\'block\';">+INFO</div>';
						$str.='<div>';
							$str.='<pre id="args'.$k.'" style="display:none;text-align:left;font-size:10pt;padding:10px;color:#333333">'.print_r($v['args'],true).'</pre>';
						$str.='</div>';
					}			
				
					$str.='</div>';
				$str.='</div>';
				
			}
			
			$str.="</div></div>";
			return $str;
	    	
			
	    	//return "<pre>".print_r($this->getTrace(),true)."</pre>";
			//return "<b>{$this->file} (line {$this->line})</b><br/>".__CLASS__ . ": [{$this->code}]: {$this->message}<br/>";
	    }
    }
	
?>