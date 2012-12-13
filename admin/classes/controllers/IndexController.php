<?php
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial do admin.
    */
    use \admin\classes\controllers\AdminController;
    
    class Index extends AdminController {

        /**
        *Conteúdo da página home do admin.
        */
        function actionIndex(){
            try{                
                //Home
                $objViewPart = $this->mkViewPart('home');
                
                //Template
                $tpl = $this->mkView();                                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'ADM | SuperPro';
                
                $tpl->forceCssJsMinifyOn();
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

