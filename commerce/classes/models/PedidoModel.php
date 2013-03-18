<?php

    namespace commerce\classes\models;
    use \sys\classes\mvc\Model;  
    use \auth\classes\helpers\Error;
    use \common\db_tables as TB;    
    
    class PedidoModel extends Model {   

        private $objDadosPedido = NULL;
        private $arrDadosPedido = NULL;
        private $arrItensPedido;
        private $arrDadosSacado;
        
        public function loadPedido($numPedido){
            $out = FALSE;
            if ($numPedido > 0) {
                $objDadosPedido             = $this->dadosPedido($numPedido);
                $this->objDadosPedido       = $objDadosPedido;
                $this->arrItensPedido       = $this->itensPedido($numPedido);
                if (is_object($this->objDadosPedido) && count($this->arrItensPedido) > 0) {
                    $this->arrDadosSacado = $this->dadosSacado((int)$objDadosPedido->ID_CLIENTE);
                    $out = TRUE;
                }
            }
            return $out;
        }
        
        function dadosPedido($numPedido){
            $objDados = NULL;
            if ((int)$numPedido) {
                $tbEcommPedido = new TB\EcommPedido();     
                $fields        = 'ID_CLIENTE,NOME_CLIENTE,FORMA_PGTO,ID_MEIO_PGTO,PARCELAS,VALOR_TOTAL,VALOR_PARCELA,ID_STATUS_LOJA,CUPOM,';
                $fields        .= 'MSG_BANCO,VISA_TID,AMEX_NUM_CAPTURA,COD_BANCO,COD_AUTH,COD_RET,EMAIL_ENV_CLI,DATA_VENC_BOLETO,DATA_REGISTRO'; 
                $row = $tbEcommPedido->select($fields)
                ->where("NUM_PEDIDO = {$numPedido}")
                ->execute();
            }
            if (count($row) > 0) {
                $objDados = new \stdClass();
                foreach($row[0] as $key=>$value) {
                    $objDados->$key = $value;
                }
                $this->arrDadosPedido = $row[0];
            }
            return $objDados;
        }
        
        function itensPedido($numPedido) {
            $row = array();
            if ((int)$numPedido) {
                $tbEcommItemPedido  = new TB\EcommPedido();     
                $fields = "DESCR_PRODUTO,1 AS QUANTIDADE, 'CDTS' AS UNIDADE, VALOR_TOTAL AS VALOR, CREDITOS,VALIDADE_CREDITOS";
                $row    = $tbEcommItemPedido->select($fields)
                ->where("NUM_PEDIDO = {$numPedido}")
                ->execute();
            }
            return $row;            
        }   
        
        function dadosSacado($idUser){
            $row = array();
            if ((int)$idUser) {
                $fields     = "ID_USER,PF_PJ,CPF_CNPJ,COD_POSTAL,LOGRADOURO,NUMERO,COMPLEMENTO,BAIRRO,CIDADE,UF,NOME,EMAIL";
                $tbUser     = new TB\VwUserCadastro();                    
                $row        = $tbUser->select($fields)
                ->where("ID_USER = {$idUser}")
                ->execute();
                
                if (count($row) == 0) {
                    //Verifica na tabela de Clientes
                    $fields     = "ID_CLIENTE AS ID_USER,PF_PJ,CPF_CNPJ,COD_POSTAL,LOGRADOURO,NUMERO,COMPLEMENTO,BAIRRO,CIDADE,UF,NOME_PRINCIPAL AS NOME,EMAIL";
                    $tbCliente  = new TB\Cliente();                        
                    $row        = $tbCliente->select($fields)
                    ->where("ID_CLIENTE = {$idUser}")
                    ->execute();                                        
                }
                if (count($row) > 0) $row = $row[0];
            }
            return $row;               
        }
        
        function saveNumeroTituloBoleto(){
            $tbUser     = new TB\EcommTitulo();       
            $tbUser->
        }
        
        function getObjDados(){
            return $this->objDadosPedido;
        }
        
        function getArrDados(){
            return $this->arrDadosPedido;
        }
        
        function getItens(){
            return $this->arrItensPedido;
        }

        function getDadosSacado(){
            return $this->arrDadosSacado;
        }        
    }

?>
