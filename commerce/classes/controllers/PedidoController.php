<?php
    
use \commerce\classes\models\FaturaModel;
use \sys\classes\util\Request;   
use \sys\classes\mvc as mvc; 
use \sys\classes\util as util;
use \sys\classes\commerce as commerce;
use \commerce\classes\controllers\IndexController;
use \auth\classes\models\AuthModel;

class Fatura extends IndexController {
   
    
    function actionInfoFatura(){
        $objDadosCfg    = $this->getDadosCfg();
        $numFatura      = Request::all('numFatura','NUMBER');
        
        if ($numPedido > 0) {
            $objFaturaModel  = new FaturaModel();
            if ($objFaturaModel->loadFatura($numFatura)){
                $objDadosFatura = $objFaturaModel->getObjDados();
                $arrDadosFatura = $objFaturaModel->getArrDados();
                $arrItensFatura = $objFaturaModel->getItens();
                
                foreach($arrDadosFatura as $key=>$value) {
                    $this->addResponse($key,$value);
                }
            } else {
                $this->setStatus('PEDIDO_NOT_FOUND');                  
            }
        } else {
            throw new \Exception('Um número de fatura válido não foi informado.');
        }
        $this->response();
    }
    
    /**
     * Cadastro de nova fatura
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
        
        $objFatura = new commerce\Fatura($arrDados);   
        $objFatura->getTotalFatura();
        $objFatura->saveSacadoOn();
        //$objFatura->debugOn();
        
        //Criação dos itens da fatura:
        $objPlanoA      = new commerce\ItemFatura('Plano 400',297.5,3,'ASS','Natal 2013');
        $objPlanoA->saveItemOn();
        
        $objPlanoB      = new commerce\ItemFatura('Plano 800',396);
        $objPlanoC      = new commerce\ItemFatura('Plano 1800',412.543);
        
        //Incluir itens à fatura atual:
        $objPedido->addItemFatura($objPlanoA);
        $objPedido->addItemFatura($objPlanoB);
        $objPedido->addItemFatura($objPlanoC);

        $response = $objFatura->send();
        echo 'OK: '.$response;   

    }
    
    function actionRequest(){
        $msgErr         = '';
        $hashAssinatura = Request::post('uid', 'STRING'); 
        $xmlNovaFatura  = Request::post('xmlNovaFatura', 'STRING');
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
                    if (strlen($xmlNovaFatura) > 0) {
                        //Faz a validação do XML recebido.
                        echo $xmlNovaFatura;
                    } else {
                        $msgErr = "O parâmetro obrigatório xmlNovaFatura não foi informado.";
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
}

?>
