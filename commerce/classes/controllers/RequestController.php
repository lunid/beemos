<?php
    
use \sys\classes\util as util;
use \commerce\classes\controllers\IndexController;
use \commerce\classes\helpers\XmlRequestHelper;
use \commerce\classes\helpers\ErrorMessageHelper;
use \commerce\classes\models\PedidoModel;
use \commerce\classes\models\NumPedidoModel;
use \auth\classes\models\AuthModel;

class Request extends IndexController {
    
    private $objAuth;
    private $objPedidoModel         = NULL;
    private $objNumPedidoModel      = NULL;
    private $objXmlResponseHelper   = NULL;

    
    /**
     * Recebe a requisição do cliente e valida os parâmetros obrigatórios 'action' e 'uid'.
     * A partir de 'uid' verifica se a assinatura está ativa.
     * Se nenhum erro for encontrado executa o método informado em 'action' e imprime a resposta no formato XML.
     * 
     * @return void
     */
    function actionIndex(){
        $msgErr         = '';
        $response       = '';
        $msgErr         = '';
        $action         = util\Request::post('action', 'STRING');
        $hashAssinatura = util\Request::post('uid', 'STRING');                      

        //Faz a validação da action informada:
        $method = 'action'.ucfirst($action);
        if (strlen($action) > 0 && method_exists($this,$method)){
            //O método informado existe na classe atual
            if (strlen($hashAssinatura) == 40) {
                $objAuthModel   = new AuthModel();
                $objAuth        = $objAuthModel->loadHashAssinatura($hashAssinatura);
                if ($objAuth !== FALSE) {
                    $bloqEm = $objAuth->BLOQ_EM;                                 
                    if (util\Date::isValidDateTime($bloqEm)) {
                        //O usuário está bloqueado.
                        ErrorMessageHelper::index('USER_BLOQ');                                         
                    } else {                        
                        $this->objAuth  = $objAuth;
                        $response       = $this->$method();
                    }
                } else {
                    ErrorMessageHelper::index('USER_NOT_EXISTS');                       
                }
            } else {
                $arrReplace['HASH_ASSINATURA'] = $hashAssinatura;
                ErrorMessageHelper::index('ERR_HASH_ASS',$arrReplace);                
            }           
            
        } elseif (strlen($action) > 0) {
            //A action informada não existe.
            $arrReplace['ACTION_NAME'] = $action;
            ErrorMessageHelper::index('ERR_ACTION_NOT_EXISTS',$arrReplace);   
        } else {  
            //o parâmetro $action é obrigatório e não foi informado.
            ErrorMessageHelper::index('ERR_ACTION_NOT_INFO',$arrReplace);             
        }
        echo $response;
    }        
    
    /**
     * Localiza os dados do pedido informado.
     * 
     * @return FALSE | XML Retorna FALSE caso o pedido não seja localizado, ou então, o XML com dados do pedido.
     */
    private function actionGetDadosPedido(){
        $msgErr     = '';
        $response   = '';
        $numPedido  = (int)util\Request::post('xmlNovoPedido', 'NUMBER');  
        if ($numPedido > 0) {
            
        } else {
            $msgErr = "[COD/ERR: REQ-1] O parâmetro numPedido não foi informado ou não é um valor válido.";
        }
        if (strlen($msgErr) > 0) die($msgErr); 
        return $response;        
    }
    
    private function actionSavePedido(){  
        $msgErr         = '';
        $response       = '';
        $xmlNovoPedido  = util\Request::post('xmlNovoPedido', 'STRING');       
        $objNumPedidoModel  = $this->getObjNumPedidoModel();//Deve ser chamado após definir $objAuth;      
        if (is_object($objNumPedidoModel)) {        
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
                                $response = 'Tudo certo';
                            } else {
                                $msgErr = "[COD/ERR: REQ-14] Não foi possível concluir o cadastro do novo pedido. Por favor, entre em contato com o suporte.";
                            }
                        } else {
                            $msgErr = "[COD/ERR: REQ-13] Impossível identificar um número de pedido válido. Entre em contato com o suporte.";
                        }                                                                
                    }
               } catch(Exception $e) {
                   $msgErr = $e->getMessage();
               }
            } else {
                $msgErr = "[COD/ERR: REQ-12] Um objeto obrigatório necessário para o processo solicitado não foi gerado com sucesso.";
            }
        } else {
            $msgErr = "[COD/ERR: REQ-11] O parâmetro obrigatório xmlNovoPedido não foi informado.";
        }
        
        if (strlen($msgErr) > 0) die($msgErr);   
        return $response;
    }    
    
    private function getObjNumPedidoModel(){
        return $this->singletonModel('\commerce\classes\models\NumPedidoModel', 'objNumPedidoModel');             
    }
    
    private function getObjPedidoModel(){
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
    private function saveNovoPedido($objDadosPedido,$arrObjItensPedido){
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
    private function getProxNumPedido(){
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
    
    function actionError($exception){                                         
        
    }    
}

?>
