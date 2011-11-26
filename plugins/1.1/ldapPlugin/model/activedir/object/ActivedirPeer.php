<?php
class ActivedirPeer extends CubePeer{
	
	protected $FIELDS=array("samaccountname","description");
	const BASE_DN="DC=parcsanitari,DC=local";
	protected $base;
	protected $ldap;

	public function __construct(){
		
		$this->setBase();
		parent::__construct();
	}
	
	public function configure()
	{
	
	}
	
	public function setBase($basedn=null){
		$this->ldap=$this->getDatabase();
		
		if ($basedn===null) $this->base=self::BASE_DN;
		else{
			$ous=explode("/",$basedn);
			$ous=array_reverse($ous);
			$this->base="OU=".implode(",OU=",$ous).",".self::BASE_DN;
		}
		
	}
	
	public function getBase(){
		if ($this->base===null) return '';
		return $this->base;
	}
	
        public function doCount($select=null)
	{
            return 0;
	}
        
	public function getAccountControlVars(){
		$a=LdapAD::$ACCOUNT_CONTROL;
		$t=array();
		foreach($a as $i=>$cur){
			if ($cur!=null) $t[]=array('value'=>pow(2,$i),'text'=>$cur);
		}
		return $t;
	}
	
	public function bindQueryFilters($fields,$filters){
		
		$a=array('fields'=>$fields,'filters'=>$filters,'basedn'=>$this->base);
		return $a;
	}
	
	public function doSelect($select,$hidrate=true){
		if (!is_array($select['fields']))  $select['fields']=$this->FIELDS;
                return $this->executeSelect($select,$hidrate);
	}

	public function retrieveByPk(){
		
		$columns=$this->getColumns();
		$fields=array_keys($columns);
		$php=$this->getPHPNames();
		
		$pks=$this->extractPK($columns); // extraigo PK
		$table=$this->getTable();
		
		if (count($pks)==func_num_args()) // el numero de argumentos es igual a las PK de la tabla
		{
			$args = func_get_args();
			$criteria=array();
			foreach($pks as $i=>$pk){$criteria[$php[$pk]]=array('value'=>$args[$i]);}

            $objects=$this->retrieveByColumns($criteria,true,null);
			if (!empty($objects)) return $objects[0];
			else $message="Not select an element from {$table}:  ";
			
		}else $message="Number of PK for {$table} is incorrect:  ";
		
		//Log::_add(__METHOD__,"Number of PK for {$table} is incorrect:  ".implode(",",$pks),"model",__CLASS__,Log::ERROR);
		Controller::triggerHook('debug','model',array(
								'message' =>$message.implode(",",$pks),
								'type'=>'model',
								'error'=>Log::ERROR,
								'class'=>__CLASS__,
								'method'=>__METHOD__));
		
		return null;
	}

	public function doSelectAll($hidrate=true,$fields=null,$order=null)
	{
		return $this->retrieveByColumns(array(),$hidrate,$fields,$order);
	}

	public function retrieveByColumns($info=array(),$hidrate=true,$fields=null,$order=null){
		
		$class=preg_replace('/Peer$/','',get_class($this));
		$obj=new $class();
		
		foreach($info as $key=>$props){
			if (isset($props['value'])) {
				$obj->{$key}=$props['value'];
			}
		}
		
		//echo _r($obj);
		if ($fields===null) $fields=$this->FIELDS;
		else $fields=array_keys(array_intersect($this->getPHPNames(),$fields));
		
		$query=$this->bindQueryFilters($fields,$obj->getObjectFilter());

		$data=$this->doSelect($query,$hidrate);
		
		if (is_array($order)){
			$str='';
			foreach($order as $k=>$sort){$str.="'{$k}', SORT_{$sort}";}
			eval("\$data=Util::array_csort (\$data,{$str});");
		}
		
		//if ($data) {$data[0]->clearModifiedAttributes();return $data[0];}
		//return null;
		return $data;
	}

	/////
	
	static public $ACCOUNT_CONTROL=array('SCRIPT','ACCOUNTDISABLE',null,'HOMEDIR_REQUIRED','LOCKOUT',
		'PASSWD_NOTREQD',null,'ENCRYPTED_TEXT_PWD_ALLOWED','TEMP_DUPLICATE_ACCOUNT',
		'NORMAL_ACCOUNT',null,'INTERDOMAIN_TRUST_ACCOUNT','WORKSTATION_TRUST_ACCOUNT','SERVER_TRUST_ACCOUNT',
		null,null,'DONT_EXPIRE_PASSWORD','MNS_LOGON_ACCOUNT','SMARTCARD_REQUIRED','TRUSTED_FOR_DELEGATION',
		'NOT_DELEGATED','USE_DES_KEY_ONLY','DONT_REQ_PREAUTH','PASSWORD_EXPIRED','TRUSTED_TO_AUTH_FOR_DELEGATION');
	
	static public function accountControl($options){
        $val=0;

        if (is_array($options)){
           
        	foreach($options as $option){
        		$exp=array_search($option,self::$ACCOUNT_CONTROL);
        		if ($exp!==false) $val+=pow(2,$exp);
        	}
        }
        return ($val);
    }
    
    static public function getAccountControl($value){
    	
    	$str=strrev(decbin($value));
    	$res=array();
    	for($i=0;$i<strlen($str);$i++){
    		if ($str[$i]==1) $res[]=self::$ACCOUNT_CONTROL[$i*intval($str[$i])];
    	}
    	return $res;
    }

	 static public function toDate($date,$format=null){
		 // $date está en formato AD (20100224135139.0Z)
		 $formato=array(	'%Y'=>substr($date,0,4),
								'%m'=>$month=substr($date,4,2),
								'%d'=>substr($date,6,2),
								'%H'=>substr($date,8,2),
								'%M'=>substr($date,10,2),
								'%S'=>substr($date,12,2)
			 );
		 if ($format!==null) eval ('$res="'.preg_replace("/(%\w)/","{\$formato['$1']}",$format).'";');
		 else $res=mktime($formato['%H'],$formato['%M'],$formato['%S'],$formato['%m'],$formato['%d'],$formato['%Y']);
		 
		 return $res;
	 }
}
?>