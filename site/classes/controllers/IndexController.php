<?php       
    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Index extends mvc\ExceptionController {
        
        public function actionIndex(){
            
                $objViewPart = mvc\MvcFactory::getViewPart('home');
                
                /*
                 * Cria uma view que irá concatenar o contéudo de $objViewPart com
                 * um template. Caso nenhum template seja definido, o padrao.html será usado.
                 */
                $objView            = mvc\MvcFactory::getView();
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Teste';
                $objView->MENU_MAIN = Menu::main(__CLASS__);

                $listCss    = 'site.home';
                $listJs     = '';
                $listCssInc = '';
                $listJsInc  = '';
                $listPlugin = '';
                
               
                $objView->setCss($listCss);
                            
                $layoutName = 'index';
                $objView->render($layoutName);
        }                
    }
?>
