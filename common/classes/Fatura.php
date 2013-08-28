<?php

namespace sys\classes\commerce;

class Fatura {
    
    function __construct($hashAssinante, $numFatura){
       $this->loadFatura($numFatura);
    }    
    
    /**
     * Localiza os dados de uma fatura para o assinante atual a partir do 
     * número da fatura informado.
     * 
     * @param integer $numFatura
     * @return mixed Retorna um XML dos dados localizados 
     * ou FALSE caso nenhum registro seja encontrado.
     * 
     */
    function loadFatura($numFatura){
        $numFatura = (int)$numFatura;
        if ($numFatura > 0) {
            /*
             * Um número de fatura foi informado.
             * Verifica se o assinante possui registros para a fatura atual.
             * 
             */
            
            if (is_array($resultset)) {
                //Uma fatura foi localizada
                
            }
        }
    }
    
    
    function nova($numFatura){
        
    }
    
    private function checkObjXml($obj){
        if (is_object($obj) && $obj instanceof XmlValidation) {
            return $obj;
        } else {
            throw new \Exception("Fatura: o objeto informado em {$param} não é válido.");
        }
    }
    
    function setConfig($obj){
        try {
            $obj        = $this->checkObjXml($obj,__FUNCTION__);
            $numFatura  = $obj->getNumFatura();
            if ($numFatura == 0) {
                /*
                 * Um número de fatura não foi informado. Localiza o próximo número de fatura
                 * disponível para o convêncio atual.
                 */
                
            }
        } catch (\Exception $e) {
            throw new $e;
        }
    }
    
    function setSacado($obj){
        
    }
    
    function setItens($obj){
        
    }
    
    function setCheckout($objCc, $objBlt){
        
    }    
}

?>
