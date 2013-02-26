<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class PlanosEprecos extends mvc\ExceptionController {
        
        public function actionIndex(){
                $bodyHtmlName   = 'planosEprecos';
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
                $objView        = mvc\MvcFactory::getView();
                
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip - Planos & Preços';
                $objView->MENU_MAIN = Menu::main(__CLASS__);

                $listCss    = 'site.planosEprecos2';
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
                $layoutName = 'planosEprecos';
                $objView->render($layoutName);            
            
        }
        
        function actionCompare(){
            
        }
    }
?>
