<?php
    
use \commerce\classes\models\PedidoModel;
use \sys\classes\util\Request;   
use \sys\classes\mvc as mvc; 
use \sys\classes\commerce as commerce;
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
        
        $arrDados    = array(
            'NUM_PEDIDO'    => '12347',
            'NOME_SAC'      => "Claudio João da Costa Aguiar D'ávila",
            'EMAIL_SAC'     => 'claudio@supervip.com.br',
            'ENDERECO_SAC'  => 'Rua Maestro Cardim, 1218 - apto 71 - Bela vista',
            'CIDADE_SAC'    => 'São Paulo',
            'UF_SAC'        => 'sp',
            'CPF_CNPJ_SAC'  => '04067415000133',           
            'VALOR_FRETE'   => 34.43
        );
        
        $objPedido      = new commerce\Pedido($arrDados);   
        $objPedido->getTotalPedido();
        $objPedido->saveSacadoOn();
        //$objPedido->debugOn();
        
        //Criação dos itens do pedido:
        $objPlanoA      = new commerce\ItemPedido('Plano 400',297.5,3,'ASS','Natal 2013');
        $objPlanoA->saveItemOn();
        
        $objPlanoB      = new commerce\ItemPedido('Plano 800',396);
        $objPlanoC      = new commerce\ItemPedido('Plano 1800',412.543);
        
        //Incluir itens ao pedido atual:
        $objPedido->addItemPedido($objPlanoA);
        $objPedido->addItemPedido($objPlanoB);
        $objPedido->addItemPedido($objPlanoC);

        $response = $objPedido->send();
        echo 'OK: '.$response;   

    }
    
    function actionRequest(){
        $uid            = Request::post('uid', 'STRING'); 
        $xmlNovoPedido  = Request::post('xmlNovoPedido', 'STRING');
        echo 'RECEBIDO: '.$uid.' XML: '.$xmlNovoPedido;
    }
    
    function actionLoad(){
        
    }
    
    function actionDel(){
        
    }
    
    function actionUpdate(){
        
    }
}

?>
