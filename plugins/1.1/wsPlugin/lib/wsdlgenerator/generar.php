<?php

 require_once("WSDLCreator.php");

 $test = new WSDLCreator("laboratori.ICSCampTarragona", "http://dharma:8000/ws/server.php");
 $test->addFile("../class/laboratori/laboratori.class.php");

 $test->addURLToClass("Laboratori","http://dharma:8000/ws/server.php");
 $test->addURLToTypens('LabResult','http://dharma:8000/ws/class/laboratori/labResult.class.php');
 $test->createWSDL();

 //$test->saveWSDL(dirname(__FILE__)."/laboratori.ICSCampTarragona.wsdl", true);
 $test->saveWSDL("../wsdl/laboratori.ICSCampTarragona.wsdl", true);

 

 
?>