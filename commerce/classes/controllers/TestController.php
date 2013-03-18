<?php

    use \commerce\classes\models\EcommModel;    
    use \sys\classes\util as util;

    class Test {
        
        public function actionIndex(){            

            $request    = "uid=b98af3c46666cb58b73677859074e116&format=text";
            $objCurl    = new util\Curl('http://dev.superproweb.com.br/commerce/bradesco/boleto/');
            $objCurl->setPost($request);
            $objCurl->createCurl();
            $errNo = $objCurl->getErro();
            if ($errNo == 0){
                    $ret = $objCurl->getResponse();
                    echo $ret;
            } else {
                    $msgErr = 'Erro: '.$objCurl->getOutput();
                    echo $msgErr;
                    die();
            }  
        }
    }
?>
