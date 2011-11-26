<?php

	if (isset($vars['selected']) && $vars['selected'] == true) {
		$selected = "class=\"selected\"";
	} else {
		$selected = "";
	}
	
	if (isset($vars['onclick'])) {
		$onclick = "onclick=\"".$vars['onclick']."\"";
	} else if (isset($vars['action'])){ 
		$onclick=" onclick=\"document.location='".$vars['url']."/".$vars['action']."';\" ";
		
	}else {
		$onclick = "";
	}
	
	if (!isset($vars['href'])) $vars['href']="#";
	
	
?>
<li <?php echo $selected; ?>><a href="<?php echo $vars['href']; ?>" <?php echo $onclick; ?>><?php echo $vars['label']; ?></a></li>