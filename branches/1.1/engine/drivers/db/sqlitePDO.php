<?php 
class SqliteGeneric extends dbDriver{
	protected $conn;
	protected $numrows;
	protected $resultado;
	protected $cur;
	protected $charset;
	protected $DateFormat;

	public $ALIAS_SEPARATOR=' as ';

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
			//echo var_export($args,true);
			switch($args[1]){
				case 'BOOL': $tipo='boolean'; break;
				case 'NUMERIC': case 'INTEGER': case 'REAL': case 'DOUBLE': case 'FLOAT':
					$tipo='numeric';break;
				case 'DATETIME':
					return array('type'=>'date','params'=>array('format'=>$this->getDateFormatter())); //,'extra'=>trim($args[5]));
				case 'CHAR': case 'VARCHAR': 
					$tipo='varchar'; break;
				case 'TEXT':
					$tipo='text';break;
				case 'BLOB': 
					$tipo='varchar'; break;

			}
			
			$par=array('type'=>$tipo);
			//if (!empty($args[3])) $par['size']=$args[3]; //,'extra'=>trim($args[5]));
			return $par;
		}
		return array('type'=>$type);
	}

	public function getTableInfo($table,&$temp_schemas){

		$query="select sql from sqlite_master where type='table' and name='{$table}' and tbl_name='{$table}'";
		$t=$this->query($query);
		$sql=$t[0]['sql'];
		//echo $sql;
		preg_match("/\(([^()]*)\)/",$sql,$regs);
		$x=explode(',',$regs[1]);
		$data=array();
		//echo _r($x);
		foreach($x as $i=>$cur){
			if (!empty($cur)){
				$cur=trim($cur);
				$params=preg_split('/[ ,]|^[ ]/',$cur);
				preg_match('/"([^"]*)"/i',$cur,$args);
				$name=$args[1];
				$name2=explode("_",$name);
				$fieldname=implode("",array_map("ucfirst",$name2));

				//echo "\n--------------".$name.':'.$params[1];
				$data[$name]=$this->selectType($params[1]);
				if (preg_match('/primary key/i',$cur)) $data[$name]['pk']=true;
				if (preg_match('/not null/i',$cur)) $data[$name]['params']['null']=false;
				if (preg_match('/default ([^ ]*)/i',$cur,$args)) $data[$name]['params']['default']=$args[1];
				$data[$name]['phpname']=$fieldname;
			}
		}
		//echo _r($data);die();
		return $data;
	}



	public function getDateFormatter()
	{
      return $this->DateFormat;
	}

	public function setDateFormatter($format="%Y-%m-%d")
	{
      $this->DateFormat=$format;
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

class SqlitePDO extends SqliteGeneric{


	public function __construct($props=array())
	{
		if ($props!=null)
		{
			parent::__construct($props);
			extract($props);

			try {
	        	$this->conn = new PDO("sqlite:".$schema);
	    		if (isset($encoding)) {
					switch($encoding)
					{
						case 'ISO8859': break;
						default: $this->setCharset($encoding);
					}
					//$this->setCharset($encoding);
				}
				$this->setSchema($schema);
				if (isset($dateformat)) $this->setDateFormatter($dateformat);
				else $this->setDateFormatter();
			}
			catch(PDOException $e){
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

	private function _convert(&$txt){
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
				if ($this->charset!=null && $this->charset!='UTF-8') // sqlite trabaja internamente con UTF8 = ningun cambio
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
			if (!$bool) throw new PDOException(isset($info[2])?$info[2]:"sqlite error",999);
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

	public function lastInsertId(){
		return $this->conn->lastInsertId();
	}
}



?>