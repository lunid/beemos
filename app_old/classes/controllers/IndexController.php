<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    use \sys\classes\security\Token;
    
    class Index extends Controller {

        /**
        *Conteúdo da página home
        */
        function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('home');
                
                //Token para Login
                $objTk              = new Token(2);
                $objViewPart->TOKEN = $objTk->protectForm();
                
                //Template
                $tpl        = new ViewSite();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web';
                
                //JS
                $tpl->setPlugin('diapo');
                $tpl->setJs('app/home');                
                
                $tpl->render('home');            
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
