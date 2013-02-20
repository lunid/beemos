<?php 
    use \sys\classes\mvc as mvc;   
    
    class Index extends mvc\Controller {
        
        public function actionIndex(){
            
            $bodyHtmlName   = 'home';
            $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);

            /*
             * Cria uma view que irá concatenar o contéudo de $objViewPart com
             * um template. Caso nenhum template seja definido, o padrao.html será usado.
             */
            $objView            = mvc\MvcFactory::getView();     
            $objView->setLayout($objViewPart);
            $objView->TITLE     = 'MVC - Bem-vindo';
            
            $objView->setPlugin('form');
            
            $objView->render($bodyHtmlName);    
        }                
    }
?>
