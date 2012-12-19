<?php
    
    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;      
    use \sys\classes\mvc\View;
    use \modelo\classes\models\Pagina1Model;
    
    class Pagina1 extends Controller {
        
        public function actionIndex(){
            try{
                
                //Parâmetros da página atual:
                $htmlName       = 'pagina1';
                $title          = 'EXEMPLO - Página 1';
                $layoutName     = 'pagina1';
                $listCss        = 'modelo/menuVertical';
                
                $objModel       = new Pagina1Model();        
                $objViewPart    = new ViewPart($htmlName);                
                $objViewPart->METODO_ATUAL  = __METHOD__;                
                $objViewPart->MENU_VERTICAL = $objModel->loadMenuVertical();                         
                
                $objView = new View();
                $objView->setCss($listCss);
                $objView->setPlugin('jquery_autoajax');
                $objView->setLayout($objViewPart);
                $objView->TITLE = $title;
                $objView->render($layoutName);
                
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }    
                    
        public function actionSubitem1(){
            $htmlName       = 'subitem1';            
            $layoutName     = 'pagina1';
            
            $objViewPart    = new ViewPart($htmlName);                                      

            $objView = new View();
            $objView->setTemplate('blank');            
            $objView->setLayout($objViewPart);
            $objView->render($layoutName);
        }        
        
        public function actionSubitem2(){
                        
            $layoutName     = 'pagina1';            
            $objViewPart    = new ViewPart();                                      
            $objViewPart->setContent('<div>Você está na página 1, subitem 2.</div>');

            $objView = new View();
            $objView->setTemplate('blank');            
            $objView->setLayout($objViewPart);
            $objView->render($layoutName);
        } 
        
        public function actionWs(){
            
        }
    }
?>
