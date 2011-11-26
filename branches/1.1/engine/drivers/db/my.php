<?php 
class MysqlGeneric extends dbDriver{
	protected $conn;
	protected $numrows;
	protected $resultado;
	protected $cur;
	protected $charset;
	protected $DateFormat;
		
	public function setCharset($cs){}
	public function query($select,$limit=null){}
	public function execute($select){}
	public function numrows(){}

	public function errorInfo($exception){

		$info=new stdClass();
		$code=$exception->getCode();
		$info->code=$code;

		switch($code){
			case '999':		$info->message='database:errors:constraint:unique';break;
			default:			$info->message='database:errors:others';break;
		}
		
		return $info;
	}

	protected function selectType($type){
		if (preg_match("/([^(]*)(\(){0,1}([^)]*)(\){0,1})(.*)/",$type,$args)){
			
			switch($args[1]){
				case 'bit': $tipo='boolean'; break; 
				case 'tinyint':	case 'smallint':case 'mediumint':case 'int':case 'bigint': case 'integer':
				case 'decimal': case 'float': case 'double': case 'double precision': case 'real': 
				case 'numeric': case 'dec':
					$size=explode(",",$args[3]);
					$par=array('type'=>'numeric','size'=>$size[0]);
					$extra=trim($args[5]);
					if (!empty($extra))$par['params']=array($extra=>true);
					if (isset($size[1])) $par['params']['presicion']=$size[1];
					return $par;			
				case 'datetime':case 'date': case 'timestamp': case 'time': case 'year': 
					return array('type'=>'date','params'=>array('format'=>$this->getDateFormatter())); //,'extra'=>trim($args[5]));
				case 'char': case 'varchar': case 'character': case 'nchar': case 'char byte':
				case 'national char': 
					$tipo='varchar'; break;
				case 'text':
					$tipo='text';break;
				case 'binary': case 'varbinary': case 'blob': case 'enum': case 'set':
				case 'tinyblob': case 'tinytext': case 'mediumblob': case 'mediumtext':
				case 'longblob': case 'longtext':
					$tipo='varchar'; break; 
				 
			}
			
			$par=array('type'=>$tipo);
			if (!empty($args[3])) $par['size']=$args[3]; //,'extra'=>trim($args[5]));
			return $par;
		}
		return array('type'=>$type);
	}
	
	public function getTableInfo($table,&$temp_schemas){
		
		//$info=$this->query('describe '.$table);
		$info=$this->query('SHOW FULL COLUMNS FROM '.$table);
		
		$schema=$this->getSchema();
		
		$info2=$this->query("SELECT CONSTRAINT_NAME, TABLE_NAME,COLUMN_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
						FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
						where CONSTRAINT_SCHEMA='{$schema}'
						and TABLE_NAME='{$table}'
						ORDER BY ORDINAL_POSITION ASC, POSITION_IN_UNIQUE_CONSTRAINT");
		
		$data=array();
				
		if (!empty($info)){
			
			foreach($info as $field){
				
				$name=strtolower($field['Field']);
				$name2=explode("_",$name);
				$fieldname=implode("",array_map("ucfirst",$name2));
				
				$data[$field['Field']]=$this->selectType($field['Type']);
				if ($field['Extra']=='auto_increment') $data[$field['Field']]['autonumeric']=true;
				
				$data[$field['Field']]['params']['null']=($field['Null']=='NO')?false:true;
				if (!empty($field['Default'])) $data[$field['Field']]['params']['default']=$field['Default'];
				if (!empty($field['Comment'])) $data[$field['Field']]['params']['comment']=$field['Comment'];
				$data[$field['Field']]['phpname']=$fieldname;
			}
			
			$temp_schemas=array();
			$cols=array_keys($data);
			foreach($info2 as $constraint){
				$pos=Util::array_nsearch($constraint['COLUMN_NAME'],$cols);
				if ($pos!==false){
					if ($constraint['CONSTRAINT_NAME']=='PRIMARY')	
						$data[$cols[$pos]]['pk']=true;
					else{ 
						$reftable=$constraint['REFERENCED_TABLE_NAME'];
						$refcolumn=$constraint['REFERENCED_COLUMN_NAME'];
						$temp_schemas[$reftable][]=$refcolumn;
						$data[$cols[$pos]]['fk']=$reftable.'.'.$refcolumn;
					}
					
				}
			}
		}
		
		return $data;
	}
	
	
	
	public function getDateFormatter()
	{
      return $this->DateFormat;
	}
	
	public function setDateFormatter($format="%Y-%m-%d")
	{
      $this->DateFormat=$format;
      //$this->alterSessionForDate($format);
	}
	
	static public function upper($text,$column=null){
		return "upper({$text}) ".(($column==null)?"":mb_strtoupper($column,'UTF-8'));
	}
	
	static public function transFormatDate($txt)
    {
    	return strtr($txt,array("%d"=>"%d","%m"=>"%m","%Y"=>"%Y","%H"=>"%H","%M"=>"%i","%S"=>"%S"));
    }
	
	static public function transFormatDatePHP($value,$props){
		
		$format=$props['format'];
		$key=$props['filter_key'];
		$range=isset($props['range'])?$props['range']:"=";
		$format=self::transFormatDate($format); 
		return "date_format({$key},'{$format}'){$range}'{$value}'";
		//return "{$key}{$range} str_to_date('{$value}','{$format}')";
		
	}
	
	public function filters($select,$filters=null,$offset=0){

		$pos=strripos($select, 'where',$offset);
		if ($pos===false) $where=' where ';else $where=' and ';
		if (!empty($filters)){
			$select.=$where.$filters;
		}
		return $select;
	}
	
	public function pagination($select,$props=array())
	{
		if (isset($props['page']))	
		{
			$limit=$props['limit'];
			if ($props['page']!=null) $offset=$props['page']*$props['limit'];
			else $offset=null;
		}
		else
		{
			$limit=$props['limit'];
			$offset=$props['offset'];
		}
		
		if ($offset!=null){ $select.=" limit {$offset},{$limit}";}
		else $select.=" limit ".$limit;
		
		/*$this->execute("SET @".self::ROW_NUMBER."=".$offset);
		$sql="select *,@".self::ROW_NUMBER.":=@".self::ROW_NUMBER."+1 as ".self::ROW_NUMBER." from ({$select}) sub1";
		return $sql;*/
		
		return $select;
	}
	
	public function nextValueSequence($sequence){ return null;} // not supported
}

class MysqlPDO extends MysqlGeneric{
	
	public function __construct($props=array())
	{
		if ($props!=null)
		{
			extract($props);
							
			try {
	        	$this->conn = new PDO("mysql:host={$host};dbname={$schema}",$username,$password);
	    		if (isset($encoding)) $this->setCharset($encoding);
				$this->setSchema($schema);
				if (isset($dateformat)) $this->setDateFormatter($dateformat);
				else $this->setDateFormatter();
			}
			catch(PDOException $e)
	    	{
	    		throw new dbException($e->getMessage(),$e->getCode());
			}
		}
		
	}
	
	
	
	public function setCharset($cs)
	{
		$this->charset=$cs;
	}
	
	public function getCharset()
	{
		return $this->charset;
	}
	
	private function _convert(&$txt)
	{
		$txt=utf8_encode($txt); // sqlite solo trabaja con utf8 y iso-8859-1
	}
	
	
	
	public function query($select,$limit=null)
	{
		if ($limit!=null) $select.=" limit ".$limit;
		
		try{
			$x=$this->conn->query($select);
			if ($x!==false)
			{
				$t = $x->fetchAll(PDO::FETCH_ASSOC);
				if ($this->charset!=null) 
				{
					array_walk_recursive ($t, array(__CLASS__,'_convert'));
				}
				if (count($t)>0 && $limit==1) return $t[0];
			}
			else $t=null;
			$this->numrows=count($t);
		}catch(PDOException $e)
    	{
    		throw new dbException($e->getMessage(),$e->getCode());
		}
				
		return $t;
	}
	
	public function execute($select)
	{
		try{
			$x= $this->conn->prepare($select);
			$bool=$x->execute();
			$info=$x->errorInfo();
			if (!$bool) throw new PDOException(isset($info[2])?$info[2]:"mysql error",999);
			$this->numrows=$x->rowCount();
			$this->lastQueryExecuted=$select;	
			//echo _r(mysql_info());
			//$this->resultado=$this->conn->exec($select);
		}catch(PDOException $e)
    	{
    		throw new dbException($e->getMessage(),$e->getCode());
		}
		return $bool;
	}
	
	public function beginTransaction(){	$this->conn->beginTransaction();}
	public function commit(){$this->conn->commit();}
	public function rollBack() {$this->conn->rollBack();}
	public function getConn(){return $this->conn;}
	public $FORCE_UPDATE_NUM_ROWS_AFFECTED=true; 
	
	public function numrows()
	{
		//Log::_add(__METHOD__,$this->numrows." elements are affected","model",__CLASS__,($this->numrows==0)?Log::ERROR:Log::SUCCESS);
		Controller::triggerHook('debug','model',array(
					'message' =>$this->numrows." elements are affected",
					'type'=>'model',
					'error'=>($this->numrows==0)?Log::ERROR:Log::SUCCESS,
					'class'=>__CLASS__,
					'method'=>__METHOD__));
		return $this->numrows;
	}
	
	public function __destruct(){
		
	}
	
	public function alterSessionForDate($phpFormat)
	{
		//$format=$this->transFormatDate($phpFormat);
		//$stmt = $this->execute("ALTER SESSION SET NLS_DATE_FORMAT='{$format}'");  //se llama en DBAdapter!
	}
	
	public function lastInsertId(){
		return $this->conn->lastInsertId(); 
	}
	
	
}

class Mysql_i extends MysqlGeneric{
	
	private $charset_direct;
	
	public function __construct($props=array())
	{
		if ($props!=null)
		{
			//list($db,$login,$pass,$db_select,$charset)=$props;
			extract($props);
			
			if (!($this->conn=mysqli_connect($host, $username, $password,$schema)))
			{
				throw new dbException(mysqli_error($this->conn),mysqli_errno($this->conn));
			}	
			
			if (isset($dateformat)) $this->setDateFormatter($dateformat);
			else $this->setDateFormatter();
			
			if (isset($encoding)) $this->setCharset($encoding);
		}
	}
	
	public function setCharset($cs)
	{
		$this->charset_direct=mysqli_set_charset ($this->conn,$cs);	
		$this->charset=$cs;
	}
	
	private function _convert($txt)
	{
		/*switch($this->charset)
		{
			case 'UTF-8': return utf8_encode($txt); // en utf8 se pierden las palabras!
			default: return htmlentities ($txt,ENT_NOQUOTES,$this->charset);
		}*/
		return stripslashes(utf8_encode($txt));
	}
	
	public function query($select,$limit=null)
	{
		if ($this->resultado=$this->execute($select))
		{
			$t=Array();
			$i=0;
			
			while (($limit==null || $i<$limit) && ($row = mysqli_fetch_array($this->resultado, MYSQL_ASSOC))) {
	    		
				foreach($row as $id=>$value)
				{
					$t[$i][$id]=(!$this->charset_direct)?$this->_convert($value):$value;
				}
				$i++;
			}
			if ($limit==1) return $t[0];
			
			return $t;
		}
		
		throw new dbException(mysqli_error($this->conn),mysqli_errno($this->conn));
		
	}
	
	public function beginTransaction(){	mysqli_autocommit($this->conn, FALSE);}
	public function commit(){mysqli_commit($this->conn);}
	public function rollBack() {mysqli_rollback($this->conn);}
	public function getConn(){return $this->conn;}
	public $FORCE_UPDATE_NUM_ROWS_AFFECTED=true;
	
	
	public function execute($select)
	{
		if (!($x=mysqli_query($this->conn,$select)))
		{
			throw new dbException(mysqli_error($this->conn),mysqli_errno($this->conn));
		}
		$this->resultado=$x;
		$this->lastQueryExecuted=$select;	
		return $x;
	}
	
	public function numrows()
	{
		return mysqli_affected_rows($this->conn);
	}
	
	public function __destruct(){
		if ($this->resultado) mysqli_free_result($this->resultado);
	}
	
	public function lastInsertId(){
		// ojo bigint!
		return mysqli_insert_id($this->conn);	
	}
}
?>