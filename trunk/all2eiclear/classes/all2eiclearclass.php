<?php

//include_once("extension/all2eiclear/classes/iclear/nusoap.php");
//include_once("extension/all2eiclear/classes/iclear/iclear_config.php");
//include_once("extension/all2eiclear/classes/iclear/iclear_error.php");
//include_once("extension/all2eiclear/classes/iclear/iclear_wsdl.php");
//include_once("extension/all2eiclear/classes/iclear/iclear_catalog.php");


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
    
    
	static function initialize()
    {
        
    }

    function getShopAccount()
    {
		$all2eiclearINI = eZINI::instance( 'all2eiclear.ini' );
    	$this->shopID = $all2eiclearINI->variable( 'iclearSettings','ShopID' );
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
    
        
	function processBasket()
    {
		eZDebug::writeNotice( 'Processing Basket items' );
		
		$basket = eZBasket::currentBasket();
		$basketItemCollection = $basket->productCollection();
		$basketItems = $basketItemCollection->itemList();

		foreach ($basketItems as $index => $basketItem)
		{
			// get the object to fetch the currency
			// what happens here if we have different currencies ?
			
			$productContentObject = $basketItem->attribute( 'contentobject' );
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
	        $items[$index]->itemNr = $basketItem->ContentObjectID;
	        $items[$index]->title = $basketItem->Name;
	        $items[$index]->numOfArtikel = $basketItem->ItemCount;
	        
	        if ($basketItem->IsVATIncluded == '0')
	        {
	        	$priceExVAT = $basketItem->Price;
	        	$priceInVAT = $basketItem->Price * ( (100 + $basketItem->VATValue) / 100);
	        }	
	        else
	        {
	        	$priceInVAT = $basketItem->Price;
	        	$priceExVAT = $basketItem->Price / ( (100 + $basketItem->VATValue) / 100);
	        	$priceExVAT = round( $priceExVAT, 2); // rounded to cents value
	        }
	        
	        $items[$index]->priceN = $priceExVAT * 100; // Price converted to cents
	        $items[$index]->priceB = $priceInVAT * 100; // Price converted to cents
	        
	        $items[$index]->ustSatz = $basketItem->VATValue;		
		}
		
		$this->basketItems = $items;
		$this->orderID = $basket->OrderID;
		$this->currency = $currency;
            		
    }

   function sendOrder()
    {

		$client = new SoapClient('http://www.iclear.de/ICOrderServices.wsdl', array('trace' => 1));
        eZDebug::writeNotice( 'Sending Basket items' );
        
        $orderResponse = $client->sendOrderS2S( $this->requestID,
                                                 $this->addrID,
                                                 $this->shopID,
                                                 $this->orderID,
                                                 $this->currency,
                                                 $this->basketItems,
                                                 $this->sessionID);
        return $orderResponse;    
    }    

    
	function processOrder()
    {
		
    	// Login
    	if ($this->sessionID == '')
		{
			$result = $this->login();			
		}	
		
		// Get customers address 
    	if ($this->addrID == '')
		{
			$result = $this->getCustomerAddress();			
		}			

		// Get the shop account information
		$this->getShopAccount();
		
		// Set customers delivery address 
    	
    	// Proess the basket items
    	$this->processBasket();	

    	// Send the basket to iclear
    	$this->sendOrder();	
    	
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




/*<part name="orderStatus" type="xsd:long"/>
<part name="orderStatusMessage" type="xsd:string"/>
<part name="deliveryAddress" type="typens:Addres

  <part name="requestID" type="xsd:string" /> 
  <part name="deliveryAddrID" type="xsd:long" /> 
  <part name="shopID" type="xsd:long" /> 
  <part name="basketID" type="xsd:string" /> 
  <part name="currency" type="xsd:string" /> 
  <part name="basketItems" type="typens:BasketItemArray" /> 
  <part name="sessionID" type="xsd:string" /> 
  <part name="language" type="xsd:string" /> 

  
  
        $server = new soap_server();
        $server->soap_defencoding = 'UTF-8';
        $server->configureWSDL('SH_ICUserServices', 'urn:SH_ICUserServices');
        $server->wsdl->schemaTargetNameSpace = 'urn:SH_ICUserServices';
        
        
        $server->wsdl->addComplexType(
      																'login',
      																'complexType',
      																'array',
      																'all',
      																'',
      																array(
      																	'userUN'	=> array('name'	=> 'testkundeA', 'type' => 'xsd:string'),
      																	'userPW'	=> array('name'	=> 'testkundeB', 'type' => 'xsd:string'),
      																	'sessionID'	=> array('name'	=> session_id(), 'type' => 'xsd:string')
      																)
      															);
        
        
        $server->register(
        									'userLogin',
        									array(
        										'userUN' => 'testkundeA',
        										'userPW' => 'testkundeB',
        										'sessionID' => session_id()
        									),
        									array(
        										'requestID' => 'xsd:string',
        										'status' => 'xsd:long',
        										'statusMessage' => 'xsd:string',
        										'sessionID' => 'xsd:string'
        									),
        									'urn:SH_ICUserServices',
        									'urn:SH_ICUserServices#login',
        									'rpc',
        									'encoded',
        									'Iclear user login'
        								);
        
        //$HTTP_RAW_POST_DATA = '<soapenv:Envelope> <soapenv:Body> <ns0:login> <userUN soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xsi:type="xsd:string">testkundeA</userUN> <userPW soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xsi:type="xsd:string">testkundeB</userPW> <sessionID soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xsi:type="xsd:string">'.session_id().'</sessionID> </ns0:login> </soapenv:Body> </soapenv:Envelope>';
        $HTTP_RAW_POST_DATA  = preg_replace('/xmlns=""/', '', $HTTP_RAW_POST_DATA);
        $res = $server->send($HTTP_RAW_POST_DATA);
        */































/*
    // DUMP
    
    $server->register(
        									'userLogin',
        									array(
        										'userUN' => 'testkundeA',
        										'userPW' => 'testkundeB',
        										'sessionID' => session_id()
        									),
        									array(
        										'requestID' => 'xsd:string',
        										'status' => 'xsd:long',
        										'statusMessage' => 'xsd:string',
        										'sessionID' => 'xsd:string'
        									),
        									'urn:SH_ICUserServices',
        									'urn:SH_ICUserServices#login',
        									'rpc',
        									'encoded',
        									'Iclear user login'
        								);
        
        $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
        // removing bogus namespace definitions - nusoap doesn't like it!
        $HTTP_RAW_POST_DATA  = preg_replace('/xmlns=""/', '', $HTTP_RAW_POST_DATA);
        $server->service($HTTP_RAW_POST_DATA);
        
        
        
        
        
        
        
        
    
    $server->configureWSDL('SH_ICOrderServices', 'urn:SH_ICOrderServices');
        $server->wsdl->schemaTargetNameSpace = 'urn:SH_ICOrderServices';
        
        
        
        // define incoming address element
        $server->wsdl->addComplexType(
        																'Address',
        																'complexType',
        																'array',
        																'all',
        																'',
        																array(
        																	'addrAnrede'	=> array('name'	=> 'addrAnrede', 'type' => 'xsd:string'),
        																	'addrFirstname'	=> array('name'	=> 'addrFirstname', 'type' => 'xsd:string'),
        																	'addrLastname'	=> array('name'	=> 'addrLastname', 'type' => 'xsd:string'),
        																	'addrOrgname'	=> array('name'	=> 'addrOrgname', 'type' => 'xsd:string'),
        																	'addrStrasse'	=> array('name'	=> 'addrStrasse', 'type' => 'xsd:string'),
        																	'addrHausNr'	=> array('name'	=> 'addrHausNr', 'type' => 'xsd:string'),
        																	'addrPLZ'	=> array('name'	=> 'addrPLZ', 'type' => 'xsd:string'),
        																	'addrOrt'	=> array('name'	=> 'addrOrt', 'type' => 'xsd:string'),
        																	'addrLand'	=> array('name'	=> 'addrLand', 'type' => 'xsd:string')
        																)
        															);
        
        // define incoming basketItem element
        $server->wsdl->addComplexType(
        																'BasketItem',
        																'complexType',
        																'struct',
        																'all',
        																'',
        																array(
        																	'itemNr'	=> array('name'	=> 'itemNr', 'type' => 'xsd:string'),
        																	'title'	=> array('name'	=> 'title', 'type' => 'xsd:string'),
        																	'numOfArtikel'	=> array('name'	=> 'numOfArtikel', 'type' => 'xsd:long'),
        																	'priceN'	=> array('name'	=> 'priceN', 'type' => 'xsd:string'),
        																	'priceB'	=> array('name'	=> 'priceB', 'type' => 'xsd:string'),
        																	'ustSatz'	=> array('name'	=> 'ustSatz', 'type' => 'xsd:string'),
        																	'Status'	=> array('name'	=> 'Status', 'type' => 'xsd:string')
        																)
        															);
        
        // define incoming basketItemList element
        $server->wsdl->addComplexType(
        																'BasketItemList',
        																'complexType',
        																'array',
        																'',
        																'SOAP-ENC:Array',
        																array(),
        																array(
        																	array('ref'	=> 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:BasketItem[]')
        																),
        																'tns:BasketItemList'
        															);
        
        // register functions as service
        $server->register(
        									'acceptOrder',
        									array(
        										'sessionID' => 'xsd:string',
        										'basketID' => 'xsd:string',
        										'currency' => 'xsd:string',
        										'orderStatus' => 'xsd:long',
        										'orderStatusMessage' => 'xsd:string',
        										'deliveryAddress' => 'tns:Address',
        										'requestID' => 'xsd:string',
        										'BasketItemList' => 'tns:BasketItemList'
        									),
        									array(
        										'requestID' => 'xsd:string',
        										'status' => 'xsd:long',
        										'statusMessage' => 'xsd:string',
        										'shopURL' => 'xsd:string'
        									),
        									'urn:SH_ICOrderServices',
        									'urn:SH_ICOrderServices#acceptOrder',
        									'rpc',
        									'encoded',
        									'Iclear shop side order web service'
        								);
        $server->register(
        									'validateOrder',
        									array(
        										'sessionID' => 'xsd:string',
        										'basketID' => 'xsd:string',
        										'currency' => 'xsd:string',
        										'orderStatus' => 'xsd:long',
        										'orderStatusMessage' => 'xsd:string',
        										'deliveryAddress' => 'tns:Address',
        										'requestID' => 'xsd:string',
        										'BasketItemList' => 'tns:BasketItemList'
        									),
        									array(
        										'requestID' => 'xsd:string',
        										'status' => 'xsd:long',
        										'statusMessage' => 'xsd:string'
        									),
        									'urn:SH_ICOrderServices',
        									'urn:SH_ICOrderServices#validateOrder',
        									'rpc',
        									'encoded',
        									'Iclear shop side order validation web service'
        								);
    

*/



?>

