<?php
    
use \sys\classes\util as util;
use \commerce\classes\controllers\IndexController;
use \commerce\classes\helpers\XmlRequestHelper;
use \commerce\classes\models\PedidoModel;
use \auth\classes\models\AuthModel;

class Request extends IndexController {
   
  
    function actionIndex(){
        $msgErr         = '';
        $hashAssinatura = util\Request::post('uid', 'STRING'); 
        $xmlNovoPedido  = util\Request::post('xmlNovoPedido', 'STRING');
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
}

?>
