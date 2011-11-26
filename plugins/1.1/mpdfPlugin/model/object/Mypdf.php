<?php
require_once CUBE_PATH_ROOT."/plugins/mpdfPlugin/lib/mpdf.php";

class Mypdf
{
    static private $instance;
    
    public function __construct() {
      
    }
    
    static public function getInstance($codepage='win-1252',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P'){
      	if (!isset(self::$instance)){
    		self::$instance=new mPDF($codepage,$format,$default_font_size,$default_font,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf, $orientation);
      	}
      	return self::$instance;
    }
}


?>