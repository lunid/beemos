<?php
    
use \commerce\classes\models\PedidoModel;
use \sys\classes\util\Request;   
use \sys\classes\mvc as mvc; 
use \sys\classes\util as util;
use \sys\classes\commerce as commerce;
use \commerce\classes\controllers\IndexController;
use \auth\classes\models\AuthModel;

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
        $msgErr         = '';
        $hashAssinatura = Request::post('uid', 'STRING'); 
        $xmlNovoPedido  = Request::post('xmlNovoPedido', 'STRING');
        if (strlen($hashAssinatura) == 40) {
            //Localiza o registro no DB
            $objAuthModel   = new AuthModel();
            $objAuth        = $objAuthModel->loadHashAssinatura($hashAssinatura);
            if ($objAuth !== FALSE) {
                $bloqEm = $objAuth->BLOQ_EM;                
                if (util\Date::isValidDateTime($bloqEm)) {
                    //O usuário está bloqueado.
                    $msgErr = "A assinatura informada está suspensa. Entre em contato com a Supervip para reativar o serviço.";
                } else {
                    //Assinatura ativa
                    if (strlen($xmlNovoPedido) > 0) {
                        //Faz a validação do XML recebido.
                        
                    } else {
                        $msgErr = "O parâmetro obrigatório xmlNovoPedido não foi informado.";
                    }
                }
            } else {
                $msgErr = "Usuário não localizado.";
            }
        } else {
            $msgErr = "O parâmetro uid ({$hash}) parece estar incorreto.";
        }
        
        if (strlen($msgErr) > 0) die($msgErr);        
    }
    
    function actionLoad(){
        
    }
    
    function actionDel(){
        
    }
    
    function actionUpdate(){
        
    }
}

?>
