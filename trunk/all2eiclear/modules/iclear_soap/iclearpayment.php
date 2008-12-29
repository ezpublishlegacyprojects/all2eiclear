<?php

include_once("extension/all2eiclear/classes/all2eiclearclass.php");
include_once('kernel/common/template.php');

$http = eZHTTPTool::instance();
$Module = $Params["Module"];
$postVariables = $http->attribute( 'post' );


if( $Module->isCurrentAction( 'SelectButton' ) && $postVariables["OrderID"] )
{
    $client = new all2eiClearClass;	
		$client->processOrder($orderID);
}
else
{
    $uri = $postVariables["RedirectUriAfterDiscard"]?$postVariables["RedirectUriAfterDiscard"]:"/";
    $Module->redirectTo($uri);
}

?>
