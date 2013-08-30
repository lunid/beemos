<?php

namespace common\classes\helpers\commerce;

class XmlCheckoutCcHelper extends XmlValidationHelper {
    protected $nodeName     = 'CARTAO';//Nome do nó XML que contém as dados para a classe atual.    
    
    protected $arrVldParams = array(            
        'BANDEIRA:string:1:15',
        'CC:string:1:15',
        'COD_SEG:integer:1:0',
        'VALIDADE:integer:1:0',
        'PARCELAS:integer:0:0',
        'CONVENIO:string:0:15',
        'CAPTURA:integer:0:0'
    );  
    
    function init(){
        $this->requiredOff();
    }
}

?>
