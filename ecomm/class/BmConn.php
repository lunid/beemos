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
    
    private function getUid(){
        $uid = trim($this->uid);
        if (strlen($uid) < 32) {
            $msgErr = "BmConn->getUid(): O identificador da assinatura, par�metro \$uid = '{$uid}' parece 
            n�o ter sido informado ou est� incorreto. Verifique a quantidade de caracteres.";
            throw new Exception($msgErr);                
        }
        return $uid;
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
    
    /**
     * Redireciona a chamada de um m�todo para o servidor remoto.
     * 
     * EXEMPLO 1 - chamada do m�todo savePedido com um array de par�metros:
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
     * EXEMPLO 2 - chamada do m�todo loadPedido com um �nico par�metro (n�mero do pedido):
     * <code>           
     *      $objConn = new BmConn();
     *      $strXml = "String XML...";
     *      $objConn->addParamXml($strXml);     
     *      $responseXml = $objConn->loadPedido(12345); 
     * </code>
     *     
     * @param type $action
     * @param type $args
     * @return string Retorna uma string XML com a resposta da chamada do m�todo
     */
    function __call($action,$args){        
        return $this->send($action,$args);
    }    
       
    
    /**
     * Gera a string XML de envio e faz a conex�o com o gateway.
     * M�todo chamado a partir do m�todo m�gico __call().
     * 
     * @see __call()     
     * @param $action Nome do m�todo a ser executado no servidor remoto.
     * @param $args Par�metros adicionais enviados ao servidor remoto. 
     * Por exemplo, pode ser um �nico valor em $args[0], ou ent�o um array de valores em $args[0].
     * 
     * @return string Resposta do gateway no formato XML.
     * @throws Exception Caso um erro ocorra na comunica��o entre o servidor local e o gateway.
     */    
     private function send($action,$args) {    
        if(!extension_loaded("curl")) {
            die('Biblioteca Curl n�o instalada. <br/>Esta biblioteca � obrigat�ria para a comunica��o com o servidor.');
        }
       
        $uid        = $this->getUid();       
        $request    = "action={$action}&uid={$uid}";
        
        //Concatena par�metros adicionais � string $params, no formato querystring (name1=value1&name2=value2&...)
        if (!empty($args)){ 
            $vars = $args[0];
            if (is_array($vars)) { 
                //Um array associativo contendo par�metros adicionais foi informado.
                foreach($vars as $name=>$value) {
                    $value       = str_replace('&','E',$value);//Retira o '&', se houver.
                    $arrParams[] = "$name=$value";
                }
                $request .= "&".join('&',$arrParams);               
            } else {
                //Uma �nica vari�vel foi informada.
                //Envia ao servidor remoto com o nome de 'variable'
                $request .= "&variable=".$vars;
            }
        }  
        
        $request .= "&strXml=".$this->getStrXml();
        
        if ($this->debug) {
            //O debug foi acionado: interrompe o envio e imprime os par�metros que ser�o enviados.
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
            $msgErr = "BmConn->send(): Erro ao na conex�o com o gateway: {$err}";
            throw new Exception($msgErr);                
        }
    }
}

?>
