<?php

include_once('kernel/common/template.php');

$Module = $Params["Module"];
$http = eZHTTPTool::instance();
$postVariables = $http->attribute( 'post' );
    
$tpl = templateInit();
    
$tpl->setVariable('params',$params);
    
$Result = array();
$Result['content'] = $tpl->fetch( 'design:shop/iclear_shopbetreiber_success.tpl' );
$Result['path'] = array( array( 'text' => 'Shopcustomer Success'
                         )
                  );
?>  
