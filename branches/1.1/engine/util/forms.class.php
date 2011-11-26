<?php
class TreeElement
{
	private $item;
	private $childs;
	static private $class;
	static private $instance;
	
	public static function getInstance($class,$item)
	{
		if (self::$instance==null)
		{
			self::$class=$class;
			self::$instance=new $class($item);
		}
		return self::$instance;
	}
	
	public function __construct($item,$class=null)
	{
		if ($class!=null) self::$class=$class;
		$this->item=$item;
		$this->childs=array();
	}
	public function add($item)
	{
		$class=self::$class;
		if ($class===null) $class=get_class($this);
		
		$obj=new $class($item);
		$this->childs[]=$obj;
		return $obj;
	}
	
	public function get()
	{
		return $this;
	}
	
	public function childNodes()
	{
		return $this->childs;
	}
	
	public function firstChild()
	{
		return $this->childs[0];
	}
	
	public function __toString()
	{
		//return (is_array($this->item)?implode("-",$this->item):$this->item).(($this->childs==null)?'':":[".implode(",",$this->childs)."]");
		return "<pre>".print_r($this,true)."</pre>";
	}
	
	public function getAttributes($param=null)
	{
		if ($param==null) return $this->item;
		else if (is_array($this->item)) return $this->item[$param];
		return null;
	}
	
	public function setAttributes($param,$attrib)
	{
		$this->item[$param]=$attrib;
	}
	
	public function getParameters(){
		if (isset($this->item['params']['parameters'])){
			//echo _r($this->item['params']['parameters']);
			return $this->item['params']['parameters'];
		}
		
		return null;
	}
	
	public function getItem(){
		return $this->item;
	}
}
	
class FormElement extends TreeElement	
{
	public function viewForm($viewtype=null)
	{
		$name=rtrim($this->getName(),"Form");
		return Viewer::form($name,$viewtype);
	}
		
	public function field($field,$filter='')
	{
		//echo $this->getMode();
		return $this->add(array('type'=>'field','params'=>$field));
	}
	
	public function menu($field)
	{
		return $this->add(array('type'=>'menu','params'=>$field));
	}
	
	public function button($field)
	{
		return $this->add(array('type'=>'button','params'=>$field));
	}
	
	public function layout($field)
	{
		return $this->add(array('type'=>'layout','params'=>array('name'=>$field,'view'=>array($field,array()))));
	}
	
	public function menugrp()
	{
		return $this->add(array('type'=>'menugrp','params'=>array('name'=>'__menugrp__','view'=>array(0,array()))));
	}
	
	public function group()
	{
		return $this->add(array('type'=>'group','params'=>array('name'=>'__group__','view'=>array(0,array()))));
	}
	
	public function table()
	{
		return $this->add(array('type'=>'table','params'=>array('name'=>'__table__','view'=>array(0,array()))));
	}
	
	public function row()
	{
		return $this->add(array('type'=>'row','params'=>array('name'=>'__row__','view'=>array(0,array()))));
	}
	
	public function text($text,$menu='')
	{
		return $this->add(array('type'=>'text'.$menu,'params'=>array('name'=>$text,'view'=>array(0,array()))));
		
	}
	
	public function hseparator($width=1)
	{
		//return $this->text(str_repeat("&#160;",$width));
		return $this->text('<div style="width:'.$width.'px;clear:inherit;"></div>');
		
	}
	
	public function vseparator($height=1)
	{
		//return $this->text(str_repeat("<br/>",$height));
		return $this->text('<div style="height:'.$height.'px;clear:both;"></div>');
	}
	
	private $open_section=0;
	
	public function section($txt)
	{
		if ($this->open_section==0)	$str=""; else $str="</div></div><div class=\"minicontentWrapper\"></div>";
		$is=$this->open_section;
		$str.='<div class="section" id="section'.$is.'" >'.
				'<a href="javascript:openSection('.$is.');"><h3>'.
				'<span class="toggle_box_contents"> - </span>'.$txt.'</h3></a>'.
				'<div class="collapsable_box">';

		$this->open_section++;
		//return $this->text("<div class=\"user_settings\"><h3>{$txt}</h3></div>");
		return $this->text($str);
	}
	
	public function view($field,$params)
	{
		return $this->add(array('type'=>'view','params'=>array('name'=>$field,'view'=>array($field,$params))));
		
	}
	
	public function form($field)
	{
		return $this->add(array('type'=>'form','params'=>$field));
	} 
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name=$name;
	}
	
	
	
	public function isValid(&$formErrors=array(),$formName='',$elem=null,$vars=array(),&$valid=true,$first=true,$is_ajax=false)
	{
		$my=$this->getAttributes();
		
		
		$params=$my['params']['view'][1];
                
                if ($first) 
		{
			$params['internalname']=$this->getName();
			
			$vars=array();
			$formName=$this->getName();
			
			$request=Request::getInstance();$vars=$request->get();
			//$vars=$this->vars_request;
						
			Session::setFlash($formName."_request___",base64_encode(serialize($vars)));
                        
                        $is_ajax=isset($params['ajax'])?$params['ajax']:false;
			
		}
		else $params['internalname']=$formName."[".$my['params']['name']."]";
		
		
		$requestValue=(isset($vars[$formName][$my['params']['name']]))?$vars[$formName][$my['params']['name']]:null;
		if (!isset($params['value'])) $params['value']=$requestValue;
		
		// si lo que envias es un string, hacemos un implode (y guardamos todos los valores en el campo)
		if (is_array($params['value'])) $params['value']=implode(Form::FILTER_SEPARATOR_ARRAY,$params['value']);
		
		//echo _r($params);
		/// TODO: para los tipos calendario que sean seleccionables, no hay validador
		if (isset($params['type']) && $params['type']=='selection') return true;
		// validator
				
		if (isset($my['params']['validator']) && is_array($my['params']['validator']))
		{
			foreach($my['params']['validator'] as $validator=>$optionval)
			{
				/// si el validador es un array -> validador: [ condicion, mensajeSiNoCondicion ]	
				if (is_array($optionval)) {
					$option=$optionval[0];
					$infoText=parseText($optionval[1]);
				}
				else {
					$option=$optionval;
					$infoText=Viewer::_echo('validator:'.$validator);
				}
				//echo "<br/>".$params['internalname'].": ".$validator."?".$params['value']." --> ";
				
				if ($params['value']==Query::NULL && $validator!='required' && $validator!='notnull') break; //si es blanco, lo saltamos
				
				switch($validator)
				{
					case 'required':
					case 'notnull': 
									if ($option)
									{
										if ($params['value']==null) {
											$info=str_replace("{value}",$params['value'],$infoText);
											$this->storeError($params['internalname'], $info,$formErrors,$is_ajax);
											$valid=false;
										}
									} 	
									 
									break;
					/*
					case 'alfa': 
									$value2=$params['value'];
									$info=str_replace("{value}",$value2,Viewer::_echo('validator:alfa'));
									$value2=trim($value2, '+-.,0123456789');
									if ($option) 
									{
										$bool=(empty($value2) || empty($params['value']));
										if ($bool) {
											Session::getInstance()->setFlash($params['internalname'],$info,true);
											$valid=false;
										}
									}
									break;*/
					case 'nonumeric':
									$option=!$option;
					case 'numeric': 
									/*
									$value2=$params['value'];
									$value3=trim($value2, '+-.,0123456789');
									
									//var_dump(preg_match("/^\d+([\.\,]{1}\d+)?$/",$value2));
									$numeric=(preg_match("/^\d+([\.\,]{1}\d+)?$/",$value2));
									
									if (($option && $numeric) || (!empty($value3) && !$option && !$numeric)) $bool=false;
									else $bool=true;
									
									if ($validator=='numeric' && !$option && !is_array($optionval)) $infoText=Viewer::_echo('validator:nonumeric');
									$info=preg_replace("/{value}/",$value2,$infoText);									
									
									if ($bool) {
										Session::getInstance()->setFlash($params['internalname'],$info,true);
										$valid=false;
									}
									//echo $validator;var_dump($numeric);var_dump($option);var_dump($bool);var_dump($valid);
									*/
									
									$value2=$params['value'];
									/// permite: -- 0.3 -- 0,3 -- .3 -- ,3 --
									preg_match("/^(\d+)?([\.\,]{1}\d+)?$/",$value2,$args);
									if (empty($args)) $numeric=false;else $numeric=true;
									
									if ($option) 
									{
										$info=preg_replace("/{value}/",$value2,$infoText);
										$bool=!$numeric;
									}
									else{
										
										// el validador es numerico, pero no hay info validador -> cogemos la info generica nonumeric
										// si el validador no es numeric (es nonumeric) o tiene info validador, cogemos infoText
										if ($validator=='numeric' && !is_array($optionval)) $infoText=Viewer::_echo('validator:nonumeric');
										
										$info=preg_replace("/{value}/",$params['value'],$infoText);
										$bool=$numeric;
									}
									if ($bool) {
										$this->storeError($params['internalname'], $info,$formErrors,$is_ajax);
										$valid=false;
									}
									break;
					case 'ereg':	
						//$v = str fomat 
									if (!preg_match("/".$option."/",$params['value']) && $params['value']!=''){
										$info=str_replace("{validator}",$option,$infoText);
										$info=str_replace("{value}",$params['value'],$info);
										$this->storeError($params['internalname'], $info,$formErrors,$is_ajax);
										$valid=false;
									}
									break;
					case 'in':		if (!in_array($params['value'],$option)){
										$info=str_replace("{validator}",implode(", ",$option),$infoText);
										$info=str_replace("{value}",$params['value'],$info);
										$this->storeError($params['internalname'], $info,$formErrors,$is_ajax);
										$valid=false;			
									}
									break;
					case 'maxlength': if (strlen($params['value'])>$option){
										$info=str_replace("{validator}",$option,$infoText);
										$info=str_replace("{value}",$params['value'],$info);
										$this->storeError($params['internalname'], $info,$formErrors,$is_ajax);
										$valid=false;			
									}
									break;
					case 'minlength': if (strlen($params['value'])<$option){
										$info=str_replace("{validator}",$option,$infoText);
										$info=str_replace("{value}",$params['value'],$info);
										$this->storeError($params['internalname'], $info,$formErrors,$is_ajax);
                                                                                $valid=false;			
									}
									break;
					default:
								if (!$this->getExtendValidators($validator,$my)) $valid=false;
								break;
				}
			}
			
			if (!$valid && !Session::getInstance()->is_setFlash($formName)) //si uno no es valido, no hace falta volver a pasar por aquí (recuerda: recursivo!) 
			{
                            $errors="<div class=\"reportedcontent_content active_report\"><b>".Viewer::_echo("form:someerrors")."</b></div>";
                            if (!$is_ajax){
                                Session::getInstance()->setFlash($formName,$errors);
                                Session::getInstance()->setFlash('last_form_error',true);
                            }
			}
		}
		
		foreach($this->childNodes() as $child)
		{
			$child->isValid($formErrors, $formName, $child,$vars,$valid,false,$is_ajax);
		}
		
		return $valid;
	}
	
        private function storeError($id,$info,&$formErrors=array(),$is_ajax){
            $formErrors[$id]=$info;
            if (!$is_ajax) Session::getInstance()->setFlash($id,$info,true);
        }
        
	private function getExtendValidators($validator,$params=array()){
		if (isset(self::$extValidators[$validator])){
			$ev=self::$extValidators[$validator];
			$valor=isset($params['params']['view'][1]['value'])?$params['params']['view'][1]['value']:null;
			
			$options=array(	$params['params']['view'][1]['internalname'],$valor);
			
			if (is_array($params['params']['validator'][$validator])){
				$options[]=$params['params']['validator'][$validator][0];
				$options[]=$params['params']['validator'][$validator][1];
			}else
				$options[]=$params['params']['validator'][$validator];
				
			return call_user_func_array($ev['function'],$options);
		}
		return true; //lo dejo pasar porque, de hecho, no existe
	}
	
	static private $extValidators=array();
	
	static public function extendValidator($name,$function){
		self::$extValidators[$name]=array("function"=>$function);
	}
	
	
	private $vars_request;
	private $mode;
	
	public function setMode($mode){ $this->mode=$mode;}
	public function getMode(){ return $this->mode;}
	
	static private function var_exp($array)
	{
		$a=var_export($array,true);
		//$a=str_replace("\n","",$a);
			
		return $a;
	}
	
	/*
	public function bindFilters($vars=array(),$fieldsView=null,$viewtype=null,$formName=null)
	{
		$at=$this->getAttributes();
		if ($formName===null) $formName=$at['params']['name']; 
		
		$fields=Request::getInstance()->getFormObject($fieldsView,$viewtype);
		foreach($fields as $field=>$props){
			if ($props['type']=='field')
			{
				if (isset($vars[$props['params']['name']]))
				{
					$props['params']['view'][1]['value']=$vars[$props['params']['name']];
					
					$this->field(array(	
								//'label'=>'Id',
								'name'=>'id',
								'label'=>'Iderrr',
								'view'=>array('input/hidden',array('js'=>'style="width:30px;"','disabled'=>true,'value'=>13)),
								'validator'=>array('required'=>false,'numeric'=>true),
								'credentials'=>array(),
								'assignTo'=>"Metadata.Id"
						));
					//$this->setAttributes('params',$props['params']);
					//echo "<br/>attributes ".$at['params']['name'].": "._r($vars[$at['params']['name']],true);
				}
			}
		}
	}
	*/
	
	public function addFilters($vars=array())
	{
		$at=$this->getAttributes();
		
		
		$formName=$at['params']['name'];
	
		$vista=$this->getObjectView();
		preg_match("/^(.*)\/filters\/(.*)$/",$vista['view'],$args);
		
		$fields=Request::getInstance()->getFormObject($args[1]."/fields/".$args[2],$vista['viewtype']);
		
		/// creamos nuevo formulario con sólo los fields
		$form=Form::create($formName,'');
		$f2=$form->getRoot();
		//$f2->setMode('filter');
		
		$this->extractFields($vars,$f2); // asignamos valor a los fields del formualario
		if ($fields!==false){	
			/// buscamos los filters que hemos puesto a mano (tienen un prefijo __filter__)
			/// son los todos los fields del generador! (podemos añadir un filtro de bd que no esté
			/// definido en el render, pero si en fields)
			//echo _r($fields);die();	
		
			foreach($fields as $var=>$value){
				if (isset($vars[Form::FILTER_PREFIX.$var])){
					$value['view'][1]['value']=$vars[Form::FILTER_PREFIX.$var];
					$value['name']=$args[1].".".$var;
					/// añadimos este nuevo filtro
					$f2->field($value);
				}
				/// enviamos todos los fields del formulario para que, p.ej, al hacer un assignTo el ->search me pille el campo de referencia
				//$value['name']=$args[1].".".$var;
				//$f2->field($value);
			}
			
		}
		// se supone que cuando existe un filtro, nosotros nos quedamos con el último valor (extrafields)
		//echo _r($f2);
		return $f2;
	}
	
	
	public function setObjectView($view,$viewtype){
		$this->objview=array("view"=>$view,"viewtype"=>$viewtype);
	}
	
	public function getObjectView(){ return $this->objview; }
	
	
	private function extractFields($vars=array(),&$form){
		
		$at=$this->getAttributes();
		
		if ($at['type']=='field' && isset($at['params']['assignTo']))
		{
			if (isset($vars[$at['params']['name']]))
				$at['params']['view'][1]['value']=$vars[$at['params']['name']];
			
			$form->field($at['params']);
		}
		
		foreach($this->childNodes() as $child)
		{
			$child->extractFields($vars,$form);
		}
		
		return $this;
	}
	
	
	public function bind($vars=array(),$formName=null)
	{
		/// recorrer todo el objecto y buscar los valores del formulario y asignarles los de $vars
		$at=$this->getAttributes();
		
		if ($formName===null)  $formName=$at['params']['name'];

		if ($at['type']=='field')
		{
			if (isset($this->name)) 
			{
				$formName=$this->getName();	
			}
			
			if (isset($vars[$at['params']['name']]))
			{
				//echo " --> ".$vars[$at['params']['name']];
				//if ($at['params']['view'][0]!='input/submit') //ya no hay submits, hay buttons!
				{
					$at['params']['view'][1]['value']=$vars[$at['params']['name']];
					$this->setAttributes('params',$at['params']);
					//echo "<br/>attributes ".$at['params']['name'].": "._r($vars[$at['params']['name']],true);
				}
			}
		}
		
		foreach($this->childNodes() as $child)
		{
			$child->bind($vars,$formName);
		}
		
		//$this->vars_request=$vars;
		
		return $this;
	}
	
	private function searchModels(&$models=array(),&$lastIndex=null)
	{
		$at=$this->getAttributes();
		
		//echo _r($at);
		
		if (isset($at['params']['assignTo']))
		{
			// assignTo a una columna (tabla.columna o [tabla.columna,tabla.columna])
			if (is_array($at['params']['assignTo']) || preg_match("/\./",$at['params']['assignTo']))
			{
				if (!is_array($at['params']['assignTo'])) 
					$at['params']['assignTo']=array($at['params']['assignTo']);
				
				$CredentialsOK=true;
				
				// si estamos en modo filtro no tenemos en cuenta las credenciales
				// NOTA: el modo filtro lo activa el generador cuando llama a getModelObjects(true)
				if (!FormElement::isFilterMode()){
					////// si el campo tiene credenciales miramos las que tiene el usuario
					///// si resulta no las tiene, 
					if (isset($at['params']['credentials']) && !empty($at['params']['credentials'])){
						//$CredentialsOK=$this->makeCredentials($CredentialsOK,$at['params']['credentials']);
						$s=Session::parseCredentials($at['params']['credentials']);
						eval("\$CredentialsOK=($s);");
					}
				}	
				//echo "<br/>".$at['params']['name'].": ".var_export($CredentialsOK,true);	
				/// si puede "ver" el campo	
											
				if ($CredentialsOK){	
					//echo var_dump($lastIndex);
						
					/// miramos los assignTo que tiene para añadir esa columna a la tabla de modelos
					foreach($at['params']['assignTo'] as $assignTo)
					{
						list($class,$phpname)=explode(".",$assignTo);
						
						/// si el valor se envia (existe la entrada ..[1][value] --> no disabled
						if (isset($at['params']['view'][1]['value'])){
							$ttt=false;
							$valoraco=($at['params']['view'][1]['value']!=null)?$at['params']['view'][1]['value']:null;
							if (isset($at['params']['parameters']['for_through_class'])){
								// se añade a la for_through_class (para posterior tratamiento)
								$models[Form::FOR_THROUGH_CLASS_PREFIX][$class][$phpname]=$valoraco;
								// se añade al referenceClass si esta a true.
								$vtc=$at['params']['parameters']['for_through_class'];
								// si es bool (y cierto) se incluira como model para el saveForm 
								if (is_bool($vtc) && $vtc) $models[$class][$phpname]=$valoraco;
								// si es array servirá sólo para la through_class.
								// si habia un assignTo de ese campo, ya no tendrá efecto!
								else if (is_array($vtc)){
									foreach($vtc as $ivtc){
										$iivtc=explode('.',$ivtc);
										$models[$iivtc[0]][$iivtc[1]]=$valoraco;	
									}
								}
								
							//else // si el valor es ''
							//	$models[$class][$phpname]=($at['params']['view'][1]['value']!=null)?$at['params']['view'][1]['value']:null;
							}else if (!empty($models[$class][$phpname])){
								if (isset($at['params']['view'][1]['internalname'])){
									if (isset($at['params']['name'])) $currentIndex=str_replace(Form::FILTER_PREFIX,'',$at['params']['name']);
									
									if (!is_array($models[$class][$phpname])){
										$valorAnterior=$lastIndex['view'][1]['value'];
										
										$range=isset($lastIndex['view'][1]['range'])?$lastIndex['view'][1]['range']:null;
										if ($range===null)
											$range=isset($lastIndex['parameters']['range'])?$lastIndex['parameters']['range']:'';
										
										$models[$class][$phpname]=array($valorAnterior,$range,Query::RANGE);
									}
									
									if ($valoraco!==null){
										
										$range2=isset($at['params']['view'][1]['range'])?$at['params']['view'][1]['range']:null;
										if ($range2===null)
											$range2=isset($at['params']['parameters']['range'])?$at['params']['parameters']['range']:'';
										
										array_splice($models[$class][$phpname], -1, 0, array($valoraco,$range2));
									}
								}
								
							}
							else if (isset($at['params']['view'][1]['range']) && FormElement::isFilterMode()){	
								if ($valoraco!==null){
									$models[$class][$phpname]=array($valoraco,$at['params']['view'][1]['range'],Query::RANGE);
								}
							}
							else if (isset($at['params']['parameters']['range']) && FormElement::isFilterMode()){	
								//echo _r($valoraco,true)._r($at['params']['parameters']['range']);
								if ($valoraco!==null){
									$models[$class][$phpname]=array($valoraco,$at['params']['parameters']['range'],Query::RANGE);
								}
							}
							else {	
								$models[$class][$phpname]=$valoraco;
							}
						}
						//$lastIndex=str_replace(Form::FILTER_PREFIX,'',$at['params']['name']);
						$lastIndex=$at['params'];
					}
					
				}
			// assignTo a una tabla (sin columna) = es un adminList = tiene una gestión a parte!
			}else { 
				
				// si no hay through_class es la misma tabla assignTo la que hace de through_class!
				if (isset($at['params']['parameters']['through_class']))
					$through=$at['params']['parameters']['through_class'];
				else 
					$through=$at['params']['assignTo'];

					
				$th_class=$through."Peer";
				$th_obj=new $th_class();
				$th_table=$th_obj->getTable();
				$th_cols=$th_obj->getColumns();
				$fks=$th_obj->extractFK($th_cols);
				$pks=$th_obj->extractPK($th_cols);
				//$phpnames==$th_obj->getPHPNames();
				
				//echo _r($fks);die();
				// forzamos a enviar valor nulo, por si nos deseleccionan todas las opciones!
				if (isset($at['params']['view'][1]['value']) && $at['params']['view'][1]['value']!=null)
					$value=$at['params']['view'][1]['value'];
				else $value=null;
				
				if (!isset($models[Form::THROUGH_CLASS_PREFIX][$through])){
					$models[Form::THROUGH_CLASS_PREFIX][$through]=array(
							'values'=>$value,
							'assignTo'=>$at['params']['assignTo']
					);
				
					//echo _r($th_cols);
					
					foreach($pks as $pk){
						if (isset($th_cols[$pk]))	
							$models[Form::THROUGH_CLASS_PREFIX][$through]['pks'][$th_cols[$pk]['phpname']]=null;
					}
					
					foreach($fks as $fk=>$phpname){
						$foreignKey=$th_cols[$fk]['fk'];
						$f=explode(".",$foreignKey);
						$models[Form::THROUGH_CLASS_PREFIX][$through]['fks'][$f[0]][$f[1]]=$th_cols[$fk]['phpname'];
					}
				}else{
					if (isset($models[Form::THROUGH_CLASS_PREFIX][$through]['values']))
						$oldvalues=$models[Form::THROUGH_CLASS_PREFIX][$through]['values'];
					else 	
						$oldvalues=array();
						
					if ($value!==null) 
						$models[Form::THROUGH_CLASS_PREFIX][$through]['values']=array_merge($oldvalues,$value);
				}
			}
		}
		
		foreach($this->childNodes() as $child)
		{
			$child->searchModels($models,$lastIndex);
		}
		
		return $models;
	}
	
	//const EURO_CHAR='&#8364'; // charset
	//const EURO_CHAR='\u20AC'; // unicode
	//const EURO_CHAR='&euro';  // htmlentities
	static private $modeFilter;
	
	static private function isFilterMode(){
		return self::$modeFilter;
	}
	
	public function getModelObjects($filter=false)
	{
		self::$modeFilter=$filter;
		
		$models=$this->searchModels();
		//echo _r($models);
		//die();
		$objects=array();
		foreach($models as $model=>$data)
		{
			if ($model!=Form::THROUGH_CLASS_PREFIX && $model!=Form::FOR_THROUGH_CLASS_PREFIX){	// es un adminlist!
			
				$obj=new $model();
				
				foreach($data as $column=>$value)
				{
					// si el valor es múltiple (array) hacemos un implode
					// (habrá que pasar un parametro adicional para saber como recuperarlo de bd)
					if (is_array($value)) $value=implode(Form::FILTER_SEPARATOR_ARRAY,$value);
					
					//$obj->{$column}=addslashes(utf8_decode($value));
					$obj->{$column}=str_replace("'","\'",utf8_decode($value)); 
				}
				
				$objects[$model]=$obj;
			}else{
				
				$objects[$model]=$data;
			}
		}
		
		//echo _r($objects);die();
		return $objects;
	}
	
	//// crea "where" de select (filtros!)
	public function filterForm($objects=array())
    {
        $str=array();
        foreach($objects as $i=>$object){      // para todos los objetos encontrados
            $tmp=$object->getObjectFilter($this);
			if (!empty($tmp)) $str[]=$tmp;
        }
        return implode(" and ",$str);
    }
	
	public function saveForm($referenceClass,$objects=array(),&$pks=array(),$insert=true)
	{
		$model=array(); //nos guardamos las conexiones para hacer un commit de todas ellas si todo ha ido bien!
		$old=null;
		$ok=array();
		
		$aux=$objects[$referenceClass];
		$mod=$aux->getModel();
		$col=$mod->getColumns();
		
		
		//echo _r($phpnames);die();	
		//echo "aux"._r($aux);
		//die();
		//unset($objects[$referenceClass]);
		//$objects=array_merge(array($referenceClass=>$aux),$objects);
		
		
		$at=$this->getAttributes();
		
		//$delete_for_through_class=array();
		
		if (is_array($at['params']['view']['1']['model_order']))
		{
			$models=array();
			$mode=$insert?'new':'edit';
			if (isset($at['params']['view']['1']['model_order'][$mode]))  // si hay un orden especificado
			{
				$m=$at['params']['view']['1']['model_order'][$mode];
				foreach($m as $mod) // para todos los modelos "ordenados"
				{
					// generamos el nuevo orden de inclusión
					if ($mod==Form::THROUGH_CLASS_PREFIX) $models[$mod]=array();
					else if (isset($objects[$mod])) $models[$mod]=$objects[$mod];
					// borramos la entrada de los objetos
					//$delete_for_through_class[]=$objects[$mod];
					//unset($objects[$mod]);
				}
			}else $models[$referenceClass]=$objects[$referenceClass];  // si no hay orden, solo miramos el de referencia

			//incluimos todos los objetos después de incluir los ordenados
			$models=array_merge($models,$objects); 
			
		}else $models=$objects;
		
		//echo _r($objects);die();
		//foreach($delete_for_through_class as $cur) unset($cur);
		
		// si hay una clase intermedia
		if (isset($models[Form::THROUGH_CLASS_PREFIX])){
			// mirar 
			$keystc=array_keys($models[Form::THROUGH_CLASS_PREFIX]);
				
			foreach($keystc as $tcname) {
				/*
				$tclist=array_keys($models[Form::THROUGH_CLASS_PREFIX][$tcname]['pks']);
				//echo _r($models[Form::FOR_THROUGH_CLASS_PREFIX][$tcname]);
				foreach($tclist as $pk2){
					if (isset($models[Form::FOR_THROUGH_CLASS_PREFIX][$tcname][$pk2])){
						$models[Form::THROUGH_CLASS_PREFIX][$tcname]['pks'][$pk2]=$models[Form::FOR_THROUGH_CLASS_PREFIX][$tcname][$pk2];
					}
				}*/
				
				// mira si se pasan por FOR_THROUGH_CLASS algun parámetro 
				// aunque NO SEAN PKS y lo adjuntamos en pks  
				if (isset($models[Form::FOR_THROUGH_CLASS_PREFIX][$tcname])){
					foreach($models[Form::FOR_THROUGH_CLASS_PREFIX][$tcname] as $ktc=>$vtc){
						$models[Form::THROUGH_CLASS_PREFIX][$tcname]['pks'][$ktc]=$vtc;
					}
				}
			}
		}
		
		// borramos la info de FOR_THROUGH_CLASS porque ya no nos sirve (y además no es un objeto!!!)
		unset($models[Form::FOR_THROUGH_CLASS_PREFIX]);
			
		//echo _r($models);die();
		//echo $referenceClass;
		
		foreach($models as $i=>$object){  	// para todos los objetos encontrados
			
			// extraer la clase del objeto 
			if ($i===Form::THROUGH_CLASS_PREFIX)  		
			{
				reset($object);
				//$obj_class=current($object);	// array que contiene la información
			  foreach($object as $cc=>$obj_class){	
				
				if (!is_array($obj_class['values'])) 
					$obj_class['values']=array(); // si no se envian datos, no hay valores, forzamos array 
				
				$peer=$cc."Peer";		// nombre de la clase Peer
				$mclass=new $peer();			// objeto Peer
				$phpnames=$mclass->getPHPNames();
				$flipphpnames=array_flip($phpnames);
				// creamos un criteria con las FK de la clase intermedia que son PK de la clase  
				// de referencia (estatico).
				
				
				// FALTA HACER CAST DE LOS CAMPOS
				$cclass=new $cc(); // clase para inserts!
				
				//$criteria=array();
				//echo _r($object);die();
				foreach($obj_class['fks'][$referenceClass] as $_fk=>$_data){
					//$cclass->{$_fk}=$models[$referenceClass]->{$_fk};
					$cclass->{$_data}=$models[$referenceClass]->{$_fk};
					
					// las pks de la clase de referencia ya existen porque ya se ha hecho save de ella
					// si el valor es nulo, miramos si en las pks esta el mismo valor.
					if (empty($cclass->{$_data}) && !empty($pks[$_fk])) $cclass->{$_data}=$pks[$_fk];
					//$criteria[$_data]=array("value"=>$cclass->{$_fk});
				}
				
				// buscamos los elementos intermedios que coinciden con la PK de referencia
				// y obtenemos los ids (dinamicos)
				//echo _r($criteria);die();
				//echo _r($cclass);//die();
				//$data2=$mclass->retrieveByColumns($criteria);
				$ref=$referenceClass."Peer";
				$modref=new $ref();
				$method='doSelectJoin'.$obj_class['assignTo'];
				if (is_callable(array($modref,$method)))
					$data2=call_user_func_array(array($modref,$method),$pks);
				
				//echo $ref.'.'.$method._r($data2)._r($pks);die();
				/// cogemos la base de datos y comenzamos transaccion
				if ($old!=$db->getConn()) //si la conexion es diferente (varias bases de datos a la vez!)
				{
					$db=$mclass->getDatabase();
					$db->beginTransaction();
					$ok[]=$db;
				}
				//echo _r($object);die();
				// suponemos que los valores nos llegan separados por FILTER_SEPARATOR_ARRAY 
				// aunque de momento solo tenemos una PK por tabla!
				try{
					$yaestaban=array();
					
					foreach($data2 as $cur){
						// values: valor de fk (puede ser que tenga más de una en una tabla)
						$values=array();
						
						//echo _r($obj_class['fks'][$obj_class['assignTo']])._r($phpnames);die();
						// recogemos sólo las otras fk de la tabla de destino (valores)
						
						$currentObject=new $cc;
						//$currentObject=new $cclass;
						
						//echo $cc;						
						//echo _r($obj_class['fks'][$obj_class['assignTo']]);
						
						foreach($obj_class['fks'][$obj_class['assignTo']] as $key=>$cur2){ 
							//$values[]=$cur->{$cur2};
							$values[]=$cur[$flipphpnames[$cur2]];
						}
						
						
						foreach($cur as $cur1=>$cur2){
							if (isset($phpnames[$cur1])) $currentObject->{$phpnames[$cur1]}=$cur2;
						}
						
						//echo _r($obj_class['fks'][$obj_class['assignTo']])._r($values);die();						
						$valor=implode(Form::FILTER_SEPARATOR_ARRAY,$values);
							
						// si estaba en base de datos y ahora no está entre los valores enviados, la borramos
						//echo "<br/>".$valor._r($obj_class['values']);
						if (!in_array($valor,$obj_class['values'])){ 
							//echo 'delete'._r($currentObject);
							$currentObject->delete();
						}else $yaestaban[]=$valor;
						
					}
					
					/// extraemos las que ya estaban en la base de datos de las que envio, para sólo
					/// hacer inserts de las nuevas
					$inserts=array_diff($obj_class['values'],$yaestaban);
					//echo _r($inserts)._r($yaestaban);die();
					foreach($inserts as $cur){
						$ctemp=clone $cclass;
						/*
						// puede ser que alguna fk sea tambien pk y por tanto se tenga que añadir tambien
						// normalmente será un valor de la clase de referencia
						$pkstemp=$ctemp->getPKs(true); // para todas las PK con hash phpname
						
						foreach($pkstemp as $key=>$cur2){
							// si el valor del criteria no es nulo, añadimos al select ese valor
							if (!empty($criteria[$key])) $ctemp->{$key}=$criteria[$key]['value'];
						}
						*/
						//echo $cur._r($obj_class['fks'][$obj_class['assignTo']]);die();
						// las otras fk de la tabla de destino tendran los valores de la select anterior
						
						$curvalues=explode(Form::FILTER_SEPARATOR_ARRAY,$cur);
						foreach($obj_class['fks'][$obj_class['assignTo']] as $key=>$cur2) {
							$ctemp->{$cur2}=current($curvalues);
							next($curvalues);
						}
						
						// Añadir al query las pks que no esta en el through_class.
						// Estos valores se pueden pasar mediante el atributo for_through_class
						foreach($obj_class['pks'] as $key=>$cur2){ 
							
							// los campos autonumericos tambien se deben incluir
							// para campos autonumericos automaticos (como AUTONUMERIC EN Mysql)
							if ($ctemp->getModel()->getDatabase()->addAutonumericColumn($cur2)){
								// para crear el objeto con el attributo a nul.
								$ctemp->{$key}=$cur2;
							}
						}
						
						$error="";
						//echo _r($obj_class['fks'])._r($ctemp);die();
						$ctemp->save($error,null,null,$this,strtolower($cc));
						//&$error=false,$forceInsertUpdate=null,$l=null,$attribs=null,$refClass=null
					}
					
					//echo _r($inserts);die();
				}catch(Exception $e){	
						// ha petado, tenemos que hacer rollback de la transaccion
						$error=$obj_class.": ".$e->getMessage();
						//echo _r($e->getTrace());
						$db->rollBack();
						break;
				}
				$old=$db->getConn(); // nos guardamos la conexion actual
			  }
			}else {
				
				$class=get_class($object); // objeto real
				$modelclass=$class."Peer";		
				$model=new $modelclass();		// crear nuevo peer
				
				$db=$model->getDatabase();		// y extraer db para hacer transaccion
				
				if ($old!=$db->getConn()) //si la conexion es diferente (varias bases de datos a la vez!)
				{
					$db->beginTransaction(); 	// nueva transaccion
					$ok[]=$db;					// nos la guardamos para, si todo ha ido bien, hacer commit de todas a la vez.
				}
					
				try{
					// al save le pasamos el modelo, que funciona con transacciones 
					//echo $referenceClass.'='.$class.'!!!!!!'._r($object,true);
					
					/// $insert=false: forzamos a que sea update, pero solo para la clase de referencia
					$obj2=$object->save($error,($referenceClass!=$class)?null:$insert,$model,$this,$referenceClass);	
					//echo "aaa"._r($obj2);//die();
					// si la clase es igual que la de referencia, nos guardamos las pks
					if ($referenceClass==get_class($obj2)){
						$pks=$obj2->getPKs(true); 
						//echo "bbbb"._r($obj2)._r($pks);
					}
					//die();
					//// si ha habido un error en la sql saltamos una CubeException
					if ($error) throw new CubeException($error,1);
					
				}catch(Exception $e){	
						// ha petado, tenemos que hacer rollback de la transaccion
						//echo $e->getTraceAsString();	
						$error=$class.": ".$e->getMessage(); //._r($e->getTrace());
						$db->rollBack();
						break;
				}
				$old=$db->getConn(); // nos guardamos la conexion actual
			}
		}
		//die();
		// si no ha habido ningun error hacemos commit de todas las bases de datos!
		if (!$error) foreach($ok as $cur) $cur->commit(); 
		
		//echo _r($pks);
		//die();
		// devolvemos error
		return $error;
	}
	
	public function searchParameters($itemName){
		// para los casos en que el nombre de columna (generator) no coincide con el nombre assignTo
		//  p.ej   FILTRO_CIP: 
		//    assigTo: tabla.cip
		$item=$this->getItem();
		
		if (isset($item['params']['assignTo'])){
			
			if (!is_array($item['params']['assignTo'])) $ops=array($item['params']['assignTo']);
	   		else $ops=$item['params']['assignTo'];
	
	   		foreach($ops as $cur){
	    		if (strtoupper($cur)==strtoupper($itemName)){ // itemname es clase.phpname 
	     			return $this->getParameters();
	    		}
	   		}
	  	}
	
	  	foreach($this->childNodes() as $child)
	  	{
	   		$ret=$child->searchParameters($itemName);
	   		if ($ret!==null) return $ret;
	  	}
	}
	
	public function search($itemName,$allAttributes=true){
		
		$at=$this->getAttributes();
		
		$ret="-";
		
		if ($at['params']['name']==$itemName) 
		{
			if ($allAttributes) return $at; 
			return $at['params']['view'][1];
		}
		else{
			foreach($this->childNodes() as $child)
			{
				$ret=$child->search($itemName,$allAttributes);
				if ($ret!==null) return $ret;
			}
		}
	}
	
	public function searchAssignTo($itemName,$allAttributes=true){
		
		$at=$this->getAttributes();
		$ret=array();
		
		if (isset($at['params']['view'][1]['internalname']) && isset($at['params']['assignTo']) && $at['params']['assignTo']==$itemName) 
		{
			if ($allAttributes) return $at; 
			return $at['params']['view'][1];
		}
		else{
			foreach($this->childNodes() as $child)
			{
				$aux=$child->searchAssignTo($itemName,$allAttributes);
				if (!empty($aux)) $ret[]=$aux;
			}
			return $ret;
		}
	}
	
	private function makeCredentials(&$passCredential=false,$credentials=array(),&$creden=array()){
		// si $passCredential==true, queremos filtrar por credenciales (searchModels), excluyendo campos del modelo.	
		// si $passCredential==false, queremos generar las condiciones de credenciales en la vista (render)
		
		if (!empty($credentials)) 
		{
			foreach($credentials as $cred) 
	 		{
	 			if (preg_match("/^!(.*)/",$cred,$arg)){
	 				$passCredential=$passCredential && !Session::hasCredential($arg[1]);
	 				$creden[]="!Session::hasCredential('{$arg[1]}')";
	 			}
	 			else {
	 				$passCredential=$passCredential && Session::hasCredential($cred);
	 				$creden[]="Session::hasCredential('{$cred}')";
	 			}
			}
		}
		else return !$passCredential;
				
		//echo "<br/>".$name.": ".print_r($creden,true)." === ".var_export($passCredential,true);
		return $passCredential;
	}
	
	public function render(&$str='',$formName='',$vars=array(),&$valid=true,$first=true,$mode="")
	{
		$isConsole=(Controller::getInstance()===null);
		$my=$this->getAttributes();
		//$str.= "/* \n"._r($my,true)."\n */";
		$passCredential=true;
		$endPassCredential="";
		if (isset($my['params']['credentials']) && !empty($my['params']['credentials'])) 
		{
			$passCredential=false;
			$s=Session::parseCredentials($my['params']['credentials']);
			if (!$isConsole) eval("\$passCredential=($s);"); /// se utiliza en generacion onthefly
			$str.="\n<?php if (".$s."){ ?>\n";
	 		$endPassCredential="<?php \n} //end Credentials ?>\n";
		 }
		$params=$my['params']['view'][1];
		//echo '<br/>'.(isset($params['internalname'])?$params['internalname']:'-').': '.(isset($s)?$s:'-');
		if (isset($my['params']['parameters'])) $params=array_merge($params,$my['params']['parameters']);
		
		if ($first) 
		{
			if (isset($params['internalname'])) 
			{
				$str.="<?php /* -------------- ".$params['internalname']." ------------------ */ ?>\n";	
				//$params['internalname']=$this->getName();
			}
			else  // para elementos que no cuelguen de un formulario (Form::renderElements)
			{
				$mtime = substr(str_replace(' ','',microtime()),2); 
				$params['internalname']="form".$mtime;
				//$params['internalname']="form".md5(rand().microtime());
				
				$this->setName($params['internalname']);
			}
				
			if (Session::is_setFlash($this->getName()."_send___")) Session::un_setFlash();
			
		}
		else $params['internalname']=$formName."[".$my['params']['name']."]";
		//$requestValue=isset($vars[$params['internalname']])?$vars[$params['internalname']]:null;
		
		$requestValue=(isset($vars[$formName][$my['params']['name']]))?$vars[$formName][$my['params']['name']]:null;
		
		//echo $my['params']['name']." "._r($requestValue,true);
		if (!isset($params['value'])) $params['value']=$requestValue;
		
		$params['mode']=$mode;
		if (isset($my['params']['assignTo'])) $params['assignTo']=$my['params']['assignTo'];
		
		switch($my['type'])
		{
			case 'texttable':
			case 'textgroup':
			case 'textelement':
			case 'textmenu':
			case 'textrow':
							$string=$my['params']['name'];
							$string=preg_replace("/_echo\((.*)\)/","Viewer::_echo('$1')",$string);
							$string=preg_replace("/_title\((.*)\)/","Viewer::title('$1')",$string);
			case 'text':				
							if (!isset($string)){ //$string=parseText($my['params']['name']);
								$string=$my['params']['name'];
								$string=preg_replace("/_echo\((.*)\)/","<?php echo Viewer::_echo('$1');?>",$string);
								$string=preg_replace("/_title\((.*)\)/","<?php echo Viewer::title('$1');?>",$string);
							}
							else if ($string!=$my['params']['name']) $string='<?php echo '.$string.' ?>'; 
							
							
							$str.=$string."\n";
							if (!$passCredential){$str.=$endPassCredential;return '';}

							return $string;
							
			case 'menugrp': $menus_data='';
							
							foreach($this->childNodes() as $child)
							{
								$menu_data[]=$child->render($str,$formName,$vars,$valid,false);
							}
							
							$string=$this->var_exp($menu_data);
							
						
							$string=preg_replace("/'_echo\((.*)\)'/","Viewer::_echo('$1')",$string);
							$string=preg_replace("/'_title\((.*)\)'/","Viewer::title('$1')",$string);
							//if ($bool!==false) $item['label']=Viewer::_echo($args[1]);
							
							
							$str.="<?php \$menu_data=".$string.";\n";
							$str.="echo Viewer::submenu(\$menu_data,'actions');\n?>";
							
							//$titulo=Viewer::title(Viewer::_echo('actions'));
							$titulo="";
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return Viewer::submenu($menu_data,'actions',$titulo);
							
			case 'menu':	
							$params=$my['params']['view'][1];
							
							$item=array("selected"=>false);
							if (isset($params['action'])) $item['action']=$params['action'];
							if (isset($params['js']['onclick'])) $item['onclick']=$params['js']['onclick'];
							
							if (isset($params['value']))  $item['label']=$params['value'];
							else if (isset($my['params']['label'])) {
								if (is_array($my['params']['label'])) $item['label']=$my['params']['label']['text'];
								else $item['label']=$my['params']['label'];
							}
							else $item['label']="item";
							
							/// assignTo para añadir las pk!
							
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return $item;	
			case 'button':  
			case 'field':	
							
							//$str.="<!-- ".var_export($my['params']['parameters'],true)." -->\n";
							if (isset($my['params']['validator']['required']) && $my['params']['validator']['required'] &&
								!preg_match("/FormFilter$/i",$formName)){
								$params['class']="required";
							}
							
							
							if (isset($my['params']['parameters']['through_class'])){
								$params['parameters']=$my['params']['parameters'];
								if (!isset($my['params']['parameters']['query']) && !isset($my['params']['parameters']['peerMethod'])) 
									$params['parameters']['peerMethod']='doSelectAll';
							}
							
							if (isset($params['action'])){
								$count=0;
								$action_=preg_replace("/_field\(([^\)]*)\)|:([^\/?$&]*)/","'.(isset(\$vars['values']['$1$2'])?\$vars['values']['$1$2']:'').'",$params['action'],-1,$count);
								
								if ($count>0) {
									$params['action']=$action_;
								}
								// formato var_export 'key' => 'value',\n
								$action="'action' => '".$params['action']."',\n";
							}else $action='';
							
							$temp=$params;
							unset($temp['value']);
                            unset($temp['action']);

                            $a=$this->var_exp($temp);
							$a=preg_replace("/'_echo[(](.*)[)]'/","Viewer::_echo('$1')",$a);
							$a=preg_replace("/'_title[(](.*)[)]'/","Viewer::title('$1')",$a);
							$a=preg_replace("/'_field\((.*)\)'/","(isset(\$vars['values']['$1'])?\$vars['values']['$1']:'')",$a);
							
							if (isset($params['value'])) {
								$valuexxx="'".addslashes($params['value'])."'";	
							}else {
								$valuexxx="(isset(\$vars['values']['".$my['params']['name']."'])?\$vars['values']['".$my['params']['name']."']:'')";         
                            }
							
                            $pars=substr($a,0,strlen($a)-1)."{$action}'value'=>".$valuexxx.")";
                            
							//echo _r($pars);
							
							
							$str.="<?php /* -------------- ".$params['internalname']." ------------------ */ ?>\n";
							
							if ($my['params']['view'][0]=='input/hidden') //caso especial hidden 
							{
								if (isset($my['params']['label'])) unset($my['params']['label']);	// no debe tener label
								if (isset($my['params']['validator'])) unset($my['params']['validator']); //no debe tener validador
							} 
							
							if (isset($my['params']['validator'])){
								//eval("");
								$name=$params['internalname'];
								$parseError=<<<EOF
										\$errors=Session::getInstance()->getFlash('{$name}');
								    	if (is_array(\$errors)) \$errors=implode(', ',\$errors);
								    	if (\$errors!==null) { 
								    		\$class='error';
								    		\$errors=' : <div class="message">'.\$errors.'</div>';
								    	}else{
								    		\$class='formparagraph_inline';
								    		\$errors='';
										}
EOF;
								eval($parseError);
								$str.="<?php ".$parseError." ?>\n";
							}
							
							
							$label=null;
							if (isset($my['params']['label'])){
								
								if (is_array($my['params']['label'])){
									/// recogo todos los parámetros
									$label=$my['params']['label'];
									
									/// compatibilidad con anteriores versiones
									if (isset($my['params']['label']['text'])){
										$label['title']=$my['params']['label']['text'];
										unset($my['params']['label']['text']);
									}
									
									if (isset($my['params']['label']['align'])){
										switch($my['params']['label']['align']){
											case 'vertical': 	$label['position']='top';
																$label['align']='middle';break;
											case 'horizontal': 	$label['position']='left';
																$label['align']='middle';
																break;
											default: $label['position']=isset($label['position'])?$label['position']:'left';break;
										}
									}
								}else{
									$label=array(	'title'=>$my['params']['label'],
													'position'=>'left',
													'align'=>'middle');
								}
								
								$parlab=var_export($label,true);
								
								if ((isset($my['params']['validator']))){
									$parlab=preg_replace("/'title' => '(.*?(?<!\\\\))'/","'title' => '$1'.\$errors",$parlab);
									$parseLabel="'label'=>".$parlab.",";
								}else $parseLabel="'label'=>".$parlab.",";
								
							}else if (isset($my['params']['validator'])){
								
								$label=array(	'title'=>'',
												'position'=>'left',
												'align'=>'middle');
								
								$parlab=var_export($label,true);
								$parlab=preg_replace("/'title' => '([^']*)'/","'title' => '$1'.\$errors",$parlab);
								$parseLabel="'label'=>".$parlab.",";
							}else $parseLabel='';	
							
								
							
							$parseField="Viewer::view('canvas/column',array({$parseLabel}	
							 					'render'=>Viewer::view('{$my['params']['view'][0]}',$pars)";
							if (isset($my['params']['validator'])) $parseField.=",'class' => \$class";
							$parseField.="))";
							
							$str.="<?php echo ".$parseField."; ?>\n";
							eval("\$field={$parseField};");
							//echo '<br/>'.$params['internalname'];var_dump($passCredential);
							//$str.='/* '.var_export($passCredential,true).' */';
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return $field;
			case 'form':	//echo "<i>"._r($my['params'])."</i>";
							$name=$this->getName();
							///// falta pasar el request por str!
							if (!Session::is_setFlash($name."_request___"))
							{
								$request=Request::getInstance();
								$vars_request=$request->get();
							}
							else 
								$vars_request=unserialize(base64_decode(Session::getFlash($name."_request___")));
							/*
							$va=array();
							$this->vars_request=$vars_request;
							foreach($vars_request as $k=>$v)	
							{
								if (preg_match("/^{$name}_/",$k)) 
								{
									// mirar validadores
									$va[$k]=$v;
								}
							}
							*/
							//echo _r($my['params']);	
								
								
							$str.="<?php ob_start(); ?>";
							$body='';
							$name=$this->getName();
							
							$errors=Session::getInstance()->getFlash($name);
							$info=Session::getInstance()->getFlash($name."_info");
							
							$str.="\n<?php echo Session::getInstance()->getFlash('{$name}_info'); ?>\n";
							$str.="\n<?php echo Session::getInstance()->getFlash('{$name}'); ?>\n";
							$str.="<?php /* -------------- INIT ".$name." ------------------ */ ?>\n";
							foreach($this->childNodes() as $child)
							{
								$body.=$child->render($str,$name,$vars_request,$valid,false,$this->getMode());
							}
							
							if ($this->open_section>0) {
                                    $body.="</div></div>";
                                    $str.="</div></div><div class=\"minicontentWrapper\"></div>";
                            }
                            
							$my['params']['view'][1]['body']=$body;
							
							/// formulario no enviado
							$sendForm=base64_encode(serialize($this));
							Session::getInstance()->setFlash("{$name}_send___",$sendForm);
							
							$temp=$my['params']['view'][1];
							
							$straction="'action' => Route::url((isset(\$vars['params']['action']))?\$vars['params']['action']:'".$temp['action']."')";
							
							unset($temp['action']);
							unset($temp['body']);
							$a=$this->var_exp($temp);
							
							$pars=substr($a,0,strlen($a)-1)." 'body'=>\$body_{$name},\n  {$straction})";
							$str.="\n<?php /* -------------- END ".$name." ------------------ */ ?>\n";		
							$str.=	"\n<?php \$body_{$name}=ob_get_clean();\n".
									"\necho Viewer::view('".$my['params']['view'][0]."',".$pars.");?>\n";
							
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return $info.$errors.Viewer::view($my['params']['view'][0],$my['params']['view'][1]);	
							
			
			case 'group':	$group='';
							
							foreach($this->childNodes() as $child)
							{
								$group.=$child->render($str,$formName,$vars,$valid,false,$mode);
							}
							//$str.=$group;
							
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return $group;
			case 'row':	
							$table="\n<div class=\"row\">";
							$str.="\n<div class=\"row\">";
							foreach($this->childNodes() as $child)
							{
								$table.="\n".$child->render($str,$formName,$vars,$valid,false,$mode);
								
							}
							$table.="\n</div>";
							$str.="\n</div>\n";
							
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return $table;
			
			case 'table':	$table="\n<table class=\"formtable\"><tr>";
							$str.="\n<table class=\"formtable\"><tr>";
							foreach($this->childNodes() as $child)
							{
								$str.="\n<td>";
								$table.="\n<td>".$child->render($str,$formName,$vars,$valid,false,$mode)."</td>";
								$str.="\n</td>";
							}
							$table.="\n</tr></table>";
							$str.="\n</tr></table>\n";
							
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return $table;
				
			case 'layout':  //echo "<b>"._r($my['params'])."</b>";
							//echo "<b>"._r($this->childNodes())."</b>";
							
							$children=array();
							foreach($this->childNodes() as $child)
							{
								$children[]=$child->render($str,$formName,$vars,$valid,false,$mode);
							}
							
							$str.="\n<?php echo Viewer::layout('".$my['params']."',".implode(",",$children)."); ?>";
							
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return call_user_func_array(array('Viewer','layout'),array_merge(array($my['params']),$children));
			case 'view':  
							$children='';
							foreach($this->childNodes() as $child)
							{
								$children.=$child->render($str,$formName,$vars,$valid,false,$mode);
							}
							
							//echo _r($my);
							$str.="\n<?php echo Viewer::view('".$my['params']['view'][0]."',".$this->var_exp($my['params']['view'][1])."); ?>"; //GENERATOR
							
							if (!$passCredential){$str.=$endPassCredential;return '';}
							return call_user_func_array(array('Viewer','view'),array($my['params']['view'][0],$my['params']['view'][1]));

		}
		
	}
}	

	class Form{
		private $name;
		private $action;
		private $method;
		private $enctype;
		private $root;
		private $request;
		
		const FILTER_PREFIX='__filterform__';
		const THROUGH_CLASS_PREFIX="__through_class__";
		const FOR_THROUGH_CLASS_PREFIX="__for_through_class__";
		const FILTER_SEPARATOR_ARRAY='##,##';
		//private $objform=array();	// tendremos el objeto formulario (columnas con vista y validador)
		
		public function __construct($name,$action='',$method='POST',$enctype=null,$model_order=null) //'multipart/form-data'
		{
			$this->name=$name;
			$this->action=$action;
			$this->method=$method;
			$this->enctype=$enctype;
			$this->modelOrder=$model_order;
		}
		
		static public function customValidator($validator,$function){
			FormElement::extendValidator($validator,$function);
		} 
		
		static public function create($name,$action='',$method='POST',$enctype=null,$model_order=null)
		{
			$form=new Form($name,$action,$method,$enctype,$model_order);
			
			$root=FormElement::getInstance('FormElement',
											array(	'type'=>'form',
													'params'=>array(
														'name'=>$form->name,
														'view'=>array('input/form',array(
																'name'=>$form->name,
																//'body' => $form_body,
																'internalid'=>$form->name,
																'internalname'=>$form->name,
																'method'=>$form->method,
																'action'=>Route::url($form->action),
																//// model_order: orden de los modelos para update/insert cuando hay
																//// más de uno modelon en un formulario (por defecto null)
																'model_order'=>$form->modelOrder))
											)));
			$root->setName($form->name);
			
			$form->setRoot($root);
			//Log::_add(__METHOD__,"Create form '{$name}'.","form",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','form',array(
							'message' =>"Create form '{$name}'.",
							'type'=>'form',
							'error'=>Log::SUCCESS,
							'class'=>__CLASS__,
							'method'=>__METHOD__));
			
			return $form;	
		}
				
		public function setRoot($root)
		{
			$this->root=$root;
		}
		public function getRoot()
		{
			return $this->root;
		}
		
		/////////// para generator.yml /////////
		
		private $render=array();
		private $fields=array();  // elementos en el formulario
		private $modelOrder=array();
		
		public function getRender() {return $this->render; }
		public function setRender($render) {$this->render=$render; }
		public function setModelOrder($order=array()){$this->modelOrder=$order;}
		public function getModelOrder(){return $this->modelOrder;}
		
		public function setFields($f) {$this->fields=$f; }
		public function getName() {return $this->name; }
		
		const CUBE_GROUP_SEPARATOR=".";
		const CUBE_TABLE_SEPARATOR="|";
		const CUBE_MENU_SEPARATOR="-";
		static private $count=0;
		
		static function load($generator,$directory='modules',$render='new',$forcePlugin=false)
		{
			$params=array(	'name'=>'object'.microtime(),
							'method'=>'POST',
							'enctype'=>'text/html',
							'action'=>'');
		
			$route=Controller::getInstance()->getRoute();
			$generators=Site::getInstance()->getConfiguration('generators');
			
			
			/// diferenciar entre (1) generador de modulo en aplicacion, (2) generador de modelo en aplicacion
			///                   (3) generador de modelo general, (4) generador de modulo de plugin, (5) generador de modelo de plugin
			
			if ($forcePlugin!==false){
				$modulePlugin=explode("/",$forcePlugin);
				
				if (isset($modulePlugin[1])){ // nos pasan el nombre del plugin/módulo
					$plugin=$modulePlugin[0];
					$route['module']=$modulePlugin[1];
				}else{ // nos pasan módulo. debemos coger el primer plugin que contenga ese módulo (estan ordenados por prioridad)
					foreach($generators as $l=>$h){
						$reg="/^plugins\/([^\/]*)\/{$directory}\/{$forcePlugin}\/{$generator}$/";
						if (preg_match($reg,$l,$args)) {$ruta=$l;break;}
					}
				}
			}
			else{
				$plugin=Route::PluginMode();
			}
			
			if (!isset($ruta)){ // definida antes?
				if ($directory=='modules'){
					if ($plugin===false) $ruta='apps/'.$route['app']."/modules/".$route['module']."/".$generator;
					else						$ruta='plugins/'.$plugin."/modules/".$route['module']."/".$generator;
					$model='';
				}else if ($directory=='model'){
					if ($forcePlugin===false)
						$ru=explode("/",$generator);
					else{
						$ru=array($modulePlugin[1],$generator);
					}

					if (count($ru)==1)
						throw new CubeException("'Generator name not found in ".(($plugin===false)?'module':'plugin')." {$directory}.", 1);

					$model=$ru[0];$generator=$ru[1];
					if ($plugin===false) $ruta='apps/'.$route['app']."/model/".$model."/".$generator;
					else $ruta='plugins/'.$plugin."/model/".$model."/".$generator;

					if (!array_key_exists($ruta,$generators))  $ruta='model/'.$model."/".$generator;
				}
			}
			//echo $ruta._r($generators)	;die();

			if (array_key_exists($ruta,$generators))
			{
				$file=$generators[$ruta];
				
				$data=Site::getInstance()->importFile($file);
				if (is_array($data[$generator]['form']['params'])) $params=array_merge($params,$data[$generator]['form']['params']);
				
				$data1=(isset($data[$generator]['fields']))?$data[$generator]['fields']:array();
				
				if ($render=='filters') $rend=$data[$generator]['list']['filters'];
				else $rend=$data[$generator]['form']['render'][$render];
				
				//echo _r($rend);die();
				$rows=explode("\n",$rend);
				$data2=array();
				foreach($rows as $i=>$row){
					$temp=array();
					self::renderElement($row,$temp,true);
					$a=current($temp);
					if (!empty($a)){
						
						if (is_array($a) &&  !preg_match("/^__row_/",key($a)))
							$data2['__row_000'.$i]=$a; 
						else if (!is_array($a) && !preg_match("/^@([^@]*)@$/",$a)) 
							$data2['__row_000'.$i]=$temp;
						else 
							$data2=array_merge($data2,$temp);
					}
				}
				//echo _r($data2);die();
				
				if (isset($data[$generator]['form']['model_order'])) $model_order=$data[$generator]['form']['model_order'];
				else $model_order=null;
				
				$form=self::create($params['name'],$params['action'],$params['method'],$params['enctype'],$model_order);
				
				$form->render=$data2;
				$form->fields=$data1;
				
				//echo _r($form);
				
				return $form->render();
			}
			else throw new CubeException("'{$model}/{$generator}' does not exist in ".((Route::PluginMode()===false)?'module':'plugin')." {$directory}.", 1);
		}	
		
		private $mode;
		
		public function render($mode="")	
		{
			$root=$this->getRoot();
			$root->setMode($mode);	// nos guardamos el modo del render (new/edit)
			$this->mode=$mode;
			
			$this->renderField($this->getRender(),'',$root);
			
			//Log::_add(__METHOD__,"Render form '{$this->getName()}'.","form",__CLASS__,Log::SUCCESS);
			Controller::triggerHook('debug','form',array(
							'message' =>"Render form '{$this->getName()}'.",
							'type'=>'form',
							'error'=>Log::SUCCESS,
							'class'=>__CLASS__,
							'method'=>__METHOD__));
			
			return $root;
		}
		
		private function renderField($element,$key='',&$item,$type=null,$elementbr=true)
		{
			
			if (is_array($element))
			{
				if (preg_match("/^__table_(.*)/",$key)) {$type="table";}
				else if (preg_match("/^__row_(.*)/",$key)) {$type="row";}
				else if (preg_match("/^__group_(.*)/",$key)) {$type="group";}
				else if (preg_match("/^__menu_(.*)/",$key)) {$type="menu";}
				else {$type="element";}
					
				foreach($element as $k=>$elem)
				{
					$element=false;
					if ($item==null) $item=$this->root;
					
					if (preg_match("/^__table_(.*)/",$k)) {$temp=$item->table();}
					else if (preg_match("/^__row_(.*)/",$k)) {$temp=$item->row();}
					else if (preg_match("/^__group_(.*)/",$k)) {$temp=$item->group();}
					else if (preg_match("/^__menu_(.*)/",$k)) {$temp=$item->menugrp();}
					else {
						$temp=$item;
						$elementbr=true;
					}
					//echo "<br/>$k: $type";
					$this->renderField($elem,$k,$temp,$type,$elementbr);
				}
			}
			else 
			{
				if ($item==null) $item=$this->root;
				
				if (preg_match("/{([\*\$]{0,1})([^}]*)}/i",$element,$args) && isset($this->fields[$args[2]]))
				{
					$nameField=$args[2];
					
					if (isset($this->fields[$args[2]]['assignTo'])) /// si el campo está asignado a un campo de bd, añadimos la tabla
					{
						if (is_array($this->fields[$args[2]]['assignTo'])) $n=explode(".",$this->fields[$args[2]]['assignTo'][0]);
						else  $n=explode(".",$this->fields[$args[2]]['assignTo']);
						
						$fieldName=strtolower($n[0]).".".$nameField;
					} 
					else $fieldName=$nameField;
					
					if ($this->mode=="filter") $fieldName=self::FILTER_PREFIX.$nameField;
					//echo _r($this->fields[$nameField]);
					
					$this->fields[$nameField]['view'][1]['internalname']=$this->name."[".$fieldName."]";
					
					/*
					EJEMPLO:
					$f2->field(array(	
								//'label'=>'Id',
								'name'=>'id',
								'view'=>array('input/text',array('js'=>'style="width:30px;"','disabled'=>true,'value'=>13)),
								'validator'=>array('required'=>false,'numeric'=>true),
								'credentials'=>array(),
								'assignTo'=>array('Id','Metadata')
						));
					*/
					
					$props=array(	'name'=>$fieldName,
									'view'=>array(trim($this->fields[$nameField]['view'][0]),$this->fields[$nameField]['view'][1]));
					
					if (isset($this->fields[$nameField]['label'])) 
					{
						//if (isset($i18n)) $props['label']=
						$props['label']=$this->fields[$nameField]['label'];
					}
					if (isset($this->fields[$nameField]['validator'])) $props['validator']=$this->fields[$nameField]['validator'];
					if (isset($this->fields[$nameField]['credentials'])) $props['credentials']=$this->fields[$nameField]['credentials'];
					if (isset($this->fields[$nameField]['assignTo'])) $props['assignTo']=$this->fields[$nameField]['assignTo'];
					if (isset($this->fields[$nameField]['parameters'])) $props['parameters']=$this->fields[$nameField]['parameters'];
					
					switch($args[1])
					{
						case '*': $item->menu($props); break; 
						case '$': $item->text($props); break;
						default:  
									if ($element && $type!='group') {$props['br']=true;$props['elemSep']=true;}
									$item->field($props);break;
					}
				}		
				else 
				{
					if (preg_match("/^__separator_(.*)/",$key))
					{
						//if ($type=='table' || $type=='group') $item->hseparator(trim($element,'#'));
						if ($type=='row' || $type=='group') $item->hseparator(trim($element,'#'));
						else $item->vseparator(trim($element,'#'));
					}
					else if (preg_match("/^__section_(.*)/",$key))
					{
						$item->section(trim($element,'@'));
					}
					else {
						//echo "\n{$key} ({$type}): {$element}";
						$item->text($element,$type);
					}
				}
			}
		} 
		
		static public function renderElement($row,&$data=array(),$principalRow=false)
		{
			$r=trim($row);
			$fields=explode(self::CUBE_TABLE_SEPARATOR,trim($r));
			
			if (count($fields)>1) // if subdivisions
			{
				$temp=array();
				
				foreach($fields as $i=>$field) // render elements in other subdivision
				{
					if ($field!='')	self::renderElement($field,$temp);
				}
				//$data['__table_'.self::$count]=$temp;
				
				/// new row
				$data['__row_'.self::$count]=$temp;
				self::$count++;
			}
			else {
				
				$fields=explode(self::CUBE_MENU_SEPARATOR,trim($r));
				if (count($fields)>1)  // subdivisions of menu
				{
					$temp=array();
					
					foreach($fields as $i=>$field)
					{
						if ($field!='')	self::renderElement($field,$temp);
					}
					$data['__menu_'.self::$count]=$temp;
					self::$count++;
				}
				else { // others
					$fields=explode(self::CUBE_GROUP_SEPARATOR,$row);
					if (count($fields)>1) // subdivisions of group ('.' linker)
					{
						$temp=array();
						
						foreach($fields as $i=>$field)
						{
							self::renderElement($field,$temp);
						}
						$data['__group_'.self::$count]=$temp;
						self::$count++;
					}
					else // no subdivision = final element
					{
						if (preg_match('/\#([^\#]*)\#/',$r)) // separator 
						{
							$data['__separator_'.self::$count]=$r;
							self::$count++;
						}
						else if (preg_match('/@([^@]*)@/',$r)) // section
						{
							$data['__section_'.self::$count]=$r;
							self::$count++;
						}
						else 
						{
							$data[]=$r;
						}
					}
				}
			}
		}
	}
?>