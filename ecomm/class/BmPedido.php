<?php
    class BmPedido extends BmXml implements BmXmlInterface {

        //Lista de parâmetros permitidos ao criar um novo pedido
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
            'OBS:setObs',
            'FORMA_PGTO:setFormaPgto'
        );

        private $arrParams          = array();
        private $objCheckout        = NULL;
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
         * Caso um array seja informado, valida os parâmetros recebidos de acordo com $arrLibParams.
         * Os parâmetros corretos são armazenados em $arrParams, onde o nome do campo torna-se um índice
         * associativo do array com seu respectivo valor.
         * 
         * @param array $arrDados Array associativo onde cada índice deve existir em $arrLibParams.
         */
        function __construct($arrDados=array()){
            $this->loadArrDados($arrDados);
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
        
        /**
         * Salva os dados do sacado no servidor remoto.
         * Este recurso pode ser útil caso seja necessário efetuar uma nova cobrança 
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
         * Recebe um array associativo de dados onde cada índice deve coincidir com um índice em $arrLibParams.
         * 
         * @param string[] $arrDadosSac
         * @return void
         * 
         * @throws Exception Caso um ou mais parâmetros informados não possuam correspondência em $arrLibParams.
         */
        public function loadArrDados($arrDados){
            $arrMsgErr = NULL;
            if (is_array($arrDados) && count($arrDados) > 0) {
                $arrLibParams    = $this->arrLibParams;//Parâmetros permitidos
                $arrAction       = array();
                $arrTag          = array();

                //Separa um array com os parâmetros autorizados e outro com os seus respectivos métodos.
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
                            //Existe um método para definir o valor do parâmetro atual:
                            $this->$action($value);
                        } else {
                            throw new \Exception('Pedido->loadArrDadosSac() A tag informada '.$name.' parece ser inválida ou o método '.$action.' associado a ela não existe.');
                        }
                    } else {
                        $arrMsgErr[] = "Parâmetro {$name} não permitido.";
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
        
        public function addFormaPgto($objFormaPgto){
            if (is_object($objFormaPgto)) $this->objFormaPgto = $objFormaPgto;
        }

        /**
         * Define o parâmetro numPedido.
         * O valor deve ser numérico.
         * 
         * @param integer $numPedido
         */
        public function setNumPedido($numPedido){
            if (ctype_digit($numPedido)) {
                $this->addParam('NUM_PEDIDO',$numPedido);
            } else {
                $msgErr = 'O número do pedido deve ser um valor numérico inteiro.';
                throw new \Exception($msgErr);
            }
        }

        /**
         * Informa o valor total do pedido.
         * Este valor refere-se ao valor que será cobrado do cliente (soma de produtos + frete + acréscimos - descontos).
         * Caso não seja informado, o valor total do pedido será calculado pelo sistema.
         * 
         * @param float $value Valor decimal (formato 9999.99)
         * @return void
         * @throws \Exception Caso o valor informado não seja numérico
         */
        public function setTotalPedido($value){
            if (is_numeric($value)) {
               $valueDec                    = number_format($value, 2, '.', '');
               $this->valorTotalDoPedido    = $valueDec;

               $this->addParam('VALOR_TOTAL',$valueDec);
            } else {
                $msgErr = "Pedido->setTotalPedido() O valor informado {$value} não é um valor válido.";
                throw new \Exception($msgErr);
            }
        }

        public function getTotalPedido(){
            $valorTotalDoPedido = $this->valorTotalDoPedido;
            if ($valorTotalDoPedido == 0) {
                //Um valor explícito não foi informado. Calcula o total do pedido           
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
         * Informa um valor numérico que representa o valor do frete do pedido.              
         * A informação de frete é opcional. Se não for informado o valor zero será enviado.          
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
                $msgErr = "Pedido->setFrete() O valor informado {$value} não é um valor numérico válido. ";
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
        * Recebe e guarda um valor referente a um CPF (11 dígitos) ou a um CNPJ (14 dígitos).
        * 
        * @param string $value
        */
        function setCpfCnpjSac($value){
            $value = trim($value);
            if (strlen($value) > 0) {
                $arrValue   = str_split($value);
                $arrChar    = array();

                //Retira caracteres que não sejam numéricos:
                foreach ($arrValue as $char){
                    if (ctype_digit($char)) $arrChar[] = $char;
                }

                $valueChar = join('',$arrChar);

                if (strlen($valueChar) >=11 && strlen($valueChar) <= 14 && ctype_alnum($valueChar)) {
                    $this->cpfCnpjSac = $valueChar;
                    $this->addParam('CPF_CNPJ_SAC',$valueChar);
                } else {
                    $msgErr = 'Pedido->setCpfCnpjSac() O CPF/CNPJ informado ('.$value.') parece ser inválido.';
                    throw new Exception($msgErr);
                }       
            }
        }           

        /**
         * Armazena uma variável com seu respectivo valor em um array que será usado
         * posteriormente para gerar o XML de envio.
         * 
         * @param string $name Nome que será usado no atributo 'id' da tag XML 'PARAM'.
         * @param mixed $value Valor da tag.
         * @throws Exception caso o parâmetro $name não seja um parâmetro válido.
         */
        private function addParam($name,$value){
           $name            = strtoupper($name);
           $arrLibParams    = $this->arrLibParams;//Parâmetros permitidos
           $arrTag          = array();

           foreach($arrLibParams as $label){
               list($indice,$action) = explode(':',$label);
               if (strlen($indice) > 0) $arrTag[] = $indice;
           }

           $key = array_search($name,$arrTag);
           if ($key !== FALSE) {
               $this->arrParams[$name] = $value;
           } else {
               $msgErr = 'O parâmetro informado não é válido.';
               throw new Exception($msgErr);
           }
        }
        
        function checkout($objCheckout){
            if (is_object($objCheckout)){
                $this->objCheckout = $objCheckout;
            } else {
               $msgErr = 'BmPedido->checkout(): O parâmetro informado não é um objeto válido.';
               throw new Exception($msgErr);                
            }
        }

        /**
         * Gera a string XML que será enviada ao gateway de pagamento.
         * 
         * @throws Exception Caso nenhum parâmetro tenha sido informado ou então não exista produto(s).
         */
        function getXml(){
            $xml            = '<ROOT>';
            $arrParams      = $this->arrParams;
            $arrItemPedido  = $this->arrItemPedido;
            $objCheckout    = $this->objCheckout;
            
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
                    
                    $xml .= "<CHECKOUT>";
                    if (is_object($objCheckout)) {
                        $xml .= $objCheckout->getXml();
                    }
                    $xml .= "</CHECKOUT>";
                } else {
                    $msgErr = 'Pedido->getXml() Nenhum produto foi adicionado ao pedido.';
                    throw new Exception($msgErr);
                }

                $xml .= "</PEDIDO>";
            } else {
                $msgErr = 'Pedido->getXml() Nenhum parâmetro foi informado.';
                throw new Exception($msgErr);
            }
            $xml .= "</ROOT>";

            return $xml;
        }
        
        function printXml(){
            $xml = $this->getXml();
            $this->headerXml($xml);
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
         * Grava um novo pedido no servidor remoto.
         * 
         * @param string $uid
         * @return TRUE|string Caso um erro ocorra retornará mensagem de erro, ou então TRUE.
         */
        function savePedido($uid=''){
            $response       = FALSE;
            $xmlNovoPedido  = $this->getXml();         
            if (strlen($xmlNovoPedido) > 0) {
                $params         = "xmlNovoPedido=".$xmlNovoPedido;
                $xmlResponse    = $this->send('savePedido',$params,$uid);
                echo $xmlResponse;
                //if (is_bool($response)) $response = (bool)$response;//Converte para um valor booleano
            } else {
                $msgErr = "Impossível salvar o pedido. A string XML contendo os dados do pedido está vazia.";
                throw new Exception($msgErr);
            }
            return $response;
        }

        /**
         * Gera a string XML de envio e faz a conexão com o gateway.
         *  
         * @param $action Nome do método a ser executado no servidor remoto.
         * @param $params Parâmetro(s) enviado(s) ao servidor remoto. 
         * Por exemplo, xmlNovoPedido contendo o XML para o cadastro de novo pedido.
         * @param $uid Contém o identificador da assinatura que deseja consultar.
         * @return string Resposta do gateway no formato XML.
         * @throws Exception Caso um erro ocorra na comunicação entre o servidor local e o gateway.
         */
        private function send($action,$params,$uid=''){    
            if(!extension_loaded("curl")) {
                die('Biblioteca Curl não instalada. <br/>Esta biblioteca é obrigatória para a comunicação com o servidor.');
            }
            
            $uid = (strlen($uid) == 0)?self::UID:$uid;
            if ($this->debug) {
                //O debug foi acionado: interrompe o envio e imprime os parâmetros que serão enviados.
                echo "Método Pedido->send(): <br/>action: {$action}<br/>uid: {$uid}<br/>params: {$params}";
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
                $msgErr = "Pedido->send() Erro ao se comunicar com o gateway: {$err}";
                throw new Exception($msgErr);                
            }                
        }

    }
    ?>
