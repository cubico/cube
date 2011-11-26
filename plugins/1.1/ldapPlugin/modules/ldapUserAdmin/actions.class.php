<?php

class ldapUserAdminLdapPluginActions extends Actions{

  
 
  public function executeIndex($request)
	{
		
		//echo "<pre>".print_r($datos,true)."</pre>";
		//$fields=array("cn","displayname","description","distinguishedname","operatingsystem","operatingsystemservicepack"); //$fields=null;
		//$fields=array("displayname","name","samaccountname","mail","memberof","department","displayname","telephonenumber","primarygroupid");	
		//$fields=array("samaccountname","displayname","cn","memberof","ou");
		$datos=$request->get();
		
		$fields=array("name","samaccountname","mail","department","displayname","telephonenumber");
		$info=array();
		$list=array();
		$lista=array();
		
		try
		{
			$ldap=new Ldap();	
				
			$mostrando="Resultats Cerca per ";
			
			switch($datos['busca'])
			{
				default:
				case '1':
					
					$mostrando.="Nif";
					$data="*".$datos['nif']."*";
					
					$lista=$ldap->getUserBy($data,'userPrincipalName',$fields);
					echo $data;die();
					break;
				case '2':
					$mostrando.="Nom";
					$data="*".str_replace(" ","*",$datos['nom'])."*";
					$lista=$ldap->getUserBy($data,'cn',$fields);
					break;
				case '3':
					$mostrando.="Mail";
					$lista=$ldap->getUserBy($datos['mail'],'mail',$fields);
					break;
				case '4':
					$mostrando.="Departament";

					$datos['dept']=strtr($datos['dept'],array("("=>"\(",")"=>"\)"));
					$lista=$ldap->getUserBy($datos['dept'],'department',$fields);
					break;
			}
			
		
			if (count($lista)>0)
			{
				foreach($lista as $item)
				{
					$aux=array();
					foreach($item as $key=>$cur)
					{
						switch($key)
						{
							case 'memberof':	
								$mo=$ldap->extractMemberOf($cur);
								if (isset($mo['CN'])) $aux['categ']=implode("/",$mo['CN']);
								if (isset($mo['OU'])) $aux['uu']=implode("/",$mo['OU']);
								//if (isset($mo['DC'])) echo "ad:".implode("/",$mo['DC'])."</br>";
								break;
							case 'samaccountname':
								$aux['login']=($cur);
								break;
							case 'cn':
							case 'name':
							case 'displayname':
								$aux['name']=($cur);
								break;
							default:
								$aux[$key]=($cur);
						}
					}
					//$aux['id']=$aux['login'];
					$list[]=$aux;
				}
				
				sort($list);	
				//foreach($list as $cur){$list2[]=json_encode($cur);}
				//echo '{items:['.implode(",",$list2).'],totalCount:"'.count($list2).'"}';
			}
						
			echo json_encode(array('data'=>$list,'message'=>$mostrando));
			
		}catch(LDAPException $e)
		{
			switch($e->getCode())
			{
				case 2: 
						$mostrando="Resultat massa gran. Acura la cerca.";
						return json_encode(array('data'=>array(),'message'=>$mostrando));
						break; 
				
			}
			
		}
		return Viewer::NONE;
	}
  
  
  public function executeModUsuari($request)
  {
      $username=$request->getParameter('nif');
      $request->surname=$request->surname1.';'.$request->surname2;
      $request->display_name=$request->first.' '.$request->surname1.' '.$request->surname2;

      $ldap=new Ldap();	
      //$ldap->_use_ssl=true;
      
      //echo _r($ldap,true);die();
      $attributes=array(
                'address_city',
                'address_code',
                'address_country',
                'address_pobox',
                'address_state',
                'address_street',
                'company',
                'change_password',
                'company',
                'department',
                'description',
                'display_name',
                'email',	
                'expires',
                'firstname',
                'home_directory',
                'home_drive',
                'initials',
                'logon_name',
                'manager',
                'office',
                'password',
                'profile_path',
                'script_path',
                'surname',
                'title',
                'telephone',
                'web_page',
      			'userAccountControl'
              );

        foreach ($attributes as $item)
        {
          $val = $request->getParameter($item);
          if (!empty($val)) $mod[$item]=$val;
        }
              
        
        if (!empty($username))
        {
          if (count($mod)>0)
            var_dump($ldap->user_modify($username,$mod));
          else
          echo "No hi han camp per modificar";
        }
        else
        echo "Usuari a modificar desconegut";
  		
		return Viewer::NONE;
  }
  
}
