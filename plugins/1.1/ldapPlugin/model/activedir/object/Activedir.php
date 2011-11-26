<?php
abstract class Activedir extends Cube{

	

	public function logicDelete(){	
		//return true;	// not execute save method (ex. condition credentials) 
	}

	public function logicInsert(){}
			
	public function __construct(){
		$this->setLogic(false);	// delete are logic
	}
	
	abstract public function adModify($pks,$modifiedFields,$forceInsertUpdate=null);
	
	public function save(&$error=false,$forceInsertUpdate=null,$l=null,$attribs=null,$refClass=null){
		
		$class=get_class($this);
        $modelclass=$class."Peer";
        
        $pks=$this->getPKs();
        //echo _r($pks);die();
        $fieldsnotallow=array_merge(array_keys($pks),
        							array('cn','objectclass','objectcategory','samaccounttype'));
        
		$phpnames=array_flip($this->getModel()->getPHPNames());
		$attributes=$this->getArray();

		$mod=array();
		foreach ($attributes as $k=>$item) {
			if (!in_array($phpnames[$k],$fieldsnotallow)) $mod[$phpnames[$k]]=$item;
		}
			
		try{
			//echo $select;
			$noerror=$this->adModify($pks,$mod,$forceInsertUpdate);
					
			if (!$noerror)
				throw new LDAPException(LDAPException::LDAPEXCEPTION_GENERIC_TEXT, LDAPException::LDAPEXCEPTION_GENERIC);
			
			//Log::_add(__METHOD__,utf8_encode($select),"model",$modelclass,($noerror)?(Log::SUCCESS):(Log::ERROR));
			Controller::triggerHook('debug','model',array(
						'message' =>var_export($mod,true),
						'type'=>'model',
						'error'=>($noerror)?(Log::SUCCESS):(Log::ERROR),
						'class'=>$modelclass,
						'method'=>__METHOD__));
				
				/////// trigger para la accion UPDATE 
				Controller::triggerHook('log','update',array(
						'message' =>utf8_encode(var_export($mod,true)),
						'type'=>'update',
						'error'=>($noerror)?(Log::SUCCESS):(Log::ERROR),
						'class'=>$modelclass,
						'method'=>__METHOD__));
		}catch(Exception $e){
			
			switch($e->getCode())
			{
				case 5000: 	$msg=Viewer::_echo("model:error:noupdate");break;
				default: 	$msg=$e->getMessage();break; 
			}
			//Log::_add(__METHOD__,utf8_encode($select)."<br/><b>".$msg."</b>","model",$modelclass,Log::ERROR);
			Controller::triggerHook('debug','model',array(
						'message' =>utf8_encode(var_export($mod,true))."<br/><b>".$msg."</b>",
						'type'=>'model',
						'error'=>Log::ERROR,
						'class'=>$modelclass,
						'method'=>__METHOD__));
			$error=$msg;
		}
		return $this;
	}
	
	public function getObjectFilter($attribs=null,$l=null){
        
		$vars=get_object_vars($this);  
       
        $class=get_class($this);
        $modelclass=$class."Peer";
       
        if ($l===null) $l=new $modelclass();
        $table=$l->getTable();
        $db=$l->getDatabase();
       
        $trad=$l->getPHPNames();
        $columns=array_flip($trad);
        $cols=$l->getColumns();
        $pks=$l->extractPK($cols); // extraigo PK
        $cols2=array();

		  $attr=$this->getModifiedAttributes();
		  
        $values=array();
               
        foreach($attr as $k=>$v)
        {
            if (isset($columns[$k])) $key=$columns[$k];else $key=$k;
           
            $column=$l->getColumn($key);
            $val=$this->{$k};
           
            // echo "<br/>{$k}: {$v} - {$key} - ".$val._r($attribs->searchParameters(strtolower($class).".".$key));
            if (!isset($column['params'])) $params=array();else $params=$column['params'];   
            
            if ($val!=""){
            	$params['filter_key']=$key;
            	
            	// NOTA: Ojo, hemos cambiando $key (nom columna generador) por $k (phpname)
            	//$parameters=$attribs->searchParameters(strtolower($class).".".$key);
            	if (!empty($attribs))
            		$parameters=$attribs->searchParameters(strtolower($class).".".$k);
            	
            	//echo strtolower($class).".".$key.","._r($column,true)." "._r($params,true)._r($parameters,true);
            	if (!empty($parameters)) $params=array_merge($params,$parameters);	
				
            	if (!isset($column['type'])) $text=$key." ".Cube::castByValue($val);
            	else {$text=$this->castByColumnType($val,$column['type'],$params);}
           		
            	if ($v!=null) $values[]=$text;
            }
        }
		//echo _r($values);die();               
        return "(&".implode("",$values).")";
    }
}
?>