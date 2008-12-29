<?php

$Module = array( 'name' => 'iclear_soap' );

$ViewList = array();
$ViewList['accept_order'] = array( 'script' => 'accept_order.php',
                                   'functions' => array( 'accept_order' )
                                 );
$ViewList['testorder'] = array( 'script' => 'testorder.php',
                                   'functions' => array( 'accept_order' )
                                 );
$ViewList['ICShopServices.wsdl'] = array( 'script' => 'icshopservices.php',
                                   'functions' => array( 'accept_order' )
                                 );
$ViewList['iclearpayment'] = array( 'script' => 'iclearpayment.php',
                                   'functions' => array( 'accept_order' ),
                                   'single_post_actions' => array( 'SelectButton' => 'SelectButton', 'CancelButton' => 'CancelButton')
                                 );


$ViewList['register'] = array( 'script' => 'register.php',
                               'functions' => array( 'register' ),
                               'single_post_actions' => array( 'Submit' => 'Register', 'Discard' => 'Cancel' )
                             );
$ViewList['register_success'] = array( 'script' => 'register_success.php',
                                       'functions' => array( 'register' )
                                     );


$FunctionList = array();
$FunctionList['accept_order'] = array();
$FunctionList['register'] = array();

?>
