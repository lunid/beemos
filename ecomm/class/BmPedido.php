<?php
    class BmPedido {

        //Lista de par�metros permitidos ao criar um novo pedido
        private $arrLibParams = array(
            'NUM_PEDIDO:setNumPedido',
            'VALOR_COMPRA:setValorCompra',                
            'VALOR_TOTAL:setTotalPedido',
            'VALOR_FRETE:setFrete',
            'NOME_SAC:setNomeSac',
            'EMAIL_SAC:setEmailSac',
            'ENDERECO_SAC:setEnderecoSac',
            'CIDADE_SAC:setCidadeSac',
            'UF_SAC:setUfSac',
            'CPF_CNPJ_SAC:setCpfCnpjSac',
            'CAMPANHA:setCampanha',
            'OBS:setObs'
        );

        private $arrParams          = array();
        private $arrItemPedido      = array(); //Array de objetos do tipo ItemPedido.
        private $valorTotalDoPedido = 0;
        private $valorFrete         = 0;
        private $nomeSac;
        private $emailSac;
        private $enderecoSac;
        private $cidadeSac;
        private $ufSac;
        private $cpfCnpjSac;
        private $_urlSend   = 'http://www.supervip.com.br/dev/commerce/request/';
        private $debug      = FALSE;
        private $saveSacado = FALSE;//Grava os dados do sacado no servidor remoto.

        const UID           = '4ce6bf71d9a0d938761470c6e134ee5a8a97ef60';//Chave do cliente

        /**
         * Caso um array seja informado, valida os par�metros recebidos de acordo com $arrLibParams.
         * Os par�metros corretos s�o armazenados em $arrParams, onde o nome do campo torna-se um �ndice
         * associativo do array com seu respectivo valor.
         * 
         * @param array $arrDados Array associativo onde cada �ndice deve existir em $arrLibParams.
         */
        function __construct($arrDados=array()){
            $this->loadArrDados($arrDados);
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
        
        public function pgtoBltBradesco(){
            $this->setFormaPgto('BLT_BRADESCO');
        }
        
        public function pgtoBltItau(){
            $this->setFormaPgto('BLT_ITAU');
        }        
        
        public function pgtoBltBb(){
            $this->setFormaPgto('BLT_BB');
        }        
        
        /**
         * Define o pagamento com cart�o de cr�dito via operadora CIELO.
         * 
         * @param string $cc N�mero do cart�o, sem separadores.
         * @param integer $validade Validade do cart�o no formato yyyymm
         * @param integer $codSeg C�digo de seguran�a do cart�o
         */
        public function pgtoCielo($cc,$validade,$codSeg){
            $this->setFormaPgto('CIELO');
        }        
        
        public function pgtoRedecard(){
            $this->setFormaPgto('REDECARD');
        }
        
        /**
         * Define o pagamento com cart�o de cr�dito via operadora AMEX.
         * 
         * @param string $cc N�mero do cart�o, sem separadores.
         * @param integer $validade Validade do cart�o no formato yyyymm
         * @param integer $codSeg C�digo de seguran�a do cart�o
         */        
        public function pgtoAmex(){
            $this->setFormaPgto('AMEX');
        }               

        public function setFormaPgto($formaPgto){
            $key          = FALSE;
            $arrFormaPgto = array('BLT_BRADESCO','BLT_ITAU','BLT_BB','CIELO','REDECARD','AMEX');
            if (strlen($formaPgto) > 0) {
                $formaPgto = strtoupper($formaPgto);//Converte para caixa alta (mai�sculas).
                $key = array_search($arrFormaPgto, $formaPgto);
                if ($key !== FALSE) {
                    $this->addParam('FORMA_PGTO',$formaPgto);
                }
            }
            
            if ($key === FALSE) {
                $msgErr = 'A forma de pagamento '.$formaPgto.' n�o � v�lida.';
                throw new \Exception($msgErr);                
            }
        }
        
        public function setCartaoDeCredito($cc,$validade,$codSeg){
            
        }
        
        /**
         * Salva os dados do sacado no servidor remoto.
         * Este recurso pode ser �til caso seja necess�rio efetuar uma nova cobran�a 
         * para o mesmo sacado via painel de controle.
         * 
         * @return void     
         */
        public function persistSacadoOn(){
            $this->saveSacado = true;
        }  

        public function persistSacadoOff(){
            $this->saveSacado = FALSE;
        }

        /**
         * Recebe um array associativo de dados onde cada �ndice deve coincidir com um �ndice em $arrLibParams.
         * 
         * @param string[] $arrDadosSac
         * @return void
         * 
         * @throws Exception Caso um ou mais par�metros informados n�o possuam correspond�ncia em $arrLibParams.
         */
        public function loadArrDados($arrDados){
            $arrMsgErr = NULL;
            if (is_array($arrDados) && count($arrDados) > 0) {
                $arrLibParams    = $this->arrLibParams;//Par�metros permitidos
                $arrAction       = array();
                $arrTag          = array();

                //Separa um array com os par�metros autorizados e outro com os seus respectivos m�todos.
                foreach($arrLibParams as $label){
                    list($indice,$action) = explode(':',$label);

                    if (strlen($action) > 0) $arrAction[]   = $action;
                    if (strlen($indice) > 0) $arrTag[]      = $indice;
                }     

                //Valida o array de dados do sacado:
                foreach($arrDados as $name=>$value) {
                    $key = array_search($name,$arrTag);
                    if ($key !== FALSE) {
                        $action = $arrAction[$key];
                        if (method_exists($this, $action)) {
                            //Existe um m�todo para definir o valor do par�metro atual:
                            $this->$action($value);
                        } else {
                            throw new \Exception('Pedido->loadArrDadosSac() A tag informada '.$name.' parece ser inv�lida ou o m�todo '.$action.' associado a ela n�o existe.');
                        }
                    } else {
                        $arrMsgErr[] = "Par�metro {$name} n�o permitido.";
                    }               
                }
            }

            if (is_array($arrMsgErr)) {
                $msgErr = join(', ',$arrMsgErr);
                throw new \Exception($msgErr);                
            }
        }

        /**
         * Adiciona um item (produto) ao pedido atual.
         * 
         * @param ItemPedido $objItemPedido
         * @return void
         */
        public function addItemPedido($objItemPedido){
            if (is_object($objItemPedido)) $this->arrItemPedido[] = $objItemPedido;
        }

        /**
         * Define o par�metro numPedido.
         * O valor deve ser num�rico.
         * 
         * @param integer $numPedido
         */
        public function setNumPedido($numPedido){
            if (ctype_digit($numPedido)) {
                $this->addParam('NUM_PEDIDO',$numPedido);
            } else {
                $msgErr = 'O n�mero do pedido deve ser um valor num�rico inteiro.';
                throw new \Exception($msgErr);
            }
        }

        /**
         * Informa o valor total do pedido.
         * Este valor refere-se ao valor que ser� cobrado do cliente (soma de produtos + frete + acr�scimos - descontos).
         * Caso n�o seja informado, o valor total do pedido ser� calculado pelo sistema.
         * 
         * @param float $value Valor decimal (formato 9999.99)
         * @return void
         * @throws \Exception Caso o valor informado n�o seja num�rico
         */
        public function setTotalPedido($value){
            if (is_numeric($value)) {
               $valueDec                    = number_format($value, 2, '.', '');
               $this->valorTotalDoPedido    = $valueDec;

               $this->addParam('VALOR_TOTAL',$valueDec);
            } else {
                $msgErr = "Pedido->setTotalPedido() O valor informado {$value} n�o � um valor v�lido.";
                throw new \Exception($msgErr);
            }
        }

        public function getTotalPedido(){
            $valorTotalDoPedido = $this->valorTotalDoPedido;
            if ($valorTotalDoPedido == 0) {
                //Um valor expl�cito n�o foi informado. Calcula o total do pedido           
                $subtotalItens  = 0;
                $frete          = $this->valorFrete;
                $arrItemPedido  = $this->arrItemPedido;
                if (is_array($arrItemPedido)) {
                    foreach($arrItemPedido as $objItemPedido){
                        //Soma o subtotal do produto atual com os anteriores:
                        $subtotalItens += $objItemPedido->calcSubtotal();
                    }
                }

                $valorTotalDoPedido = $subtotalItens+$frete;
            }
            return $valorTotalDoPedido;
        }

        /**
         * Informa um valor num�rico que representa o valor do frete do pedido.              
         * A informa��o de frete � opcional. Se n�o for informado o valor zero ser� enviado.          
         * 
         * @param float $value Valor do frete
         * @return void
         */    
        public function setFrete($value=0){
            if (is_numeric($value)) {
               $valueDec            = number_format($value, 2, '.', '');
               $this->valorFrete    = $valueDec;

               $this->addParam('VALOR_FRETE',$valueDec);
            } elseif (strlen($value) > 0) {
                $msgErr = "Pedido->setFrete() O valor informado {$value} n�o � um valor num�rico v�lido. ";
                $msgErr .= "Utilize ponto como separador decimal (formato: 9999.99).";
                throw new \Exception($msgErr);
            }               
        }

        public function setNomeSac($value){
            $value = trim($value);
            if (strlen($value) > 0){
                $this->nomeSac = $value;
                $this->addParam('NOME_SAC',$value);
            }          
        }

        public function setEmailSac($value){
            $value = trim($value);
            if (strlen($value) > 0){
                $this->emailSac = $value;
                $this->addParam('EMAIL_SAC',$value);
            }          
        }

        public function setEnderecoSac($value){
            $value = trim($value);
            if (strlen($value) > 0){
                $this->enderecoSac = $value;
                $this->addParam('ENDERECO_SAC',$value);
            }        
        }

        public function setCidadeSac($value){
            $value = trim($value);
            if (strlen($value) > 0){
                $this->cidadeSac = $value;
                $this->addParam('CIDADE_SAC',$value);
            }
        }

        public function setUfSac($value){
            $value = trim($value);
            if (strlen($value) == 2 && ctype_alpha($value)) {
                $value          = strtoupper($value);
                $this->ufSac    = $value;
                $this->addParam('UF_SAC',$value);
            }
        }

       /**
        * Recebe e guarda um valor referente a um CPF (11 d�gitos) ou a um CNPJ (14 d�gitos).
        * 
        * @param string $value
        */
        function setCpfCnpjSac($value){
            $value = trim($value);
            if (strlen($value) > 0) {
                $arrValue   = str_split($value);
                $arrChar    = array();

                //Retira caracteres que n�o sejam num�ricos:
                foreach ($arrValue as $char){
                    if (ctype_digit($char)) $arrChar[] = $char;
                }

                $valueChar = join('',$arrChar);

                if (strlen($valueChar) >=11 && strlen($valueChar) <= 14 && ctype_alnum($valueChar)) {
                    $this->cpfCnpjSac = $valueChar;
                    $this->addParam('CPF_CNPJ_SAC',$valueChar);
                } else {
                    $msgErr = 'Pedido->setCpfCnpjSac() O CPF/CNPJ informado ('.$value.') parece ser inv�lido.';
                    throw new Exception($msgErr);
                }       
            }
        }           

        /**
         * Armazena uma vari�vel com seu respectivo valor em um array que ser� usado
         * posteriormente para gerar o XML de envio.
         * 
         * @param string $name Nome que ser� usado no atributo 'id' da tag XML 'PARAM'.
         * @param mixed $value Valor da tag.
         * @throws Exception caso o par�metro $name n�o seja um par�metro v�lido.
         */
        private function addParam($name,$value){
           $name            = strtoupper($name);
           $arrLibParams    = $this->arrLibParams;//Par�metros permitidos
           $arrTag          = array();

           foreach($arrLibParams as $label){
               list($indice,$action) = explode(':',$label);
               if (strlen($indice) > 0) $arrTag[] = $indice;
           }

           $key = array_search($name,$arrTag);
           if ($key !== FALSE) {
               $this->arrParams[$name] = $value;
           } else {
               $msgErr = 'O par�metro informado n�o � v�lido.';
               throw new Exception($msgErr);
           }
        }

        /**
         * Gera a string XML que ser� enviada ao gateway de pagamento.
         * 
         * @throws Exception Caso nenhum par�metro tenha sido informado ou ent�o n�o exista produto(s).
         */
        function getXml(){
            $xml            = '<ROOT>';
            $arrParams      = $this->arrParams;
            $arrItemPedido  = $this->arrItemPedido;

            if (is_array($arrParams) && count($arrParams) > 0) {
                $xml .= "<PEDIDO>";

                foreach ($arrParams as $key=>$value){
                    $xml .= $this->setTagXml($key, $value);
                }

                $totalPedido = $this->getTotalPedido();
                $xml .= $this->setTagXml('TOTAL_PEDIDO', $totalPedido);

                //Salvar dados do sacado no servidor remoto:           
                if ($this->saveSacado) $xml .= $this->setTagXml('SAVE_SAC', 1);

                if (is_array($arrItemPedido) && count($arrItemPedido) > 0) {
                    foreach ($arrItemPedido as $objItemPedido){
                        $categoria           = $objItemPedido->getCategoria();
                        $codigo              = $objItemPedido->getCodigo();
                        $unidade             = $objItemPedido->getUnidade();
                        $descricao           = $objItemPedido->getDescricao();
                        $quantidade          = $objItemPedido->getQuantidade();
                        $precoUnit           = $objItemPedido->getPrecoUnit();
                        $campanha            = $objItemPedido->getCampanha();
                        $subtotal            = $objItemPedido->calcSubtotal();
                        $persistItem         = ($objItemPedido->getPersistItem())?1:0;

                        $xml .= "
                        <ITEM>
                                ".$this->setTagXml('CATEGORIA', $categoria)."
                                ".$this->setTagXml('CODIGO', $codigo)."      
                                ".$this->setTagXml('UNIDADE', $unidade)."   
                                ".$this->setTagXml('DESCRICAO', $descricao)."                        
                                ".$this->setTagXml('QUANTIDADE', $quantidade)."             
                                ".$this->setTagXml('PRECO_UNIT', $precoUnit)."  
                                ".$this->setTagXml('CAMPANHA', $campanha)." 
                                ".$this->setTagXml('SUBTOTAL', $subtotal)."
                                ".$this->setTagXml('SAVE', $persistItem)."    
                        </ITEM>";
                    }                
                } else {
                    $msgErr = 'Pedido->getXml() Nenhum produto foi adicionado ao pedido.';
                    throw new Exception($msgErr);
                }

                $xml .= "</PEDIDO>";
            } else {
                $msgErr = 'Pedido->getXml() Nenhum par�metro foi informado.';
                throw new Exception($msgErr);
            }
            $xml .= "</ROOT>";

            return $xml;
        }

        /**
         * M�todo auxiliar de getXml(), retira caracteres n�o permitidos antes de criar 
         * a tag PARAM com seu respectivo valor.
         * 
         * @param string $tag
         * @param mixed $value
         * @return string Tag que ser� usada para compor o XML de envio.
         */
        private function setTagXml($tag,$value){
            $value  = str_replace('"', '', $value);
            $value  = str_replace('<', '', $value);
            $value  = str_replace('>', '', $value);
            $tagXml = "<PARAM id='{$tag}'>{$value}</PARAM>";
            return $tagXml;
        }

        /**
         * Captura dados de um pedido a partir do $numPedido informado.
         * 
         * @param integer $numPedido
         * @param string $uid Opcional. Se informado, deve conter o identificador da assinatura que deseja consultar.
         * @return FALSE|string 
         */
        function getPedido($numPedido,$uid=''){
            $response = FALSE;        
            if ((int)$numPedido > 0) {            
                $params     = "numPedido=".$numPedido;
                $response   = $this->send('getPedido',$params,$uid);
            }
            return $response;
        }

        /**
         * Grava um novo pedido no servidor.
         * 
         * @param string $uid
         * @return TRUE|string Caso um erro ocorra retornar� mensagem de erro, ou ent�o TRUE.
         */
        function savePedido($uid=''){
            $response       = FALSE;
            $xmlNovoPedido  = $this->getXml();         
            if (strlen($xmlNovoPedido) > 0) {
                $params         = "xmlNovoPedido=".$xmlNovoPedido;
                $response       = $this->send('savePedido',$params,$uid);
                if (is_bool($response)) $response = (bool)$response;//Converte para um valor booleano
            } else {
                $msgErr = "Imposs�vel salvar o pedido. A string XML contendo os dados do pedido est� vazia.";
                throw new Exception($msgErr);
            }
            return $response;
        }

        /**
         * Gera a string XML de envio e faz a conex�o com o gateway.
         *  
         * @param $uid Cont�m o identificador da assinatura que deseja consultar.
         * @return string Resposta do gateway.
         * @throws Exception Caso um erro ocorra na comunica��o entre o servidor local e o gateway.
         */
        private function send($action,$params,$uid=''){    
            if(!extension_loaded("curl")) {
                die('Biblioteca Curl n�o instalada. <br/>Esta biblioteca � obrigat�ria para a comunica��o com o servidor.');
            }
            
            $uid = (strlen($uid) == 0)?self::UID:$uid;
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
                $response = $objCurl->getResponse();
                //@todo Tratar a resposta do servidor (permitir apenas resposta esperada para eliminar mensagens de erros inesperados).
                return $response;
            } else {
                $err    = $objCurl->getOutput();
                $msgErr = "Pedido->send() Erro ao se comunicar com o gateway: {$err}";
                throw new Exception($msgErr);                
            }                
        }

    }
    ?>
