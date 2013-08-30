<?php

namespace common\classes\helpers\commerce;

class XmlCheckoutBltHelper extends XmlValidationHelper {
   protected $nodeName     = 'BOLETO';//Nome do nó XML que contém as dados para a classe atual.
   

    protected $arrVldParams = array(            
        'BANCO:string:0:20',
        'VENCIMENTO:date:0:0'
    );  
}

?>
