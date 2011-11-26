<?php

class LdapSearchsLdapPluginActions extends Actions{

   ///////////////////////////////////////
  public function executeRetrievebyNif($request)
  {
		
	   $peer=new AduserPeer();
		$respuesta=$peer->retrievebyNif(strtoupper($request->param));
		
		//$ldap=new Ldap();
  		//$respuesta=$ldap->retrievebyNif(strtoupper($request->param));
  		
  		$template=$request->template; 				
 		if ($template!=null) {
 			$template=preg_replace("/%%([^%]*)%%/","'.\$respuesta['$1'].'",$template);
 			eval("\$respuesta['NOM']='$template';");
 		}
 		  		
  		if ($request->error_message) $respuesta['message']=$request->error_message;
  		echo json_encode($respuesta);
  		$this->setTemplate(false);
  }
  
  public function executeRetrievebyNom($request)
  {
		$id =  $request->id;	
		$result=array(array('value'=>'',
							'text'=>array(	//'NOM_COGNOMS'=>Viewer::_echo('otherelements'),
											'NOM'=>Viewer::_echo('otherelements'),
											//'COGNOM1'=>'',
											//'COGNOM2'=>'',
										)));
			
		$ldap=new AduserPeer();
		
		$template=$request->template; 				
 		if ($template!=null) $template=preg_replace("/%%([^%]*)%%/","'.\$respuesta['$1'].'",$template);
 		else //$template="'.\$respuesta['NOM'].' '.\$respuesta['COGNOM1'].' '.\$respuesta['COGNOM2'].'";
 			   $template="'.\$respuesta['NOM'].'";
 		
 		if ($request->id){
			//$respuesta=$ldap->retrievebyNif($id);
			eval("\$str_template='$template';");
			$result=array(array('value'=>$id,'text'=>$str_template)); 
		}
		else{			
			
			$respuesta=$ldap->retrievebyNom(strtoupper($request->queryString));		
			
	  		$entrades = $respuesta['USUARIS'];
	  		
	  		if (count($entrades)>0){
		  		foreach ($entrades as $ent){
		 			$nif=isset($ent['samaccountname'])?$ent['samaccountname']:null;
		 			if ($nif!==null && strlen($nif)==9)
		 			{
		 				$aux = isset($ent['cn'])?$ent['cn']:$nif;
		 				//if (isset($ent['sn'])) $cognoms=explode(";",$ent['sn']); // estan separados por ; !!!
		 				
		 				$respuesta=array(	'NOM'=>isset($ent['displayname'])?$ent['displayname']:$aux
		 									//,'NOM'=>isset($ent['givenname'])?$ent['givenname']:$aux
		 									//,'COGNOM1'=>isset($cognoms[0])?$cognoms[0]:' '
		 									//,'COGNOM2'=>isset($cognoms[1])?$cognoms[1]:' '
		 								);
		 				//$respuesta['NOM_COGNOMS']=$respuesta['NOM'].' '.$respuesta['COGNOM1'].' '.$respuesta['COGNOM2'];
		 				
		 				eval("\$str_template='$template';");
						
		 				$result[]=array('value'=>$nif,'text'=>array('NOM'=>$respuesta['NOM']
		 															//,'COGNOM1'=>$respuesta['COGNOM1']
		 															//,'COGNOM2'=>$respuesta['COGNOM2']
		 															//,'NOM_COGNOMS'=>$str_template
		 														)); 				
		 			}
				}
			}
		}
		echo json_encode($result);
		$this->setTemplate(false);
  }

  public function executeUpServeibyLdap($request)
	{
		//echo Util::validaNifCifNie($request->param)." ".$request->param;
		$nifnie=Util::validaNifCifNie($request->param);
		
		if ($nifnie!=1 && $nifnie!=3){
			switch($nifnie){			
			case -1:
				$respuesta=array("message"=>"$nifnie.Nif '{$request->param}' incorrecte. Recorda que el Nif requereix una lletra","error"=>1);
				break;
			case -3:
				$respuesta=array("message"=>"$nifnie. Nie '{$request->param}' amb format incorrecte.","error"=>1);
				break;
			default:
				$respuesta=array("message"=>"$nifnie.El format de les dades '{$request->param}' no és un Nif/Nie","error"=>1);
			}
				
			echo json_encode($respuesta);
			$this->setTemplate(false);
			return Viewer::NONE;
		}
		
		$ldap=new Ldap();
  		$respuesta=$ldap->retrievebyNif(strtoupper($request->param));
		//echo _r($respuesta);die();
		
  		$codi_centre=$request->getParameter('for_d_cursos_inscripcions.INS_ID_CURS'); // 'ES014'
		
  		$db=db::create(Config::get('database:sfintra'));
		// codi up
  		$query="select up_id_up cur_id_centre
				from intra_a_up_servei 
				where up_codi_ldap='{$respuesta['CODI_CENTRE']}' 
				-- and up_es_centre=1
				and up_data_baixa is null";
		
		$t=$db->query($query);
		if (!empty($t)) $respuesta['CODI_UP']=$t[0]['CUR_ID_CENTRE'];else $respuesta['CODI_UP']="0";
		
		//*$query="select up_id_up cur_id_servei 
		//		from intra_a_up_servei 
		//		where up_codi_ldap='{$respuesta['CODI_SERVEI']}'
		//		 and up_es_centre=0
		//		and up_data_baixa is null";
		
		//$t=$db->query($query);
		//if (!empty($t)) $respuesta['CODI_SERVEI']=$t[0]['CUR_ID_SERVEI'];else $respuesta['CODI_SERVEI']="-1";
		
		// categoria
		$query="select cat_id_categoria from intra_a_categ_professionals 
				where cat_codi_ldap='{$respuesta['CATEGORIA']}'
				and cat_data_baixa is null";
		
		$t=$db->query($query);
		$respuesta['CATEGORIA_PROFESSIONAL']=$t[0]['CAT_ID_CATEGORIA'];
		
		$query="select sit_agrup from intra_a_situacions_laborals where sit_codi='{$respuesta['SITUACIO']}'";
		//echo $query;
		$t2=$db->query($query);
		
		$respuesta['SIT_AGRUP']=$t2[0]['SIT_AGRUP'];
		echo json_encode($respuesta);
  		$this->setTemplate(false);
		
		return Viewer::NONE;
			
	}
  
}

?>