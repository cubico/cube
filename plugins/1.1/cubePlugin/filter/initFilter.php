<?php
class initFilter extends Filter{
	
	public function execute($filterChain)
	{
		$i18n=Config::get("settings:i18n","site");
		Site::setLocaleConfig($i18n);
		$filterChain->execute();
	}
}

?>