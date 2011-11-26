<?php
		$contents = "";
		
		if (isset($vars['content']))
			$contents .= $vars['content'];
		
		if (isset($vars['submenu']))
			$contents .= "<div id=\"owner_block_submenu\">" . $vars['submenu'] . "</div>"; // plugins can extend this to add menu options
		
		
			
		if (!empty($contents)) {
			echo "<div id=\"owner_block\"  ".((isset($vars['align']))?'align="'.$vars['align'].'"':'')."  >";
			echo $contents;
			echo "</div><div id=\"owner_block_bottom\"></div>";
		}

?>