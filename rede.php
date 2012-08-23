<?php
session_start();

$config = dirname(__FILE__) . '\lib\hybridauth\config.php';
require_once( dirname(__FILE__) . "\lib\hybridauth\Hybrid\Auth.php" );

try {
    $rede = @$_GET["rede"];

    if($rede != null){
        // initialize Hybrid_Auth with a given file
        $hybridauth = new Hybrid_Auth($config);

        // try to authenticate with the selected provider
        $adapter = $hybridauth->authenticate($rede);

        // then grab the user profile
        $user_profile = $adapter->getUserProfile();
        
        echo "<pre style='color:#FF0000;'>";
        print_r($user_profile);
        echo "</pre>";
        die;
    }
} catch (Exception $e) {
    echo "Error: please try again!";
    echo "Original error message: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div>TODO write content</div>
    </body>
</html>