<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WsController
 *
 * @author Supervip
 */
use \sys\classes\mvc\Controller;        
use \sys\classes\mvc\ViewPart;      
use \sys\classes\mvc\View;
use \app\classes\superpro\WsUsuarioClient;

class Ws extends Controller {

    public function actionTest(){                 
        $layoutName     = 'wsTest';
        
        $objWs          = new WsUsuarioClient();
        $objWs->callNovoUsuario('Claudio', 'claudio@supervip.com.br','claudio','abcdadf');
        
        $objViewPart    = new ViewPart();                                      
        $objViewPart->setContent('Teste');
        $objView = new View();             
        $objView->setLayout($objViewPart);
        $objView->render($layoutName);
    }     
}

?>
