<?php

    namespace app\classes\views;
    
    use \sys\classes\mvc\View;      
    use \app\classes\html\Menu;
    
    class ViewSite extends View {       
        function render($layoutName='', $objMemCache = NULL){
            try {
                $objMenu = new Menu();
                
                $objMenu->addItem("Home", "/interbits", "home");
                $objMenu->addItem("Empresa", "/interbits/empresa", "empresa");
                $objMenu->addItem("Planos", "/interbits/planos", "planos");
                $objMenu->addItem("Depoimentos", "/interbits/depoimentos", "depoimentos");
                $objMenu->addItem("Tutoriais", "/interbits/tutoriais", "tutoriais");
                $objMenu->addItem("Perguntas Frequentes", "/interbits/perguntas", "perguntas");
                $objMenu->addItem("Blog", "http://www.sprweb.com.br/lab/blog/", "blog");
                $objMenu->addItem("Contato", "/interbits/contato", "contato");
                
                $objMenu->setItemSel($layoutName);
                
                $this->MENU = $objMenu->render($layoutName);
                
                parent::render($layoutName, $objMemCache = NULL);
            } catch(\Exception $e) {
                throw($e);
            }
        }
    }
?>
