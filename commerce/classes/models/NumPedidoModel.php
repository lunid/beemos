<?php

    namespace commerce\classes\models;
    use \sys\classes\mvc\Model;  
    use \common\db_tables as TB; 
    
    class NumPedidoModel extends Model {   
        private $idAssinatura;
        private $ambiente;
        
        function setAssinaturaEAmbiente($idAssinatura,$ambiente){            
            $this->idAssinatura = (int)$idAssinatura;
            $this->ambiente     = $ambiente;
        }        
        
        /**
         * Localiza o último NUM_PEDIDO utilizado no ambiente atual e soma um.
         * Se nenhum pedido for encontrado para a assinatura atual, retorna zero.
         * 
         * @return integer
         */        
        function getProxNumPedido(){
            $proxNumPedido      = 0;            
            $ultimoNumPedido    = $this->getUltimoNumPedido();  
            if ($ultimoNumPedido > 0) $proxNumPedido = $ultimoNumPedido+1;
            return $proxNumPedido;
        }
        
        /**
         * Localiza o último NUM_PEDIDO usado na assinatura atual, no ambiente atual (PROD ou TEST).
         *          
         * @return integer
         */
        function getUltimoNumPedido(){
            $tbNumPedidoRelAssinatura = $this->getTbNumPedidoRelAssinatura();            
            
            $field          = 'NUM_PEDIDO';
            $result         = $tbNumPedidoRelAssinatura->select($field)
                            ->where('ID_ASSINATURA='.$this->idAssinatura)
                            ->orderBy('NUM_PEDIDO DESC')
                            ->limit('1')
                            ->execute();
            //print_r($result);
            $ultimoNumPedido  = (count($result) > 0)?(int)$result[0][$field]:0;
            return $ultimoNumPedido;
        }    
        
        private function getTbNumPedidoRelAssinatura(){
            $tbNumPedidoXAssinatura = new TB\NumPedidoRelAssinatura();
            if ($this->ambiente == 'TEST') $tbNumPedidoXAssinatura = new TB\TestNumPedidoRelAssinatura();
            return $tbNumPedidoXAssinatura;
        }
        
        /**
         * Verifica se o NUM_PEDIDO informado está em uso no ambiente atual (PROD ou TEST).
         * 
         * @param integer $numPedido
         * @return boolean
         * @throws \Exception Caso o $numPedido informado seja zero ou inválido.
         */
        function checkNumPedidoDisponivel($numPedido){   
            $disponivel             = FALSE;
            $tbNumPedidoXAssinatura = $this->getTbNumPedidoRelAssinatura($this->ambiente);  
            $numPedido              = (int)$numPedido;
            
            if ($numPedido > 0) {
                $field          = 'NUM_PEDIDO';
                $result         = $tbNumPedidoXAssinatura->select($field)
                                ->where('ID_ASSINATURA='.$this->idAssinatura.' AND NUM_PEDIDO='.$numPedido)
                                ->limit('1')
                                ->execute();
                
                if (count($result) == 0) $disponivel = TRUE;                
            } else {
                $msgErr = "Impossível checar a disponibilidade de NUM_PEDIDO. O valor informado é zero ou não é válido.";
                throw new \Exception($msgErr);
            }
            return $disponivel;
        }
        
        function saveNumPedido($numPedido){
            $out = FALSE;
            if ($numPedido > 0) {
                $tbNumPedidoXAssinatura = $this->getTbNumPedidoRelAssinatura($this->ambiente);  
                $tbNumPedidoXAssinatura->ID_ASSINATURA = $this->idAssinatura;
                $tbNumPedidoXAssinatura->NUM_PEDIDO    = $numPedido;
                $tbNumPedidoXAssinatura->DATA_REGISTRO = date('Y-m-d H:i:s');
                $idNumPedidoXassinatura = $tbNumPedidoXAssinatura->save();
                if ($idNumPedidoXassinatura > 0) $out = TRUE;
            }
            return $out;
        }
    }

?>
