<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolaModel;

    class Escola extends AdminController {
        /**
         * Inicializa a página de Escola
         */
        public function actionIndex(){
            try{
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/escola');
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro | Área da Escola';
                $tpl->SUBTITLE      = 'Escola';
                                
                //$tpl->setJs('admin/escola');
                //$tpl->setCss('admin/escola');
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('escola');
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
