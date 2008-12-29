<?php

class all2eiClearClass
{
    var $shopID = '';
    var $language = 'de';
    var $sessionID = '';
    var $requestID = '';
    var $addrID = '';
    var $currency = '';
    var $basketItems = '';
    var $orderID = '';
    var $shopURL = '';
    var $AquiseID = '';
    
    
    function initialize()
    {
        $this->sessionID = session_id();
        $this->getShopURL();
        $this->getShopAccount();
    }

    function getShopAccount()
    {
    	  $all2eiclearINI = eZINI::instance( 'all2eiclear.ini' );
      	$this->shopID = $all2eiclearINI->variable( 'iclearSettings','ShopID' );
    }
    
    function getShopURL()
    {
        $all2eiclearINI = eZINI::instance( 'all2eiclear.ini' );
      	$this->shopURL = $all2eiclearINI->variable( 'iclearSettings','shopURL' );
    }

    function login( $iclearAccount, $iclearAccountPW )
    {
        eZDebug::writeNotice( 'Processing iclear login' );
        $client = new SoapClient('http://www.iclear.de/ICUserServices.wsdl');
        $login_result = $client->login($iclearAccount, $iclearAccountPW, session_id()); 
    	    	
        if( $login_result["status"] == 0 )
        {
        		$this->sessionID = $login_result["sessionID"];
        		$this->requestID = $login_result["requestID"];
        		eZDebug::writeNotice( 'Sucessfully loged in' );
        		return true;
        }
        
        eZDebug::writeError( 'Unable to Login' );
        return false;
    }

    function getCustomerAddress()
    {
		    eZDebug::writeNotice( 'Processing customers iclear address data' );
        $client = new SoapClient('http://www.iclear.de/ICUserServices.wsdl');
		
		    $adressList = $client->getAddressList( $this->requestID, $this->sessionID );
            
		    $addrID = $adressList["resultElements"][0]->addrID;
            
		    $this->addrID = $addrID;   
    }
    
        
    function processBasket( $order_id )
    {
        eZDebug::writeNotice( 'Processing Basket items' );
        
        $order = eZOrder::fetch($order_id);
        $orderitems = $order->attribute("product_items");
        
        foreach ($orderitems as $index => $item)
        {
        	// get the object to fetch the currency
        	// what happens here if we have different currencies ?
        	   
            $productContentObject = $item["item_object"]->attribute( 'contentobject' );
            $productItem = $item["item_object"];
            $priceObj = null;
            $price = 0.0;
            $attributes = $productContentObject->contentObjectAttributes();
            foreach ( $attributes as $attribute )
            {
                $dataType = $attribute->dataType();
                if ( eZShopFunctions::isProductDatatype( $dataType->isA() ) )
                {
                    $priceObj = $attribute->content();
                    break;
                }
            }			
            $currency = $priceObj->attribute( 'currency' );

            $items[$index] = new stdClass();
            $items[$index]->itemNr = $productContentObject->ID;
            $items[$index]->title = $item["object_name"];
            $items[$index]->numOfArtikel = $item["item_count"];
            
            $price = round($productItem->Price * ( (100 - $item["discount_percent"] ) / 100 ),2);
            
            if ($productItem->IsVATIncluded == '0')
            {
              	$priceExVAT = $price;
              	$priceInVAT = $price * ( (100 + $productItem->VATValue) / 100);
            }	
            else
            {
              	$priceInVAT = $price;
              	$priceExVAT = $price / ( (100 + $productItem->VATValue) / 100);
              	$priceExVAT = round( $priceExVAT, 2); // rounded to cents value
            }
            
            $items[$index]->priceN = $priceExVAT * 100; // Price converted to cents
            $items[$index]->priceB = $priceInVAT * 100; // Price converted to cents
            
            $items[$index]->ustSatz = $productItem->VATValue;
        }
        
        $this->basketItems = $items;
        $this->orderID = $order_id;
        $this->currency = $currency;
    }

    function sendOrder()
    {
		    $client = new SoapClient('http://www.iclear.de/ICOrderServices.wsdl', array('trace' => 1));
		    //$client = new SoapClient('http://api.iclear.de/EndpointDebugger/ICOrderServices.wsdl', array('trace' => 1));
        eZDebug::writeNotice( 'Sending Basket items' );
        
        $orderResponse = $client->sendOrder( $this->shopID,
                                             $this->orderID,
                                             $this->currency,
                                             $this->basketItems,
                                             $this->sessionID,
                                             $this->language);
        $result = $client->__getLastResponse();
    
        return $orderResponse;    
    }
    
    function shopBetreiberImplement($params)
    {
        $all2eiclearINI = eZINI::instance( 'all2eiclear.ini' );
        $aquise = $all2eiclearINI->variable( 'iclearSettings','AquiseID' );
        $user = eZUser::currentUser();
        
        $client = new SoapClient('http://www.iclear.de/ICShopServices.wsdl', array('trace'=>1, 'encoding '=>'UTF-8' ) );
        
        $res = $client->ShopBetreiberImp( $params["FirmaName"],         
                                          $params["BetreiberVorname"],  
                                          $params["BetreiberNachname"], 
                                          $params["FirmaStrasse"],      
                                          $params["FirmaHausNr"],       
                                          $params["FirmaPLZ"],          
                                          $params["FirmaOrt"],          
                                          $params["FirmaLand"],         
                                          $params["FirmaEmail"],        
                                          $params["FirmaFon"],          
                                          $params["FirmaFax"],          
                                          $params["FirmaBankName"],     
                                          $params["FirmaBLZ"],          
                                          $params["FirmaKto"],          
                                          $params["FirmaKtoInhaber"],   
                                          $params["FirmaUSTID"],        
                                          $params["FirmaStNr"],         
                                          $params["FirmaFA"],           
                                          $params["FirmaFALand"],       
                                          $params["FirmaHRBNr"],        
                                          $aquise,                             
                                          $user->attribute("contentobject_id"),
                                          $this->sessionID                      
        );
        $result = $client->__getLastResponse();
        
        return array( "response"=>$res, "xml_trace"=>$result );
    }
    
    
    
    function processOrder( $orderID )
    {
        $req = $this->checkPaymentRequirement($orderID);
        if( $req === true )
        {
            $this->initialize();
    				// Proess the basket items
    				$this->processBasket($orderID);
    				
    				// Send the basket to iclear
    				$result = $this->sendOrder();
    				
    				if( $result["status"] == 0 )
    				{
                header("Location: ".$result["iclearURL"]);
                exit;
            }
        }
        return true;
    }    
    
    function checkPaymentRequirement($orderID, $description="iclear")
    {
        $order = eZOrder::fetch( $orderID );
        $orderItems = $order->attribute( 'order_items' );	
        $orderStatus = $order->StatusID;
        
        $all2eiclearINI = eZINI::instance( 'all2eiclear.ini' );
        $pendingStatusIDs = $all2eiclearINI->variable( 'acceptOrderSettings','pendingStatusIDs' );
        
        $payment = false;
        
        foreach ( array_keys( $orderItems ) as $key )
        {
            $orderItem =& $orderItems[$key];
            if( $orderItem->attribute( 'type' ) == "all2eiclearpayment" )
            {
                if( $orderItem->attribute( 'description' ) == $description && in_array( $orderStatus, $pendingStatusIDs ) )
                {
                    return true;
                }
            }
        }
    }
    
	/**
	*  Print SOAP request and response
	*/
	function printRequestResponse($client) {
	  echo '<h2>Transaction processed successfully.</h2>'. "\n";
	  echo '<h2>Request</h2>' . "\n";
	  echo '<pre>' . htmlspecialchars($client->__getLastRequest()). '</pre>';  
	  echo "\n";
	   
	  echo '<h2>Response</h2>'. "\n";
	  echo '<pre>' . htmlspecialchars($client->__getLastResponse()). '</pre>';
	  echo "\n";
	}
	
	/**
	*  Print SOAP Fault
	*/  
	function printFault($exception, $client) {
	    echo '<h2>Fault</h2>' . "\n";                        
	    echo "<b>Code:</b>{$exception->faultcode}<br>\n";
	    echo "<b>String:</b>{$exception->faultstring}<br>\n";
	    //writeToLog($client);
	}
    
}

?>

