<?php

include_once("extension/all2eiclear/classes/all2eiclearclass.php");

class all2eiClearType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'all2eiclear';

    /*!
     Constructor
    */
    function all2eiClearType()
    {
        $this->eZWorkflowEventType( self::WORKFLOW_TYPE_STRING,  ezi18n( 'all2eiclear/event', "Adds iClear Support" ) );
        $this->setTriggerTypes( array( 'shop' => array( 'checkout' => array( 'before', 'after' ) ) ) );
    }

    /*!
      Executes the workflow.
    */
    function execute( $process, $event )
    {
        $http = eZHTTPTool::instance();
               
        $parameters = $process->attribute( 'parameter_list' );
		    $orderID = $parameters['order_id'];

        $order = eZOrder::fetch( $orderID );

        $selectedPayment = $this->getCurrentPayment( $event );
        
        
        switch ( $selectedPayment )
        {
            case 'prepayment':
            {
                //$this->setPaymentType( $orderID, 'prepayment' );
                return eZWorkflowType::STATUS_ACCEPTED;
            }
            break;

            case 'iclear':
            {
                $workflowstatus = $this->getPaymentWorkflowStatus( $event );
                
            	//$workflowstatus = $event->Attribute('data_text2');
            	
            	//echo $workflowstatus;
                
                // Get account inforamtionen
                if ($workflowstatus == "login")
                {
	                $process->Template = array();
    	            $process->Template['templateVars'] = array ( 'event' => $event );                	
                	$process->Template['templateName'] = 'design:workflow/iclear/login.tpl';	
                	return eZWorkflowType::STATUS_FETCH_TEMPLATE_REPEAT;                  
                }
                
                // Check account inforamtionen
                elseif ($workflowstatus == 'checklogin')
                {                	
					$account = $event->Attribute( 'data_text3' );
					$account = explode('|',$account);
					
					$iclearAccount = $account[0];
					$iclearAccountPW = $account[1];
					
                	$server = new all2eiClearClass;	
					$server->initialize();
					
					$result = $server->login($iclearAccount,$iclearAccountPW);
					
					// retry if login failed
					if ($result != 1)
					{
		                $process->Template = array();
	    	            $process->Template['templateVars'] = array ( 'event' => $event , 'error' => 'true' );                	
	                	$process->Template['templateName'] = 'design:workflow/iclear/login.tpl';	
	                	return eZWorkflowType::STATUS_FETCH_TEMPLATE_REPEAT;    						
					}
					// successful login -> continue with the workflow
					else 
					{
						$result = $server->processOrder();
						return eZWorkflowType::STATUS_ACCEPTED;
					}
                }
                
                
            }
            break;
        }
        
        
        $process->Template = array();
        $process->Template['templateName'] = 'design:workflow/selectpaymentmethod.tpl';
        $process->Template['templateVars'] = array ( 'event' => $event );
        
		    return eZWorkflowType::STATUS_FETCH_TEMPLATE_REPEAT;
    }

    function getCurrentPayment( $event )
    {
        $paymentType = $event->Attribute( 'data_text1' );
        
        $http = eZHTTPTool::instance();

        if ( $http->hasPostVariable( 'SelectButton' ) && $http->hasPostVariable( 'SelectedPaymentMethod' ) )
        {
            $paymentType = $http->postVariable( 'SelectedPaymentMethod' );
            $event->setAttribute( 'data_text1', $paymentType );
            $event->store();
        }
        else if( $http->hasPostVariable( 'CancelButton' ) )
        {
            $paymentType = null;
        }

        return $paymentType;
    }
    
    function getPaymentWorkflowStatus( $event )
    {
        echo $event->Attribute('data_text2');
        $http = eZHTTPTool::instance();	
        
    	// Request Login
        if ($event->Attribute('data_text2') == '')
        {
			$event->setAttribute( 'data_text2', 'login' );
			$event->store();        	
			return 'login';
        }
        
        // got account information, proceed with login
        elseif ( $http->hasPostVariable( 'iclearAccount' ) && $http->hasPostVariable( 'iclearAccountPW' ) )
        {
            $iclearAccount = $http->postVariable( 'iclearAccount' );
            $iclearAccountPW = $http->postVariable( 'iclearAccountPW' );
            
            $event->setAttribute( 'data_text2', 'checklogin' );
            $event->setAttribute( 'data_text3', $iclearAccount.'|'.$iclearAccountPW );
            $event->store();
            return 'checklogin';
        }
        
        else 
        {
        	return 'login';	
        }
    }
}

eZWorkflowEventType::registerEventType( all2eiClearType::WORKFLOW_TYPE_STRING, "all2eiClearType" );

?>
