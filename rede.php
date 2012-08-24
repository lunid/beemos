<?php
session_start();

$config = dirname(__FILE__) . '\lib\hybridauth\config.php';
require_once( dirname(__FILE__) . "\lib\hybridauth\Hybrid\Auth.php" );

$rede = @$_GET["rede"];

if($rede != null){
    // initialize Hybrid_Auth with a given file
    $hybridauth = new Hybrid_Auth($config);

    // try to authenticate with the selected provider
    $adapter = $hybridauth->authenticate($rede);

    // then grab the user profile
    $user_profile = $adapter->getUserProfile();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <? if($user_profile->identifier != null){ ?>
        <script type="text/javascript">
            window.opener.document.getElementById("rede_id").value = '<?=$user_profile->identifier?>';
            window.opener.document.getElementById("rede_name").value = '<?=$rede?>';
            window.opener.document.getElementById("nome").value = '<?=$user_profile->firstName . ' ' . $user_profile->lastName?>';
            window.opener.document.getElementById("email").value = '<?=$user_profile->emailVerified?>';
            window.opener.document.getElementById("email").readOnly = true;
            window.opener.document.getElementById("email").style.backgroundColor = '#DADADA';
            window.close();
        </script>
        <? } ?>
    </head>
    <body>
        <div>Falha ao efetuar login com Rede Social. Tente novamente</div>
    </body>
</html>