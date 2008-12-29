<?php

include_once("extension/all2eiclear/classes/all2eiclearclass.php");

class all2eSelectPaymentType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'all2eselectpayment';

    function all2eSelectPaymentType()
    {
        $this->eZWorkflowEventType( self::WORKFLOW_TYPE_STRING,  ezi18n( 'all2eiclear/event', "Select Payment Method" ) );
        $this->setTriggerTypes( array( 'shop' => array( 'confirmorder' => array( 'before' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
		    $orderID = $parameters['order_id'];

        $order = eZOrder::fetch( $orderID );

        $selectedPayment = $this->getCurrentPayment( $event );
        
        switch ( $selectedPayment )
        {
            case 'prepayment':
            {
                $this->setPaymentCosts( $orderID, 0.0,'prepayment' );
                return eZWorkflowType::STATUS_ACCEPTED;
            }
            break;

            case 'iclear':
            {
                $this->setPaymentCosts( $orderID, 0.0, 'iclear' );
                return eZWorkflowType::STATUS_ACCEPTED;
            }
            break;
            case null:
            {
                
            }
            break;
            default:
            {
                $this->setPaymentCosts( $orderID, 0.0, $selectedPayment );
                return eZWorkflowType::STATUS_ACCEPTED;
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
        $paymentType =  null;
        $http = eZHTTPTool::instance();

        if ( $http->hasPostVariable( 'SelectButton' ) && $http->hasPostVariable( 'SelectedPaymentMethod' ) )
        {
            $paymentType = $http->postVariable( 'SelectedPaymentMethod' );
            $event->setAttribute( 'data_text1', $paymentType );
            $event->store();
        }
        else if ( $http->hasPostVariable( 'CancelButton' ) )
        {
            $paymentType = null;
        }

        return $paymentType;
    }
    
    function setPaymentCosts( $orderID, $price, $type )
    {
        $order = eZOrder::fetch( $orderID );
        $orderItems = $order->attribute( 'order_items' );		
        
        foreach ( array_keys( $orderItems ) as $key )
        {
            $orderItem =& $orderItems[$key];
            if ( $orderItem->attribute( 'type' ) == "all2eiclearpayment" )
            {
                $orderItem->remove();
            }
        }
        
        $vat = '19';

        $orderItem = new eZOrderItem( array( 'order_id' => $orderID,
                                             'description' => $type,
                                             'price' => $price,
                                             'vat_value' => $vat,
                                             'is_vat_inc' => 0,
                                             'type' => 'all2eiclearpayment' ) );
        $orderItem->store();
    } 
    
}

eZWorkflowEventType::registerEventType( all2eSelectPaymentType::WORKFLOW_TYPE_STRING, "all2eSelectPaymentType" );

?>
