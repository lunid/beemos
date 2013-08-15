<?php

namespace commerce\classes\helpers;
use \sys\classes\commerce\XmlValidation;

class XmlCheckoutBlt extends XmlValidation {
   protected $nodeName     = 'CFG';//Nome do nó XML que contém as dados para a classe atual.
   
    protected $arrVldParams = array(            
        'NUM_PEDIDO:integer:0:0'
    );  
}

?>
