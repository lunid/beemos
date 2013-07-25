<?php
    namespace auth\classes\models;
    
    use \sys\classes\mvc\Model;   
    use \common\db_tables as TB;
    
    class AssinaturaModel extends Model {                
        
        /**
         * Carrega os dados da assinatura a partir de um hash informado.
         * 
         * @param string $hashAssinatura
         * @return boolean|stdClass
         */
        function loadHashAssinatura($hashAssinatura){
            if (strlen($hashAssinatura) >= 32 && ctype_alnum($hashAssinatura)) {
                return $this->loadAssinatura('HASH_ASSINATURA', $hashAssinatura);           
            }
            return FALSE;
        }
        
        function loadIdAssinatura($id){
            if ((int)$id > 0) {
                return $this->loadAssinatura('ID_ASSINATURA', $id);           
            } 
            return FALSE;
        }
        
        private function loadAssinatura($field,$value){
            $objAssinatura    = FALSE;
            $tbAssinatura     = new TB\VwAssinatura();
            $result     = $tbAssinatura->select('*')->where("{$field}='{$value}'")->execute();
            if (count($result) == 1) {
                $objAssinatura = $tbAssinatura->getObj($result);                
            }
            return $objAssinatura;            
        }            
    }
?>
