<?php
class Util{
	
	/**
	 * Performs the same function as array_search except that it is case
	 * insensitive
	 * @param mixed $needle
	 * @param array $haystack
	 * @return mixed
	 */

	static public function recursiveArraySearch($haystack, $needle, $index = null)
	{
		 $aIt     = new RecursiveArrayIterator($haystack);
		 $it    = new RecursiveIteratorIterator($aIt);

		 while($it->valid())
		 {
			  if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {
					return $aIt->key();
			  }

			  $it->next();
		 }

		 return false;
	}


	static function array_merge_deep($arr) { // an array-merging function to strip one or more arrays down to a single one dimension array
    $arr = (array)$arr;
    $argc = func_num_args();
    if ($argc != 1) {
      $argv = func_get_args();
      for ($i = 1; $i < $argc; $i++) $arr = array_merge($arr, (array)$argv[$i]);
    }
    $temparr = array();
    foreach($arr as $key => $value) {
      if (is_array($value)) $temparr = array_merge($temparr, self::array_merge_deep($value));
      else $temparr = array_merge($temparr, array($key => $value));
    }
    return $temparr;
  }


	static public function super_unique($array)
   {
     $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

     foreach ($result as $key => $value)
     {
       if ( is_array($value) )
       {
         $result[$key] = self::super_unique($value);
       }
     }

     return $result;
   }

	static public function array_nsearch($needle, array $haystack) {
	   $it = new IteratorIterator(new ArrayIterator($haystack));
	   foreach($it as $key => $val) {
	       if(strcasecmp($val,$needle) === 0) {
	           return $key;
	       }
	   }
	   return false;
	} 

	public static function array_csort()
	{  
		$i=0;
		//coded by Ichier2003
   		$args = func_get_args();
		   $marray = array_shift($args);

		   $msortline = "return(array_multisort(";
		   foreach ($args as $arg) {
       		$i++;
		       if (is_string($arg)) {
        		   foreach ($marray as $row) {
               		$sortarr[$i][] = isset($row[$arg])?$row[$arg]:'';
		           }
       			} else {
		           $sortarr[$i] = $arg;
		       }
			       $msortline .= "\$sortarr[".$i."],";
		   }
		   $msortline .= "\$marray));";
			
			eval($msortline);
		   return $marray;
	}
	
    ///////// comprueba palabras iguales o parecidas a la dada ($aguja) de entre un string ($pajar)
	/////////
	///////// la fórmula es:
	/////////                 
	/////////  (aciertos(elem_busqueda,elem_pajar)>=tamaño(elem_busqueda) 
	/////////	 y (dif_tamaños(elem_busqueda,elem_pajar)<=3) o elementos_misma_familia))
	///////// o(aciertos(elem_busqueda,elem_pajar)>=(tamaño(elem_busqueda)-1) 
	///////// 	 y errores(elem_busqueda,elem_pajar)<=(tamaño(elem_busqueda)-2))		
	
	static public function comprueba($aguja,$pajar)
	{
		$elem_busca=strtoupper($aguja);
		//$elem_pajar=strtr($pajar,".,","  ");
		$elem_pajar_orig=split("[,. \"]",$pajar);
		$elem_pajar=array_map("strtoupper",$elem_pajar_orig);
	
		for ($j=0;$j<sizeof($elem_pajar);$j++)
		{
			$sim=similar_text($elem_pajar[$j],$elem_busca);
			$lev=levenshtein($elem_pajar[$j],$elem_busca);
			$len=strlen($elem_busca);
			$len2=strlen($elem_pajar[$j]);
			$sou1=soundex($elem_busca);
			$sou2=soundex($elem_pajar[$j]);
			$sou=strncasecmp ($sou1,$sou2,3);

			//print "<br>$j) ".$elem_pajar[$j].",".$elem_busca.":";
						
			if (($sim>=$len && ((($len2-$len)<=3) || !$sou)) || ($sim>=($len-1) && $lev<=($len-2)))
			{
				//print "<br>$sou1 $sou2 --- si ($sim>=$len && (((".($len2-$len)."<=3) || !$sou)) || ($sim>=".($len-1)." && $lev<=".($len-2).")";
				//print " --- ok!";
				return($elem_pajar_orig[$j]);		
			}
		}
		return(false);	
	}
	
	
	public static function validaNifCifNie($cif) {
	//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF bad, -2 = CIF bad, -3 = NIE bad, 0 = ??? bad
	   $cif = strtoupper($cif);
	   for ($i = 0; $i < 9; $i ++)
	      $num[$i] = substr($cif, $i, 1);
	//si no tiene un formato valido devuelve error
	   if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/i', $cif))
	      return 0;
	//comprobacion de NIFs estandar
	   if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/i', $cif))
	      if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1))
	         return 1;
	      else
	         return -1;
	//algoritmo para comprobacion de codigos tipo CIF
	   $suma = $num[2] + $num[4] + $num[6];
	   for ($i = 1; $i < 8; $i += 2)
	      $suma += substr((2 * $num[$i]),0,1) + substr((2 * $num[$i]),1,1);
	   $n = 10 - substr($suma, strlen($suma) - 1, 1);
	//comprobacion de NIFs especiales (se calculan como CIFs)
	   if (preg_match('/^[KLM]{1}/i', $cif))
	      if ($num[8] == chr(64 + $n))
	         return 1;
	      else
	         return -1;
	//comprobacion de CIFs
	   if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/i', $cif))
	      if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1))
	         return 2;
	      else
	         return -2;
	//comprobacion de NIEs
	   //T
	   if (preg_match('/^[T]{1}/i', $cif))
	      if ($num[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/i', $cif))
	         return 3;
	      else
	         return -3;
	   //XYZ
	   if (preg_match('/^[XYZ]{1}/i', $cif))
	      if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1))
	         return 3;
	      else
	         return -3;
	//si todavia no se ha verificado devuelve error
	   return 0;
	}
	
	public static function getRemoteInfo ($type=null) 
	{
	   $proxy="";
	   $IP = "";
	   if (isSet($_SERVER)) {
	       if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
	           $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
	           $proxy  = $_SERVER["REMOTE_ADDR"];
	       } elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
	           $IP = $_SERVER["HTTP_CLIENT_IP"];
	       } else {
	           $IP = $_SERVER["REMOTE_ADDR"];
	       }
	   } else {
	       if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
	           $IP = getenv( 'HTTP_X_FORWARDED_FOR' );
	           $proxy = getenv( 'REMOTE_ADDR' );
	       } elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
	           $IP = getenv( 'HTTP_CLIENT_IP' );
	       } else {
	           $IP = getenv( 'REMOTE_ADDR' );
	       }
	   }
	   if (strstr($IP, ',')) {
	       $ips = explode(',', $IP);
	       $IP = $ips[0];
	   }
	   
	   $RemoteInfo=array(	"ip"=>$IP,"host"=>@GetHostByAddr($IP),"proxy"=>$proxy);
	   
	   if ($type==null) return $RemoteInfo;
	   else if (isset($RemoteInfo[$type])) return $RemoteInfo[$type];
	   return null;
	}
	
	public static function createThumb($name,$filename,$new_w,$new_h){
	        $system=explode('.',$name);
	        $ext=end($system);
	        if (preg_match('/jpg|jpeg/i',$ext)){
	            $src_img=imagecreatefromjpeg($name);
	        }else if (preg_match('/png/i',$ext)){
	           
	            $src_img=imagecreatefrompng($name);
	        }
	               
	        $old_x=imageSX($src_img);
	        $old_y=imageSY($src_img);
	        if ($old_x > $old_y) {
	            $thumb_w=$new_w;
	            $thumb_h=$old_y*($new_h/$old_x);
	        }
	        if ($old_x < $old_y) {
	            $thumb_w=$old_x*($new_w/$old_y);
	            $thumb_h=$new_h;
	        }
	        if ($old_x == $old_y) {
	            $thumb_w=$new_w;
	            $thumb_h=$new_h;
	        }
	       
	        $dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
	        imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
	       
	        if (preg_match("/png/",$ext))
	        {
	            imagepng($dst_img,$filename);
	        } else {
	            imagejpeg($dst_img,$filename);
	        }
	        imagedestroy($dst_img);
	        imagedestroy($src_img);
	}
	
	static public function parseIp($paramA)
	{
		//////// cambiar de IP.IP.IP.IP a IP-IP-IP-IP
		//print "BUSQUEDA: ".$paramA;
		$i=0;	// num de ip (0,1,2,3)
		$j=0;	// indice cadena PARAMA
		$k=0;	// indice cadena IP
		$max=strlen($paramA);	// Longitud de la cadena PARAMA
		$estado="INI";		// estado actual

		while ($j<$max)
		{
			$c=substr($paramA,$j,1);	// Caracter en curso
			switch($estado)
			{
				case "INI":
							if (($c<='9' && $c>='0') || $c=='*') {$t[$i][$k]=$c;$estado="NUM";$k++;} break;
				case "NUM":
							if ($k==3) { //print "<br>$i) '".$t[$i][0].$t[$i][1].$t[$i][2]."'<br>";
								 $i++;$k=0;$estado="PUN";
								  }
							else
							{
								if (($c<='9' && $c>='0')){$t[$i][$k]=$c;$estado="NUM";$k++;}
								else if ($c=='*') {$t[$i][$k]=$c;$estado="AST";$k++;}
								else if ($c=='.') { //print "<br>$i) '".$t[$i][0].$t[$i][1].$t[$i][2]."'<br>";
													$i++;$k=0;$estado="PUN";}
							}
							break;
				case "AST":
							if (($c<='9' && $c>='0')) {$t[$i][$k]=$c;$estado="NUM";$k++;}
							else if ($c=='.') { //print "<br>$i) '".$t[$i][0].$t[$i][1].$t[$i][2]."'<br>";
												$i++;$k=0;$estado="PUN";}
							break;
				case "PUN":
							if (($c<='9' && $c>='0')) {$t[$i][$k]=$c;$estado="NUM";$k++;}
							else if ($c=='*') {$t[$i][$k]=$c;$estado="AST";$k++;}
							break;
			}
			$j++;
				//print"<br>ESTADO: $estado";
		}
		//print "<br>$i) '".$t[$i][0].$t[$i][1].$t[$i][2]."'<br>";
		$i++;

		if ($estado!="NUM") {while ($i<4) {$t[$i][0]='*'; //print "<br>$i) '".$t[$i][0].$t[$i][1].$t[$i][2]."'<br>";
		$i++; }}
		
		//echo _r($t);
		
		$j=4;
		while ($i>0)
		{
			$i--;
			$aux=(isset($t[$i][0])?$t[$i][0]:'').(isset($t[$i][1])?$t[$i][1]:'').(isset($t[$i][2])?$t[$i][2]:'');

			eval("\$ip$j=\$aux;");
			$j--;
		}

		$ip1=str_replace('*','(.*)',$ip1);
		if (strlen($ip1)==0) $ip1='(.*)';
		$ip2=str_replace('*','(.*)',$ip2);
		if (strlen($ip2)==0) $ip2='(.*)';
		$ip3=str_replace('*','(.*)',$ip3);
		if (strlen($ip3)==0) $ip3='(.*)';
		$ip4=str_replace('*','(.*)',$ip4);
		if (strlen($ip4)==0) $ip4='(.*)';

		return ($ip1."[.]".$ip2."[.]".$ip3."[.]".$ip4);
	}
	
	static public function noCacheHeader(){
		header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
  		header("Pragma: no-cache");
	}
	
	function stringToFilename($word) {
	    $tmp = preg_replace('/^\W+|\W+$/', '', $word); // remove all non-alphanumeric chars at begin & end of string
	    $tmp = preg_replace('/\s+/', '_', $tmp); // compress internal whitespace and replace with _
	    return strtolower(preg_replace('/\W-/', '', $tmp)); // remove all non-alphanumeric chars except _ and -
	}

	/**
	* Adds Nodes to an Existing XML Writer Object
	*
	* @param XMLWriter $xml
	* @param string $nodeName
	* @param string | array | stdObject $nodeValue
	*/
	static private function _addNodeToXML(&$xml,$nodeName,$nodeValue){
		if(is_array($nodeValue)){
			$xml->startElement($nodeName);
			foreach($nodeValue as $nodeValueKey => $nodeValueValue){
				self::_addNodeToXML($xml, $nodeValueKey, $nodeValueValue);
			}
			$xml->endElement();
		}elseif($nodeValue instanceof stdClass){
			$xml->startElement("item");
			foreach($nodeValue as $nodeValueKey => $nodeValueValue){
				self::_addNodeToXML($xml, $nodeValueKey, $nodeValueValue);
			}
			$xml->endElement();
		}else{
			$xml->writeElement($nodeName,$nodeValue);
		}
	}

	/**
	* Converts an Object of stdClass to an XML (XMLWriter) “Object”
	*
	* @param stdClass|array $stdClass
	* @return s
	*/
	static public function stdClassToXML($stdClass){
		$xml = new XMLWriter();
		$xml->openMemory();
		self::_addNodeToXML($xml,'root',$stdClass);
		return $xml->outputMemory();
	}
}
?>