<?php 

class Sqlite extends dbDriver{
	
	private $conn;
	private $numrows;
	private $resultado;
	private $cur;
	private $charset;
		
	public function __construct($props=array())
	{
		extract($props);
				
		if (!$this->conn = sqlite_open($schema))
			throw new dbException(sqlite_error_string($this->conn),sqlite_last_error($this->conn));
		
		switch($encoding)
		{
			case 'ISO8859': break;
			default: $this->setCharset($encoding);
		}
	}
	
	public function setCharset($cs)
	{
		//$this->charset_direct=mysql_set_charset ($cs,$this->conn);	
		$this->charset=$cs;
	}
	
	private function _convert(&$txt)
	{
		$txt=utf8_encode($txt); // sqlite solo trabaja con utf8 y iso-8859-1
	}
	
	public function query($select,$limit=null)
	{
		if ($limit!=null) $select.=" limit ".$limit;
		
		if ($this->resultado=sqlite_query($this->conn,$select))
		{
			$t=Array();
			$i=0;
			
			$t = sqlite_fetch_all($this->resultado, SQLITE_ASSOC);
			
			if ($this->charset!=null && $this->charset!='UTF-8') // sqlite trabaja internamente con UTF8 = ningun cambio
			{
				array_walk_recursive ($t, array(__CLASS__,'_convert'));
			}
			
			if ($limit==1) return $t[0];
			
			return $t;
		}
		
		throw new dbException(sqlite_error_string($this->conn),sqlite_last_error($this->conn));
		
	}
	
	public function execute($select)
	{
		if (!($this->resultado=sqlite_exec($this->conn,$select)))
		{
			throw new dbException(sqlite_error_string($this->conn),sqlite_last_error($this->conn));
		}	
		$this->lastQueryExecuted=$select;	
		return $this->resultado;
	}
	
	public function numrows()
	{
		return sqlite_num_rows($this->conn);
	}
	
	public function __destruct(){
		if ($this->resultado) mysql_free_result($this->resultado);
	}
}
?>