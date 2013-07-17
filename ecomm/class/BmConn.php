<?php

/**
 * Classe responsável pela comunicação com o servidor beeMoS.
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
    
    private function getUid(){
        $uid = trim($this->uid);
        if (strlen($uid) < 32) {
            $msgErr = "BmConn->getUid(): O identificador da assinatura, parâmetro \$uid = '{$uid}' parece 
            não ter sido informado ou está incorreto. Verifique a quantidade de caracteres.";
            throw new Exception($msgErr);                
        }
        return $uid;
    }

    /**
     * Ativa o debug antes do envio dos dados para o servidor remoto, via chamada do método send().
     * Ao chamar o método send(), interrompe o envio e imprime os parâmetros que serão enviados.
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
    
    /**
     * Redireciona a chamada de um método para o servidor remoto.
     * 
     * EXEMPLO 1 - chamada do método savePedido com um array de parâmetros:
     * <code>
     *      
     *      $objConn = new BmConn();
     *      $strXml = "String XML...";
     *      $objConn->addParamXml($strXml);
     * 
     *      $arrParams['numPedido'] = 12345;
     *      $arrParams['sonda']     = 'BOLETO';
     *      $responseXml            = $objConn->savePedido($arrParams); 
     * </code>
     * 
     * EXEMPLO 2 - chamada do método loadPedido com um único parâmetro (número do pedido):
     * <code>           
     *      $objConn = new BmConn();
     *      $strXml = "String XML...";
     *      $objConn->addParamXml($strXml);     
     *      $responseXml = $objConn->loadPedido(12345); 
     * </code>
     *     
     * @param type $action
     * @param type $args
     * @return string Retorna uma string XML com a resposta da chamada do método
     */
    function __call($action,$args){        
        return $this->send($action,$args);
    }    
       
    
    /**
     * Gera a string XML de envio e faz a conexão com o gateway.
     * Método chamado a partir do método mágico __call().
     * 
     * @see __call()     
     * @param $action Nome do método a ser executado no servidor remoto.
     * @param $args Parâmetros adicionais enviados ao servidor remoto. 
     * Por exemplo, pode ser um único valor em $args[0], ou então um array de valores em $args[0].
     * 
     * @return string Resposta do gateway no formato XML.
     * @throws Exception Caso um erro ocorra na comunicação entre o servidor local e o gateway.
     */    
     private function send($action,$args) {    
        if(!extension_loaded("curl")) {
            die('Biblioteca Curl não instalada. <br/>Esta biblioteca é obrigatória para a comunicação com o servidor.');
        }
       
        $uid        = $this->getUid();       
        $request    = "action={$action}&uid={$uid}";
        
        //Concatena parâmetros adicionais à string $params, no formato querystring (name1=value1&name2=value2&...)
        if (!empty($args)){ 
            $vars = $args[0];
            if (is_array($vars)) { 
                //Um array associativo contendo parâmetros adicionais foi informado.
                foreach($vars as $name=>$value) {
                    $value       = str_replace('&','E',$value);//Retira o '&', se houver.
                    $arrParams[] = "$name=$value";
                }
                $request .= "&".join('&',$arrParams);               
            } else {
                //Uma única variável foi informada.
                //Envia ao servidor remoto com o nome de 'variable'
                $request .= "&variable=".$vars;
            }
        }  
        
        $request .= "&strXml=".$this->getStrXml();
        
        if ($this->debug) {
            //O debug foi acionado: interrompe o envio e imprime os parâmetros que serão enviados.
            $arrParamsDebug = explode('&',$request);
            print_r($arrParamsDebug);
            die();
        }

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
            $msgErr = "BmConn->send(): Erro ao na conexão com o gateway: {$err}";
            throw new Exception($msgErr);                
        }
    }
}

?>
