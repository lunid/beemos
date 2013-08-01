<?php
namespace commerce\classes\helpers;

class XmlCfg extends XmlValidation {
    protected $nodeName     = 'CFG';//Nome do nó XML que contém as dados para a classe atual.
    protected $arrVldParams = array(            
        'NUM_PEDIDO:integer:0:0'
    );     
    
    function getNumPedido(){
        $numPedido = (int)$this->NUM_PEDIDO;
        if ($numPedido == 0) {
            return 12345;
        }
    }
}

?>
