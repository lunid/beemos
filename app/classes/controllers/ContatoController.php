<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    
    class Contato extends Controller {
        /**
         * Conteúdo da página Empresa
         */
        function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('contato');
                
                //Template
                $tpl = new ViewSite();
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | Contato';
                
                $tpl->setJs('app/contato');
                
                $tpl->render('contato');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
    }

?>
