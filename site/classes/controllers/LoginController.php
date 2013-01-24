<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Login extends mvc\ExceptionController {
        public function actionIndex(){
                $bodyHtmlName   = 'login';
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
                $objView        = mvc\MvcFactory::getView();
                $authMsgErr     = \Auth::getMessage();
                //Mensagem de erro Auth
                if(strlen($authMsgErr) > 0){
                    $msgErr = "<div id='msgErr' class='boxForm notice error'>
                        <span class='icon medium' data-icon='X'></span>
                        " . $authMsgErr . "
                        <a class='icon close' data-icon='x' href='#close' style='display: inline-block;'></a>                            
                    </div>";
                    \Auth::unsetMessage();
                }else{
                    $msgErr = "";
                }

                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip - Área do Assinante';
                $objView->MENU_MAIN = Menu::main(__CLASS__);
                $objView->MSG_ERR   = $msgErr;
                
                $listCss    = 'site.login';
                $listJs     = '';
                $listCssInc = '';
                $listJsInc  = '';
                $listPlugin = '';
                
               
                $objView->setCss($listCss);
                
                /*
                $objView->setJs($listJs);
                $objView->setCssInc($listCssInc);
                $objView->setJsInc($listJsInc);
                $objView->setPlugin($listPlugin);
                */             
                $layoutName = $bodyHtmlName;
                $objView->render($layoutName);            
            
        }
    }
?>
