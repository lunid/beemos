<?php
   
    use \sys\classes\util\Request;
    use \commerce\classes\helpers as helpers;
    use \commerce\classes\controllers\IndexController;
    
    /**
     * Classe que possui os métodos para as operações possíveis no ambiente do Bradesco.
     * Opções de pagamento implementadas:
     * BOLETO - URL de teste:
     * <code>
     * //Consulta que dá origem a um boleto:
     * http://dev.superproweb.com.br/commerce/bradesco/boleto/?orderid=35869
     * 
     * //Consulta que simula o acesso do MUP Bradesco após a consulta acima:
     * http://dev.superproweb.com.br/commerce/bradesco/notifBoleto/?numOrder=35869     
     * </code>
     * 
     */
    class Bradesco extends IndexController {
        private $tipoTransacao;//BOLETO ou TRANSF
                
        function actionTransf(){
            echo 'transf';
        }
        
        /**
         * Cria e dispara a URL responsável por gerar o boleto referente ao pedido solicitado.
         * Abre a página do boleto no navegador.
         * 
         * @return string Imprime a saída no formato solicitado pelo usuário (XML, TEXT, JSON).
         */
        public function actionBoleto(){
            
            $this->setTipoBoleto();
            
            $objCfg         = $this->getDadosCfg();
            $orderId        = Request::all('orderid','');
            $urlPgto        = '';
            $objBradesco    = $this->init($orderId);
  
            $urlPgto = $objBradesco->getUrlPagamento();             
            if (strlen($urlPgto) > 0) {
                //$this->addResponse('URL',$urlPgto);
            } else {
                $this->setStatus('ERR_URL_BOLETO'); 
                $this->response();
            }                

            header('Location:'.$urlPgto);
                      
        }
        
        private function setTipoBoleto(){
            $this->tipoTransacao = 'BOLETO';
        }
        
        private function setTipoTransf(){
            $this->tipoTransacao = 'TRANSF';
        }
        
        /**
         * Inicializa as configurações do usuário atual e retorna um objeto 
         * específico para a operação solicitada (BOLETO ou TRANSF).
         * 
         * @param string $orderId Valor alfanumérico de até 27 caracteres.
         * @return object Pode ser BradescoTransfHelper ou BradescoBoletoHelper.
         */
        private function init($orderId){
            $tipoTransacao  = $this->tipoTransacao;
            $objCfg         = $this->getDadosCfg();
            $objBradesco    = NULL;
 
            if (is_object($objCfg)) {                
                if (strlen($orderId) > 0) {
                    if ($tipoTransacao == 'BOLETO') {
                        $objBradesco    = new helpers\BradescoBoletoHelper($objCfg,$orderId);                    
                    } elseif ($tipoTransacao == 'TRANSF') {
                        $objBradesco    = new helpers\BradescoTransfHelper($objCfg,$orderId);  
                    }
            
                    if ($objBradesco == NULL) {
                        $this->setStatus('ERR_OBJ_BRADESCO');                                     
                    } 
                    
                } else {
                    $this->setStatus('ERR_ORDER_ID');                     
                }
            }         
            
            if (!is_object($objBradesco)) $this->response();    
            
            return $objBradesco;
        }
        
        /**
         * Método chamado na notificação do MUP.
         * Ao solicitar a geração de um boleto o MUP chama o método atual para consultar o XML de retorno.
         * 
         * @return string Xml de retorno que será usado no MUP para construir o boleto.
         */
        function actionNotifBoleto(){
            $this->setTipoBoleto();  
            $this->notificacao();
        }
        
        function actionNotifTransf(){            
            $this->setTipoTransf();
        }    
        
        /**
         * Carrega um objeto Pedido a partir do número de pedido informado e gera o XML 
         * de notificação para o MUP Bradesco.
         * Método de suporte aos métodos actionNotifBoleto e actionNotifTransf. 
         * 
         * Exemplo:
         * BOLETO - URL de teste:           
         * <code>
         *  http://dev.superproweb.com.br/commerce/bradesco/boleto/?numOrder=35869
         * </code>
         * 
         * @return void
         */
        private function notificacao(){
            //Captura as variáveis recebidas            
            $transId        = Request::all('transId','STRING');            
            $orderId        = Request::all('numOrder','NUMBER');            
            $objPedido      = $this->loadPedido($orderId);
            
            if (strtoupper($transId) == 'PUTAUTHBOLETO') {
                //Boleto com retorno.
                $numeroTitulo = Request::all('NumeroTitulo','STRING');//Número do código de barras.
                if (strlen($numeroTitulo) > 0) {
                    $objPedido->saveNumeroTituloBoleto($numeroTitulo);
                }
                
            }
            
            if ($tipoTransacao == 'BOLETO') {
                //Captura e persiste as variáveis de reotorno do boleto atual.
                //Apenas para boleto com retorno.                
                
            }
            $objBradesco    = $this->init($orderId);    
                       
            $xmlNotif       = $objBradesco->setXmlNotif($objPedido);
            
            header ("Content-Type:text/xml");  
            echo $xmlNotif;
        }
                         
    }

?>
