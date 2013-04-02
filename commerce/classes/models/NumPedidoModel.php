<?php

    namespace commerce\classes\models;
    use \sys\classes\mvc\Model;  
    use \common\db_tables as TB; 
    
    class NumPedidoModel extends Model {   
        
        function checkNumPedidoDisponivel($numPedido){
            if ($numPedido > 0) {
                $tbEcommPedido = new TB\EcommPedido();     
            }
        }
    }

?>
