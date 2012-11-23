<?php

    namespace admin\classes\views;
    
    use \sys\classes\mvc\View;      
    use \admin\classes\html\MenuVertical;
    
    class ViewAdmin extends View {       
        
        function render($layoutName='', $objMemCache = NULL){
            
            try {
                $objMenuVert    = new MenuVertical('xml/menuVertical.xml');
                $objMenuVert->setItemSel($layoutName);

                $this->MENU_VERTICAL = $objMenuVert->render($layoutName);
                
                parent::render($layoutName, $objMemCache = NULL);
                 
            } catch(\Exception $e) {
                throw($e);
            }
        }
    }
?>
