<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Sobre extends mvc\ExceptionController {
        
        public function actionIndex(){
                $bodyHtmlName   = 'sobre';
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
                $objView        = mvc\MvcFactory::getView();
                
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip - Sobre Nós';
                $objView->MENU_MAIN = Menu::main(__CLASS__);

                $listCss    = 'site.sobre';
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
                $layoutName = 'sobre';
                $objView->render($layoutName);            
            
        }
        
        public function actionPolitica(){
                $bodyHtmlName   = 'politicaPrivacidade';
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
                $objView        = mvc\MvcFactory::getView();
                
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip - Política de Privacidade';
                $objView->MENU_MAIN = Menu::main(__CLASS__);

                $listCss    = 'site.home';
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
                $layoutName = 'sobre';
                $objView->render($layoutName);            
            
        }        
     }
?>
