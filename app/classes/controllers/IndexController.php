<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \sys\classes\mvc\View;
    use \sys\classes\util\Request;
    
    class Index extends Controller {

        /**
        *Conteúdo da página home
        */
        function actionIndex(){
            try{                     
                //$this->cacheOff(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('home');

                //Template
                $tpl = new View();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web';

                $tpl->setJsInc('app/home');                
                $tpl->setPlugin('diapo');
                $tpl->setCss('app/site');
                //$tpl->forceCssJsMinifyOn();
                $tpl->onlyExternalCssJs();

                $tpl->render('index');            
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
