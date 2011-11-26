<?php
	class ValidationCubePluginActions extends Actions
	{
		private function getLoginForm(){
			return Form::load("loginpass","modules","edit");
		}

		public function executeFormError($request){
			$this->executeLogin($request);
			$this->setTemplate('login');
		}
		
		public function executeLogin($request)
		{
			Config::set('view:metas:title',Viewer::_echo('validation'));
			$vars=$request->getFormVars('validation');
			
			$route=Controller::getRoute();
			$referer=($route['module']=='validation' && $route['action']=='login')?'formError':Route::url("");
			$ifok=isset($vars['ifok'])?$vars['ifok']:Route::url(".");
			
			
			$f=$this->getLoginForm();
			$f->bind(array("referer"=>$referer, "ifok"=>$ifok));
			$this->form=$f->render();
		}

		public function executeSendlogin($request)
		{
			$user=User::createInstance('MyUser');

			/// recogemos las variables del formulario
			$vars=$request->getFormVars('validation');
			/// OJO: como el formulario no estaba generado por generador, no tiene "object" serializado
			/// y debemos crear el formulario, otra vez, para ver los validadores.
			$form=$this->getLoginForm();
			if (!isset($vars['login']) && $user->isLogged()) $vars['login']=$user->getProperties('CubUsrNickname');
			$form->bind($vars);

			if (!$form->isValid()){
				$request->setInfo("validation",Viewer::_echo('form:validation:error'),true,'reportedcontent_content active_report');
			}else{
				$validation=$user->authenticateUser($vars['login'],$vars['password']);
				
				if ($validation<0){
					switch($validation){
						case -3: $error=Viewer::_echo('form:validation:inactive');break;
						case -2: $error=Viewer::_echo('form:validation:notexists');break;
						default: $error=Viewer::_echo('form:validation:error');break;
					}
					$request->setInfo("validation",$error,true,'reportedcontent_content active_report');
				}else{
					$request->setInfo("validation",Viewer::_echo("form:validation:success"),true,'reportedcontent_content archived_report');
					$request->setFormVars("validation");
					/// hacer consulta base de datos sqlite
					$user->executeLogin();
					
					$this->redirect($vars['ifok'],true); // true: redireccion sin traduccion de url
				}
			}

			$vars['error']=1;
			$request->setParameter($request->getFormName('validation'),$vars);
			$request->updateFormVars('validation');
			$this->redirect($vars['referer'],true);
			//$this->forward('default','error403');
		}
	}
?>