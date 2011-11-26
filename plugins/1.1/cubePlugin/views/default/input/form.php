<?php
	/**
	 * Create a form for data submission.
	 * Use this view for forms rather than creating a form tag in the wild as it provides
	 * extra security which help prevent CSRF attacks.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['body'] The body of the form (made up of other input/xxx views and html
	 * @uses $vars['method'] Method (default POST)
	 * @uses $vars['enctype'] How the form is encoded, default blank
	 * @uses $vars['action'] URL of the action being called
	 * 
	 */
         
	if (isset($vars['internalid'])) { $id = $vars['internalid']; } else { $id = ''; }
	if (isset($vars['internalname'])) { $name = $vars['internalname']; } else { $name = ''; }
	$body = $vars['body'];
	$action = $vars['action'];
	if (isset($vars['enctype'])) { $enctype = $vars['enctype']; } else { $enctype = ''; }
	if (isset($vars['method'])) { $method = $vars['method']; } else { $method = 'POST'; }

	// Generate a security header
	$security_header = "";
	if ($vars['disable_security']!=true)
	{
		$ts = time();
		$token = Controller::generateActionToken($ts);
		$security_header = Viewer::view('input/hidden', array('internalname' => '___token', 'value' => $token));
		$security_header .= Viewer::view('input/hidden', array('internalname' => '___ts', 'value' => $ts));
	}
       
       if (isset($vars['ajax']) && $vars['ajax']): ?>
        <script type="text/javascript">
        
        var serializeFormParams=function(obj){
                var params  = $(obj).serializeObject();                
                var params2 = $(obj).attr('action').split(/[?&]([^=]*)=([^&]|$)/);
				
				var json='';
				 jQuery.each(params2, function(i, val){
                    if (i!=0 && val!=''){
                        if ((i%2)==1) {
                                    json += '"' + val + '":';
                            } else {
                                    json += '"' + val.replace(/"/g, '\\"') + '",\n';
                            }
                    }
                });
				
				params2=$.parseJSON("{" + json.substring(0, json.length - 2) + "}");
				return mergeObjects(params,params2);
            }
            
            var sendajaxFunction<?php echo $id; ?>=function(json){                
                $.ajax({
                            url: '<?php echo $action;?>',
                            type: '<?php echo $method;?>',
                            data: json,
                            cache: false,
                            dataType: 'json',
                            timeout: 60000,
                            success: function(res){                                    
                                 //alert('Data is loaded'+res.error+res.message);    								 
								 var hasinfo=$(".reportedcontent_content.archived_report_blue");
								 $('.error').removeClass('error').find('label .message').remove(); // borrar posibles errores anteriores
                              if (res.error==0){                                         
                                    // no recargamos la página hasta que se produce la inserción									
                                    if (res.redirect!=undefined) document.location=res.redirect;
                                    else{
										var hasok=$(".reportedcontent_content.archived_report");										
										$('.admin_statistics').replaceWith(res.content);
										//$('.admin_statistics').find(':first').before('<div class="reportedcontent_content archived_report">'+hasok.html()+'</div>');
										$('.admin_statistics').find(':first').before('<div class="reportedcontent_content archived_report"><b>El llistat conté '+res.count+' element/s.</b></div>');
										var updateok='<div class="reportedcontent_content archived_report">'+res.message+'</div>';
										var haserrors=hasinfo.next('.active_report');																				
										if (haserrors.length!=0) haserrors.html('').hide();

										if (hasok.length!=0) {	
											hasok.html(res.message);
											hasok.show();
										}
										else{										
												var hasinfo=$(".reportedcontent_content.archived_report_blue");
												hasinfo.next().before(updateok);
											}
										}                                        
                                 }
                                 else{                                      
                                    var haserrors=hasinfo.next('.active_report');
                                    var hasok=$(".reportedcontent_content.archived_report");								
                                    var someerrors='<div class="reportedcontent_content active_report">'+res.message+'</div>';
									
                                    if (haserrors.length!=0) haserrors.html(res.message).show();                                    
                                    else if (hasinfo) hasinfo.next().before(someerrors)                                    
                                    else $(event.target).before(someerrors);
									
									//if (hasok.length!=0) 
									hasok.html('').hide();
									
                                    // para cada error
                                    $.each(res.errors, function(index,valor) {
                                        
                                        // buscamos su padre contenedor (uno con clase column)
                                        var ob=$('[name="<?php echo $id; ?>['+res.errors[index].name+']"]').parents('div.column');
                                        // si ya tenia la clase error, actualizamos el contenido de ese error (que esta en label)
                                        if (ob.hasClass('error')) ob.find('label .message').html(res.errors[index].val);
                                        // si no tenia error, buscamos label y añadimos el contenido (message)
                                        else ob.addClass('error').find('label').append('<div class="message">'+res.errors[index].val+'</div>');
                                    });
                                 }
								 
								 
                                 $('#formloader').remove();
                                } // success
                                
                    }); // ajax
            } // function
        
        
        
        $(document).ready(function() {
                var url = '<?php echo $action;?>';                
            $("#<?php echo $id; ?>").live('submit',function (event) {  
                
				var params=serializeFormParams(this);
                $(event.target).before('<div id="formloader"><img src="/img/ajax-loader2.gif" /></div>');
                sendajaxFunction<?php echo $id; ?>(params);
                return false;
            });
        });
        
        $("a.pagination_number, a.pagination_next, a.pagination_previous").live('click',function() {
             var obj=$(this);
             var href=obj.attr('href');
             if (href!=null){
                 obj.data('offset',href.replace(/(.*)\?offset=/,''))
                 obj.removeAttr('href');
             }
             sendajaxFunction<?php echo $id; ?>({offset: obj.data('offset')});
        });
            
        </script>
		
<?php endif;   ?>     
<form <?php if ($id) { ?>id="<?php echo $id; ?>" <?php } ?> <?php if ($name) { ?>name="<?php echo $name; ?>" <?php } ?> action="<?php echo $action; ?>" method="<?php echo $method; ?>" <?php if ($enctype!="") echo "enctype=\"$enctype\""; ?>>
<?php echo $security_header; ?>
<?php echo $body; ?>
</form>
 <script type="text/javascript">		
	if ($('.admin_statistics').length==0){
			// caso default_query=false no tenemos grid
			$("#<?php echo $id; ?>").append('<div class="admin_statistics"></div>');
	}
</script>	 
		