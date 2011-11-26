<?php
	abstract class Generator
	{
		abstract protected function generateMethods();
		
		abstract protected function generateFormTemplate();
		abstract protected function generateListTemplate();
		abstract public function generateActions($is_plugin);
		abstract public function generateGrid();
				
		protected $module;
		protected $model;
		protected $generator;
		protected $app;
		protected $appmodel;
		protected $plugin;
		protected $pluginmodel;
		
		protected $viewtype;
		protected $defaultSort;
		protected $defaultFilter;
		//protected $maxPerPage;
		protected $pagination;
		
		protected $titleList;
		protected $titleNew;
		protected $titleEdit;
		
		protected $pks;
		protected $columns;
		protected $class;
		protected $phpnames;
		protected $labels;
		protected $validators;
		
		protected $dir;
		protected $ROOT;
		
		protected $query;
		protected $referenceClass;
		protected $options;
		protected $batch_actions;
		
		protected $layoutForm;
		protected $layoutList;
		protected $layoutFilter;
		
		protected $row_actions;
		protected $show_numbers;
		protected $default_query;
		
		protected $widths;
		protected $scroll;
		protected $ajax_validators=false;
		protected $ajax_form=false;
		protected $ajax_list=false;
		
		
		private function getConfig($ROOT){
			$config=sfYaml::load(real($ROOT."/engine/config.yml"));

			// databases.yml
			$databases=real($ROOT."/engine/databases.yml");
			if (realpath($databases)!==false){
				$config2=sfYaml::load($databases);

				foreach($config2 as $env=>$data){
					foreach($data as $connection=>$info){
						$config[$env]['database'][$connection]=$info;
					}
				}
			}
			return $config;
		}
	
		public function __construct($props=array(),$ROOT='',$dir='')
		{
			
			$this->ROOT=$ROOT;
			$this->model=$props['model'];
			$this->module=$props['module'];
			$this->generator=$props['generator'];
			$this->app=$props['app'];
			$this->plugin=$props['plugin'];
			$this->appmodel=$props['appmodel'];
			$this->pluginmodel=$props['pluginmodel'];
			
			$site=$this->getConfig($ROOT);
			//$site=Spyc::YAMLLoad($ROOT."/engine/config.yml");
			//$site=sfYaml::load($ROOT."/engine/config.yml");
			
			// databases.yml
			$databases=realpath(CUBE_PATH_ROOT."/engine/databases.yml");
			if ($databases!==false){
				$dataDB=$site->importFile($databases); // core
				foreach($dataDB as $env=>$data){
					foreach($data as $connection=>$info){
						$site[$env]['database'][$connection]=$info;
					}
				}
			}
			
			
			if (preg_match("/Plugin$/i",$props['app']))$rootDir=$ROOT.'/plugins/';
			else if (preg_match("/Plugin$/i",$props['appmodel']))$rootDir=$ROOT.'/plugins/';
			else $rootDir=$ROOT.'/apps/';

			if ($props['appmodel']!=null) $dir=real($rootDir.$props['appmodel']."/model/".$props['model']);
			else {echo "---- ".__METHOD__." error ----\n";die();}
			
			//echo real($dir."/generators/{$props['generator']}.yml");
			//$config=Spyc::YAMLLoad(real($dir."/generators/{$props['generator']}.yml"));
			$config=sfYaml::load(real($dir."/generators/{$props['generator']}.yml"));
			$cfg=$config[$props['generator']];
			
			if (isset($cfg['list']['title'])) 			$this->titleList=parseText($cfg['list']['title']);else $this->titleList="Viewer::_echo('form:element:list')";
			if (isset($cfg['form']['title']['new'])) 	$this->titleNew=parseText($cfg['form']['title']['new']);else $this->titleNew="Viewer::_echo('form:element:create')";
			if (isset($cfg['form']['title']['edit'])) 	$this->titleEdit=parseText($cfg['form']['title']['edit']);else $this->titleEdit="Viewer::_echo('form:element:edit')";
			if (isset($cfg['form']['ajax_validators']) && $cfg['form']['ajax_validators']){
				$this->ajax_validators=true;
			}
			if (isset($cfg['form']['ajax']) && $cfg['form']['ajax'])$this->ajax_form=true;
			if (isset($cfg['list']['ajax']) && $cfg['list']['ajax'])$this->ajax_list=true;

			$this->formName=$cfg['form']['params']['name'];
			$this->formAction=$cfg['form']['params']['action'];
			
			$this->batch_actions=isset($cfg['list']['batch_actions'])?$cfg['list']['batch_actions']:array();
			
			if (preg_match_all("/{([^}]*)}/",$cfg['list']['actions']['rows'],$args)!==false)
				$this->row_actions=$args[1];
			
			/// layouts views
			if (isset($cfg['form']['layout'])) 	$this->layoutForm=$cfg['form']['layout'];else $this->layoutForm="two_column_left_sidebar";
			if (isset($cfg['list']['layout'])) 	$this->layoutList=$cfg['list']['layout'];else $this->layoutList="two_column_left_sidebar";
			
			if (isset($cfg['list']['width'])) $this->widths=$cfg['list']['width'];else $this->widths=array();
			if (isset($cfg['list']['scroll'])) $this->scroll=$cfg['list']['scroll'];else $this->scroll=array();
			
			
			if (isset($cfg['list']['layout_filter'])) 	$this->layoutFilter=$cfg['list']['layout_filter'];else $this->layoutFilter="menu";
			
			if (isset($cfg['list']['pagination'])) $this->pagination=$cfg['list']['pagination'];
			
			if (isset($cfg['list']['query']) && trim($cfg['list']['query'])!='') $this->query=$cfg['list']['query'];
			
			if (isset($cfg['list']['default_sort']) && is_array($cfg['list']['default_sort'])) 
			{
				$sortCols=$cfg['list']['default_sort'];
				$temp=array();
				foreach($sortCols as $key=>$value) $temp[]='"'.$key.'","'.strtolower($value).'"';	
				$this->defaultSort=$temp;
			}
			
			if (isset($cfg['list']['default_filter']) && is_array($cfg['list']['default_filter'])) 
			{
				$filterCols=$cfg['list']['default_filter'];
				$temp=array();
				foreach($filterCols as $key=>$value) $temp[]='"'.$key.'","'.$value.'"';	
				$this->defaultFilter=$temp;
			}
			
			// layout template
			if (!isset($cfg['param']['layout'])) $this->viewtype=$site['all']['settings']['views']['global_template'];
			else $this->viewtype=$cfg['param']['layout'];
			$dir=str_replace(DIRECTORY_SEPARATOR,"/",$dir);
			$this->dir=$dir;
			
			//// recoger todas las columnas visibles 
			$fields=$cfg['fields'];
			$render=$cfg['list']['render']['grid'];
			$cols=preg_split("/[\.,|-]|\\s/",$render); //bug guapo saltos de linia OK
			$this->options=array();
			$this->labels=array();
			$this->validators=array();

			foreach($fields as $column=>$data){
				//echo $column._r($data['validator']);
				
				if (isset($data['validator']) && !empty($data['validator'])) {
					if (isset($data['assignTo']) && !empty($data['assignTo'])) 
						$this->validators[$props['model'].'.'.$column]=$data['validator'];
					else $this->validators[$column]=$data['validator'];
				}

				if (isset($data['label'])){
					if (is_array($data['label']) && isset($data['label']['text'])) $this->labels[$column]=$data['label']['text'];
					else if (!is_array($data['label'])) $this->labels[$column]=$data['label'];
					else $this->labels[$column]="";
				}
				
				if (isset($data['view'][1]['options_grid']) && $data['view'][1]['options_grid'])
				{ 
					if (isset($data['view'][1]['options']))
					{
						if (!isset($this->options['options'])) $this->options['options']=array();
						$this->options['options'][$column]=$data['view'][1]['options'];
					}
					else if (isset($data['view'][1]['options_values']))
					{
						if (!isset($this->options['options_values'])) $this->options['options_values']=array();
						$this->options['options_values'][$column]=$data['view'][1]['options_values'];
					}else if (isset($data['view'][1]['query'])){
						echo 'WARNING: not implemented yet! :)';
						/*
						$paramModel=$data['view'][1]['query'];
						if (is_array($paramModel)) {
							$src=explode(".",$paramModel['select']);
						}else{
							$src=explode(".",$paramModel);
						}
						$class=$src[0]."Peer";
						$select=$src[1];
						
						//$refschema=sfYaml::load($dir."/schema.yml");
						import('model.UpServei.*');
						die();
						$ruta=str_replace("/",'.',substr($dir,strlen($ROOT)+1));
						import($ruta.'.*');
						echo $ruta;die();
							//	$site=Site::getInstance(array('app'=>'llicencies','debug'=>false));
							//$conf=$site->readConfiguration();	
							//Controller::createInstance($conf)->init();
						$peer=new $class();
						
						$data=$peer->doSelect($peer->getQuery($select),false);
						foreach($data as $cur){
							if (!is_array($paramModel) || !isset($paramModel['value'])){
								$option=reset($cur);
								$text=next($cur);
							}else{
								$option=$cur[$paramModel['value']];
								$text= $cur[$paramModel['text']];
							}
							$this->options['options_values'][$option]=$text;
						}
						*/
					}
				}
				
			}	
						
			//////// extraer todos assignTo del render de filters
			$renderFilters=$cfg['list']['filters'];
			$colsFilters=preg_split("/[\\\n\\\\r \\\.,|-]/",$renderFilters);
			$filters=array();
			
			foreach($colsFilters as $col)
			{
				$re=preg_match_all("/{[\$]{0,1}([^\}]*)}/",$col,$args);
				//$render=preg_replace("/{[\$]([^\}]*)}/","$1",$render);
				foreach($args[1] as $k=>$column)
				{
					if (isset($fields[$column]['assignTo'])) 
					{
						if (!is_array($fields[$column]['assignTo'])) $fields[$column]['assignTo']=array($fields[$column]['assignTo']);
						
						foreach($fields[$column]['assignTo'] as $assignTo)
						{
							list($class,$phpname)=explode(".",$assignTo);
							$filters[$class][$column]=$phpname;
						}
					
					}
				}
			}
					
			//echo _r($cols);
			
			$this->columns=array();
			$this->models=array();
			$this->credentials=array();
			$this->show_numbers=isset($cfg['list']['show_numbers'])?$cfg['list']['show_numbers']:false;
			
			/// default_query: queremos hacer una select que pinte el listado (por defecto:true), o solo ver los filtros
			$this->default_query=isset($cfg['list']['default_query'])?$cfg['list']['default_query']:true;
			//// modelo de referencia: estar� en dir/schema.yml ->class:
			//$refschema=Spyc::YAMLLoad($dir."/schema.yml");
			$refschema=sfYaml::load($dir."/schema.yml");
			
			$this->referenceClass=$refschema['class'];
			
			
			
			foreach($cols as $col)
			{
				$render=preg_match_all("/{[\$]{0,1}([^\}]*)}/",$col,$args);
				//$render=preg_replace("/{[\$]([^\}]*)}/","$1",$render);
				foreach($args[1] as $k=>$column)
				{
					
					if (isset($fields[$column]['assignTo'])) 
					{
						if (!is_array($fields[$column]['assignTo'])) $fields[$column]['assignTo']=array($fields[$column]['assignTo']);
						
						foreach($fields[$column]['assignTo'] as $assignTo)
						{
							list($class,$phpname)=explode(".",$assignTo);
							
							/// no hemos pasado por este modelo, y es igual al que estamos generando.
							// suponemos que el modelo y la clase se llaman igual!
							if (!array_key_exists($class,$this->models)) // && strtolower($class)==$this->model)
							//if (!array_key_exists($class,$this->models) && strtolower($class)==$this->model) 
							{
								// miramos en la misma aplicacion apps/app/model, sino está miramos en el modelo general /model
								// OJO: los modelos dentro de aplicaciones solo estan disponibles para esa aplicacion.
								// OJO: si quieres hacerlos visibles a todas las apps, hay que ponerlo en /model.
								preg_match("/(.*)\/model\/(.*)/",$dir,$args2);
								$file=$args2[1]."/model/".strtolower($class)."/schema.yml";
															
								echo "--> Get info from {$class}\n";
								if (!file_exists($file)) 
								{
									$file=$ROOT."/model/".strtolower($class)."/schema.yml";
								}
								//$schema=Spyc::YAMLLoad($file);
								$schema=sfYaml::load($file);
								echo "\n************* $file **************\n";
								if (!empty($schema))
								{
									//$this->class=$schema['class'];
									
									$columns=array();
									$columns['columns']=array_keys($schema['columns']);
									
									foreach($schema['columns'] as $key=>$col)
									{
										if (isset($col['pk']) && $col['pk']==true) $columns['pks'][]=$key;
										$columns['phpname'][]=(isset($col['phpname']))?$col['phpname']:$key;
										$columns['types'][$key]['type']=$col['type'];
											
										$database=$site['all']['database'][$schema['database']];
										// si en la configuracion de la base de datos no hay definido un dateformat, le ponemos Y-m-d H:i:s --> timestamp
										if (isset($database["dateformat"])) $dateformat=$database["dateformat"];else $dateformat="%Y-%m-%d %H:%M:%S"; 
										
										switch($col['type'])
										{
											case 'date': $columns['types'][$key]['format']=(isset($col['params']['format']))?$col['params']['format']:$dateformat; 
														 if (isset($fields[$key]['view'][1]['format_grid'])) 
														 	$this->options['format'][$schema['table'].".".$key]=$fields[$key]['view'][1]['format_grid'];	
														 else if (isset($fields[$key]['view'][1]['format']))
														 	$this->options['format'][$schema['table'].".".$key]=$fields[$key]['view'][1]['format'];
														 else if (isset($schema['columns'][$key]['format']))	// formato del schema	
														 	$this->options['format'][$schema['table'].".".$key]=$schema['columns'][$key]['format'];
														 else // formato de la base de datos
														 	$this->options['format'][$schema['table'].".".$key]=$columns['types'][$key]['format'];
														 
														 if (isset($fields[$key]['view'][1]['default']))
														 	$this->options['default'][$schema['table'].".".$key]=$fields[$key]['view'][1]['default'];
														 break;
										}
										
										//echo "\n$key: ".var_export($fields[$key]['view'][1],true);
										if (!isset($fields[$key]['view'][1]['options_grid']) || $fields[$key]['view'][1]['options_grid'])
										{
										 	if (isset($fields[$key]['view'][1]['options']))
											{
												$this->options['options'][$schema['table'].".".$key]=$fields[$key]['view'][1]['options'];
											}
											else if (isset($fields[$key]['view'][1]['options_values']))
											{
												$this->options['options_values'][$schema['table'].".".$key]=$fields[$key]['view'][1]['options_values'];
											}
										}
										
									}
									
									$this->models[$class]=array('columns'=>$columns['columns'],
																'table'=>$schema['table'],
																'pks'=>$columns['pks'],
																'phpnames'=>$columns['phpname'],
																'types'=>$columns['types'],
																'filters'=>isset($filters[$class])?$filters[$class]:null
																);
								}
								
							}
						}
						//echo "{$column}: ".$fields[$column]['assignTo']."\n";
						
						if (preg_match("/^{[\$]/",$args[0][$k])) $this->columns[]=$column; 
						else $this->columns[]=$fields[$column];
					}
					else $this->columns[]=$column;
					
					
				}
				
				if (isset($fields[$column]['credentials']) && is_array($fields[$column]['credentials'])) 
				{
					$this->credentials[$column]=$fields[$column]['credentials'];
				}
				
			}
			
			//print_r($this->models);die();
			//echo $this->model;
		}
		
	}
?>