<?php
    
    /**
    * Classe Controller que refere-se à Administração de Micro-Conteúdos
    */
    use \admin\classes\controllers\AdminController;
    
    class MicroConteudo extends AdminController {

        /**
        * Conteúdo da página inicial do admin.
        */
        function actionIndex(){
            try{                
                //Home
                $objViewPart = $this->mkViewPart('micro_conteudo');
                
                //Template
                $tpl = $this->mkView();                                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'ADM | SuperPro | Micro-conteúdo';
                                
                //Instância do Plugin Dinatree
                $tpl->setPlugin("dynatree");
                
                //Instância de JS
                $tpl->setJs('admin/micro_conteudo');
                $tpl->forceCssJsMinifyOn();
                
                
                $tpl->render('micro_conteudo');            
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

