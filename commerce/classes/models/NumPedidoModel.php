<?php

    namespace commerce\classes\models;
    use \sys\classes\mvc\Model;  
    use \common\db_tables as TB; 
    
    class NumPedidoModel extends Model {   
        
        function getNumPedidoIni($idAssinatura){
            $tbAssinaturaConfig = new TB\AssinaturaConfig();
            $field              = 'NUM_PEDIDO_INI';
            $result             = $tbAssinaturaConfig->select($field)->where('ID_ASSINATURA='.$idAssinatura)->execute();
            $numPedidoIni       = (count($result))?$result[0][$field]:0;
            return $numPedidoIni;
        }
        
        function getProxNumPedidoTest($idAssinatura){
            return $this->getProxNumPedido($idAssinatura, 'TEST');            
        }        
        
        function getProxNumPedido($idAssinatura,$ambiente='PROD'){
            $tbAssinaturaConfig = new TB\AssinaturaConfig();
            $field              = 'NUM_PEDIDO_INI';
            $result             = $tbAssinaturaConfig->select($field)->where('ID_ASSINATURA='.$idAssinatura)->execute();
            $numPedidoIni       = (count($result))?$result[0][$field]:0;
            return $numPedidoIni;
        }    
        
        function checkNumPedidoDisponivel($numPedido){
            if ($numPedido > 0) {
                $tbEcommPedido = new TB\EcommPedido();   
            }
        }
    }

?>
