<?php $strippedname=strtr($vars['internalname'],"[].","___"); ?>
Nom:<input type="text"  name="nom<?php echo $strippedname; ?>" id="nom<?php echo $strippedname; ?>" />