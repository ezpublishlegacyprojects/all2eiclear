<?php

$client = new SoapClient("http://iclear.all2e.com/index.php/ger/iclear_soap/ICShopServices.wsdl", 
                          array(
                            'soap_version' => SOAP_1_2,
                            'encoding' => 'UTF-8'
                          ));

$result = $client->acceptOrder( "123","edfrgdger","EUR","0","OK",array(),"asda","asdf" );



echo "<pre>";
var_dump($result); 
echo "</pre>";

eZExecution::cleanExit();


?>
