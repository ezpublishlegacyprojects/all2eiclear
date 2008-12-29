<?php
/* Concrete implementation of the enpoint script */
$url = 'http://iclear.all2e.com/iclear_soap/';
$wsdl = 'ICShopServices.wsdl';

$server = new SoapServer($url.$wsdl,
                          array(
                            'soap_version' => SOAP_1_2,
                            'encoding' => 'UTF-8'
                          )
                        );

// second (third, fourth..... n+1) argument are used in the constructor of the class specified ($initarg)
$server->setClass( 'ICShopServices', false );
$server->handle();


/* Implementation of the acting class */
class ICShopServices {
	
	public function __construct($initarg = false) {
		// this argument comes from $server->setClass(<classname>, $initarg) above
		if($initarg) {
			// setup something
		}
	}
	
	public function acceptOrder(
                              $sessionID,
                              $basketID,
                              $currency,
                              $orderStatus,
                              $orderStatusMessage,
                              $BasketItemList,
                              $deliveryAddress,
                              $requestID 
	                           )
  {
      $remoteIP = $_SERVER["REMOTE_ADDR"];
      $all2eiclearINI = eZINI::instance( 'all2eiclear.ini' );
      $iclearIP = $all2eiclearINI->variable( 'acceptOrderSettings','iclearIP' );
      
      // Check if the request comes from the iclear server
  		if( $_SERVER["REMOTE_ADDR"] == $iclearIP )
  		{
          $status = 0; // OK
      		$statusMessage = 'OK';
      		$shopURL = $all2eiclearINI->variable( 'iclearSettings','shopURL' );
      		
      		if( $shopURL == "orderview" )
      		{
              $shopURL = "/shop/orderview/".$basketID;
              eZURI::transformURI($shopURL, false, "full");
          }
      		
      		if( $orderStatus === 0 )
      		{
      		    $payedByIclearStatus = $all2eiclearINI->variable( 'acceptOrderSettings','payedByIclearStatus' );
      		    $order = eZOrder::fetch($basketID);
      		    $order->modifyStatus($payedByIclearStatus, 14);
      		    
              $out = array(
          		  'requestID' => $requestID,
          		  'status' => $status,
          		  'statusMessage' => $statusMessage,
          		  'shopURL' => $shopURL
          		);
          }
          else
          {
              $out = array(
          		  'requestID' => $requestID,
          		  'status' => $status,
          		  'statusMessage' => $statusMessage,
          		  'shopURL' => $shopURL
          		);
          }
      }
      else
      {
          $FailedURL = $all2eiclearINI->variable( 'acceptOrderSettings','FailedURL' );
          $out = array(
              'requestID' => $requestID,
              'status' => 1,
              'statusMessage' => "Failed",
              'shopURL' => $FailedURL
          );
      }
  		
  		return $out;
	}
}

eZExecution::cleanExit();


?>
