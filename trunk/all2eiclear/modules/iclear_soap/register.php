<?php

include_once("extension/all2eiclear/classes/all2eiclearclass.php");
include_once('kernel/common/template.php');

$Module = $Params["Module"];
$http = eZHTTPTool::instance();
$postVariables = $http->attribute( 'post' );

$parameters = array();

if( $Module->isCurrentAction( 'Register' ) )
{
    $parameters = array(  "FirmaName"               => $postVariables["FirmaName"],         
                          "BetreiberVorname"        => $postVariables["BetreiberVorname"],  
                          "BetreiberNachname"       => $postVariables["BetreiberNachname"], 
                          "FirmaStrasse"            => $postVariables["FirmaStrasse"],      
                          "FirmaHausNr"             => $postVariables["FirmaHausNr"],       
                          "FirmaPLZ"                => $postVariables["FirmaPLZ"],          
                          "FirmaOrt"                => $postVariables["FirmaOrt"],          
                          "FirmaLand"               => $postVariables["FirmaLand"],         
                          "FirmaEmail"              => $postVariables["FirmaEmail"],        
                          "FirmaFon"                => $postVariables["FirmaFon"],          
                          "FirmaFax"                => $postVariables["FirmaFax"],          
                          "FirmaBankName"           => $postVariables["FirmaBankName"],     
                          "FirmaBLZ"                => $postVariables["FirmaBLZ"],          
                          "FirmaKto"                => $postVariables["FirmaKto"],          
                          "FirmaKtoInhaber"         => $postVariables["FirmaKtoInhaber"],   
                          "FirmaUSTID"              => $postVariables["FirmaUSTID"],        
                          "FirmaStNr"               => $postVariables["FirmaStNr"],         
                          "FirmaFA"                 => $postVariables["FirmaFA"],           
                          "FirmaFALand"             => $postVariables["FirmaFALand"],       
                          "FirmaHRBNr"              => $postVariables["FirmaHRBNr"]                      
    );
    $errors = array();
    
    if( $postVariables["FirmaName"] != "" && $postVariables["BetreiberVorname"] != "" && $postVariables["BetreiberNachname"] != "" && $postVariables["FirmaStrasse"] != "" &&
        $postVariables["FirmaHausNr"] != "" && $postVariables["FirmaPLZ"] != "" && $postVariables["FirmaOrt"] != "" && $postVariables["FirmaLand"] != "" &&
        $postVariables["FirmaEmail"] != "" && $postVariables["FirmaFon"] != "" && $postVariables["FirmaBankName"] != "" && $postVariables["FirmaBLZ"] != "" &&
        $postVariables["FirmaKto"] != "" && $postVariables["FirmaKtoInhaber"] != "" && $postVariables["FirmaUSTID"] != "" )
    {
        $shop = new all2eiClearClass();
        $shop->initialize();
        
        $ret = $shop->shopBetreiberImplement($parameters);
        
        /* Workaround, because the response is not clear */ 
        $result = $ret["xml_trace"];
        
        $xmldomxml = new eZXML();
    		$response =  $xmldomxml->domTree($result);
        $dom = $response->elementsByName( "status" );
    		$ret = $dom[0]->textContent();
    		
    		if( $ret == 0 )
    		{
            $Module->redirectTo("/iclear_soap/register_success");
        }
    }
    else
    {
        $errors = array( "text" => "Bitte füllen Sie Pflichtfelder aus!" );
    }
    
    
    
}                                         
elseif( $Module->isCurrentAction( 'Cancel' ) )
{
    $uri = $postVariables["RedirectUriAfterDiscard"]?$postVariables["RedirectUriAfterDiscard"]:"/";
    $Module->redirectTo($uri);            
}

$tpl = templateInit();                    

$tpl->setVariable('parameters',$parameters);
$tpl->setVariable('errors',$errors);

$Result = array();                        
$Result['content'] = $tpl->fetch( 'design:shop/iclear_shopbetreiber.tpl' );
$Result['path'] = array( array( 'text' => 'Shopcustomer'
                         )
                  );
?>
