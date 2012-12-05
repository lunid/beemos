<?php

use \sys\classes\mvc\ViewPart; 
use \sys\classes\mvc\View; 
use \sys\classes\util as util;

class Index {
    function actionIndex(){
        try {
            $objConcat = new util\Concat('email/teste.html'); 
            $objConcat->setStrMatriz('');
            $objConcat->addParam('FULANO', 'Claudio');
            $objConcat->addParam('NUM_PEDIDO', 'ADB1234');
            echo $objConcat->render();
            
            $objViewPart = new ViewPart('home');
            $objView     = new View();
            $objView->setLayout($objViewPart);
            $objView->render('index');  
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}

?>
