<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    use \app\classes\html\MenuInterno;
    
    class Empresa extends Controller {
        /**
         * Conteúdo da página Empresa
         */
        function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('empresa');
                
                //Menu
                $this->montaMenu($objViewPart);

                //Template
                $tpl        = new ViewSite();
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | A Empresa';

                $tpl->render('empresa');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        /**
         * Conteúdo da página Equipe
         */
        function actionEquipe(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('equipe');
                
                //Menu
                $this->montaMenu($objViewPart);

                //Template
                $tpl        = new ViewSite();
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | A Empresa';

                $tpl->render('empresa');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        private function montaMenu($objView){
            $objMenu = new MenuInterno();
            
            $objMenu->addItem("Empresa", "/interbits/empresa", "empresa");
            $objMenu->addItem("Equipe", "/interbits/empresa/equipe", "equipe");
            
            $objView->MENU_INTERNO = $objMenu->render();
        }
    }

?>
