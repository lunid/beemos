<?php

    namespace commerce\classes\models;
    use \sys\classes\mvc\Model;  
    use \auth\classes\helpers\ErrorHelper;
    use \common\db_tables as TB;    
    
    class PedidoModel extends Model {   

        private $objDadosPedido = NULL;
        private $arrDadosPedido = NULL;
        private $arrItensPedido;
        private $arrDadosSacado;
        private $idAssinatura;
        private $ambiente;//PROD ou TEST        
               
        function setAssinaturaEAmbiente($idAssinatura,$ambiente){            
            $this->idAssinatura = (int)$idAssinatura;
            $this->ambiente     = $ambiente;
        }
        
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
        
        /**
         * Salva um novo registro de pedido.
         * 
         * @param stdClass $objDadosPedido
         * @return integer
         */
        function savePedido($objDadosPedido) {
            $idNumPedido    = 0;
            $idAssinatura   = $this->idAssinatura;
            
            if ($idAssinatura > 0) {
                $tbNumPedido = $this->getTbNumPedido();
                $arrVars     = get_object_vars($objDadosPedido);
                
                foreach($arrVars as $key=>$value){
                    if ($key == 'SAVE_SAC') continue;
                    //echo "$key = $value<br>";
                    $tbNumPedido->$key = $value;                
                }
                
                $tbNumPedido->ID_ASSINATURA = $idAssinatura;
                $tbNumPedido->DATA_REGISTRO = date('Y-m-d H:i:s');                
                $idNumPedido = $tbNumPedido->save();
            } else {
                $msgErr = "Impossível salvar o pedido solicitado. O identificador da assinatura não é válido. Por favor, entre em contato com o suporte.";
                throw new \Exception($msgErr);                
            }
            return $idNumPedido;
        }
        
        /**
         * Exclui os itens do pedido informado.
         * 
         * @param integer $numPedido
         * @return integer Retorna o total de itens excluídos.
         */
        function delItens($numPedido){
            $numDel         = 0;
            $idAssinatura   = (int)$this->idAssinatura;
            
            if ($numPedido > 0 && $idAssinatura > 0) {
                $tbItemPedido   = $this->getTbItemPedido();
                $arrWhere       = array("NUM_PEDIDO=%i AND ID_ASSINATURA=%i",$numPedido,$idAssinatura);
                $numDel         = $tbItemPedido->delete($arrWhere);
            }
            return $numDel;
        }
        
        function saveItemPedido($idPedido,$numPedido,$objItemPedido){
            $idItemPedido = 0;
            if ($idPedido > 0 && (int)$numPedido > 0 && is_object($objItemPedido)) {
                $tbItemPedido   = $this->getTbItemPedido();
                $arrVars        = get_object_vars($objItemPedido);
                
                foreach($arrVars as $key=>$value){
                    if ($key == 'SAVE') continue;
                    //echo "$key = $value<br>";
                    $tbItemPedido->$key = $value;                
                }
                
                $tbItemPedido->ID_ASSINATURA    = $this->idAssinatura;
                $tbItemPedido->ID_ECOMM_PEDIDO  = $idPedido;
                $tbItemPedido->NUM_PEDIDO       = $numPedido;
                $tbItemPedido->DATA_REGISTRO    = date('Y-m-d H:i:s');  
                
                $idItemPedido = $tbItemPedido->save();                
            } else {
                $msgErr = "Impossível salvar o item solicitado para o pedido atual. Um ou mais parâmetros obrigatórios não foram informados.";
                throw new \Exception($msgErr);                   
            }
            return $idItemPedido;
        }
        
        private function getTbNumPedido(){           
            $tbNumPedido = new TB\EcommPedido();
            if ($this->ambiente == 'TEST') $tbNumPedido = new TB\TestEcommPedido();
            return $tbNumPedido;
        }
             
        private function getTbItemPedido(){           
            $tbNumPedido = new TB\EcommItemPedido();
            if ($this->ambiente == 'TEST') $tbNumPedido = new TB\TestEcommItemPedido();
            return $tbNumPedido;
        }        
    }

?>
