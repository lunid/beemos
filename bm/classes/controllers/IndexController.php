<?php

use \sys\classes\mvc\ViewPart; 
use \sys\classes\mvc\View; 


class Index {
    function actionIndex(){
        try {
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
