<?php 
/*
 * //realpath en linux, si hay un montaje te da el path del montaje
 * //$path=realpath($host.$pathbd);

 * MANUAL (SIMPLE)
 * 
 * Viewer::view('output/imagegallery',
						array(
							'id'=>'photos',
							'root'=>'C:\Apache2\htdocs\cube\web',
							'images'=>array(
								'/public_oftalmo/OCLO035033100/20091118/image1.jpg',
								'/public_oftalmo/OCLO035033100/20091118/image2.jpg'
						)));	

  * MANUAL (ARRAY)
  * 
  *  Viewer::view('output/imagegallery',
						array(
							'id'=>'photos',
							'root'=>'C:\Apache2\htdocs\cube\web',
							'images'=>array(
								array('url'=>'/public_oftalmo/OCLO035033100/20091118/image1.jpg',
											 '/public_oftalmo/OCLO035033100/20091118/image2.jpg')
						)));	

 
 * PATH
 * 
 *  Viewer::view('output/imagegallery',
						array(
							'id'=>'photos',
							'path'=>realpath(".")."/public_oftalmo".$fonsull->Path
							//'root'=>'C:\Apache2\htdocs\cube\web',
						));	
 * 
 *  
 */

$id=$vars['id'];

if (isset($vars['path'])){

	if (!defined('JQUERY_GALLERY_GET_IMAGES')){
		function getImages($dirname,&$images=array(),$root,$filter=array()) {
		     if (is_dir($dirname) && substr($dirname,0,1)!='.')
		         $dir_handle = opendir($dirname);
		     if (!isset($dir_handle))
		         return false;
		     while($file = readdir($dir_handle)) {
		         if (substr($file,0,1)!='.') {
		             if (!is_dir($dirname."/".$file)){
						//echo "<br/>".$root." ".$dirname;
		             	$path=substr($dirname,strlen($root))."/".$file;
						$k=explode(".",$path);
						if (in_array(strtoupper(end($k)),$filter)){
		             		$info=array('url'=>str_replace("\\","/",$path),
		             					'type'=>end($k),
		             					'title'=>$file,
		             					'description'=>'',
		             					'file'=>$path
		             				);
		             		$images[]=$info;
						}
		             }    
		             else
		                getImages($dirname.'/'.$file,$images,$root,$filter);          
		         }
		     }
		     closedir($dir_handle);
		     return true;
		 }
		 define('JQUERY_GALLERY_GET_IMAGES',true);
	}
	
	$images=array();
	if (!isset($vars['filters']))$vars['filters']=array('JPEG','JPG','PNG','GIF');
	if (!isset($vars['root']))$vars['root']=$_SERVER['DOCUMENT_ROOT'];
	
	getImages($vars['path'],$images,$vars['root'],$vars['filters']);
	$vars['images']=$images;
}

if (!empty($vars['images'])){

if (!defined('JQUERY_GALLERY_VIEW_OUTPUT')){ 
define('JQUERY_GALLERY_VIEW_OUTPUT',true);

?>
<link rel="stylesheet" href="/js/jquerygalleryview/galleryview.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="/js/jquerygalleryview/style.css" media="screen,projection">
<script type="text/javascript" src="/js/jquerygalleryview/jquery.galleryview-1.1.js"></script>
<script type="text/javascript" src="/js/jquerygalleryview/jquery.timers-1.1.2.js"></script>

<?php } ?>
	
<!-- InstanceBeginEditable name="head" -->

<script type="text/javascript">
	$(window).load(function() {
		
		if ($.browser.msie && $.browser.version.substr(0,1)=='6'){
			var clear="/img/clear.gif"; //path to clear.gif
			pngfix=function(){var els=document.getElementsByTagName('*');var ip=/\.png/i;var i=els.length;while(i-- >0){var el=els[i];var es=el.style;if(el.src&&el.src.match(ip)&&!es.filter){es.height=el.height;es.width=el.width;es.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+el.src+"',sizingMethod='crop')";el.src=clear;}else{var elb=el.currentStyle.backgroundImage;if(elb.match(ip)){var path=elb.split('"');var rep=(el.currentStyle.backgroundRepeat=='no-repeat')?'crop':'scale';es.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+path[1]+"',sizingMethod='"+rep+"')";es.height=el.clientHeight+'px';es.backgroundImage='none';var elkids=el.getElementsByTagName('*');if (elkids){var j=elkids.length;if(el.currentStyle.position!="absolute")es.position='static';while (j-- >0)if(!elkids[j].style.position)elkids[j].style.position="relative";}}}}}
			pngfix();
		}
	});
	
	$(document).ready(function(){
		$('#<?php echo $id; ?>').galleryView({
			panel_width: 450,
			panel_height: 300,
			frame_width: 100,
			frame_height: 100,
			transition_interval: 0
		});
	});
</script>
<?php $links='<ul class="filmstrip">'; ?>
<div id="<?php echo $id; ?>" class="galleryview">
  <?php foreach($vars['images'] as $k=>$image){
  		
  		if (!is_array($image))$image=array('url'=>$image);
  		else if (!isset($image['url'])) $image['url']=null;
  		
  		if (!isset($image['title'])) $image['title']=$image['url'];
  		if (!isset($image['description'])) $image['description']=Viewer::_echo('imagegallery:image')." ".$k;
  		
  		if ($image['url']!==null){
  ?>
  <div class="panel">
     <a href="<?php echo $image['url']; ?>" target="_blank"><img width="450" src="<?php echo $image['url']; ?>" /></a> 
    <!-- <div class="panel-overlay"><h2><?php echo $image['title']; ?></h2><p><?php echo $image['description']; ?></p></div>  -->
  </div>
  <?php 
  	
  			$links.='<li><img src="'.$image['url'].'" width="98" alt="'.$image['title'].'" title="'.$image['title'].'" /></li>';
		} // image - null
	} 
  	echo $links.'</ul>'; 
?>
</div>
<?php 
}
else { echo Viewer::_echo('imagegallery:notfound');}
?>
