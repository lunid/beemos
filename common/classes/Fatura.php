<?php

namespace common\classes;
use \common\classes\helpers\commerce\XmlValidationHelper;

class Fatura {
    
    private $objAssinatura;
    
    function __construct($objAssinatura){
       if (is_object($objAssinatura) && $objAssinatura instanceof \auth\classes\helpers\Assinatura) {
           //Assinatura informada
           $this->objAssinatura = $objAssinatura;
       } else {
           $msgErr = "Fatura: Impossível identificar a assinatura do usuário.";
           throw new \Exception($msgErr);
       }
    }    
    
    function nova($objCfg,$objSacado,$objItens){
        $this->checkObjXml($objCfg);
        //$this->setSacado($objSacado);
        //$this->setItens($objItens);
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
    
    /**
     * Verifica se o objeto informado é do tipo XmlValidation.
     * XmlValidation é o objeto responsável por receber/validar dados de uma string
     * XML contendo dados de uma fatura.
     * 
     * @param \common\classes\XmlValidationHelper $objXmlValidation
     * @return \common\classes\XmlValidationHelper
     * 
     * @throws \Exception Caso o objeto informado seja de um tipo diferente do esperado. 
     */
    private function checkObjXml($objXmlValidation){
        if (is_object($objXmlValidation) && $objXmlValidation instanceof XmlValidationHelper) {
            $class = get_class($objXmlValidation);
            $class::checkObjParams($objXmlValidation);//Verifica se os parâmetros do objeto são válidos.
            return $obj;
        } else {
            throw new \Exception("Fatura: o objeto informado não é do tipo XmlValidation");
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
