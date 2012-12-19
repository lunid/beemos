<?php
    namespace modelo\classes\models;
        
    use \sys\classes\mvc\Model;        
    use \common\db_tables as TB;
    use \common\classes\models as MD;
    
    class Pagina1Model extends Model {
        /**
         * Carrega dados, geralmente a partir de um banco de dados.
         * Pode conter também métodos para lógica de negócio.
         *          
         */
        public function loadMenuVertical(){
            try{
                $arrMenu    = array('Home;index','Subitem 1;pagina1/subitem1','Subitem 2;pagina1/subitem2');
                $menu       = "<ul class='list'>";//A classe .list está definida em /assets/modelo/pagina1.js
                foreach($arrMenu as $item) {
                    list($label,$link) = explode(';',$item);
                    $menu .= "<li><a href='{$link}#result'>{$label}</a></li>";
                }
                $menu .= "</ul>";
                return $menu;
                
            }catch(Exception $e){
                throw $e;
            }
        }
        
        
    }
?>
