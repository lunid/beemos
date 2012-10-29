<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;      
    use \sys\classes\util\Request;
    use \sys\classes\util\Date;
    use \admin\models\Top10Model;
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial do admin.
    */
    class Index extends Controller {

        /**
        *Conteúdo da página home do admin.
        */
        function actionIndex(){
            try{
                //Home            
                $objViewPart = new ViewPart('home');
                
                //Template
                $tpl = new View();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'ADM | beeMoS';
                
                //$tpl->forceCssJsMinifyOn();
                //$tpl->onlyExternalCssJs();
              
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

