<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Ajuda extends mvc\ExceptionController {
        
        public function actionIndex(){
                $bodyHtmlName   = 'ajuda';
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
                $objView        = mvc\MvcFactory::getView();
                
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip - Central de Ajuda';
                $objView->MENU_MAIN = Menu::main(__CLASS__);

                $listCss    = 'site.ajuda';
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
