<?php

    namespace commerce\classes\helpers;
    use \commerce\classes\models\PedidoModel;
    
    class PedidoHelper {
        
        private $objDadosPedido = NULL;
        private $arrDadosPedido = array();
        private $arrItensPedido = array();
        private $arrDadosSacado = array();
        
        function __construct($numPedido){
            if ($numPedido > 0) {
                $objPedidoModel  = new PedidoModel();
                if ($objPedidoModel->loadPedido($numPedido)){
                    $objDadosPedido         = $objPedidoModel->getObjDados();
                    $this->objDadosPedido   = $objDadosPedido;
                    $this->arrDadosPedido   = $objPedidoModel->getArrDados();
                    $this->arrItensPedido   = $objPedidoModel->getItens();
                    $this->arrDadosSacado   = $objPedidoModel->getDadosSacado();
                }
            } else {
                throw new \Exception('Um número de pedido válido não foi informado.');
            }                   
        }
        
        function getObjInfo(){
            return $this->objDadosPedido;            
        }
        
        function getArrInfo(){
            return $this->arrDadosPedido;
        }
        
        function getItens(){
            return $this->arrItensPedido;
        }    
        
        function getDadosSacado(){
            return $this->arrDadosSacado;
        } 
        
        function saveNumeroTituloBoleto($numeroTitulo){
            $objPedidoModel  = new PedidoModel();
            $objPedidoModel->saveNumeroTituloBoleto($numeroTitulo);
        }
    }
?>
