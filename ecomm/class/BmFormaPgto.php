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
            $banco = strtoupper($banco);//Converte para caixa alta (mai�sculas).
            $key = array_search($banco, $this->arrFormaPgto);
            if ($key !== FALSE) {
                $this->formaPgto = $banco;
            }
        }

        if ($key === FALSE) {
            $msgErr = 'A forma de pagamento '.$formaPgto.' n�o � v�lida.';
            throw new \Exception($msgErr);                
        }        
    }

    function setAmex($cc,$validade,$codSeg,$parcelas){
        $this->setCc('AMEX', $cc, $validade, $codSeg,$parcelas);      
    }    
    
    /**
    * Define o pagamento com cart�o de cr�dito via operadora CIELO.
    * 
    * @param string $cc N�mero do cart�o, sem separadores.
    * @param integer $validade Validade do cart�o no formato yyyymm
    * @param integer $codSeg C�digo de seguran�a do cart�o
    * @param $parcelas N�mero de parcelas
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
