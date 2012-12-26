<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    
    class Tutoriais extends Controller {
        /**
         * Conteúdo da página Empresa
         */
        function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('tutoriais');
                
                //Template
                $tpl        = new ViewSite();
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | Tutoriais';

                $tpl->render('tutoriais');            
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
