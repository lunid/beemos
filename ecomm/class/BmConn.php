<?php

/**
 * Classe respons�vel pela comunica��o com o servidor beeMoS.
 * 
 */
class BmConn {
   
    private $strXml;
    private $debug;
    private $uid       = '4ce6bf71d9a0d938761470c6e134ee5a8a97ef60';//Chave do cliente
    private $_urlSend  = 'http://www.supervip.com.br/dev/commerce/request/';
    
    function __construct($uid='') {
        if (strlen($uid) > 0) $this->uid = $uid;
    }

    /**
     * Ativa o debug antes do envio dos dados para o servidor remoto, via chamada do m�todo send().
     * Ao chamar o m�todo send(), interrompe o envio e imprime os par�metros que ser�o enviados.
     * 
     * @return void
     */
    public function debugOn(){
        $this->debug = TRUE;
    }

    public function debugOff(){
        $this->debug = FALSE;
    }
    
    function addParamXml($strXml){
        if (strlen($strXml) > 0) {
            $this->strXml = $strXml;
        } else {
            
        }
    }
    
    private function getStrXml(){
        return $this->strXml;
    }    
    
    function __call($action,$args){        
        $this->send($action,$args);
    }    
       
    
    /**
     * Gera a string XML de envio e faz a conex�o com o gateway.
     *  
     * @param $action Nome do m�todo a ser executado no servidor remoto.
     * @param $params Par�metros adicionais enviados ao servidor remoto. 
     * Por exemplo, xmlNovoPedido contendo o XML para o cadastro de novo pedido.
     * 
     * @param $uid Cont�m o identificador da assinatura que deseja consultar.
     * @return string Resposta do gateway no formato XML.
     * @throws Exception Caso um erro ocorra na comunica��o entre o servidor local e o gateway.
     */    
     private function send($action,$args) {    
        if(!extension_loaded("curl")) {
            die('Biblioteca Curl n�o instalada. <br/>Esta biblioteca � obrigat�ria para a comunica��o com o servidor.');
        }

        //$params = "strXml=".$this->getStrXml();  
        $params = '';        
        if (!empty($args)){ 
            $vars = $args[0];
            if (is_array($vars)) { 
                foreach($vars as $var=>$value) {
                    $arrParams[] = "$var=$value";
                }
                $params .= "&".join('&',$arrParams);               
            } else {
                $params .= "&variable=".$vars;
            }
        }
        echo $params;
        //print_r($args);
        die();
        $uid    = $this->uid;
        
        if ($this->debug) {
            //O debug foi acionado: interrompe o envio e imprime os par�metros que ser�o enviados.
            echo "M�todo Pedido->send(): <br/>action: {$action}<br/>uid: {$uid}<br/>params: {$params}";
            die();
        }
        
        $request	= "action={$action}&uid={$uid}&".$params;
        $objCurl 	= new Curl($this->_urlSend);

        $objCurl->setPost($request);
        $objCurl->createCurl();
        $errNo = $objCurl->getErro();
        if ($errNo == 0){
            $xmlResponse = $objCurl->getResponse();
            //@todo Tratar a resposta do servidor (permitir apenas resposta esperada para eliminar mensagens de erros inesperados).
            return $xmlResponse;
        } else {
            $err    = $objCurl->getOutput();
            $msgErr = "BmConn->send(): Erro ao na conex�o com o gateway: {$err}";
            throw new Exception($msgErr);                
        }
    }
}

?>
