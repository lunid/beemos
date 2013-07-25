<?php

    namespace auth\classes\helpers;
    use \auth\classes\models as models;
    use \sys\classes\util as util;
    
    class Assinatura {

        private $objDados   = NULL;
        private $msgStatus  = '';
        
        function __construct($hashAssinatura=''){
            $this->loadHash($hashAssinatura);
        }
        
        function loadHash($hashAssinatura=''){
            $hash                = (strlen($hashAssinatura)) ? $hashAssinatura : $this->hashAssinatura;
            $objAssinaturaModel  = new models\AssinaturaModel();
            $this->objDados      = $objAssinaturaModel->loadHashAssinatura($hash);
        }
        
        function getIdAssinatura(){
            $idAssinatura = (int)$this->ID_ASSINATURA;
            return $idAssinatura;
        }
        
        /**
         * Verifica se a assinatura atual está vigente, sem bloqueios.
         * @return boolean
         */
        function assinaturaValida(){
            $out = TRUE;
            if ($this->usuarioBloq() || $this->assinaturaBloq()) $out = FALSE;
            return $out;
        }
        
        function usuarioBloq(){            
           $bloq = $this->checkDateBloq('BLOQ_USUARIO_EM');     
           if ($bloq) $this->msgStatus = "O usuário informado está bloqueado.";
        }
        
        function assinaturaBloq(){
            $bloq =  $this->checkDateBloq('BLOQ_ASSINATURA_EM');         
            if ($bloq) $this->msgStatus = "A assinatura informada está bloqueada.";            
            return $bloq;
        }
        
        private function checkDateBloq($field){
            $idAssinatura = $this->getIdAssinatura();
            if ($idAssinatura > 0) {
                $dateBloq = $this->$field;    
                if (util\Date::isValidDateTime($dateBloq)) {
                    //Registro bloqueado;                    
                    return TRUE;
                }
                return FALSE;
            } else {
                throw new \Exception('Impossível verificar o bloqueio da assinatura, pois nenhum registro foi encontrado.');
            }
        }
        
        function getStatus(){
            return $this->msgStatus;
        }
        
        function __get($nameVar) {
            $value      = '';
            $objDados   = $this->objDados;
            
            if (is_object($objDados) && isset($objDados->$nameVar)) {
                $value = $objDados->$nameVar;
            }
            return $value;
        }
    }
?>
