<?php

	/**
	 * Elgg standard tools drop down
	 * This will be populated depending on the plugins active - only plugin navigation will appear here
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008
	 * @link http://elgg.org/
	 * 
	 */
	 
		//$topbar_menu = get_register('menu');
		
		
		$topbar_menu=$vars['submenus'];
		//var_export($topbar_menu);
		if (is_array($topbar_menu) && count($topbar_menu) > 0) {
			$alphamenu = array();
			foreach($topbar_menu as $item) {
				if (!is_array($item['name'])) $alphamenu[$item['name']] = $item;
			}
			if (isset($vars['sort']) && $vars['sort']) ksort($alphamenu);
		
?>
<script type="text/javascript">
 $(function() {
 $('ul.topbardropdownmenu').elgg_topbardropdownmenu();
 });
</script>
<ul class="topbardropdownmenu">
    <li class="drop">
    	<a href="#" class="menuitemtools"><?php echo $vars['menu']; ?></a>
    <ul style="z-index: 5004; display: none;">      
      <?php

			foreach($alphamenu as $item) {
    			if (isset($item['target'])) $target="target=\"{$item['target']}\"";else $target="";
				
    			echo "<li class=\"\" >
    					<a class=\"\" {$target} href=\"".$item['value']."\">" . ucfirst($item['name']) . "</a>    					
    				  </li>";
    			
			} 
				
     ?>
      </ul>      
    </li>
</ul>
<?php

		}

?>
