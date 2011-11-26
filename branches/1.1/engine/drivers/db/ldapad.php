<?php 

class LdapAD extends dbDriver{
	
	private $conn;
	private $numrows;
	private $resultado;
	private $cur;
	private $charset;
	//private $usernameDB;
	private $charset_direct;
	private $TRANSACTION_MODE;

	const ADLDAP_NORMAL_ACCOUNT=805306368;
	const ADLDAP_WORKSTATION_TRUST=805306369;
	const ADLDAP_INTERDOMAIN_TRUST=805306370;
	const ADLDAP_SECURITY_GLOBAL_GROUP=268435456;
	const ADLDAP_DISTRIBUTION_GROUP=268435457;
	const ADLDAP_SECURITY_LOCAL_GROUP=536870912;
	const ADLDAP_DISTRIBUTION_LOCAL_GROUP=536870913;

	protected  $_account_suffix;
	protected  $_base_dn;
	
	protected  $_domain_controllers = array ();
	protected  $_ad_username;
	protected  $_ad_password;

	protected  $_real_primarygroup=true;
	protected  $_use_ssl=false;
	protected  $_recursive_groups=true;

	//other variables
	
	protected $_bind;
	private $_optionsLdap;

	public function __construct($props=array())
	{
		if ($props!=null)
		{
			//list($db,$login,$pass,$db_select,$charset)=$props;
			extract($props);
			
			$config=Config::get('ldapPlugin');
			
			$base_dn=explode(".",$schema);

			$options=array(
				"account_suffix"=>	  	'@'.$schema,
		        "base_dn"=>				'DC='.implode(', DC=',$base_dn),
		        "domain_controllers"=>	$host,
		        "ad_username"=>			$username,
		        "ad_password"=>			$password,
		        "real_primarygroup"=>	$config['real_primarygroup'],
		        "use_ssl"=>				$config['use_ssl'],
		        "recursive_groups" => 	$config['recursive_groups']);
			
			//Controller::activeErrorHandler(false,'errorHandler');
			$this->_optionsLdap=$options;
			$this->init();

			if (isset($encoding)) $this->setCharset($encoding);
			if (isset($dateformat)) $this->setDateFormatter($dateformat);
			else $this->setDateFormatter();
			
			$this->TRANSACTION_MODE=false;
		}
	}
	
	///////////// ldap
	
	public function init(){
		
		$options=$this->_optionsLdap;
		
		//you can specifically overide any of the default configuration options setup above
		if (count($options)>0){
			if (array_key_exists("account_suffix",$options)){ $this->_account_suffix=$options["account_suffix"]; }
			if (array_key_exists("base_dn",$options)){ $this->_base_dn=$options["base_dn"]; }
			if (array_key_exists("domain_controllers",$options)){ $this->_domain_controllers=$options["domain_controllers"]; }
			if (array_key_exists("ad_username",$options)){ $this->_ad_username=$options["ad_username"]; }
			if (array_key_exists("ad_password",$options)){ $this->_ad_password=$options["ad_password"]; }
			if (array_key_exists("real_primarygroup",$options)){ $this->_real_primarygroup=$options["real_primarygroup"]; }
			if (array_key_exists("use_ssl",$options)){ $this->_use_ssl=$options["use_ssl"]; }
			if (array_key_exists("recursive_groups",$options)){ $this->_recursive_groups=$options["recursive_groups"]; }
		}

		//connect to the LDAP server as the username/password
		
		if (!is_array($this->_domain_controllers)) 		$dc=$this->_domain_controllers; 
		else if (count($this->_domain_controllers)==1) 	$dc=$this->_domain_controllers[0];
		else $dc=$this->random_controller();
		 		
		if ($this->_use_ssl){
			$this->conn = ldap_connect("ldaps://".$dc);
		} else {
			$this->conn = ldap_connect($dc);
		}
		
		//set some ldap options for talking to AD
		ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);
		
		//bind as a domain admin if they've set it up
		if ($this->_ad_username!=NULL && $this->_ad_password!=NULL){
			try{
				$this->_bind=@ldap_bind($this->conn,$this->_ad_username.$this->_account_suffix,$this->_ad_password);
			}catch(CubeException $e){
				if ($this->_use_ssl){
					//if you have problems troubleshooting, remove the @ character from the ldap_bind command above to get the actual error message

					throw new CubeException ("FATAL: AD bind failed. Either the LDAPS connection failed or the login credentials are incorrect.<br/>".$e->getMessage(),-2);
					//exit();
				} else {
					//echo ($this->_ad_username.",".$this->_ad_password." - FATAL: AD bind failed. Check the login credentials.");
					throw new CubeException ("FATAL: AD bind failed. Check the login credentials.<br/>".$e->getMessage(),-3);
					//exit();
				}
			}
		}
		else throw new CubeException ("FATAL: AD bind failed. Check the configuration file.<br/>".$e->getMessage(),-4);
				
		return (true);
	}

	// default destructor
	public function __destruct(){ //var_dump($this->conn); echo "close!!!";
				if ($this->conn) ldap_close ($this->conn); 
	}

	public function addAttribute($user_dn,$attribute){
  		return  @ldap_mod_add($this->conn,$user_dn,$attribute);
  	}
	
	public function get_cn($gid,$type,$params=null){
		// coping with AD not returning the primary group
		// http://support.microsoft.com/?kbid=321360
		// for some reason it's not possible to search on primarygrouptoken=XXX
		// if someone can show otherwise, I'd like to know about it :)
		// this way is resource intensive and generally a pain in the @#%^

		if ($gid==NULL){ return (false); }
		$r=false;
		
		switch($type){
			case 'group':		$filter="(objectCategory=group)(samaccounttype=". self::ADLDAP_SECURITY_GLOBAL_GROUP .")";break;
			case 'user':		$filter="(objectCategory=person)(objectClass=user)(samaccounttype=". self::ADLDAP_NORMAL_ACCOUNT .")";break;
			case 'computer':	$filter="(objectCategory=computer)";break;
		}
		if ($params!==null){foreach($params as $i=>$j) $filter.='('.$i.'='.$j.')';}
		
		$fields=array("primarygrouptoken","samaccountname","distinguishedname");

		$sr=ldap_search($this->conn,$this->_base_dn,'(&'.$filter.')',$fields);

		$entries = ldap_get_entries($this->conn, $sr);

		for ($i=0; $i<$entries["count"]; $i++){
			if ($entries[$i]["primarygrouptoken"][0]==$gid){
				$r=$entries[$i]["distinguishedname"][0];
				$i=$entries["count"];
			}
		}

		return ($r);
	}

	public function errorInfo($exception){

		$info=new stdClass();
		$info->message=$exception->getMessage();
		$info->code=$exception->getMessage();

		return $info;
	}

	public function getConn(){ return $this->conn;	}
  	public function getBind(){ return $this->_bind;	}
	public function getBaseDn(){ return $this->_base_dn; }
	public function hasRecursiveGroups(){ return $this->_recursive_groups;}
	public function hasRealPrimaryGroup(){ return $this->_real_primarygroup;}
	
	//validate a users login credentials
	public function authenticate($username,$password,$prevent_rebind=false){
		if ($username==NULL || $password==NULL){ return (false); } //prevent null binding
		
		//bind as the user		
		$this->_bind = @ldap_bind($this->conn,$username.$this->_account_suffix,$password);
		if (!$this->_bind){ return (false); }
		
		//once we've checked their details, kick back into admin mode if we have it
		if ($this->_ad_username!=NULL && !$prevent_rebind){
			$this->_bind = @ldap_bind($this->conn,$this->_ad_username.$this->_account_suffix,$this->_ad_password);
			if (!$this->_bind){ echo ("FATAL: AD rebind failed."); exit(); } //this should never happen in theory
		}
		return (true);
	}

	private function encode_password($password){
        $password="\"".$password."\"";
        $encoded="";
        for ($i=0; $i <strlen($password); $i++){ $encoded.="{$password{$i}}\000"; }
        return ($encoded);
   }

	public function change_password($dn,$password,$conn=null,$hasEncrypted=false){
		////////////// EN DESAROLLO //////////////
		
		return false;
	}

/*
	public function change_password($dn,$password,$conn=null,$hasEncrypted=false){

			if (!$this->_bind || $dn==NULL || $password==NULL){ return (false); } //prevent null binding
	      if ($conn===null) $conn=$this->conn;

			$new=array();
			if ($hasEncrypted) $new["userPassword"]= '{md5}' . base64_encode(pack('H*', $password));
			else $new['unicodePwd']=$this->encode_password($password);

			// if (!$this->_use_ssl && !$this->_use_tls)
		   //	throw new adLDAPException('SSL must be configured on your webserver and enabled in the class to set passwords.');
	    	return @ldap_mod_replace($conn,$dn,$new);
   }

	
	private function EncodePwd($pw) {

	  $newpw = '';
	  $pw = "\"" . $pw . "\"";
	  $len = strlen($pw);
	  for ($i = 0; $i < $len; $i++)
			$newpw .= "{$pw{$i}}\000";
	  $newpw = base64_encode($newpw);
	  return $newpw;
	}

	public function change_password($dn,$password){
		
		$newpw64 = EncodePwd($newpw);
		$oldpw64 = EncodePwd($oldpw);

		$ldif=<<<EOF
dn: $userdn
changetype: modify
delete: unicodePwd
unicodePwd:: $oldpw64
-
add: unicodePwd
unicodePwd:: $newpw64
-
EOF;

	  $cmd = sprintf("/usr/bin/ldapmodify -H %s -D '%s' -x -w %s", $adserver, $userdn, $oldpw);

	  if (($fh = popen($cmd, 'w')) === false )
		  die("Open failed: ${php_errormsg}\n");

	  fwrite($fh, "$ldif\n");
	  pclose($fh);
	}
	*/

	/*
	///// user modify directamente con el DN...y  copiar adldap_schema!
	public function user_modify($username,$attributes,$ldap_schema_trad=true){

		if ($username==NULL){ return ("Missing compulsory field [username]"); }
		// $this->_use_ssl --> true, false, null
		if (array_key_exists("password",$attributes) && $this->_use_ssl===false){ echo ("FATAL: SSL must be configured on your webserver and enabled in the class to set passwords."); exit(); }
		//if (array_key_exists("container",$attributes)){
			//if (!is_array($attributes["container"])){ return ("Container attribute must be an array."); }
			//$attributes["container"]=array_reverse($attributes["container"]);
		//}

		//find the dn of the user
		$user=$this->user_info($username,array("cn"));
		if ($user[0]["dn"]==NULL){ return (false); }
		$user_dn=$user[0]["dn"];

		//translate the update to the LDAP schema
		if ($ldap_schema_trad) $mod=$this->adldap_schema($attributes);else $mod=$attributes;
		//echo _r($mod,true);die();
		if (!$mod){ return (false); }
		//do the update
		//echo _r($mod);die();
		$result=ldap_modify($this->conn,$user_dn,$mod);
		if ($result==false){ return (false); }

		return (true);
	}
	*/

	// Return a random controller
	public function random_controller(){
		if ($this->_domain_controllers!==null){
			//select a random domain controller
			mt_srand(doubleval(microtime()) * 100000000); // for older php versions
			return ($this->_domain_controllers[array_rand($this->_domain_controllers)]);
		}
		return null;
	}
	//////////////// driver db

	public function setCharset($cs)
	{
		$this->charset=$cs;
	}
	
	public function getCharset()
	{
		return $this->charset;
	}
	
	static public function upper($text,$column=null){
		//return "upper(".strtoupper($text).") ".(($column==null)?"":$column);
		return strtoupper($text);
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
	
	/*
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
	*/
	public function getTableInfo($table,&$temp_schemas){
	/*	
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
		*/
		$data=array();
		return $data;
	}
	
	
	public function filters($select,$filters=null,$offset=0){
			
		//if (preg_match("/[\s\t\r\n ]where /i",$select)) $where=" and ";else $where=" where ";
		//if (!empty($filters)) $select.=$where.$filters;  //$select.=$where.implode(" and ",$filters);
		return $select;
	}
	
	public function pagination($select,$props=array(),$filters=null)
	{
		/*
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
		*/
		$sql=$select;
		return $sql;
	}

	public function getEntries($sr,$fields){
		$entries = ldap_get_entries($this->conn, $sr);
		$entries2=array();
		for($i=0;$i<$entries['count'];$i++){
			$entries2[$i]=array_fill_keys($fields, null);
			$this->getInfo($entries[$i],$entries2[$i]);
		}
		return $entries2;
	}

	public function getInfo($a,&$resultat,$actualkey=0)
	{
		for($j=0;$j<$a['count'];$j++)
		{
			$key=$a[$j];
			
			if (array_key_exists($key,$a))
			{
				$this->getInfo($a[$key],$resultat,$key);
			}
			else 
			{
				if ($a['count']>1) $resultat[$actualkey][$j]=$a[$j];
				else $resultat[$actualkey]=$a[$j];
				//return 0;
			}
		}
		//if (isset($a['dn'])) $resultat['dn']=$a['dn'];
	}
	static public $cont=0;

	public function query($expr,$limit=null)
	{
		if (!$this->_bind){ return (false); }
		
		$limit=1000;	// maximo de elementos que va a enviar!
		$base=isset($expr['basedn'])?$expr['basedn']:$this->_base_dn;
		
		try{
		$sr=ldap_search($this->conn,$base,$expr['filters'],$expr['fields'],0,$limit);
		return $this->getEntries($sr,$expr['fields']);
		}catch(CubeException $e){
			return array();
		}

		
	}
	
	public function execute($select)
	{
		echo "ldap->execute: $select";
		/*
		$select2=str_replace("\'","''",$select);
		if (!($this->cur = OCIParse($this->conn,$select2)))
		{
			$this->setOcierror($this->cur);	
		}	
		
		OCIExecute($this->cur,$this->TRANSACTION_MODE?OCI_DEFAULT:OCI_COMMIT_ON_SUCCESS);
					 	
		return $this->cur;
		*/
		return true;
	}
	
	public function beginTransaction(){	$this->TRANSACTION_MODE=true;}
	public function commit(){
		
		$this->TRANSACTION_MODE=false;
		//$committed = oci_commit($this->conn);
	}
	public function rollBack() {
		//oci_rollback($this->conn);
	}
		
	public function numrows()
	{
		//return ocirowcount($this->cur);
		return 0;
	}
	
	public function alterSessionForDate($phpFormat)
	{
		$format=$this->transFormatDate($phpFormat);
		//$stmt = $this->execute("ALTER SESSION SET NLS_DATE_FORMAT='{$format}'");  //se llama en DBAdapter!
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
        
	public function nextValueSequence($sequence){ 
		//return "select {$sequence}.nextval {sequence} from dual";
		return '';
	}
	public function lastInsertId(){	return null;} // not supported
	
	/////
	static public function cast($cur,$type,$props=array())
	{
		if ($cur==Query::NULL || $cur==Query::NOTNULL){ 
			if (isset($props['filter_key'])) $val=$cur; 	// no hacemos nada si es un filtro, porque lo haremos más ter	
			else $val="null";
		}else if ($cur===null) $val="null";
		else {
			switch(strtolower($type))
			{
				case 'bin': $val="{$cur}";break; // not tested
				case 'text':
				case 'timestamp':
				case 'varchar': 
						if (isset($props['filter_key']) && isset($props['autolike']) && ($props['autolike']===true || $props['autolike']=='right')) $cur.="*";
						$val="{$cur}";
						break;
				case 'const':
				case 'int':
				case 'integer':
				case 'numeric':  $val="{$cur}";break;
				
				case 'bool':
				case 'boolean':	 
					if ($cur==null || $cur=='0' || $cur==0) $val='0';
					else $val='1';
					break;
				
				case 'date':
						
						if ($cur!=null) 
						{
							//if (!isset($props['format'])) $props['format']=$props['format_db'];
							//echo _r($props);
							// si nos pasan más de un parámetro hay que controlarlo
							if (preg_match("/".Form::FILTER_SEPARATOR_ARRAY."/",$cur)){
								// los separamos por el separador 
								if (isset($props['filter_key'])) {
									$key=$props['filter_key'];
									unset($props['filter_key']);
								}
									
								$current=explode(Form::FILTER_SEPARATOR_ARRAY,$cur); //tengo los valores
								
								$criteria=end($current);
								switch($criteria)
								{
									case Query::CUSTOM:  return stripslashes($current[0]);break;
									/*case Query::BETWEEN: $vals=array_splice($current, 2); 
														 if (count($vals)==2) $props['format']=$vals[0]; //formato
														 break; // tengo el formato (si hay) y el between  
									
									case Query::RANGE:
														$max=count($current)-1;
														$props['range']=array();
														for ($i=1;$i<$max;$i++){
															$props['range']=array_merge($props['range'],array_splice($current, $i, 1));
														}
														$props['type']=strtolower($type);
														array_pop($current);
															break;
									*/
								}
								
								$v2='';
								foreach ($current as $c) {
									$v2.=trim(self::cast($c,$type,$props),"'").Form::FILTER_SEPARATOR_ARRAY;
								}
								$v2.=$criteria; //between (o lo que sea)
								
								if (isset($key)) $val=self::castForFilter($key,$v2,$props);
								
								break;
							}else if (isset($props['filter_key'])){
								$valx=call_user_func_array(array($props['driver'],"transFormatDatePHP"),array($cur,$props));
								//echo "<br/>".var_export($cur,true)."<br/>".var_export($props,true).$valx;
								return $valx; 
								//return "date_format(".$props['filter_key'].",'".call_user_func_array(array($driver,"transFormatDatePHP"),array($props['format']))."')='{$cur}'";
							}
							//echo "dddddd"._r($props);
							
							//si hacemos el cast para coger una pk debemos pasarle el timestamp (en formato de la db) , no el texto con máscara!!
							if (isset($props['pk'])){ 
								if (!is_numeric($cur)) $cur=dbDriver::toTimestamp($cur,$props['format_db']);
								return $cur;
							}
							
							if (!is_numeric($cur)){
								$cur=dbDriver::toTimestamp($cur,$props['format']);
							}	
							
							if ($cur===false) return 'null';
							
							eval("\$time=$cur;"); // hay que hacerlo antes porque $cur es texto y con intval no va bien.
							//$val=date($props['format_db'],$time);
							$val=utf8_encode(strftime($props['format_db'],$time));
							$val="'{$val}'";
							
						}
						else $val='null';
			}
		}
		
		if (isset($props['filter_key'])) {
				
			return self::castForFilter($props['filter_key'],$val,$props);
		}
		//echo "<----------------------------".$cur." ".$type;
		
		return $val;
	}
	
	static public function castForFilter($key,$cur,$parameters=array()){
			
		$autolike=isset($parameters['autolike'])?$parameters['autolike']:false;
		
		if (preg_match("/[%\*]/",$cur)) {
			$method="like";
			$cur=preg_replace("/^['\"]|['\"]$/","",$cur);
			if ($autolike===true || $autolike=='left') $cur="*{$cur}";else $cur="{$cur}";				
		
		}else $method="=";
		
		// si pasamos case sensitive y esta a falso!
		if (!preg_match("/".Form::FILTER_SEPARATOR_ARRAY."/",$cur) &&
			isset($parameters['casesensitive']) && !$parameters['casesensitive']) {  
			//echo "<br/>{$key} : casesentitive!";
			
			$value=call_user_func_array(array($parameters['driver'],'upper'),array($cur));
			$key=call_user_func_array(array($parameters['driver'],'upper'),array($key));
				
			$cur="({$key}=".$value.")".Form::FILTER_SEPARATOR_ARRAY.Query::CUSTOM;		
		}
		
		//echo "<br/>".$key.",".$method.",".$cur;
		//call_user_func_array(array($parameters['driver'],'transFormatDatePHP')
		//echo _r($parameters);
		
		if ($cur=="null" || $cur==Query::NULL) $valor="{$key} is null";
		else if ($cur==Query::NOTNULL) 	$valor="{$key} is not null";
		else if (is_bool($cur)) 	$valor="{$key} {$method}".($cur?'1':'0');
		else{
			
			// si encuentra un separador de array es que los datos son "multiple"
			if (preg_match("/".Form::FILTER_SEPARATOR_ARRAY."/",$cur)){
				
				// los separamos por el separador (trim -> nos pasan el dato entre ' ')
				$current=explode(Form::FILTER_SEPARATOR_ARRAY,trim($cur,"'"));
				//echo _r($current);
				/// si el ultimo parametro es un criteria..
				// const CUSTOM='##CUSTOM##'; const BETWEEN='##BETWEEN##'; const IN='##IN##';
				$operator=end($current);
				$operator=preg_replace("/[%]$/","",$operator); // si es autolike te añade un %, y se lo quito aquí.
				$current[0]=preg_replace("/^[%]/","",$current[0]);
				
				switch($operator){
					case Query::IN:
						array_pop($current); //extraemos el último elemento que es Query::IN
						$valor='(|';
						foreach($current as $val) $valor.="({$key}={$val})";
						$valor.=')';
						
						break;
					case Query::NOTEQUAL:
						$valor="!({$key}=".stripslashes($current[0]).")";
						break;
					case Query::CUSTOM:
						$valor=stripslashes($current[0]);
						break; 
					/*case Query::BETWEEN:
						$valor="{$key} between '{$current[0]}' and '{$current[1]}'";
						break;
					case Query::RANGE:
						$rango=$parameters['range'];
						$parameters['filter_key']=$key;
						//echo _r($parameters);
						$parameters['format']=$parameters['format_db'];
						unset($parameters['range']);
						$valorx=array();
						foreach($rango as $i=>$range){
							if (isset($parameters['type'])){
								$parameters['range']=$range; 
								$valorx[]=self::cast($current[$i],$parameters['type'],$parameters);
							}else
							$valorx[]="{$key} {$range} '{$current[$i]}'";
						}
						$valor=implode(" and ",$valorx);
						
						break;
					*/
				}
			}else {
				$valor="({$key}={$cur})";
			}
		}
		
		return $valor;
	}
	
	
}
?>