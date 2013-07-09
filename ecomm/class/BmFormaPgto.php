<?php

class BmFormaPgto {
    
    private $arrFormaPgto   = array('BLT_BRADESCO','BLT_ITAU','BLT_BB','AMEX');
    private $formaPgto      = NULL;
    
    function __construct() {
        
    }
    
    function setBltBradesco(){
        $this->setBoleto('BRADESCO');
    }
    
    function setBltBb(){
        $this->setBoleto('BB');
    }
    
    function setBltItau(){
        $this->setBoleto('ITAU');
    }
    
    private function setBoleto($banco){
        $key  = FALSE;
        if (strlen($banco) > 0) {
            $banco = strtoupper($banco);//Converte para caixa alta (maiúsculas).
            $key = array_search($banco, $this->arrFormaPgto);
            if ($key !== FALSE) {
                $this->formaPgto = $banco;
            }
        }

        if ($key === FALSE) {
            $msgErr = 'A forma de pagamento '.$formaPgto.' não é válida.';
            throw new \Exception($msgErr);                
        }        
    }

    function setAmex($cc,$validade,$codSeg,$parcelas){
        $this->setCc('AMEX', $cc, $validade, $codSeg,$parcelas);      
    }    
    
    /**
    * Define o pagamento com cartão de crédito via operadora CIELO.
    * 
    * @param string $cc Número do cartão, sem separadores.
    * @param integer $validade Validade do cartão no formato yyyymm
    * @param integer $codSeg Código de segurança do cartão
    * @param $parcelas Número de parcelas
    */
    function setCielo($cc,$validade,$codSeg,$parcelas){
        $this->setCc('CIELO', $cc, $validade, $codSeg,$parcelas);      
    }
    
    function setRedecard($cc,$validade,$codSeg,$parcelas) {
        $this->setCc('REDECARD', $cc, $validade, $codSeg,$parcelas);      
    }
    
    private function setCc($operadora,$cc,$validade,$codSeg,$parcelas){
        
    }
}

?>
