<?php
    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Identificacao extends mvc\ExceptionController {
        
        protected function actionForm($idPlano,$plano){
                $bodyHtmlName   = 'identificacao';
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
                $objView->TITLE     = 'Identifique-se';
                 $objView->ID_PLANO = $idPlano; //Básico, Profissional ou Corporativo
                $objView->PLANO     = $plano; //Básico, Profissional ou Corporativo
                $objView->MENU_MAIN = Menu::main(__CLASS__);
                $objView->MSG_ERR   = $msgErr;
                
                $listCss    = 'site.identificacao';
                $listJs     = 'site.identificacao';
                $listCssInc = '';
                $listJsInc  = '';                                
               
                $objView->setCss($listCss);
                $objView->setJs($listJs);
                $objView->setPlugin('form');
                $objView->setPlugin('facebook');
                
                $layoutName = $bodyHtmlName;
                $objView->render($layoutName);              
        }
        
        function actionNovo(){
            //Novo cadastro
            $login = Request::post('LOGIN');
            echo $login;
        }
        
        function actionNovoCadastroConf(){
            
        }
    }
        
?>
