<?php 

class Oci extends dbDriver{
	
	private $conn;
	private $numrows;
	private $resultado;
	private $cur;
	private $charset;
	private $territory;
	private $charset_direct;
	private $TRANSACTION_MODE;
	
	public function __construct($props=array())
	{
		if ($props!=null)
		{
			//list($db,$login,$pass,$db_select,$charset)=$props;
			extract($props);
			
			$charset=explode('.',$encoding);
			if (isset($charset[1])) {$encoding=$charset[1];$this->setTerritory($charset[0]);}
			else $encoding=$charset[0];

			if (function_exists('OCILogon')){
				if (!$this->conn=OCILogon($username,$password,$host,$encoding))
				{
					$error=ociError();
					throw new dbException($error['message'],$error['code']);
				}
			}else {throw new dbException('The Oci module of PHP is not loaded',999);return null;}
			
			if (isset($encoding)) $this->setCharset($encoding);
			
			if (isset($dateformat)) $this->setDateFormatter($dateformat);
			else $this->setDateFormatter();
			
			$this->TRANSACTION_MODE=false;
			//$this->usernameDB=$username;
		}
	}
	
	static public function addAutonumericColumn($value){ 
		if (!empty($value)) return true;
		return false;
	}
	
	private function setTerritory($cs)
	{
		$this->territory=$cs;
	}
	
	private function getTerritory()
	{
		return $this->territory;
	}
	
	public function setCharset($cs)
	{
		$this->charset=$cs;
	}
	
	public function getCharset()
	{
		return $this->charset;
	}
	
	static public function upper($text,$column=null){
		return "upper(".strtoupper($text).") ".(($column==null)?"":mb_strtoupper($column,'UTF-8'));
	}
	
	private function _convert($txt)
	{
		if (is_object($txt)){
			$data=$txt->load();
			$txt->free();
			return utf8_encode($data);
		}
		
		return stripslashes(utf8_encode($txt));
	}
	
	protected function selectType($type,$len,$precision,$scale){
		
		$par=array('type'=>$type);
		
		switch($type){
			case 'NUMBER': case 'INTEGER':
			case 'PLS_INTEGER': CASE 'BINARY_INTEGER':  case 'DEC': CASE 'DECIMAL': 
			CASE 'NUMERIC': CASE 'DOUBLE PRECISION': CASE 'INT': CASE 'SMALLINT': CASE 'REAL': CASE 'FLOAT':
			CASE 'NATURAL': CASE 'POSITIVE': CASE 'NATURALN': CASE 'POSITIVEN': CASE 'SIGNTYPE':
				$par['type']='numeric';
				if (!empty($precision)) $par['size']=$precision;
				break;
			case 'DATE': 
			CASE 'TIMESTAMP': CASE 'INTERVAL YEAR': CASE 'INTERVAL DAY':
				return array('type'=>'date','params'=>array('format'=>$this->getDateFormatter())); //,'extra'=>trim($args[5]));
			case 'VARCHAR': case 'VARCHAR2': case 'CHAR':case 'NVARCHAR2': case 'NCHAR':
			CASE 'ROWID': CASE 'UROWID':
				$par['type']='varchar';
				if (!empty($len)) $par['size']=$len;
				break;
			case 'LONG': CASE 'CLOB':
				$par['type']='varchar';
				if (!empty($len)) $par['size']=$len;
				break;  
			//// not tested
			case 'BLOB': CASE 'NCLOB': CASE 'BFILE':
			CASE 'RAW': CASE 'LONG RAW': CASE 'XMLType':
			CASE 'BINARY_FLOAT': CASE 'BINARY_DOUBLE':
				$par['type']='bin';
				if (!empty($len)) $par['size']=$len;
				break;  
		}

		return $par;
	}
	
	public function getTableInfo($table,&$temp_schemas){
		
		// from all_tab_columns col, all_col_comments com
		// where com.owner=col.owner 
		// and com.owner='{$this->usernameDB}'
							
		
		$info=$this->query("select col.COLUMN_NAME,col.DATA_TYPE , col.DATA_LENGTH, col.DATA_PRECISION, col.DATA_SCALE, col.NULLABLE, col.DATA_DEFAULT, com.comments COMMENTS
							from user_tab_columns col, user_col_comments com 
							where col.table_name = '{$table}'
							and col.table_name=com.table_name
							and col.column_name=com.column_name");
		
		//$info=$this->query('SHOW FULL COLUMNS FROM '.$table);
		
		$schema=$this->getSchema();
		
		$info2=$this->query("select cc1.POSITION,cc1.CONSTRAINT_NAME, cc1.TABLE_NAME, cc1.COLUMN_NAME, cc2.TABLE_NAME REFERENCED_TABLE_NAME, cc2.COLUMN_NAME REFERENCED_COLUMN_NAME
							from user_cons_columns cc1, user_cons_columns cc2, user_constraints r, user_constraints c
							where r.constraint_name = c.r_constraint_name 
							and c.constraint_type = 'R'
							and cc1.owner = c.owner
							and cc1.constraint_name = c.constraint_name
							and cc1.table_name = c.table_name
							and cc2.owner = r.owner
							and cc2.constraint_name = r.constraint_name
							and cc2.table_name = r.table_name
							and cc2.position = cc1.position
							and c.table_name like upper('{$table}')
							union
							select c2.position,'PRIMARY',u.table_name, c2.column_name, '' REFERENCED_TABLE_NAME, '' REFERENCED_COLUMN_NAME
							from user_constraints u,user_cons_columns c2 
							where u.constraint_type='P' 
							and u.owner = c2.owner
							and u.constraint_name = c2.constraint_name
							and u.table_name = c2.table_name
							and u.table_name='{$table}'
							order by position asc");
		
		$data=array();
				
		if (!empty($info)){
			
			foreach($info as $field){
				$name=strtolower($field['COLUMN_NAME']);
				$name2=explode("_",$name);
				$fieldname=implode("",array_map("ucfirst",$name2));
				
				//if (isset())
				
				$data[$field['COLUMN_NAME']]=$this->selectType($field['DATA_TYPE'],$field['DATA_LENGTH'], $field['DATA_PRECISION'],$field['DATA_SCALE']);
				if (!empty($field['DATA_DEFAULT'])) $data[$field['COLUMN_NAME']]['params']['default']=$field['DATA_DEFAULT'];
				$data[$field['COLUMN_NAME']]['params']['null']=($field['COLUMN_NAME']['NULLABLE']=='N')?false:true;
				if (!empty($field['COMMENTS'])) $data[$field['COLUMN_NAME']]['params']['comment']=$field['COMMENTS'];
				$data[$field['COLUMN_NAME']]['phpname']=$fieldname;
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
	
	
	public function filters($select,$filters=null,$offset=0){

		$pos=strripos($select, 'where',$offset);
		if ($pos===false) $where=' where ';else $where=' and ';
		if (!empty($filters)){
			$select.=$where.$filters;
		}
		return $select;
	}
	
	public function pagination($select,$props=array(),$filters=null)
	{
		if (isset($props['page']))	
		{
			$firstRec = $props['page'] * $props['limit'] + 1;
			$lastRec = ($props['page']+1) * $props['limit'];
		}
		else
		{
			$firstRec = $props['offset']  + 1;
			$lastRec = $firstRec + $props['limit']-1;
		}
		
		$sql = " SELECT * FROM( SELECT sub1.*, RowNum as ".self::ROW_NUMBER." FROM ( $select ".
				" ) sub1 WHERE RowNum <= $lastRec ) WHERE $firstRec <= ".self::ROW_NUMBER;
		
		return $sql;
	}
	
	public function query($select,$limit=null)
	{
		if ($this->resultado=$this->execute($select))
		{
			$t=Array();
			$i=0;
			
			while (($limit==null || $i<$limit) && (OCIFetchInto($this->resultado, $row,OCI_RETURN_NULLS + OCI_ASSOC))) {
	    		foreach($row as $id=>$value)
				{
					$t[$i][$id]=($this->charset!=null)?$this->_convert($value):$value;
				}
				$i++;
			}
			if ($limit==1) return $t[0];
			
			
			return $t;
		}
		
		$this->setOcierror($this->resultado);
	}

	public function errorInfo($exception){

		$info=new stdClass();
		preg_match("/(ORA-[0-9]{5})/i",$exception->getMessage(),$args);
		if (isset($args[1])) $info->code=$args[1];else $info->code=0;

		switch($args[1]){
			case 'ORA-00001': $info->message='database:errors:constraint:unique';break;
			default:				$info->message='database:errors:others';break;
		}
		return $info;
	}

	public function setOcierror($res=null)
	{
		$errorLog=ociError($res);
		$error=utf8_encode(preg_replace("/(.*)ORA-[0-9]{5}:(.*)/","$2",$errorLog['message']));
		throw new dbException($error,$errorLog['code']);
	}
	
	public function execute($select)
	{
		$select2=str_replace("\'","''",$select);
		if (!($this->cur = OCIParse($this->conn,$select2)))
		{
			$this->setOcierror($this->cur);	
		}	
		
		OCIExecute($this->cur,$this->TRANSACTION_MODE?OCI_DEFAULT:OCI_COMMIT_ON_SUCCESS);
		$this->lastQueryExecuted=$select2;			 	
		return $this->cur;
	}
	
	
	
	public function beginTransaction(){	$this->TRANSACTION_MODE=true;}
	public function commit(){
		
		$this->TRANSACTION_MODE=false;
		$committed = oci_commit($this->conn);
	}
	public function rollBack() {
		oci_rollback($this->conn);
	}
	public function getConn(){return $this->conn;}
	 
	
	
	public function numrows()
	{
		return ocirowcount($this->cur);
	}
	
	public function __destruct(){
		if ($this->resultado) OCIFreeStatement($this->cur);
		if (isset($this->conn)) oci_close($this->conn);
	}
	
	
	public function alterSessionForDate($phpFormat)
	{
		$format=$this->transFormatDate($phpFormat);
		$territory=$this->getTerritory();
		
		if ($territory!=null) {
			$stmt = $this->execute("ALTER SESSION SET NLS_TERRITORY='{$territory}'");
		}
		$stmt = $this->execute("ALTER SESSION SET NLS_DATE_FORMAT='{$format}'");  //se llama en DBAdapter!
	}
	
	private $DateFormat;
	
	public function getDateFormatter()
	{
       return $this->DateFormat;
	}
	
	static public function transFormatDatePHP($value,$props){
		$format=$props['format'];
		$key=$props['filter_key'];
		$range=isset($props['range'])?$props['range']:"=";
		$format=self::transFormatDate($format);
		//return "to_char({$key},'{$format}'){$range}'{$value}'";
		return "{$key}{$range} to_date('{$value}','{$format}')";
		//return "to_date({$key},'{$format}') {$range} to_date('{$value}','yyyy-mm-dd')
	}
	
	public function setDateFormatter($format="%Y-%m-%d")
	{
      $this->DateFormat=$format;
      $this->alterSessionForDate($format);
	}
	
	static public function transFormatDate($txt)
    {
    	return strtr($txt,array("%d"=>"dd","%m"=>"mm","%Y"=>"yyyy","%H"=>"HH24","%M"=>"MI","%S"=>"SS"));
    }
        
	public function nextValueSequence($sequence){ return "select {$sequence}.nextval {sequence} from dual";}
	public function lastInsertId(){	return null;} // not supported
	
}
?>