<?php
/*
class User
{
	public function getURL() {echo "url";}
	public function getIcon($icon) {echo "/img/icon/user.png";}
	public $admin;
	public $siteadmin;
}
*/ 
	
class Request
	{
		static private $req;
		static private $instancia;
		
		static public function createInstance($request=null) {
	       if (self::$instancia == NULL) {
	          self::$instancia = new Request($request);
	       }
	   		return self::$instancia;
		}
		
		static public function getInstance() {
	       return self::$instancia;
		}
		
		public function __construct($r)
		{
			self::$req=$r;
		}
		
		public function get()
		{
			return self::$req;
		}
		
		public function setParameter($name,$value)
		{
			self::$req[$name]=$value;
		}
		
		public function getParameter($name, $default=null)
		{
			return (isset(self::$req[$name]))?self::$req[$name]:$default;
		}
		
		public function __get($name) {
	        if ($name==null) return self::$req;
			if (isset(self::$req[$name])) return self::$req[$name];
			return null;
	    }
	
	    public function __set($name,$value) {
	        self::$req[$name]=$value;
	    }
	    
	    /*
	    /////// send request grid
	    public function getGridVars($name)
	    {
	    	if (Session::is_setFlash($name."_requestGrid___"))
	    	{
	    		$req=unserialize(base64_decode(Session::getFlash($name."_requestGrid___")));
	    		return isset($req[$name])?$req[$name]:array();
	    	}
	    	return isset(self::$req[$name])?self::$req[$name]:array();
	    }
	    
		public function setGridVars($name)
		{
			if (!Session::is_setFlash($name."_requestGrid___"))
			{
				$vars_request=self::$req;
				Session::setFlash($name."_requestGrid___",base64_encode(serialize($vars_request)));
			}
		}
		*/
	    
	    public function getFormVars($form=null,$filter='')
	    {
	    	$f=explode("/",$form);
	    	if (count($f)>1) $name=$f[0].ucfirst($f[1]);else $name=$f[0];
			if (!preg_match("/Form".$filter."$/",$name)) $name.="Form".$filter;
	    	
			
			
			//$f=explode("/",$form);
	    	//$formName=$f[0].ucfirst($f[1])."Form";
			//echo $name;
			
			if (!Controller::isAjaxRequest() && Session::is_setFlash($name."_request___"))
			{ 
				$req=unserialize(base64_decode(Session::getFlash($name."_request___")));
				//echo "flash ba64: "._r($req);
				return isset($req[$name])?$req[$name]:array();
			}
			//echo "flash self: "._r(self::$req);
			return isset(self::$req[$name])?self::$req[$name]:array();
			
	    }
	    
		public function getFormName($form,$filter=''){
			$f=explode("/",$form);
	    	if (count($f)>1) $formName=$f[0].ucfirst($f[1]);else $formName=$f[0];
			if (!preg_match("/Form".$filter."$/",$formName)) $formName.="Form".$filter;
			return $formName;
		}
		
		public function setFormVars($form,$ajax=false)
		{
			$formName=$this->getFormName($form,'');
			if ($ajax===false) $ajax=Controller::isAjaxRequest();
			
			if (!$ajax && !Session::is_setFlash($formName."_request___"))
			{
				$vars_request=self::$req;
				
				Session::setFlash($formName."_request___",base64_encode(serialize($vars_request)));
			}
			//if ($successMessage!=null) Session::setFlash($formName,$successMessage);
		}
		
		public function updateFormVars($form){
			Session::un_setFlash($this->getFormName($form,'')."_request___");
			$this->setFormVars($form);
		}
		
		/*
		public function setInfo($form,$successMessage=null,$filter='')
		{
			$formName=$this->getFormName($form,$filter);
			if ($successMessage!=null) Session::setFlash($formName."_info",$successMessage);
		}
		*/
		private function setMessage($form,$successMessage=null,$add=true,$class='reportedcontent_content archived_report_blue',$info='',$ajax=null){
			
			$formName=$this->getFormName($form);
			if ($ajax===null) $ajax=Controller::isAjaxRequest();
			
			if (!$ajax && $successMessage!=null) {
				$successMessage='<div class="'.$class.'"><b>'.$successMessage.'</b></div>';
				
				$valor=Session::getFlash('request_concat'.$info);
				
				if ($add){
					$successMessage=Session::getFlash('request_concat'.$info).$successMessage;
					Session::un_setFlash('request_concat'.$info);
				}else 
					Session::setFlash('request_concat'.$info,$valor.$successMessage);
				
				Session::setFlash($formName.$info,$successMessage);
			}
		}
		
		public function setError($form,$successMessage=null,$add=true,$class='reportedcontent_content active_report',$ajax=null)
		{
			$this->setMessage($form,$successMessage,$add,$class,'',$ajax);
		}
		
		public function setInfo($form,$successMessage=null,$add=true,$class='reportedcontent_content archived_report_blue',$ajax=null)
		{
			$this->setMessage($form,$successMessage,$add,$class,'_info',$ajax);
		}
	    
		public function getFormObject($view,$viewtype=null)
		{
			$gen=explode("/",$view);
			$content=Viewer::view($gen[0]."/object/".(isset($gen[1])?$gen[1]:'edit')."/".$gen[2],array(),$viewtype);
			$object=unserialize(base64_decode($content));
			if (is_object($object)) $object->setObjectView($view,$viewtype); // para el caso edit/new/filters que devuelven objeto
			return $object;
		}
		
		public function getSelectedItem($modelgen)
		{
			return Session::get(str_replace("/","_",$modelgen)."_selected");
		}
		
		public function setSelectedItem($modelgen,$value=null)
		{
			Session::set(str_replace("/","_",$modelgen)."_selected",$value);
		}
		
		
		public function getClassReferencePKs($form,$class,$filter=''){
			
			$formName=$this->getFormName($form,$filter);
			
			$c=new $class();
			$pks=$c->getPKs();
			$formulario=$this->getParameter($formName);
			foreach($pks as $pk=>$val){
				$pks[$pk]=$formulario[strtolower($class).".".$pk];
			}			
			return $pks;
		}
		
		
		public function hasErrorLastForm(){
			return Session::getInstance()->is_setFlash('last_form_error');
		}
	
		/*
		 * getRouteObjectVars() 
		 * extrae las pk de los modelos que hay en el formulario y devuelves las variables asociadas
		 */
		public function getRouteObjectVars($form,$formtype="new")
		{
			$req=Request::getInstance();
			
			if ($req->hasErrorLastForm()){
				return $req->getFormVars($form);
			}
			
			$vars=array();
			
			// extraigo las variables del form sin cast 
			$vars=$this->getFormVars($form);
			//echo _r($vars)."1";//die();
			// (caso especial del tipo date, para paso de parametros entre acciones new y edit!)
			//if (empty($vars))  // no puedo descomentarlo porque hay variables sin castear (pero puede que haya otras en el formulario necesarias)
			{
				
				/// miro si hay alguna seleccionada (caso para las acciones "delete" del formulario "edit")
				if (Session::is_set(str_replace("/","_",$form)."_selected"))  // cuando pasamos por edit, guardamos los datos de las pks.
				{
					$vars=array_merge($vars,(array)Session::get(str_replace("/","_",$form)."_selected"));
				}
				//echo _r($vars)."2";
				
				// si hay otro dato en el request se añade a los que teniamos en edit (que eran solo pks!)
			
				$r=Site::getInstance()->getConfiguration('modelclass');
				//echo _r($r);
				
				foreach(Site::getInstance()->getConfiguration('generators') as $gen=>$file)
				{
					if (preg_match("/".str_replace("/","\/",$form)."/",$gen,$args)) break;
				}
				
				$generator=Site::getInstance()->importFile(realpath($file));
				$gf=explode("/",$form);
				
				$referenceClass=ucfirst($gf[0]);
				
				$fi=array();
				//foreach($generator[$gf[1]]['fields'] as $field=>$data)
				if (!isset($generator[$gf[1]]['form']['render'][$formtype])) return $vars; 
				
				preg_match_all("/{([^}]*)}/",$generator[$gf[1]]['form']['render'][$formtype],$args);
				
				foreach($args[1] as $field)
				{
					if (isset($generator[$gf[1]]['fields'][$field]['assignTo'])) 
					{
						$assign=$generator[$gf[1]]['fields'][$field]['assignTo'];
						
						if (!is_array($assign)) 
						{
							$assignk=explode(".",trim($assign));
							//echo _r($assign)._r($assignk);
							//die();
							if (isset($assignk[1])) // por si nos encontramos un assigTo a una tabla (sin columna) 
								$fi[$assignk[0]][]=$assignk[1];
							else
								$fi[$assignk[0]][$field]=Form::THROUGH_CLASS_PREFIX;
						}
						else
						{
							foreach($assign as $k=>$v)
							{
								$assignk=explode(".",trim($v));
								if (isset($assignk[1])) // por si nos encontramos un assigTo a una tabla (sin columna) 
									$fi[$assignk[0]][]=$assignk[1];
								else
								$fi[$assignk[0]][$field]=Form::THROUGH_CLASS_PREFIX;
							}
						}		
					}
				}
				
				if (!empty($fi))
				{	
					//echo _r($r->get());
					$tpeer=array();
					$through=array();
					//echo _r($vars);
					////// recorremos los campos para ver los posibles modelos y guardar las pk
					
					foreach($fi as $table=>$fields)
					{
						$class=$table."Peer";
						
						$tpeer[$table]['peer']=new $class();
						
						$cols=$tpeer[$table]['peer']->getColumns();
						$pks=$tpeer[$table]['peer']->extractPK($cols);
						$tpeer[$table]['pks']=array_flip($pks);
						
						if (in_array(Form::THROUGH_CLASS_PREFIX,$fields)){
							foreach($fields as $ff=>$dd){
								if ($dd==Form::THROUGH_CLASS_PREFIX) $through['assignTo'][$ff]=$table;
							} 
						}
						else
						{
							//echo $table." "._r($tpeer[$table]['pks']);
							/// problema con el segundo asignTo , que no se pasa por request.
							//// miramos si las foreign key ,son pk para añadir el valor del campo de referencia
							foreach($cols as $field=>$data)
							{
								if (in_array($data['phpname'],$fields))  
								{
									if (isset($data['pk']) && $data['pk'])
									{
										if (strtolower($table)==$gf[0]){
										
											$referenceClass=$table;
											
											/// recogemos el valor por request (edit), pero puede ser que no
											/// lo pasen, lo miraremos en flashvars!
											
											
											$valor=$req->{$gf[0].".".$field};
											if ($valor==null && isset($vars[$gf[0].".".$field])) $valor=$vars[$gf[0].".".$field];
																						
											//echo _r($gf[0].".".$field)._r($valor);
											//echo _r($r);
											$tpeer[$table]['pks'][$field]=$valor;
											
											$phpnames=$tpeer[$table]['peer']->getPHPNames();
											$through[$table][$phpnames[$field]]=$valor;
											// manu
											$tpeer[$table]['pks'][$field]=$valor; 
											//echo "<br/>".$field.": ".$valor;
										}
										else { 	// manu
											$tpeer[$table]['pks'][$field]=$valor;
										}
										
										 //Si la pk, es tambien fk, y es del modelo principal del form
										if (isset($data['fk']) && strtolower($table)==$gf[0]) 
	 									{
											$fk=explode(".",$data['fk']);
											$tpeer[$fk[0]]['fks'][$fk[1]]=$table.".".$field;
											
											//echo $data['fk'];
										}		
									}else if (isset($data['fk'])){
									 	//echo '<br/>***************'.strtolower($table).','.$gf[0]._r($data);
									
										$fk=explode(".",$data['fk']);
										$tpeer[$fk[0]]['fks'][$fk[1]]=$table.".".$field;
									}
								}	
							}
						}
						//echo _r($through);
					}
					
					if (isset($through['assignTo'])){
						
						// ESQUEMA MENTAL: muestrame los id de assignTo relacionados con refereceClass
						// para eso, necesitaré saber un método en la referenceClass que vaya a la assignTo
						// PROBLEMA! 
						
						$dest=$through['assignTo'];	 //me guardo las clases destino (en principio solo 1)
						//$field=key($dest);
						unset($through['assignTo']);
						
					  $claseEstatica=key($through);
					  //echo _r($through);
					  //echo $claseEstatica;die();
					  $ids=array_keys($through[$claseEstatica]);
					  //echo _r($ids);die();
					  
					  
					  foreach($dest as $field=>$z)
					  {
					  	reset($through);
					  	reset($ids);
					  	
					  	if (isset($generator[$gf[1]]['fields'][$field]['parameters']['through_class'])){
							$through_classPeer=$generator[$gf[1]]['fields'][$field]['parameters']['through_class']."Peer";
						}
						else{
							$through_classPeer=$dest."Peer";	/// si no hay through_class, es la de destino
						}

						$peer3=new $through_classPeer();
						$columns3=$peer3->getColumns();
						$fk3=$peer3->extractFK($columns3);
						
						// buscar columnas estaticas. Hay que hacerlo antes para que cuando pillemos
						// las dinamicas esten todas las estaticas borradas! (+ de una pk en estatica)
						
						foreach($ids as $ke){
							$staticCol=$claseEstatica.".".$ke;
							$vv=array_search($staticCol,$fk3);
							unset($fk3[$vv]);
						}
						
						//echo _r($generator[$gf[1]]['fields'][$field]);
						
						$fksdin=array_keys($fk3); // esta es la parte dinamica de las fk
						
						if (isset($generator[$gf[1]]['fields'][$field]['parameters']['template']))
							$template=$generator[$gf[1]]['fields'][$field]['parameters']['template'];
						else{
							//$template="id";	/// ojo.... cambiar por un id bueno!!!!
							$template='%%'.implode("%% %%",array_keys($columns3)).'%%';
						}
						//echo $template;die();
						foreach($through as $x=>$y)
						{	// para cada clase que es assignTo
							
							$classModel=$x."Peer";		// creamos objeto
							
							$peer=new $classModel();
							
							//foreach($dest as $z){		// para cada origen
								
								// is callable!
								$values=implode(", ",$y);	// recogemos los valores (estaticos) de la clase origen
								$w=$peer->{'doSelectJoin'.$z}($values); // metodo que me dará los valores
																		// de la clase $through 
								
								//echo '<br/><br/>'.$classModel.'->doSelectJoin'.$z.'('.$values.')'._r($w);
								
								
								$classDest=$z."Peer";
								$peer2=new $classDest();
								
								if ($peer2->getQuery('doSelectTable',true)!==null){
									$w2=$peer2->execute('doSelectTable',array(),false);
									//echo '<br/>'.$classDest."aaaa";
								}else{
									//echo '<br/>'.$classDest."bbbb";
									$w2=$peer2->doSelectAll(false);
								}
								
								$php=array_flip($peer2->getPHPNames());
								$colname=strtolower($z).".".$field;
								
								//echo $z._r($w2)._r($php);die();
								$vars[$colname]=array();
								// tengo que saber cual es la parte dinamica de las fk, para pasarsela a dest
								//echo _r($r->getParameter($colname),true);
								//echo _r($_REQUEST,true);
								
								if (!empty($w) && !empty($w2)){
									foreach($w as $v){
										foreach($v as $k1=>$v1){
											//echo _r($k1)._r($fksdin);
											if (in_array($k1,$fksdin)){
												$aux=explode(".",$fk3[$k1]);
												//echo _r($aux);
												foreach($w2 as $ww){	
													//echo _r($ww).','.$aux[1].','.$php[$aux[1]].var_export($v1,true);
														
													if ($ww[$php[$aux[1]]]==$v1){ 
														//$value="'".preg_replace("/%%(.*)%%/","'.\$ww[\$php['$1']].'",$template)."'";
														//eval("\$wwv=$value;");
														//$vars[$colname][$v1]=$wwv;
														$vars[$colname][]=$v1;
														
													}
												}
												//
											}
										}
									}
									
								}
								//echo _r($vars);
								//die();
								
							//}
						  
						}
					  } //	foreach($through['assignTo'] as $field=>$z)
						//die();
					}
					
					//echo _r($vars);die();
					
					if (current($tpeer[$referenceClass]['pks'])!=null)
					{
						//echo _r($tpeer);die();
						//// creamos el objecto y devolvemos todos los datos
						$pks=array();
						$fks=array();
						$fksNOpks=array();
						foreach($fi as $table=>$fields)
						{
							//echo '<h2>'.$table.'</h2>'._r($tpeer[$table]['pks']).(isset($tpeer[$table]['fks'])?_r($tpeer[$table]['fks']):'no FKS');
							
							$phpnames=$tpeer[$table]['peer']->getPHPNames();
							$columns=array_flip($phpnames);
							
							foreach($tpeer[$table]['pks'] as $pk=>$value) 
							{
								if (isset($tpeer[$table]['fks'][$phpnames[$pk]]))
								{
									// miramos si hay una fk de otra tabla que sea igual para coger su valor.
									$fks=explode(".",$tpeer[$fk[0]]['fks'][$fk[1]]);
									//echo _r($fks);die();	
									if (isset($tpeer[$fks[0]]['pks'][$fks[1]])) // si existe pk
										$tpeer[$table]['pks'][$pk]=$tpeer[$fks[0]]['pks'][$fks[1]];
									else{ // es una columna NO PK, que es FK											
										$phpnamesAUX=$tpeer[$fks[0]]['peer']->getPHPNames();
										//$fksNOpks[$table][$pk]=$phpnamesAUX[$fks[1]];
										$fksNOpks[$table][$pk]=$fks[1];
									}
								}
								
								if (strtolower($table)==$gf[0]) // si coincide la tabla en minusculas con el nombre del modelo
								{
									/*
									$colpk=$tpeer[$table]['peer']->getColumn($pk);
									if ($colpk['type']=='date' && !is_numeric($value)){ 
										$format_db=isset($colpk['params']['format'])?$colpk['params']['format']:$tpeer[$table]['peer']->getDatabase()->getDateFormatter();
										$value=dbDriver::toTimestamp($value,$format_db);
										echo $value;
									}*/
																		
									$pks[$gf[0].".".$pk]=$value;
								}
								
							}
							//if (isset($fksNOpks)) echo $table._r($fksNOpks);
													
								
							if (strtolower($table)==$gf[0]) 
							{
								//echo _r($pks);
								//echo "setSelectedItem($form,".var_export($pks,true).")";
								$this->setSelectedItem($form,$pks);	
							}
							//echo "<br/>-----".$table._r($tpeer[$table]['pks'],true);
							//echo _r($gf);
							//echo _r($tpeer);
							
							//echo "\$object=\$tpeer[$table]['peer']->retrieveByPk('".implode("','",$tpeer[$table]['pks'])."');";
							eval("\$object=\$tpeer[\$table]['peer']->retrieveByPk('".implode("','",$tpeer[$table]['pks'])."');");
							
							//echo _r($object);
							//echo _r($tpeer[$table]);
							
							if ($object!=null)
							{
								$cols=$object->getArray();
								foreach($cols as $pk=>$value) 
								{
									$vars[strtolower($table).".".$columns[$pk]]=$value;
								}
							}
							
						}
						
					}
					
				}
				//echo _r($vars)."3";
			}
			if (isset($fksNOpks))
			foreach($fksNOpks as $fktable=>$vv){
				foreach($vv as $fkcolumn=>$vv2){
					$column=strtolower($referenceClass).'.'.$vv2;
					if (isset($vars[$column])){
						$tpeer[$fktable]['pks'][$fkcolumn]=$vars[$column];
					}
				}	
				eval("\$object=\$tpeer[\$fktable]['peer']->retrieveByPk('".implode("','",$tpeer[$fktable]['pks'])."');");
				if ($object!=null)
				{
					$cols=$object->getArray();
					$phpnames=array_flip($object->getModel()->getPHPNames());
					
					foreach($cols as $pk=>$value) 
					{
						$vars[strtolower($fktable).".".$phpnames[$pk]]=$value;
					}
				}
			}	
			
			//echo _r($vars);die();
			//die();
			return $vars;
		}
		
		
		public function getForm($formName)
		{
			$serializedForm=Session::getFlash($formName.'_send___');
			$form=unserialize(base64_decode($serializedForm));
			
			//echo _r($form);
			return $form;
		
		}
				
		public function setForm($formName,$object)
		{
			////// form
			$form=base64_encode(serialize($object));
			$serializedForm=Session::setFlash($formName.'_send___',$form);
		}
		
	}
?>