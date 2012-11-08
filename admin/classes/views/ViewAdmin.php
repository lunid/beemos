<?php

    namespace admin\classes\views;
    
    use \sys\classes\mvc\View;     
    use \admin\classes\html\MenuVertical;
    
    class ViewAdmin extends View {
                
        function render($layoutName=''){
            
            try {
                $objMenuVert    = new MenuVertical();
                $objMenuVert->setItemSel($layoutName);
                
                $objMenuVert->addItem('index','home','Home');
                $objMenuVert->addItem('usuario','usuarios','Usuários');
                $objMenuVert->addItem('comercial','comercial','Comercial');
                $objMenuVert->addItem('cliente','clientes','Clientes');
                $objMenuVert->addItem('relatorio','relatorios','Relatorios');            
                $objMenuVert->addItem('escolas','escolas','Escolas');            

                $this->MENU_VERTICAL = $objMenuVert->render($layoutName);
                
                parent::render($layoutName);
                 
            } catch(\Exception $e) {
                throw($e);
            }
        }
    }
?>
