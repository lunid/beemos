<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Recursos extends mvc\ExceptionController {
        
        public function actionIndex(){
                $bodyHtmlName   = 'recursos';
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);
                
                /*
                 * Cria uma view que irá concatenar o contéudo de $objViewPart com
                 * um template. Caso nenhum template seja definido, o padrao.html será usado.
                 */
                $objView            = mvc\MvcFactory::getView();
                $objView->MENU_MAIN = Menu::main(__CLASS__);
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Recursos';

                $listCss    = 'site.recursos';
                $listJs     = 'site.recursos';
                $listCssInc = '';
                $listJsInc  = '';
                $listPlugin = '';
                
               
                $objView->setCss($listCss);                                       
                $objView->render($bodyHtmlName);             
        }
        
        function actionBeneficios(){
            
        }
        
        function actionGateway(){
            
        }
        
        function actionFaturasonline(){
            
        }
        
        function actionWebsercie(){
            
        }
        
        function actionPainel(){
            
        }
        
        function actionInterfacepagamento(){
            
        }
        
        function actionDesenvolvedor(){
            
        }
    }
?>
