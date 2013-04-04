<?php
    
use \sys\classes\util as util;
use \commerce\classes\controllers\IndexController;
use \commerce\classes\helpers\XmlRequestHelper;
use \commerce\classes\models\PedidoModel;
use \commerce\classes\models\NumPedidoModel;
use \auth\classes\models\AuthModel;

class Request extends IndexController {
    
    private $objAuth;
    private $objPedidoModel     = NULL;
    private $objNumPedidoModel  = NULL;
    
    function actionIndex(){
        $msgErr         = '';
        $hashAssinatura = util\Request::post('uid', 'STRING'); 
        $xmlNovoPedido  = util\Request::post('xmlNovoPedido', 'STRING');
        if (strlen($hashAssinatura) == 40) {
            //Localiza o registro no DB
            $objAuthModel   = new AuthModel();
            $objAuth        = $objAuthModel->loadHashAssinatura($hashAssinatura);
            if ($objAuth !== FALSE) {
                $this->objAuth      = $objAuth;
                $objNumPedidoModel  = $this->getObjNumPedidoModel();//Deve ser chamado após definir $objAuth;      
                $bloqEm             = $objAuth->BLOQ_EM;                 
                
                if (util\Date::isValidDateTime($bloqEm)) {
                    //O usuário está bloqueado.
                    $msgErr = "[COD/ERR: REQ-3] A assinatura informada está suspensa. Entre em contato com a Supervip para reativar o serviço.";
                } elseif (is_object($objNumPedidoModel)) {
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
                                
                                $numPedido = (int)$objDadosPedido->NUM_PEDIDO;
                                
                                if ($numPedido == 0) {
                                    //Gera um NUM_PEDIDO automático
                                    $numPedido = $this->getProxNumPedido();
                                }
                                
                    
                                if ($numPedido !== FALSE) {
                                    //Tudo ok. Grava o novo pedido.
                                    $objNumPedidoModel->saveNumPedido($numPedido);
                                    
                                    $objDadosPedido->NUM_PEDIDO = $numPedido;

                                    //Salva os dados do novo pedido
                                    $commit = $this->saveNovoPedido($objDadosPedido,$arrObjItensPedido);                                        
                                    if ($commit) {
                                        //Pedido cadastrado com sucesso.

                                    } else {
                                        $msgErr = "[COD/ERR: REQ-6] Não foi possível concluir o cadastro do novo pedido. Por favor, entre em contato com o suporte.";
                                    }
                                } else {
                                    $msgErr = "[COD/ERR: REQ-5] Impossível identificar um número de pedido válido. Entre em contato com o suporte.";
                                }                                                                
                            }
                       } catch(Exception $e) {
                           $msgErr = $e->getMessage();
                       }
                    } else {
                        $msgErr = "[COD/ERR: REQ-7] Um objeto obrigatório necessário para o processo solicitado não foi gerado com sucesso.";
                    }
                } else {
                    $msgErr = "[COD/ERR: REQ-4] O parâmetro obrigatório xmlNovoPedido não foi informado.";
                }
            } else {
                $msgErr = "[COD/ERR: REQ-2] Usuário não localizado.";
            }
        } else {
            $msgErr = "[COD/ERR: REQ-1] O parâmetro uid ({$hashAssinatura}) parece estar incorreto.";
        }
        
        if (strlen($msgErr) > 0) die($msgErr);        
    }    
    
    function getObjNumPedidoModel(){
        return $this->singletonModel('\commerce\classes\models\NumPedidoModel', 'objNumPedidoModel');             
    }
    
    function getObjPedidoModel(){
        return $this->singletonModel('\commerce\classes\models\PedidoModel', 'objPedidoModel');        
    }
    
    private function singletonModel($class,$param){
        $obj = $this->$param;
        if (!is_object($obj)) {
            $objAuth        = $this->objAuth;
            $idAssinatura   = (isset($objAuth->ID_ASSINATURA))?$objAuth->ID_ASSINATURA:0;   
            $ambiente       = $objAuth->AMBIENTE;//TEST ou PROD
            if ($idAssinatura > 0) {
                $obj = new $class; 
                $obj->setAssinaturaEAmbiente($idAssinatura,$ambiente);            
                $this->$param = $obj;
            }        
        }
        return $obj;        
    }
    
    /**
     * Salva os dados do novo pedido e seus respectivos itens.
     * Caso o $numPedido já exista e ainda não passou pelo checkout, faz atualização do registro.
     * Caso contrário, não permite alteração dos dados e itens do pedido.
     * 
     * @param stdClass $objDadosPedido
     * @param type $arrObjItensPedido
     */
    function saveNovoPedido($objDadosPedido,$arrObjItensPedido){
        $objPedidoModel = $this->getObjPedidoModel();                          
        if (is_object($objPedidoModel) && $objDadosPedido instanceof \stdClass && is_array($arrObjItensPedido)) {         
            
            $numPedido      = $objDadosPedido->NUM_PEDIDO;
            $idPedido       = $objPedidoModel->savePedido($objDadosPedido);
            
            if ($idPedido > 0) {
                //Pedido gravado com sucesso. Grava os itens do pedido.
                $objPedidoModel->delItens($numPedido);//Exclui itens do pedido atual, caso esteja sendo sobrescrito.
                foreach($arrObjItensPedido as $objItem){
                    $idItemPedido = (int)$objPedidoModel->saveItemPedido($idPedido,$numPedido,$objItem);
                    if ($idItemPedido == 0) {
                        $objPedidoModel->delPedido($idPedido);//Exclui o pedido cadastrado
                        $msgErr = "[COD/ERR: REQ-21] Erro ao salvar um item do pedido {$numPedido}. Por favor, entre em contato com o suporte.";
                        throw new \Exception($msgErr);                          
                    }
                }
            } else {
                $msgErr = "[COD/ERR: REQ-22] A tentativa de salvar o pedido enviado falhou. Por favor, entre em contato  com o suporte.";
                throw new \Exception($msgErr);                            
            }
            
        } else {
            $msgErr = "[COD/ERR: REQ-20] Ao salvar o pedido um ou mais parâmetros obrigatórios não foram localizados.";
            throw new \Exception($msgErr);            
        }
        return TRUE;
    }
    
    
    
    /**
     * Localiza o próximo NUM_PEDIDO disponível.
     * Caso seja o primeiro pedido o NUM_PEDIDO inicial será usado.
     * 
     * @return integer
     */
    function getProxNumPedido(){
        $objNumPedidoModel  = $this->getObjNumPedidoModel();        
        $proxNumPedido      = FALSE;
        
        if (is_object($objNumPedidoModel)) { 
            
            $numPedidoIni       = $this->objAuth->NUM_PEDIDO_INI;
            $proxNumPedido      = $objNumPedidoModel->getProxNumPedido();
           
            if ($proxNumPedido == 0) $proxNumPedido = $numPedidoIni;
                                    
            try {
                //Verifica se o número do pedido não está sendo usado, uma vez que $proxNumPedido pode ter
                //retornado zero e, neste caso, $numPedidoIni é utilizado:
                $numPedidoDisponivel = $objNumPedidoModel->checkNumPedidoDisponivel($proxNumPedido);
                if (!$numPedidoDisponivel) {                    
                    $msgErr = "[COD/ERR: REQ-12] Não foi possível gerar um número de pedido único. Por favor, entre em contato com o suporte.";
                    throw new \Exception($msgErr);                
                }
            } catch(\Exception $e) {
                $msgErr = $e->getMessage();     
                throw new \Exception($msgErr);    
            }
        } else {
            $msgErr = "[COD/ERR: REQ-10] O objeto de autenticação para a assinatura atual não é válido. Entre em contato com o suporte.";
            throw new \Exception($msgErr);
        }
        return $proxNumPedido;
    }
}

?>
