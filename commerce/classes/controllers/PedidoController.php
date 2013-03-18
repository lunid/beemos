<?php
    
use \commerce\classes\models\PedidoModel;
use \sys\classes\util\Request;   
use \sys\classes\mvc as mvc; 
use \commerce\classes\controllers\IndexController;

class Pedido extends IndexController {
   
    
    function actionInfoPedido(){
        $objDadosCfg    = $this->getDadosCfg();
        $numPedido      = Request::all('numPedido','NUMBER');
        
        if ($numPedido > 0) {
            $objPedido  = new PedidoModel();
            if ($objPedido->loadPedido($numPedido)){
                $objDadosPedido = $objPedido->getObjDados();
                $arrDadosPedido = $objPedido->getArrDados();
                $arrItensPedido = $objPedido->getItens();
                
                foreach($arrDadosPedido as $key=>$value) {
                    $this->addResponse($key,$value);
                }
            } else {
                $this->setStatus('PEDIDO_NOT_FOUND');                  
            }
        } else {
            throw new \Exception('Um número de pedido válido não foi informado.');
        }
        $this->response();
    }
    
    /**
     * Cadastro de novo pedido
     */
    function actionNovo(){
        
    }
    
    function actionLoad(){
        
    }
    
    function actionDel(){
        
    }
    
    function actionUpdate(){
        
    }
}

?>
