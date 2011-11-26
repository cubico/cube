<?php
/// core - model
class Query
{
    const NOTNULL='##NOTNULL##';
    const NULL='##NULL##';
    const NOTEQUAL='##NOTEQUAL##';
    const CUSTOM='##CUSTOM##';
    const BETWEEN='##BETWEEN##';
    const IN='##IN##';
    const RANGE='##RANGE##';
    
    public function __toString()
    {
        return "<pre>".print_r($this,true)."</pre>";
    }
	
	static public function bind($q,$params)
	{
		foreach($params as $k=>$value)
		{
			if (is_array($value)) //valor complejo (con parametros)
			{
				if (isset($value['data'])) {$type='collectionDataForBind'; $v=$value['data'];}
				else 
				{
					$v=$value;
					if (!isset($value['type'])) $type='collectionDataForBind';else $type=$value['type'];
				}
				if (!isset($value['separator'])) $separator=', '; else $separator=$value['separator'];
				if (!isset($value['format'])) $format='%Y-%m-%d %H:%M:%S'; else $format=$value['format'];
												
				if ($type=='collectionDataForBind')	{$v=implode($separator,$v);} 
				else if (isset($value['value'])) $v=$value['value'];
				else $v=null;
				
			}
			else $v=$value;
			
			//echo _r($v,true);die();
			
			if (is_bool($v)) $v=($v)?"1":"0";
			else if (is_string($v)) 
			{
				if (isset($type) &&  $type!='const') $v="'{$v}'";	
			}	
			else if (is_numeric($v))
			{
				
				//if (isset($type) && $type=="date") $v="'".Date($format,$v)."'";
				if (isset($type) && $type=="date") $v="'".utf8_encode(strftime($format,$v))."'";
				
			}
			
			
			$q=preg_replace("/{".$k."}/","{$v}",$q);
			
		}
		
		
		if (self::isValid($q)) return $q;
		return null;
	}   
	
	static private function isValid($q)
	{
		preg_match_all("/{[^}]+}/",$q,$reg,PREG_PATTERN_ORDER);
		
		if (!isset($reg[0])) {
			//Log::_add(__METHOD__,"Query needs the following fields:  ".implode(",",$reg[0]),"model",__CLASS__,Log::ERROR);
			Controller::triggerHook('debug','model',array(
							'message' =>"Query needs the following fields:  ".implode(",",$reg[0]),
							'type'=>'model',
							'error'=>Log::ERROR,
							'class'=>__CLASS__,
							'method'=>__METHOD__));
			return false;			
		}
		return true;
	}
	
}

class Cube
{
    private $data = array();
    	
    public function __set($name, $value) {
        //echo "<br/>Setting '$name' to '$value'\n";
        $this->data[$name] = $value;
        $this->modif[$name]=1;
    }

    public function __get($name) {
        //echo "<br/>Getting '$name'\n";
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }
    
    public function __isset($name) {
        if (array_key_exists($name, $this->data)) {
            return isset($this->data[$name]);
        }
        return null;
    }
   	
    private $embedColumns=null;
	
    public function getEmbedColumns()
	{
		return $this->embedColumns;
	}
	
	public function setEmbedColumns($columns)
	{
		$this->embedColumns=$columns;
	}
	
	public function getArray()
	{
		return $this->data;
	}
   
    public function __construct()    { }
   
    public function __toString()
    {
        return sprintf("Object %s: *** attributes: %s",__CLASS__,var_export($this->data,true));
    }
   
    private $modif;
	
	protected function getModifiedAttributes()
	{
		return $this->modif;
	}
	
	public function clearModifiedAttributes()
	{
		if (isset($this->modif))
			foreach($this->modif as $k=>$cur){$this->modif[$k]=0;}
	}
	
	static public function castByValue($cur)
	{
		if ($cur===null) $valor="null";
		else if (is_bool($cur)) $valor=$cur?'1':'0';
		else if (is_string($cur)) $valor="'{$cur}'";
		else //if (is_numeric($cur)) 
			$valor="{$cur}";
		
		return $valor;
	}
	
	public function castByColumnType($cur,$type='varchar',$props=array()){
		$modelclass=get_class($this)."Peer";
		$l=new $modelclass();
		
		return $l->castByColumnType($cur,$type,$props);
	}
	
	
	
	public function getModel()
	{
		$modelclass=get_class($this)."Peer";
		$model=new $modelclass();
		return $model;
	}
	
	public function getPKs($phpname=false){
		//$modelclass=get_class($this)."Peer";
		//$model=new $modelclass();
		
		$model=$this->getModel();
		$table=$model->getTable();
		$cols=$model->getColumns();
		$pks=$model->extractPK($cols); // extraigo PK
		
		$return=array();
		
		foreach($pks as $k=>$cur) 
		{
			$column=$model->getColumn($cur);	// cojo la informacion de la columna de cada pk
			$key=$column['phpname'];		// extraigo el phpname
			$val=$this->{$key};				// y el valor
			
			$params=array('pk'=>true);
			if (isset($column['params'])) $params=array_merge($params,$column['params']);
			
			//echo "ccccc"._r($val);
			// si la columna no tiene la informacio de cast, le hacemos uno por valor
			if (!isset($column['type']))$val=Cube::castByValue($val);
			else $val=$this->castByColumnType($val,$column['type'],$params); //sino, por las propiedades de la columna de base de datos 
			
										
			// lo añadimos al where
			if ($phpname) $return[$key]=trim($val,"'");
			else $return[$cur]=trim($val,"'");
			//$return[$cur]=$val;
		}
		
		
		return $return;
	}
	
	
	private function isNew(&$pks=array(),&$where=array(),&$model=null)
	{
		if ($model===null) {
			
			$modelclass=get_class($this)."Peer";
			$model=new $modelclass();
		}	
	
		$existe=true;
		$table=$model->getTable();
		$cols=$model->getColumns();
		$pks=$model->extractPK($cols); // extraigo PK
		
		foreach($pks as $k=>$cur) 
		{
			$column=$model->getColumn($cur);	// cojo la informacion de la columna de cada pk
			$key=$column['phpname'];		// extraigo el phpname
			$val=$this->{$key};				// y el valor
			
			//echo $key.": "._r($val,true);
			//$existe&=($val!=null);			// si alguna de las pk es nula
			if ($val===null) {$existe=false;break;}
			
			// si la columna no tiene la informacio de cast, le hacemos uno por valor
			if (!isset($cols[$cur]['params'])) $params=null;else $params=$cols[$cur]['params'];
			
			if (!isset($column['type']))$val=Cube::castByValue($val);
			else 
			{
				//echo _r($cols[$cur]);
				$val=$this->castByColumnType($val,$column['type'],$params); //sino, por las propiedades de la columna de base de datos	
			} 
						
			// lo a�adimos al where
			if ($val=='null') $where[]=$cur." is ".$val;
			else $where[]=$cur."=".$val;
		}
		
		return !$existe;
	}
	
	public function objectExists($model=null,&$pks=array(),&$where=array())
	{
		$existe=!$this->isNew($pks,$where,$model);
		
		if ($existe) //tiene todos los PK con valor
		{
			/// comprobamos si realmente existe en la bd
			
			/// mirar si existe en la base de datos
			$select='select '.implode(", ",$pks).' from '.$model->getTable().' where '.implode(" and ",$where);
			$data=$model->doSelect($select);
			if (count($data)==0) $existe=false;
		}
		
		return $existe;
	}
	
	public function logicDelete(){}
	
	public function logicInsert(){}
	
	public function logicFilter(){}
	
	protected $logic;
	public function setLogic($bool){
		$this->logic=$bool;
	}
	
	public function getLogic(){
		return $this->logic;
	}
	
	public function delete(&$error=false)
	{	
		$vars=get_object_vars($this);   
        $modelclass=get_class($this)."Peer";
		
		$l=new $modelclass();
		$table=$l->getTable();
		$pks=array();						
		$where=array();
		
		/// miramos si tengo info de todas las PK
		$existe=$this->objectExists($l,$pks,$where); 
		if ($this->getLogic()){					
			$error='';
			if (!$this->logicDelete()){
				$this->setLogic(false);
				$this->save($error);
				$this->setLogic(true);
				if ($error=='') return true;
			}
			return false;
		}
		else	
			$select="delete from ".$table." where ".implode(" and ",$where);
		
		try{
			if ($existe) $noerror=$l->getDatabase()->execute($select);
			else throw new dbException('',4998);
			
			if ($l->getDatabase()->numrows()==0) throw new dbException('',4999);
			
			/////// trigger para la accion DELETE
				Controller::triggerHook('log','delete',array(
							'message' =>utf8_encode($select),
							'type'=>'delete',
							'error'=>($noerror)?(Log::SUCCESS):(Log::ERROR),
							'class'=>$modelclass,
							'method'=>__METHOD__));
			
		}catch(Exception $e){
			
			switch($e->getCode())
			{
				case 4998: 	$msg=Viewer::_echo("model:error:nopks");break;
				case 4999: 	$msg=Viewer::_echo("model:error:nodelete");break;
				default: 	$msg=$e->getMessage();break; 
			}
			//Log::_add(__METHOD__,$select."<br/><b>".$msg."</b>","model",$modelclass,Log::ERROR);
			Controller::triggerHook('debug','model',array(
							'message' =>$select."<br/><b>".$msg."</b>",
							'type'=>'model',
							'error'=>Log::ERROR,
							'class'=>$modelclass,
							'method'=>__METHOD__));
			$error=$msg;
			return false;
		}
			
		//Log::_add(__METHOD__,$select,"model",$modelclass,($noerror)?(Log::SUCCESS):(Log::ERROR));
		Controller::triggerHook('debug','model',array(
							'message' =>$select,
							'type'=>'model',
							'error'=>$noerror?(Log::SUCCESS):(Log::ERROR),
							'class'=>$modelclass,
							'method'=>__METHOD__));
		return true;
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
        
		if ($this->getLogic()){
			$tmp=new $class();
			$tmp->logicFilter();
		}
						   
        $values=array();
        $pk=array();
        $c=new $class;
        
        foreach($attr as $k=>$v)
        {
            if (isset($columns[$k])) $key=$columns[$k];else $key=$k;
           
            $column=$l->getColumn($key);
            $val=$this->{$k};
            if (isset($tmp)) $vallogic=$tmp->{$k};
			
            //echo "<br/>{$k}: {$v} - {$key} - ".$val._r($attribs->searchParameters(strtolower($class).".".$key));
            if (!isset($column['params'])) $params=array();else $params=$column['params'];   
            
            if ($column['type']=='date') {
            		
            	// buscamos el formato en el generador, para no cogerlo del schema
                // soluciona BUG de lectura en schema. No funciona si hay más de un campo con el mismo
                // campo de referencia (p.ej DATA -> DATA1 ó DATA2)
            	
                if (!empty($attribs)) $ret=$attribs->search(strtolower($class).".".$key,false);
            	
                if (isset($ret['format'])) $params['format']=$ret['format'];
                //if (isset($ret['range'])) $params['range']=$ret['range'];
                
                /// tururú... :)
                if (isset($column['column'])) $key=$column['column'];
            }
            
            if ($val!="" || (isset($vallogic) && $vallogic!="")){
            	
				// OJO: prevalece el dato pasado como filtro, y si este está vacio, entonces se coge el valor de logicFilter
				if ($val=="") $val=$vallogic; // si ha entrado es porque vallogic tiene un dato
				
            	$params['filter_key']=$key;
            	
            	// NOTA: Ojo, hemos cambiando $key (nom columna generador) por $k (phpname)
            	//$parameters=$attribs->searchParameters(strtolower($class).".".$key);
            	$parameters=$attribs->searchParameters(strtolower($class).".".$k);
            	
            	//echo strtolower($class).".".$key.","._r($column,true)." "._r($params,true)._r($parameters,true);
            	if (!empty($parameters)) $params=array_merge($params,$parameters);	

            	
				if (!isset($column['type'])) $text=$key." ".Cube::castByValue($val);
            	else {$text=$this->castByColumnType($val,$column['type'],$params);}
           		
				//$text=$key." ".$this->castForFilter($valor);
            	//$c->{$k}=$valor;        // para devolver como objeto actualizado
            	if ($v!=null) $values[]=$text;
            }
        }
		//echo _r($values);//die();               
        return implode(" and ",$values);
    }
	
	private $NUMERIC_TYPES=array('numeric','number','int','integer');
	
    public function save(&$error=false,$forceInsertUpdate=null,$l=null,$attribs=null,$refClass=null)
    {
        $logic=$this->getLogic();
        if ($logic) $this->logicInsert();	// comprobaremos si existe más abajo.
                
       	$vars=get_object_vars($this);   
        
       	
       	
    	$class=get_class($this);
        $modelclass=$class."Peer";
		
		if ($l===null) $l=new $modelclass();
		
		$table=$l->getTable();
		$db=$l->getDatabase();
		
		$pks=array();
		$trad=$l->getPHPNames();
		$columns=array_flip($trad);
		$cols=$l->getColumns();
		$pks=$l->extractPK($cols); // extraigo PK
		$cols2=array();
		
		
		// modifica objeto existente para "clonar" baja logica
		//echo _r($this,true);die();
		
		//if (!$logic)	// tiene que estar comentado porque en edit pasabamos existe=false y hacia insert!! 
		{ 
			if ($forceInsertUpdate===true) $existe=false;      // forzamos insert (true)
			else if ($forceInsertUpdate===false) $existe=true; // forzamos update (false)
			else $existe=$this->objectExists($l,$pks); 			/// miramos si tengo info de todas las PK
		}
    	//if ($refClass!='Sol_d_activitat') {echo _r($l)._r($logic,true);die();}
		//$pknew='';
		
		if ($existe) // en este punto podemos afirmar que EXISTE el objeto en base de datos
		{
			$attr=$this->getModifiedAttributes();
			
			$select="update ".$table." set ";
			$values=array();
			$values2=array();
			$pk=array();
			$c=new $class;
			
			foreach($attr as $k=>$v)
			{
				if (isset($columns[$k])) $key=$columns[$k];else $key=$k;
				
				$column=$l->getColumn($key);
				$val=$this->{$k};
				
				if (!isset($column['params'])) $params=null;else $params=$column['params'];	
				
				
				if ($column['type']=='date') {
					//echo _r($params);die();
					// buscamos el formato en el generador, para no cogerlo del schema
					// soluciona BUG de lectura en schema.
					if (!empty($attribs)) $ret=$attribs->search(strtolower($refClass).".".$key,false);
					if (isset($ret['format'])) $params['format']=$ret['format'];
					//echo _r($params);
				}
				
				$valor2=$val;
				if (!isset($column['type'])) $valor=Cube::castByValue($val);
				else $valor=$this->castByColumnType($val,$column['type'],$params); 
				
				///////// los numerico nulos provocan bug, no se deben añadir a la select!
				//echo $key." "._r($val,true)." -->"._r($valor,true);
				if ($valor==null && in_array($column['type'],array('numeric','number','int','integer'))) {
					$nulo=true;
				
				}
				else $nulo=false;
				
				/// si se envia un Query::NULL el campo debe ser null
				if ($valor==Query::NULL) $valor="null";
				
				$cols[]=$key;
				//$values[]=$valor;	// pudiera ser copy&paste de insert que provocaba un bug
				$c->{$k}=$valor2; // para devolver como objeto actualizado (sin casting)
					
				$text=$key."=".$valor;
				
				
				if (!in_array($key,$pks)){
					if ($v==1 && !$nulo) $values[]=$text; // solo si ha sido modificada.
				}else{
					$pk[]=$text;
					//$pknew.="/".str_replace("'","",$valor);
				}
				
				
			}
			
			//echo _r($pk);
			//echo _r($values);			
			$select.=implode(", ",$values)." where ".implode(" and ",$pk);
			//if ($refClass!='Sol_d_activitat') {echo $select;die();}
			//Log::_add(__METHOD__,$select,"model","DEBUG",1);
			//die();
			//echo $select;die();
			
			try{
				$noerror=$db->execute($select);
				
				if ($db->numrows()==0) // o no ha habido cambios, o no existe el elemento
				{
					// si no existe el elemento, error
					// sino, es que no ha habido cambios, no es un error (bug Mysql OK)
					
					if (!$l->getDataBase()->FORCE_UPDATE_NUM_ROWS_AFFECTED) 
						throw new dbException("Update inexistent element",5000);
					else if (!$this->objectExists($l,$pks))
						throw new dbException("Update inexistent element",5001);
					
				}
				else{
					//if (preg_match("/utf[-]{0,1}8/i",$l->getDataBase()->getCharset())) 
					
					//Log::_add(__METHOD__,utf8_encode($select),"model",$modelclass,($noerror)?(Log::SUCCESS):(Log::ERROR));
					Controller::triggerHook('debug','model',array(
							'message' =>utf8_encode($select),
							'type'=>'model',
							'error'=>($noerror)?(Log::SUCCESS):(Log::ERROR),
							'class'=>$modelclass,
							'method'=>__METHOD__));
					
					/////// trigger para la accion UPDATE 
					Controller::triggerHook('log','update',array(
							'message' =>utf8_encode($select),
							'type'=>'update',
							'error'=>($noerror)?(Log::SUCCESS):(Log::ERROR),
							'class'=>$modelclass,
							'method'=>__METHOD__));
				}
					
			}catch(Exception $e){
				
				switch($e->getCode())
				{
					case 5000:		$msg=Viewer::_echo("model:error:noexists");break;
					case 5001:		$msg=Viewer::_echo("model:error:noupdate");break;
					default:		$infoerror=$db->errorInfo($e);
									$msg=sprintf(Viewer::_echo($infoerror->message),$infoerror->code);break;
				}
				//Log::_add(__METHOD__,utf8_encode($select)."<br/><b>".$msg."</b>","model",$modelclass,Log::ERROR);
				Controller::triggerHook('debug','model',array(
							'message' =>utf8_encode($select)."<br/><b>".$msg."</b>",
							'type'=>'model',
							'error'=>Log::ERROR,
							'class'=>$modelclass,
							'method'=>__METHOD__));
				$error=$msg;
			}
		}
		else
		{
			$c=new $class();
			//echo _r($vars['data']);die();
			//insert en objetos 
			$cols=array();
			$values=array();
			$values2=array();
			$autonumeric=null;
			foreach($vars['data'] as $k=>$cur)
			{
					//echo "<br/>".$k." : ".$columns[$k];
					if (isset($columns[$k])) 
					{
						$key=$columns[$k];
					
						//echo $k."---".$key."<br/>";
						
						$column=$l->getColumn($key);
						//$column=$l->getColumn('EDICIO');
						
						$autosequence=(isset($column['autonumeric']) && $column['autonumeric']);
						//echo "---------------------"._r($column);
						if (isset($column['sequence']) || $autosequence)
						{
							if (isset($column['sequence'])){
								$query=$l->getQuery($column['sequence'],true);
								
								if ($query===null) 
								{
									//$cols[]=$key;$values[]=$column['sequence'].".nextval";}
									$select=$db->nextValueSequence($column['sequence']); 
								}				
								else 
								{
									$select=$query; //$l->getQuery($column['sequence']);
									
								}
								
								//$params=array("id_curs"=>82);
								$select=Query::bind($select,$vars['data']);
								//echo $select;die();
								
								if (!preg_match('/{sequence}/',$select)) throw new cubeException("Tag {sequence} not found",0);
								$select=preg_replace('/{sequence}/',strtoupper($column['sequence']),$select);
								$data=$l->doSelect($select,false);
								
								$vv=$data[0][strtoupper($column['sequence'])];
								
								if ($autosequence){
									if (!isset($column['params'])) $params=null;else $params=$column['params'];	
									if (!isset($column['type'])) $valor=Cube::castByValue($vv);
									else $valor=$this->castByColumnType($vv,$column['type'],$params);
								}else{
									$valor=$data[0][strtoupper($column['sequence'])];
								}
								$values2[]=$vv;
								$values[]=$valor;
								$cols[]=$key;
								
							}else{
								$autonumeric[]=$key;		// nos guardamos el id autonumerico!
							}
							
						}
						else if (!isset($column['sequence']) && (!isset($column['autonumeric']) || $column['autonumeric']!==true))
						{
							
							if (!isset($column['params'])) $params=null;else $params=$column['params'];	
					
							if ($column['type']=='date') {
								//echo _r($params,true);
								// buscamos el formato en el generador, para no cogerlo del schema
								// soluciona BUG de lectura en schema.
								
								if (!empty($attribs)) $ret=$attribs->search(strtolower($refClass).".".$key,false);
								
								// si tenemos definido el formato en el schema, lo cogemos, si no el de la bd
								//if (isset($column['params']['format'])) $params['format']=$column['params']['format'];
								//else $params['format']=$db->getDateFormatter();
								if (isset($ret['format'])) $params['format']=$ret['format'];
								
								//echo _r($column)._r($params);
								//echo strtolower($refClass).".".$key." --> 2 "._r($column)."3 "._r($attribs);
								//echo $k.":".$cur.",";
								//echo _r($params,true);
								//die();
							}	
							//echo _r($params,true);
							
							$valor2=$cur;
							if (!isset($column['type'])) $valor=Cube::castByValue($cur);
							else $valor=$this->castByColumnType($cur,$column['type'],$params);
							
							//echo "<br/>$key: $valor -->".$column['type']." ";die();
							//var_export(empty($valor),true)." ".var_export($valor==null,true).var_export($valor===null,true);
														
							/// si el valor no está vacio ó no es numerico ó es 0
							if (!empty($valor) || ($valor=='0' && $column['type']=='numeric') || !in_array($column['type'],$this->NUMERIC_TYPES))
							{
								$cols[]=$key;
								$values[]=$valor;
								$values2[]=$valor2;
							} 
							
						}
			}
		
		}
				//echo _r(array_combine($cols,$values));	
				//$pknew='';foreach($pks as $i=>$col){$pknew.="/".str_replace("'","",$values[$i]);}
				
				$select='insert into '.$table.'('.implode(", ",$cols).') values ('.implode(", ",$values).')';
				//echo $select;die();
				
				$trad=$l->getPHPNames();
				foreach($cols as $i=>$key) 
				{
					$c->{$trad[$key]}=$values2[$i];
				}
				//echo $select._r($c);die();
			try{
				
				//echo $select;die();
				$noerror=$db->execute($select);
				
				if (!empty($autonumeric)) {
					foreach($autonumeric as $key) $c->{$trad[$key]}=$db->lastInsertId();
				}
				
				if ($db->numrows()==0) throw new dbException($noerror,2);
				//Log::_add(__METHOD__,$select,"model",$modelclass,($noerror)?(Log::SUCCESS):(Log::ERROR));
				Controller::triggerHook('debug','model',array(
							'message' =>$select,
							'type'=>'model',
							'error'=>($noerror)?(Log::SUCCESS):(Log::ERROR),
							'class'=>$modelclass,
							'method'=>__METHOD__));
				
				/////// trigger para la accion INSERT 
				Controller::triggerHook('log','insert',array(
							'message' =>utf8_encode($select),
							'type'=>'insert',
							'error'=>($noerror)?(Log::SUCCESS):(Log::ERROR),
							'class'=>$modelclass,
							'method'=>__METHOD__));
			
			}catch(Exception $e){
				$infoerror=$db->errorInfo($e);
				$error=sprintf(Viewer::_echo($infoerror->message),$infoerror->code);
				//Log::_add(__METHOD__,$select."<br/>".$error,"model",$modelclass,Log::ERROR);
				Controller::triggerHook('debug','model',array(
							'message' =>$select."<br/>".$error,
							'type'=>'model',
							'error'=>Log::ERROR,
							'class'=>$modelclass,
							'method'=>__METHOD__));
			}
		}
		//$this->clearModifiedAttributes();
		//Log::_add(__METHOD__,utf8_encode(var_export($vars['data'],true)),"info",$modelclass,Log::SUCCESS);
		Controller::triggerHook('debug','info',array(
							'message' =>utf8_encode(var_export($vars['data'],true)),
							'type'=>'info',
							'error'=>Log::SUCCESS,
							'class'=>$modelclass,
							'method'=>__METHOD__));
		//echo _r($pknew,true);die();
		return $c; // devolvemos el objeto
	}
	
}

class dbException extends cubeException {
	
	public function __toString() {
		if (!Site::getInstance()->isDebugMode()){
			Session::setFlash('cube_system_error',$this->message);
			header ("Location: ".Route::url("default/errordb"));
		}
		return parent::__toString();
	}
}

abstract class dbDriver{
	
		protected $props;
		protected $schema;
		protected $lastQueryExecuted;
		
		const ROW_NUMBER='RowNum__';
		
		abstract public function setCharset($cs);
		abstract public function query($select,$limit=null);
		abstract public function execute($select);
		abstract public function pagination($select,$props=array());
		abstract public function filters($select,$filters=null,$offset=0);
		abstract public function numrows();
		
		public function lastQuery(){
			return $this->lastQueryExecuted;
		}
		
		public function errorInfo($exception){
			$info=new stdClass();
			$info->code=$exception->getCode();
			$info->message=$exception->getMessage();
			return $info;
		}

		//abstract public function getTableInfo($table,&$temp_schemas);
		abstract public function getTableInfo($table,&$temp_schemas);
		
		public function getSchema(){return $this->schema;}
		public function setSchema($schema){ $this->schema=$schema;}
		
		static public function addAutonumericColumn($value){ 
			if (!empty($value)) return true;
			return false;
		}
		static public function upper($text,$column=null){}
		abstract static public function transFormatDatePHP($value,$props);
		//static public function transFormatDatePHP($value,$props){}
		
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
							$props['column_type']='varchar';
							if (isset($props['filter_key']) && isset($props['autolike']) && ($props['autolike']===true || $props['autolike']=='right')) $cur.="%";
							$val="'{$cur}'";
							break;
					case 'const':
					case 'int':
					case 'integer':
					case 'numeric':
						$props['column_type']='numeric';
						$val="{$cur}";
						$val=preg_replace("/(\d*)([\,]{1})(\d+)/","$1.$3",$val);
						//$val=preg_replace("/(\d+)([\,]){1}(\d+)/","$1.$3",$num);
						break;
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
										case Query::BETWEEN: $vals=array_splice($current, 2); 
															 if (count($vals)==2) $props['format']=$vals[0]; //formato
															 break; // tengo el formato (si hay) y el between  
										case Query::CUSTOM:  return stripslashes($current[0]);break;
										case Query::RANGE:
															$max=count($current)-1;
															$props['range']=array();
															for ($i=1;$i<$max;$i++){
																$props['range']=array_merge($props['range'],array_splice($current, $i, 1));
															}
															$props['type']=strtolower($type);
															array_pop($current);
																break;
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
			
			///// si es nulo, o no nulo, devolvemos el valor (ojo: hacer call_user_func de 'null'en driver)
			if ($cur=="null" || $cur==Query::NULL) return "{$key} is null";
			else if ($cur==Query::NOTNULL) 	return "{$key} is not null";
			else if (is_bool($cur)) 	return "{$key} {$method}".($cur?'1':'0');
			
			/// en caso contrario miramos si tiene like, separacion, ...
			
			$autolike=isset($parameters['autolike'])?$parameters['autolike']:false;
			
			//echo "-----------------------".$autolike;
			
			if (preg_match("/[%\*]/",$cur)) {
				$method="like";
				$cur=preg_replace("/[\*]/","%",$cur);
				$cur=preg_replace("/^['\"]|['\"]$/","",$cur);
				
				if ($autolike===true || $autolike=='left') $cur="'%{$cur}'";else $cur="'{$cur}'";				
			
			}else $method="=";
			
			
			// si pasamos case sensitive y esta a falso!
			if (!preg_match("/".Form::FILTER_SEPARATOR_ARRAY."/",$cur) &&
				isset($parameters['casesensitive']) && !$parameters['casesensitive']) {  
				//echo "<br/>{$key} : casesentitive!";
				
				$value=call_user_func_array(array($parameters['driver'],'upper'),array($cur));
				$key=call_user_func_array(array($parameters['driver'],'upper'),array($key));
					
				$cur="{$key} {$method} ".$value.Form::FILTER_SEPARATOR_ARRAY.Query::CUSTOM;		
			}
			
			//echo "<br/>".$key.",".$method.",".$cur;
			//call_user_func_array(array($parameters['driver'],'transFormatDatePHP')
			//echo _r($parameters);
			
			{
				
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
					reset($current);
					switch($operator){
						case Query::IN:
							//echo _r($key)._r($parameters);
							array_pop($current); //extraemos el último elemento que es Query::IN
							
							if (isset($parameters['column_type']) && $parameters['column_type']=='numeric') //si numerico
								$valor="{$key} in (".implode(", ",$current).")";
							else // si texto
							 	$valor="{$key} in ('".implode("', '",$current)."')";
							break;
						case Query::NOTEQUAL:
							if ($method=='like') $method="not like";else $method="<>";
							$valor="{$key} {$method} '".stripslashes($current[0])."'"; // ' ' porque es texto y se lo habiamos quitado (trim)
							break;
						case Query::CUSTOM:
							$valor=stripslashes($current[0]);
							break; 
						case Query::BETWEEN:
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
					}
				}else {
					$valor="{$key} {$method} {$cur}";
				}
			}
			
			return $valor;
		}
		
		//const PRELIKE=true;
		
		private static function formatDate($format)
		{
			$formatDate=str_replace("%Y","([0-9]{4})",$format);
			$formatDate=str_replace("%m","([0-9]{1,2})",$formatDate);
			$formatDate=str_replace("%d","([0-9]{1,2})",$formatDate);
			$formatDate=str_replace("%H","([0-9]{1,2})",$formatDate);
			$formatDate=str_replace("%M","([0-9]{1,2})",$formatDate);
			$formatDate=str_replace("%S","([0-9]{1,2})",$formatDate);
			$formatDate=str_replace("/","\/",$formatDate);
			$formatDate=str_replace(".","\.",$formatDate);
			
			return $formatDate;
		}
	
		public function translate($column,&$parameters=array(),$validator=false)
		{
			$input='';
			if (!$validator) 
			{
				$options=array();
				$js=array();
				$type=strtolower($column['type']);
				switch($type)
				{
					case 'text':		$input='input/longtext';
										break;
					case 'timestamp':	$input= 'input/text';
										break;
					case 'varchar':  	$input='input/text';
										break;
					case 'int':			
					case 'integer':    
					case 'numeric':     $input='input/text';
										break;
					case 'bool':
					case 'boolean':	 	$input='input/radio';
										if (!isset($column['params']['options'])) $options[]='options: {1: _echo(yes), 0: _echo(no)}';
										else 
										{
											$ops=array();
											foreach($column['params']['options'] as $op=>$val) $ops[]="$op: \"{$val}\"";
											$options[]="options: {".implode(", ",$ops)."}";										
										}
										break;
					
					case 'date':		$input='input/calendar';
										//if (!isset($column['params']['format'])) $format='%Y-%m-%d %H:%M:%S';
										//else $format=$column['params']['format'];
										
										if (isset($column['params']['format'])) $format=$column['params']['format'];
										else if (isset($column['format'])) $format=$column['format'];
										else $format='%Y-%m-%d %H:%M:%S';

										$parameters[]='format_db: '.$format;
										$parameters[]='format: '.$format;
										//$options[]='format: '.$format;
										$options[]="js: style=\"width:".(strlen(strftime($format,time()))*8)."px;\"";
										
										/*
										if (isset($column['params']['default'])){
											if (strtolower($column['params']['default'])=='sysdate') $parameters[]='default: sysdate';
											else $parameters[]='default: '.$column['params']['default'];
										} */
										
										break;
				}
				
				if (isset($column['params']['default'])){
					if (strtolower($column['params']['default'])=='sysdate') $parameters[]='default: sysdate';
					else $parameters[]='default: '.$column['params']['default'];
				}
				
				if (isset($column['params']['comment'])) $parameters[]='comment: "'.addslashes($column['params']['comment']).'"';
				
				//if (isset($column['pk']) && $column['pk']) $options[]="readonly: true"; //$js[]="readonly=\"readonly\"";
				if (isset($column['autonumeric']) && $column['autonumeric']) $input='input/hidden';
				if (isset($column['size']))
				{ 
					$js[]='maxlength="'.$column['size'].'"';
					
					if ($column['size']<60 && $column['type']!='text' && $input!='input/radio'){
						$width=$column['size']*8;
						$js[]="style=\"width:{$width}px;\"";
					}
				}
				
				
				
				if (!empty($js)) $options[]="js: '".implode(" ",$js)."'";
				
				
				//var_dump($width);
				return $input.((count($options)>0)?", {".implode(", ",$options)." }":"");
			}
			else 
			{
				if (in_array($column['type'],array('int','integer','numeric'))) 
				{
					if (!isset($column['autonumeric']) || !$column['autonumeric']) $input[]="numeric: true";
				}
				//else if (in_array($column['type'],array('varchar','text'))) $input[]="alfa: true";
				else if (in_array($column['type'],array('timestamp','date')))
				{
					//if (!isset($column['params']['format'])) $format='%Y-%m-%d %H:%M:%S';
					//else $format=$column['params']['format'];
					
					if (isset($column['params']['format'])) $format=$column['params']['format'];
					else if (isset($column['format'])) $format=$column['format'];
					else $format='%Y-%m-%d %H:%M:%S';
					
					$formatDate=self::formatDate($format);
					
					$input[]="ereg: \"^".$formatDate."$\"";
				}
				
				if ((isset($column['pk']) && $column['pk']) || (isset($column['params']['null']) && !$column['params']['null'])) 
				{
					if (!isset($column['autonumeric']) || !$column['autonumeric'])  $input[]="required: true";
				}
				
				if (isset($column['size']) && !in_array($column['type'],array('bool','boolean')) && (!isset($column['autonumeric']) || !$column['autonumeric'])) 
					$input[]="maxlength: ".$column['size'];
	
				if (is_array($input)) return implode(", ",$input);
			}
			return null;
		}
		
		public $ALIAS_SEPARATOR='x';
		
		// mysql no hace update si los campos son iguales a los que hay en la bd
		public $FORCE_UPDATE_NUM_ROWS_AFFECTED=false; 
		
		public function __toString()
		{
			return  "<pre>".print_r($this,true)."</pre>";
		}
		
		protected function __construct($props)
		{
			$this->props=$props;
		}
		
		static public function toTimestamp($stData,$stFormat)
		{
			$aDataRet = array(	'hour'=>0,'minute'=>0,'second'=>0,
								'day'=>strftime("%d",time()),
								'month'=>strftime("%m",time()),
								'year'=>strftime("%Y",time()));
			
			if (!is_array($stData))
			{
				$stData=trim($stData,"'");
				if ($stData=='' || preg_match('/^0000-00-00/',$stData)) return null;
				
				//echo "<br/>".$stData.", ".$stFormat;
				
				$aPieces = preg_split('/[:\\/\\\. -]/', $stFormat);
			    $aDatePart = preg_split('/[:\\/\\\. -]/', $stData);
			    
				foreach($aPieces as $key=>$chPiece)
				{
			     	if (isset($aDatePart[$key]))
			     	{
						//echo $chPiece;
				    	switch ($chPiece)
				        {
				            case '%d':case '%j':
				                $aDataRet['day'] = intval($aDatePart[$key]);
				                break;
				            case '%F': case '%m': case '%n':
				                $aDataRet['month'] = intval($aDatePart[$key]);
				                break;
				            case '%o': case '%Y': case '%y':
				                $aDataRet['year'] = intval($aDatePart[$key]);
				                break;
				            case '%g': case '%G': case '%h': case '%H':
				                $aDataRet['hour'] = intval($aDatePart[$key]);
				                break;   
				            case '%i': case '%M': //case '%I':
				                $aDataRet['minute'] = intval($aDatePart[$key]);
				                break;
				            case '%s': case '%S':
				                $aDataRet['second'] = intval($aDatePart[$key]);
				                break;           
				        }
			     	}
			    }
			}
			else  $aDataRet=array_merge($aDataRet,$stData);
			
			$time=mktime($aDataRet['hour'],$aDataRet['minute'],$aDataRet['second'],$aDataRet['month'],$aDataRet['day'],$aDataRet['year']);
			
		    return $time;
		}
		
		private function searchYmlFiles($dir,$ymlFile){
			$models=array();
			
			// buscar los diferentes schemas del mismo modelo (/apps/app/model)
			$dir_handle = opendir($dir);
		    if (!$dir_handle) return false;
		    while($file = readdir($dir_handle)) {
		         if (substr($file,0,1)!='.') {
		            $schema=$dir.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.$ymlFile;
		            if (file_exists($schema)) $models[$file]=$schema;
		         }
		     }
		    closedir($dir_handle);
		    
		    return $models;
		}
		
		public function extractSchemaInfo($table,$dir,$root){
			
			$temp=array();
			$info=$this->getTableInfo($table,$temp);
			// get all schemas for search fk to referenceClass
			
			$models=$this->searchYmlFiles(realpath($dir),"schema.yml");
			//$models3=$this->searchYmlFiles(realpath($dir.'/../'),"schema.yml");
			//$models2=array_merge($models,$models3);
						
			$phpnames=array();
			foreach($models as $model=>$schemafile){
				foreach($temp as $tempmodel=>$cols){
					$config=sfYaml::load($schemafile);
					if (strcasecmp($config['table'], $tempmodel) == 0){
						//echo "\n".$config['table'].": ".$config['database'];
						foreach($cols as $col){
							if (isset($config['columns'][$col])){
								//echo "\n -- ".$config['columns'][$col]['phpname'];
								$phpnames[$config['table'].'.'.$col]=$config['class'].'.'.$config['columns'][$col]['phpname'];
							}
						}
					}
				}
			}
			
			foreach($info as $i=>$data){
				if (isset($data['fk'])){
					$trad=Util::array_nsearch($data['fk'],array_flip($phpnames));
					if ($trad!==false) $info[$i]['fk']=$trad;
					else echo "\n### ERROR ### No Search php traduction of the FK ".$data['fk']." (not found schema in current and general scopes models)\n";
				}
			}
			
			return $info;
		}
		
		
		public function generatePeerMethodsFK($class,$columns=array(),$dir,$peerFile,$ROOT){
			
			/// incluimos la clase
			include_once $peerFile;
			
			// get all schemas for search fk to referenceClass
			$models=$this->searchYmlFiles(realpath($dir),"schema.yml");
			///$models2=$this->searchYmlFiles(realpath($dir.'/../'),"schema.yml");
			///$models=array_merge($models,$models2);
			//echo print_r($models);die();
			$data=array();
			$fks=array();
			foreach($models as $model=>$file){	// de todos los schemas
				
				if ($model!=strtolower($class))
				{ 	
					// buscamos los que son diferentes al modelo de ref
					$data[$model]=sfYaml::load($file);	// extraemos los datos
					// buscamos las fk de la tabla
					//echo "\nleo ({$model}) ".$file."\n"._r($data[$model]['columns']);
					reset($data[$model]['columns']);
					foreach($data[$model]['columns'] as $column=>$info){
						if (isset($info['fk'])){	// si existe el attrib fk
							//echo "\n".$info['fk']." : ";
							// nos guardamos la info del phpname y fk
							$inf=explode(".",$info['fk']);
							/// throughClass[phpForeignTable][phpnameColumn]=[realColumn,fk(phpForeignTable.phpForeignColumn)]
							//$fks[$data[$model]['class']][$inf[0]][$info['phpname']]=array($column,$info['fk'],$file);	
							$currentModelDirName=strtolower($inf[0]);
							if (!isset($models[$currentModelDirName])) $currentModelDirName=ucfirst($currentModelDirName); 
							$fil=$models[$currentModelDirName];
							
							$i=strrpos($fil,DIRECTORY_SEPARATOR); //fichero src.
							$fileM=substr($fil,0,$i).DIRECTORY_SEPARATOR;
							//$file.="..".DIRECTORY_SEPARATOR.strtolower($th).DIRECTORY_SEPARATOR;
							//$file.="object".DIRECTORY_SEPARATOR.$th.".php";
							$fileM.="object".DIRECTORY_SEPARATOR.$inf[0]."Peer.php";	
							
							//echo _r($fks);
							
							$fks[$data[$model]['class']]['columns'][$inf[0]][$inf[1]]=$info['phpname'];
							$fks[$data[$model]['class']]['files'][$inf[0]]=$fileM;
						}
					}
				}
			}
			
			//echo _r($fks);die();	// extraer de CursosDest las pks de Cursos(reference) y de Destinacions
			foreach($fks as $col=>$data2){
				$tables=array();
				$tables=array_keys($data2['files']);
				
				if (in_array($class,$tables)){
					
									
					// crear tantos métodos como combinaciones de fk
					$functions=array();
					$max=count($tables);
					for($i=0;$i<$max;$i++){
						if ($max==1) {$ini=0;$fin=1;}else {$ini=$i+1;$fin=$max;}
						for ($j=$ini;$j<$fin;$j++){
							
							
							//echo "\n..........".$tables[$i]; //.":"._r($data2);
							//echo "\n".$tables[$i]." ".$tables[$j]; //."\n--doSelectJoin".key($data2[$tables[$i]]).key($data2[$tables[$j]]);
							
							if ($tables[$i]==$tables[$j]) {
								$funcName=$col;
								$funcName2=$col;
							}else {
								$funcName=$tables[$j];
								$funcName2=$tables[$i];
							}
							
							
							$functions[$tables[$i]]=array(	"functionName"=>"doSelectJoin".$funcName,
															"throughClass"=>$col,
															"file"=>$data2['files'][$tables[$i]]
															//"columns"=>$data[$tables[$j]]
													);
													
							foreach($data2['columns'][$tables[$i]] as $ke=>$va){
								 
								$functions[$tables[$i]]['params'][$ke]=$va;																			
							}
													
	
						
						
							$functions[$tables[$j]]=array(	"functionName"=>"doSelectJoin".$funcName2,
															"throughClass"=>$col,
															"file"=>$data2['files'][$tables[$j]]
															//"columns"=>$data[$tables[$i]]
													);
							foreach($data2['columns'][$tables[$j]] as $ke=>$va){
								$functions[$tables[$j]]['params'][$ke]=$va;																			
							}
						}
					}
				
					// para cada funcion
					foreach($functions as $f=>$data3){
						//echo "\n".$f.'=>\n'.$data3['file'];
						// si existe el fichero de la clase
						if (file_exists($data3['file'])){
							// lo incluimos
							include_once $data3['file'];
							//echo "\n".$data3['file'];
							// buscamos los métodos que ya tiene creados
							
							$methods=get_class_methods($f."Peer");
							// si el método que queremos crear no existe..
							if (!method_exists($f.'Peer',$data3['functionName']))
							//if (!in_array($data3['functionName'],$methods))
							{
								//echo "\nCrear en ".$f."Peer: ".$data['functionName'];
								/// keys: los parámetros "fijos" para este método
								//$keys=array_keys($data['params']);
								$keys=$data3['params'];
								//echo _r($keys);
								
								if (!empty($keys)){
									/// argsN: parámetros en forma de argumentos de función 
									$argsN="\$".implode(", \$",$keys);						
								
									/// argsV: creacion del paso de valores para hacer un retrieveByColumns 
									$argsV='';
									foreach($keys as $key){
										$argsV.="\$criteria['{$key}']=array('value'=>\${$key});\n";
									}
								}
							
							/// generamos la función con los parámetros que hemos creado antes
							$str=<<<EOF
\n  //// Autogenerate by {$data3['throughClass']} //// 
	public function {$data3['functionName']}({$argsN}){
		\$criteria=array();
		\$peer=new {$data3['throughClass']}Peer();
		{$argsV}		\$data=\$peer->retrieveByColumns(\$criteria,false);
		return \$data;
	}\n
EOF;
						
								//// leemos el fichero, y ponemos la función al final de la clase (última })
								$contents=file_get_contents($data3['file']);
								//echo _r($contents);
								$contents=preg_replace("/\r\n/","##SEP##",$contents);
								//$contents= preg_replace('/\s+/', '##SEP##', $contents);  
								$contents=preg_replace("/\?\>(\#\#SEP\#\#)*$/","",$contents);
								$pos=strrpos($contents,'}');
								$contents=preg_replace("/\#\#SEP\#\#/","\n",substr($contents,0,$pos));
									
								/// guardamos todo el contenido en el mismo fichero
								file_put_contents($data3['file'],$contents.$str."}\n?>");
								chmod($data3['file'],0666);
							}
						}
					} // foreach($functions as $f=>$data3){
				
			}///if (in_array($class,$tables))
			
			
		}
	}
		
		public function generate($class,$columns=array(),&$pks=array())
		{
			$str='';
			foreach($columns as $key=>$column)
			{
				/*
				  id_cube: {type: numeric, size: 20, pk: true, phpname: Id, autonumeric: true, sequence: randomSequence}
				 
				  id:
			        label: Id
			        view: [input/text, {js: style=width:130px;}]
			        validator: { required: true, alfa: true }
			        credentials: 
			        assignTo: Metadata.Id	
			     */
				  if (isset($column['pk'])) $pks[]=strtolower($class).'.'.$key;
				  
		$phpname=(isset($column['phpname'])?$column['phpname']:$key);
		
		$assignTo=array($class.'.'.$phpname);
		if (isset($column['fk'])) $assignTo[]=$column['fk'];
		$assignToValues=(count($assignTo)>1)?'['.implode(", ",$assignTo).']':$assignTo[0];
		
		$paras=array();
		$validator=$this->translate($column,$paras,true);
		$view=$this->translate($column,$paras);
				
		if ($column['type']=='varchar' || $column['type']=='text'){ 
			 $paras[]='casesensitive: false';
			 $paras[]='autolike: true';
		}
		
		$parameters="\n      parameters: {".implode(", ",$paras)."}";
		$validator=($validator!==null)?"
      validator: {".$validator." }":"";
				
$str.="
    {$key}:
      view: [".$view."]".$validator."
      credentials:$parameters
      assignTo: ".$assignToValues;
       //if (!preg_match("/input\/hidden/",$view))
                {
        	$str.="
      label: ".$phpname;
       }
			}
			return $str;
		}
	}
		    	
class db {
	
		
	protected $props;
	
	public function __toString()
	{
		return "<pre>".print_r($this,true)."</pre>";
	}
	
	public static function getDriver($tipo,$props=null){
     	try{
			if (!class_exists($tipo)) throw new CubeException("dbDriver '".$tipo."' is not loaded", 1);
			if ($props!=null) return new $tipo($props);
     	}catch(CubeException $e){echo $e;}
	}
	
	static private $databases=array();
	public static function registerDatabase($database,$props)
	{
		if ($props===null) throw new CubeException("Database '".$database."' does not exist", 3);
		if (!isset($props['package'])) throw new CubeException(ucfirst($database)." package is required", 2);

		$package=dirname(__FILE__)."/../".str_replace(".","/",$props['package']).".php";
		$driver=$props['driver'];unset($props['driver']);unset($props['package']);

		if (is_file($package))
		{
			include_once $package;
			$conn=new $driver($props);

			Controller::triggerHook('debug','model',array(
						'message' =>"Connect to database '{$database}' ({$driver})",
						'type'=>'model',
						'error'=>($conn!=null)?(Log::SUCCESS):(Log::ERROR),
						'class'=>__CLASS__,
						'method'=>__METHOD__));


			self::$databases[$database]=$conn;
		}else throw new CubeException("Package {$package} not registered", 1);
	}
	
	public static function isRegistered($name)
	{
		return isset(self::$databases[$name]);
	}
	
	public static function get($name)
	{
		try{
			if (isset(self::$databases[$name])) return self::$databases[$name];
			else throw new cubeException("Database {$name} not registered", 1);
		}catch(CubeException $e){echo $e;}
	}
	
	public static function create($props=array(),$name=null){
		if ($name===null) $name="db".time();
		self::registerDatabase($name,$props);
		return self::get($name);
	}
	
}

class CubePeer
{
	 private $database;
	 public function registerDatabase($database){$this->database=$database;}
	 public function getDatabase(){ return $this->database;}
	
	 private $cube;
	 public  function registerClass($cube){$this->cube=$cube;}
	 private function getClass(){ return $this->cube;}
	
	  private $table;
	 public function registerTable($name)
	{
		$this->table=$name;
	}
	
	//public function __construct(){ $this->setup();$this->configure();}
	public function __construct(){ $this->setup();}

	public function getLastQueryExecuted(){
		return $this->database->lastQuery();
	}
	
	 public function getTable()
	{
		try{
			if (!isset($this->table)) throw new CubeException("Table is not registered", 1);
			return $this->table;
		}catch(CubeException $e){echo $e;}
	}
	
	public function getPHPNames()
	{
		$t=$this->getColumns();
		$cols=array();
		foreach($t as $i=>$col)
		{
			if (isset($col['phpname'])) $cols[$i]=$col['phpname'];
			else $cols[$i]=$i;
		}
		return $cols;
	}
	
	private $columns=array();
	public function registerColumns($columns=array())
	{
		$this->columns=$columns;
	}
	
	public function getColumns()
	{
		return $this->columns;
	}
	
	public function getColumn($name)
	{
		try{
			if (!isset($this->columns[$name])) throw new CubeException("Column {$name} not registered", 1);
			return $this->columns[$name];
		}catch(CubeException $e){echo $e;}
	}
	
	public function hasColumn($name)
	{
		return isset($this->columns[$name]);
	}
	
	/*
	 private $validators=array();
	 public function registerValidator($config=array())
	{
		$this->validators=array_merge_recursive($this->validators,$config);
	}
	
	 public function getValidators()
	{
		return $this->validators;
	}
	
	 public function getValidator($name)
	{
		try{
			if (!isset($this->validators[$name])) throw new cubeException("Validator {$name} not registered", 1);
			return $this->validators[$name];
		}catch(dbException $e){echo $e;}
	}
	*/
	public function configure() {}
	
	protected function setup()
	{
		$class=preg_replace("/(.*)Peer/","$1",get_class($this));
		if ($class==='Cube') throw new CubeException("Cube class is abstract and not contains configuration info.",2);
		$conf=Config::get('model:'.strtolower($class),'model');
		//echo 'model:'.strtolower($class)." -->"._r($conf);
		if ($conf===null) throw new CubeException("Config file not found",1);
		
		if (!isset($conf['class'])) $class=substr(__CLASS__,0,strrpos(__CLASS__,"Peer"));else $class=$conf['class'];
		$this->registerClass($class);
		
		$this->registerTable($conf['table']);
		
		if (!db::isRegistered($conf['database'])) //si la conexion no esta registrada, registrarla
		{
			$a=Config::get('database:'.$conf['database']);
			db::registerDatabase($conf['database'],$a);
		}
		
		$this->registerDatabase(db::get($conf['database'])); //registrar la conexion para esta tabla
		$this->registerColumns($conf['columns']);
		
		if (isset($conf['querys']))		{
			foreach($conf['querys'] as $k=>$v) $this->registerQuery($v,$k);
		}
		
		$this->configure();
		//if (isset($conf['validators']))	{foreach($conf['validators'] as $k=>$v) $this->registerValidator($v);}
		//echo _r($this);
	
	}
	
	 private $querys;
	 public function registerQuery($query,$name=null)
	{
		if ($name==null) $name=md5(uniqid(rand(), true));
		$this->querys[$name]=$query;
		
		return $name;
	}
	
	 public function unregisterQuery($name)
	{
		try{
			if (!isset($this->querys[$name])) throw new cubeException("Query {$name} not registered", 1);
			unset($this->querys[$name]);
		}catch(dbException $e){echo $e;}
	}
	
	 public function getQuery($name,$force=false)
	{
		try{
			if ($force && !isset($this->querys[$name])) return null;
			else if (!isset($this->querys[$name])) {
				throw new cubeException("Query {$name} not registered", 1);
			}
			return $this->querys[$name];
		}catch(dbException $e){echo $e;}
	}
	
	 public function bindQueryFilters($query,$filters){
	 	$select=$this->getQuery($query);
	 	
	 	$this->filterCriteria($filters);
	 	//$query = preg_split("/group by/i",$select);
		$pos=strripos($select,"group by");
		$pos2=strripos($select, 'from', $pos);
		//var_export($pos);var_export($pos2);
		//echo substr($select,0,$pos);
		//die();
		if ($pos2===false) // no hemos encontrado from
		{
			$pre=substr($select,0,$pos);
			$select=$this->getDatabase()->filters($pre,$this->getFilters(),0).
					' group by '.
					substr($select,$pos+9);
		}
		else
			$select=$this->getDatabase()->filters($select,$this->getFilters(),$pos2);					
		
		return $select;
	 }
	
	 public function execute($name,$params=array(),$hydrate=true)
	{
		$class=$this->getClass();
		$select=$this->getQuery($name);
		
		/// añadimos el parámetro table
		$params=array_merge(array('table'=>array("value"=>$this->getTable(),"type"=>"const")),$params);
						
		$select=Query::bind($select,$params);
		$data=$this->doSelect($select,$hydrate);
				
		return $data;
	}
	
	private $limit;
	private $offset;
	private $page;
	private $select;
	private $order;
	private $filters;
	
	public function limit($limit){$this->limit=$limit; return $this;}
	public function page($page){$this->page=$page; return $this;}
	public function offset($offset){$this->offset=$offset; return $this;}
	
	public function doCount($select=null)
	{
		if ($select==null) {
			$qc=$this->getQuery("count",true);
			
		}
		else $qc=$select;
		
		if ($qc!==null) {
			$count=$this->doSelect($qc,false);
			return $count[0][$this->getTable()];
		}
		return 0;
	}
	
	public function doSelect($select,$hidrate=true)
	{
		/// cambiamos las constantes
		$select=str_replace("{table}",$this->getTable(),$select);
		$select=str_replace("{count}",$this->getTable(),$select);
		
		$select=preg_replace("/[[:space:]]/"," ",$select);	// cambiamos saltos de linea, etc por espacio
		
		/// miramos si hay paginacion
		if (!isset($this->offset)) $this->offset=0;
		
		if (!isset($this->page)) $props=array("limit"=>$this->limit,"offset"=>$this->offset);
		else $props=array("limit"=>$this->limit,"page"=>$this->page);
		
		if (isset($this->order))
		{
			if (preg_match("/order by/i",$select)) $select=preg_replace("/(.*)(order by)([^\)]*)(.*)/","$1order by $3,{$this->order} $4",$select);
			else $select.=" order by ".$this->order;
		}
		
		//echo "<br/>sin pag: "._r($this->filters)."<br/>";
		
		if (isset($this->limit)) $select=$this->getDatabase()->pagination($select,$props);
		
		/*
		if (preg_match("/group by/i",$select)){
			$conditions=array();
			$a=preg_split("/group by/i",$select);
			preg_match("/(and|where)(.*)[)] sub1/i",$a[1],$conditions);// sacamos las condiciones
			$group_by=preg_replace("/(and|where)(.*)[)] sub1/i","",$a[1]);// sacamos el groupby
			$conditio=(!empty($conditions))?$conditions[1].$conditions[2]:"";
			//$select=$a[0].$conditions[1].$conditions[2]." group by ".$group_by.") sub1";
			$select=$a[0].$conditio." group by ".$group_by.") sub1";
		}
		echo "<br/>".$select;
		*/

		$this->toStringSelect=$select;
		//echo "<br/>con pag: ".$select."<br/>";
		
		return $this->executeSelect($select,$hidrate);
	}
	
	private $toStringSelect;
	public function getSelect(){ return $this->toStringSelect;}
	
	
	public function select($select)
	{
		$this->select=$select;
		return $this;
	}
	
	public function sortCriteria($array)
	{
		if (count($array)>0) //hay algun elemento
		{
			$order=array();
			foreach($array as $k=>$v)
			{
				if (!empty($v)) $order[]=$k." ".$v;
			}
			if (!empty($order)) $this->order=implode(" ,",$order);
		}
		
		return $this;
	}
	
	public function getFilters(){ return $this->filters;}
	
	public function filterCriteria($str){	
		if (strlen($str)!=0) $filter=$str;
		
		if (isset($filter)) $this->filters=$filter;
		
		return $this;
	}
	
	public function sort()
	{
		$argv=func_get_args();
		
		if (func_num_args()>1)
		{
			$order=array();
			for($i=1;$i<count($argv);$i+=2) $order[]=$argv[$i-1]." ".$argv[$i];
			$this->order=implode(" ,",$order);
		}
		return $this;
	}
	
	public function exec($hidrate=true)
	{
		return $this->doSelect($this->select,$hidrate);	
	}
	
	public function cast($type,$value)
	{
		//echo "<br/>$value: $type!!!";
		switch($type)
		{
			case 'date': return dbDriver::toTimestamp($value,$this->getDatabase()->getDateFormatter());
		}
		return $value;
	}
	
	public function castByColumnType($cur,$type='varchar',$props=array())
	{
		//echo "<br/>"._r($cur,true).": ".$type.", "._r($props);
		
		if ($type=='date') {
			/*if (isset($props['format'])) $props['format_db']=$props['format'];
			else */ 
			//echo $cur._r($props);
			if (!isset($props['format_db'])) 
				$props['format_db']=$this->getDatabase()->getDateFormatter();
			 
		}
		$props['driver']=$this->getDatabase();
		
		return call_user_func_array(array($this->getDatabase(),"cast"),array($cur,$type,$props));
	}
	
	protected function executeSelect($select,$hidrate=true)
    {
        $class=$this->getClass();
        
        
		// la base de datos devolverá esto
		$data=$this->getDatabase()->query($select);
		//echo $select.": "._r($data);die();
		
		//Log::_add(__METHOD__,$select,"model",$class);
		Controller::triggerHook('debug','model',array(
							'message' =>is_array($select)?var_export($select,true):$select,
							'type'=>'model',
							'error'=>Log::SUCCESS,
							'class'=>$class,
							'method'=>__METHOD__));
		
		if (!$hidrate) 
		{
			if ($data!==null)
			{		
				//echo _r($data);
				foreach($data as $w=>$cur)
				{
					foreach ($cur as $k=>$v)
					{
						// si la columna es <> del nombre de la tabla o del rownumber hace el cast 
						if (!preg_match("/".$k."/i",$this->getTable()) && !preg_match("/".$k."/i",dbDriver::ROW_NUMBER))
						{
							if ($this->hasColumn($k))
							{
								$col=$this->getColumn($k);
								
								$data[$w][$k]=$this->cast($col['type'],$v);
							}
							else $data[$w][$k]=$v;
						}
						
					}
				}
				
			}
			//echo _r($data);
			return $data;
		}
		
		
		$cubes=array();
		if ($data!==null)
		{
			$embed=new Cube();
			
			foreach($data as $cur)
			{
				$temp=new $class();
				
				foreach ($cur as $k=>$v)
				{
					if ($this->hasColumn($k)) 
					{
						$col=$this->getColumn($k);
						
						$v=$this->cast($col['type'],$v);
						if (isset($col['phpname'])) $key=$col['phpname'];else $key=$k;
						$temp->{$key}=$v;
					}
					else $embed->{$k}=$v;
				}
				$temp->setEmbedColumns($embed);
				$temp->clearModifiedAttributes();
				$cubes[]=$temp;
			}
		}
		return $cubes;
	}
		
	 public function extractPK($valid)
	{
		$pk=array();
		foreach($valid as $k=>$v)
		{
			if (isset($v['pk']) && $v['pk']) $pk[]=$k;
		}
		
		return $pk;
	}
	
 	public function extractFK($valid)
	{
		$fk=array();
		foreach($valid as $k=>$v)
		{
			if (isset($v['fk']) && $v['fk']) $fk[$k]=$v['fk'];
		}
		
		return $fk;
	}
	
	
	
	public function retrieveByPk()
	{
		//$valid=$this->getValidators();
		$columns=$this->getColumns();
		
		$pks=$this->extractPK($columns); // extraigo PK
		$table=$this->getTable();
		
		if (count($pks)==func_num_args()) // el numero de argumentos es igual a las PK de la tabla
		{
			//$columns=array_flip($l->getPHPNames());
			
			$args = func_get_args();	// argumentos pasados
			
			$select="select * from {$table} where ";
			$where=array();
			for($i=0;$i<count($pks);$i++) 
			{
				//$where[]=$pks[$i]."=".Cube::castByValue($args[$i]);
				if ($columns[$pks[$i]]['type']=='date')
					$props=array('format'=>$this->getDatabase()->getDateFormatter(),'format_db'=>$this->getDatabase()->getDateFormatter());
				else $props=array();
				//echo "<br/>"._r($props);
				$valor=call_user_func_array(array($this->getDatabase(),"cast"),array($args[$i],$columns[$pks[$i]]['type'],$props));
				
				if ($valor=='null') $where[]=$pks[$i]." is ".$valor;
				else $where[]=$pks[$i]."=".$valor;
			}
				
			$select.=implode(" and ",$where);
			
			$data=$this->doSelect($select);
			if ($data) {$data[0]->clearModifiedAttributes();return $data[0];}
			
		}else {
			//Log::_add(__METHOD__,"Number of PK for {$table} is incorrect:  ".implode(",",$pks),"model",__CLASS__,Log::ERROR);
			Controller::triggerHook('debug','model',array(
							'message' =>"Number of PK for {$table} is incorrect:  ".implode(",",$pks),
							'type'=>'model',
							'error'=>Log::ERROR,
							'class'=>__CLASS__,
							'method'=>__METHOD__));
		}
		
		return null;
		
	}
	//////////////////////////////////////
	
	public function retrieveByColumns($info=array(),$hidrate=true,$order=null)
	{
		$query="select * from ".$this->getTable();
		
		if (count($info)>0) {
			$columns=$this->getColumns();
			$phpnames=array_flip($this->getPHPNames());
			//echo _r($phpnames);
			//die();
			$where=array();
			foreach($info as $col=>$values){	
				$value=isset($values['value'])?$values['value']:null;
				
				if (is_array($value)){	// si el valor es compuesto
					
					if (is_array($value[0])) // si el primer valor es un array (query::in)  
						$val=implode(Form::FILTER_SEPARATOR_ARRAY,$value[0]);
					else $val=$value[0];  
					$val.=Form::FILTER_SEPARATOR_ARRAY.$value[1];	// operacion (in,custom,notnull,...)
				}else $val=$value; //es valor simple
				
				$props=$values;
				unset($props['value']);
				$props['filter_key']=$phpnames[$col];
				$props['hidrate']=true;
				$props['driver']=$this->getDatabase();
								
				if ($columns[$phpnames[$col]]['type']=='date'){
					
					// formato pasado por el query
					if (isset($values['format_db'])) $props['format_db']=$values['format_db']; 
					// formato del schema
					else if (isset($columns[$phpnames[$col]]['params']['format'])) 
						$props['format_db']=$columns[$phpnames[$col]]['params']['format'];
					// formato de la base de datos 
					else $props['format_db']=$this->getDatabase()->getDateFormatter();
					
					// formato pasado por el query
					if (isset($values['format'])) $props['format']=$values['format']; 
					// formato de la base de datos 
					else $props['format']=$props['format_db'];
				}
				//echo _r($props);
				
				$valor=call_user_func_array(array($this->getDatabase(),"cast"),array($val,$columns[$phpnames[$col]]['type'],$props));
				$where[]=$valor;
			}
			$query.=" where ".implode(" and ",$where);
			
			if (is_array($order)) {
				
				foreach($order as $ord=>$type) {
					if (is_numeric($ord)) $order[$ord]=$phpnames[$type]." asc";
					else $order[$ord]=$phpnames[$ord]." ".$type;
				}
				$query.=" order by ".implode(", ",$order);
			}
		}
		//echo $query;//die();
		$data=$this->doSelect($query,$hidrate);
		
		//if ($data) {$data[0]->clearModifiedAttributes();return $data[0];}
		//return null;
		return $data;
	}
	
	//////// 
	public function doSelectAll($hidrate=true)
	{
		return $this->retrieveByColumns(array(),$hidrate);
		//if ($data) {$data[0]->clearModifiedAttributes();return $data[0];}
		//return null;
	}
	
}

/////////////////////////////////////////////////////////////////////////////////////////////

?>