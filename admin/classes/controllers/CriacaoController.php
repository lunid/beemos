<?php
    use \admin\classes\controllers\AdminController;
    
    class Criacao extends AdminController{
        function actionIndex(){
            //View do Grid de Escolas
            $objViewPart = $this->mkViewPart('criacao/conteudo');
                
            //Template
            $tpl                = $this->mkView();
            $tpl->setLayout($objViewPart);
            $tpl->TITLE         = 'ADM | Criação';

            //Instância de JS
            $tpl->setJs('criacao/javascript');
            $tpl->setCss('admin/criacao/estilo');
            $tpl->forceCssJsMinifyOn();
            
            $tpl->render('escola');
        }
        
        function actionAbas(){
            //View do Grid de Escolas
            $objViewPart = $this->mkViewPart('criacao/aba');
                
            //Template
            $tpl                = $this->mkView();
            $tpl->setLayout($objViewPart);
            $tpl->TITLE         = 'ADM | Criação';

            //Instância de JS
            $tpl->setJs('criacao/javascript');
            $tpl->setCss('admin/criacao/estilo');
            $tpl->forceCssJsMinifyOn();
            
            $tpl->render('escola');
        }
    }
?>
