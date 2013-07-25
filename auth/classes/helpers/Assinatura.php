<?php

    namespace auth\classes\helpers;
    use \auth\classes\models as models;
    use \sys\classes\util as util;
    
    class Assinatura {

        private $objDados   = NULL;
        private $msgStatus  = '';
        
        function __construct($hashAssinatura=''){
            if (strlen($hashAssinatura) > 0) $this->loadHash($hashAssinatura);
        }
        
        /**
         * Carrega os dados de uma assinatura a partir de seu HASH.
         *       
         * @param string $hashAssinatura String com 32 ou 64 caracteres alfanuméricos.
         * @return object \StdClass
         * 
         * @throws \Exception Caso o HASH informado possua caracteres inválidos ou seja menor que a quantidade esperada.
         * @throws \Exception Caso nenhum registro seja encontrado com o HASH informado.
         */
        function loadHash($hashAssinatura){
            $objDados                           = NULL;
            $arrErrParams['HASH_ASSINATURA']    = $hashAssinatura;
            $hash                               = (strlen($hashAssinatura)) ? $hashAssinatura : $this->hashAssinatura;
            
            if (strlen($hash) >= 32 && ctype_alnum($hash)) {
                $objAssinaturaModel  = new models\AssinaturaModel();            
                $objDados            = $objAssinaturaModel->loadHashAssinatura($hash);
                if (is_object($objDados) && (int)$objDados->ID_ASSINATURA > 0) {
                    $this->objDados = $objDados;
                } else {                        
                    $message = ErrorHelper::eAssinatura('ERR_ASS_NOT_EXISTS',$arrErrParams);
                    throw new \Exception ($message);                    
                }   
            } else {                
                $message = ErrorHelper::eAssinatura('ERR_HASH_ASS',$arrErrParams);
                throw new \Exception ($message);                                    
            }   
            return $objDados;
        }
        
        function getIdAssinatura(){
            $idAssinatura = (int)$this->ID_ASSINATURA;
            return $idAssinatura;
        }
        
        /**
         * Verifica se a assinatura atual está vigente e sem bloqueios.
         * Assinaturas com inadimplência deve ser bloqueadas via rotina de cobrança.
         *
         * @return boolean
         */
        function assinaturaValida(){
            try {
                $out = TRUE;
                if ($this->usuarioBloq() || $this->assinaturaBloq()) $out = FALSE;
                return $out;
            } catch (\Exception $e) {
                throw $e;
            }
        }
        
        function usuarioBloq(){            
           $bloq = $this->checkDateBloq('BLOQ_USUARIO_EM');     
           if ($bloq) {
               $message = ErrorHelper::eAssinatura('VLD_BLOQ_USER');
               throw new \Exception ($message);
           }
        }
        
        function assinaturaBloq(){
            $bloq =  $this->checkDateBloq('BLOQ_ASSINATURA_EM');     
            
            if ($bloq) {
               $message = ErrorHelper::eAssinatura('VLD_BLOQ_ASS');
               throw new \Exception ($message);
            }
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
               $message = ErrorHelper::eAssinatura('VLD_ID_ASS');
               throw new \Exception ($message);                               
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
