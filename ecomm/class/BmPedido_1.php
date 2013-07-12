<?php
    include('BmConn.php');
    class BmPedido extends BmXml implements BmXmlInterface {

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
            'OBS:setObs',
            'FORMA_PGTO:setFormaPgto'
        );

        private $arrParams          = array();
        private $objMeioPgto        = NULL;
        private $arrItemPedido      = array(); //Array de objetos do tipo ItemPedido.
        private $valorTotalDoPedido = 0;
        private $valorFrete         = 0;
        private $nomeSac;
        private $emailSac;
        private $enderecoSac;
        private $cidadeSac;
        private $ufSac;
        private $cpfCnpjSac;                
        private $saveSacado = FALSE;//Grava os dados do sacado no servidor remoto.        

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
        
        public function addFormaPgto($objFormaPgto){
            if (is_object($objFormaPgto)) $this->objFormaPgto = $objFormaPgto;
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
        
        function checkout($objMeioPgto){
            if (is_object($objMeioPgto)){
                $this->objMeioPgto = $objMeioPgto;
            } else {
               $msgErr = 'BmPedido->checkout(): O par�metro informado n�o � um objeto v�lido.';
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
            $objMeioPgto    = $this->objMeioPgto;
            
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
                    if (is_object($objMeioPgto)) {
                        $xml .= $objMeioPgto->getXml();
                    }
                    $xml .= "</CHECKOUT>";
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
         * @return TRUE|string Caso um erro ocorra retornar� mensagem de erro, ou ent�o TRUE.
         */
        function savePedido($uid=''){
            $response       = FALSE;
            $strXml         = $this->getXml();         
            
            if (strlen($strXml) > 0) {
                $objConn = new BmConn();
                $objConn->addParamXml($strXml);
                $arrParams['numPedido'] = 12345;
                $arrParams['sonda']     = 'BOLETO';
                $objConn->savePedido(12345);                
                
                $xmlResponse = $objConn->send();
                echo $xmlResponse;
                //if (is_bool($response)) $response = (bool)$response;//Converte para um valor booleano
            } else {
                $msgErr = "Imposs�vel salvar o pedido. A string XML contendo os dados do pedido est� vazia.";
                throw new Exception($msgErr);
            }
            return $response;
        }

        

    }
    ?>
