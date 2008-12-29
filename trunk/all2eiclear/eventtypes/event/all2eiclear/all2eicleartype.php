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
        $parameters = $process->attribute( 'parameter_list' );
		    $orderID = $parameters['order_id'];
		    
		    $client = new all2eiClearClass;	
				if( !$client->processOrder($orderID) )
				{
            return eZWorkflowType::STATUS_ACCEPTED;
        }
    }

}

eZWorkflowEventType::registerEventType( all2eiClearType::WORKFLOW_TYPE_STRING, "all2eiClearType" );

?>
