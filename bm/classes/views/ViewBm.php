<?php

    namespace bm\classes\views;
    
    use \sys\classes\mvc\View;      
    
    class ViewBm extends View {       
        
        function render($layoutName=''){
            
            try {
                /*
                $objMenuVert    = new MenuVertical();
                $objMenuVert->setItemSel($layoutName);
                $lang           = \Application::getLanguage();            
                
                //MenuVertical->addItem(cls(class Css),link(link do item),text)       
                $objMenuVert->addItem('index',      $this->setModuleUrl('home'),      'Home');
                $objMenuVert->addItem('usuario',    $this->setModuleUrl('usuarios'),  'Usuários');
                $objMenuVert->addItem('comercial',  $this->setModuleUrl('comercial'), 'Comercial');
                $objMenuVert->addItem('cliente',    $this->setModuleUrl('clientes'),  'Clientes');
                $objMenuVert->addItem('relatorio',  $this->setModuleUrl('relatorios'),'Relatorios');            
                $objMenuVert->addItem('escolas',    $this->setModuleUrl('escolas'),   'Escolas');            

                $this->MENU_VERTICAL = $objMenuVert->render($layoutName);
                */
                parent::render($layoutName);
                 
            } catch(\Exception $e) {
                throw($e);
            }
        }
    }
?>
