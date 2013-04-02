<?php
    
use \sys\classes\util as util;
use \commerce\classes\controllers\IndexController;
use \commerce\classes\helpers\XmlRequestHelper;
use \commerce\classes\models\PedidoModel;
use \commerce\classes\models\NumPedidoModel;
use \auth\classes\models\AuthModel;

class Request extends IndexController {
    
    private $objAuth;
  
    function actionIndex(){
        $msgErr         = '';
        $hashAssinatura = util\Request::post('uid', 'STRING'); 
        $xmlNovoPedido  = util\Request::post('xmlNovoPedido', 'STRING');
        if (strlen($hashAssinatura) == 40) {
            //Localiza o registro no DB
            $objAuthModel   = new AuthModel();
            $objAuth        = $objAuthModel->loadHashAssinatura($hashAssinatura);
            if ($objAuth !== FALSE) {
                $this->objAuth  = $objAuth;
                $bloqEm         = $objAuth->BLOQ_EM;                 
                if (util\Date::isValidDateTime($bloqEm)) {
                    //O usuário está bloqueado.
                    $msgErr = "A assinatura informada está suspensa. Entre em contato com a Supervip para reativar o serviço.";
                } else {
                    //A assinatura está ativa
                    if (strlen($xmlNovoPedido) > 0) {
                        //Faz a validação do XML recebido.
                        try {
                            $objXmlRequest = new XmlRequestHelper($xmlNovoPedido);                            
                            if ($objXmlRequest->vldXmlNovoPedido() === TRUE){
                                //Validação Ok. Todos os dados informados estão corretos. 
                                //Grava no DB
                                $objDadosPedido     = $objXmlRequest->getObjDadosPedido();
                                $arrObjItensPedido  = $objXmlRequest->getArrObjItensPedido();
                                
                                $numPedido = $objDadosPedido->NUM_PEDIDO;
                                if ($numPedido == 0) {
                                    //Gera um NUM_PEDIDO automático
                                    $numPedido = $this->getProxNumPedido();
                                }
                                print_r($objDadosPedido);
                                print_r($arrObjItensPedido);
                            }
                       } catch(Exception $e) {
                           $msgErr = $e->getMessage();
                       }
                    } else {
                        $msgErr = "O parâmetro obrigatório xmlNovoPedido não foi informado.";
                    }
                }
            } else {
                $msgErr = "Usuário não localizado.";
            }
        } else {
            $msgErr = "O parâmetro uid ({$hashAssinatura}) parece estar incorreto.";
        }
        
        if (strlen($msgErr) > 0) die($msgErr);        
    }    
    
    /**
     * Localiza o próximo NUM_PEDIDO disponível.
     * Caso seja o primeiro pedido o NUM_PEDIDO inicial será usado.
     * 
     * @return integer
     */
    function getProxNumPedido(){
        $objAuth        = $this->objAuth;
        $idAssinatura   = (isset($objAuth->ID_ASSINATURA))?$objAuth->ID_ASSINATURA:0;
        
        if ($idAssinatura > 0) { 
            $ambiente           = $objAuth->AMBIENTE;//TEST ou PROD
            $objNumPedidoModel  = new NumPedidoModel();
            $numPedidoIni       = $objAuth->NUM_PEDIDO_INI;
            
            if ($ambiente == 'PROD') {
                $proxNumPedido = $objNumPedidoModel->getProxNumPedido($idAssinatura);
            } else {
                $proxNumPedido = $objNumPedidoModel->getProxNumPedidoTest($idAssinatura);
            }
            
        } else {
            $msgErr = "O objeto de autenticação para a assinatura atual não é válido. Entre em contato com o suporte.";
            throw new \Exception($msgErr);
        }            
    }
}

?>
