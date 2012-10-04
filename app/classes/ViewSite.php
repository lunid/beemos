<?php

    namespace app\classes;
    use \sys\classes\mvc\View;     
    
    class ViewSite extends View {
                
        function render($layoutName=''){
            $arrMenuHz['index']     = array('home','Home','Bem-vindo');
            $arrMenuHz['sobreNos']  = array('aInterbits,privacidade,contato','Sobre nós','Conheça a Interbits');
            $arrMenuHz['assineJa']  = array('planos,recursos,pagamento','Assine Já','Bem-vindo');
            $arrMenuHz['superpro']  = array('provas,listas,relatorios','SupePro','Principais Recursos');
            $arrMenuHz['ajuda']     = array('faq,tutor,suporte','Ajuda','FAQ & Tutoriais');              
           
            $menuAtivo              = $layoutName;
                        
            $this->MENU_HORIZONTAL = \HtmlComponent::menuHorizontal($arrMenuHz,$menuAtivo);    
            parent::render($layoutName);
        }
    }
?>
