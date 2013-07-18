<?php
    include('BmConn.php');
    class BmPedido extends BmXml {

        private $arrParams          = array();
        private $xmlParamsSacado    = '';
        private $xmlParamsMeioPgto  = '';        
        private $arrObjItemPedido   = array(); //Array de objetos do tipo ItemPedido.
        private $valorTotalDoPedido = 0;//Valor decimal
        private $valorDesconto      = 0;//Valor decimal
        private $valorFrete         = 0;//Valor decimal
        private $saveSacado = FALSE;//Grava os dados do sacado no servidor remoto.        

        /**
         * Caso um array seja informado, valida os parâmetros recebidos de acordo com $arrLibParams.
         * Os parâmetros corretos são armazenados em $arrParams, onde o nome do campo torna-se um índice
         * associativo do array com seu respectivo valor.
         * 
         * @param array $arrDados Array associativo onde cada índice deve existir em $arrLibParams.
         */
        function __construct(){
            
        }      
        
        public function addSacado($objSacado){
            $this->xmlParamsSacado = $this->getXmlFromObject($objSacado);
        }
        
        public function addMeioPgto($objMeioPgto){
            $this->xmlParamsMeioPgto = $this->getXmlFromObject($objMeioPgto);
        }         
        
        /**
         * Faz a inclusão de dois ou mais itens de pedido.
         * Este método é uma forma reduzida do método addItemPedido;
         * 
         * @see addItemPedido()
         * @param BmItemPedido[] $arrObjItensPedido Deve conter um array com objetos BmItemPedido
         */
        public function addItensPedido($arrObjItensPedido) {
            if (is_array($arrObjItensPedido)) {
                foreach($arrObjItensPedido as $objItemPedido) {
                    if (is_object($objItemPedido) && $objItemPedido instanceof BmItemPedido){
                        $this->addItemPedido($objItemPedido);    
                    } else {
                        $msgErr = "{$objItemPedido} não é um item de pedido válido.";
                        throw new \Exception($msgErr);                         
                    }
                }
            }            
        }        

        /**
         * Adiciona um item (produto) ao pedido atual.
         * 
         * @param ItemPedido $objItemPedido
         * @return void
         */
        public function addItemPedido($objItemPedido){
            if (is_object($objItemPedido)) $this->arrObjItemPedido[] = $objItemPedido;
        }

        /**
         * Define o parâmetro numPedido.
         * O valor deve ser numérico.
         * 
         * @param integer $numPedido
         */
        public function setNumPedido($numPedido){
            if (ctype_digit($numPedido)) {
                $this->addParamXml('NUM_PEDIDO',$numPedido);
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

               $this->addParamXml('VALOR_TOTAL',$valueDec);
            } else {
                $msgErr = "Pedido->setTotalPedido() O valor informado {$value} não é um valor válido.";
                throw new \Exception($msgErr);
            }
        }
        
        public function setDesconto($value){
            if (is_numeric($value)) {
               $valueDec               = number_format($value, 2, '.', '');
               $this->valorDesconto    = $valueDec;

               $this->addParamXml('VALOR_DESC',$valueDec);
            } else {
                $msgErr = "Pedido->setDesconto() O valor informado {$value} não é um valor válido.";
                throw new \Exception($msgErr);
            }            
        }

        public function getTotalPedido(){
            $valorTotalDoPedido = $this->valorTotalDoPedido;
            if ($valorTotalDoPedido == 0) {
                //Um valor explícito não foi informado. Calcula o total do pedido           
                $subtotalItens      = 0;
                $frete              = (float)$this->valorFrete;
                $valorDesc          = (float)$this->valorDesconto;
                $arrObjItemPedido   = $this->arrObjItemPedido;
                if (is_array($arrObjItemPedido)) {
                    foreach($arrObjItemPedido as $objItemPedido){
                        //Soma o subtotal do produto atual com os anteriores:
                        $subtotalItens += $objItemPedido->calcSubtotal();
                    }
                }

                $valorTotalDoPedido = $subtotalItens+$frete;
                if ($valorDesc <= $valorTotalDoPedido) {
                    $valorTotalDoPedido -= $valorDesc;
                } else {
                    $msgErr = "Pedido->getTotalPedido() O desconto de {$value} é maior que o valor total do pedido de {$valorTotalDoPedido}.";
                    throw new \Exception($msgErr);                    
                }
            }
            $this->addParamXml('VALOR_TOTAL',$valorTotalDoPedido);
            return $valorTotalDoPedido;
        }

        /**
         * Informa um valor numérico (decimal) que representa o valor do frete do pedido.              
         * A informação de frete é opcional. Se não for informado o valor zero será enviado.          
         * 
         * @param float $value Valor do frete. Apenas o ponto como separador decimal é aceito (formato 9999.99).
         * @return void
         */    
        public function setFrete($value=0){
            if (is_numeric($value)) {
               $valueDec            = number_format($value, 2, '.', '');
               $this->valorFrete    = $valueDec;

               $this->addParamXml('FRETE',$valueDec);
            } elseif (strlen($value) > 0) {
                $msgErr = "Pedido->setFrete() O valor informado {$value} não é um valor numérico válido. ";
                $msgErr .= "Utilize ponto como separador decimal (formato: 9999.99).";
                throw new \Exception($msgErr);
            }               
        }

        /**
         * Gera a string XML que será enviada ao gateway de pagamento.
         * 
         * @throws Exception Caso nenhum parâmetro tenha sido informado ou então não exista produto(s).
         */
        function getXml(){
            
            $this->getTotalPedido();   
            $arrObjItemPedido   = $this->arrObjItemPedido;            
            $valoresAdic        = $this->getTagParams();
   
            $xml  = '<ROOT>';
            $xml .= "<PEDIDO>";
            $xml .= $this->xmlParamsSacado;
            $xml .= $this->xmlParamsMeioPgto;                

                    

            if (is_array($arrObjItemPedido)) {   
                //Inclui os itens do pedido:
                foreach ($arrObjItemPedido as $objItemPedido){
                    $xml .= $objItemPedido->getXml();
                } 
            } else {
                $msgErr = 'Pedido->getXml() Nenhum produto foi adicionado ao pedido. É necessário incluir pelo menos um produto ao novo pedido.';
                throw new \Exception($msgErr);
            }
            $xml .= "<VALORES_ADIC>{$valoresAdic}</VALORES_ADIC>";
            $xml .= "</PEDIDO>";
            $xml .= "</ROOT>";

            return $xml;
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
        function save($uid=''){
            $response       = FALSE;
            $strXml         = $this->getXml();         
            
            if (strlen($strXml) > 0) {
                $objConn = new BmConn();
                $objConn->debugOff();
                if ($this->debug) $objConn->debugOn();
                $objConn->addParamXml($strXml);
                $objConn->savePedido();                
                
                $xmlResponse = $objConn->send();
                echo $xmlResponse;
                //if (is_bool($response)) $response = (bool)$response;//Converte para um valor booleano
            } else {
                $msgErr = "Impossível salvar o pedido. A string XML contendo os dados do pedido está vazia.";
                throw new \Exception($msgErr);
            }
            return $response;
        }

        

    }
    ?>
