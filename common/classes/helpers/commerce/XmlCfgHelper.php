<?php
namespace common\classes\helpers\commerce;

class XmlCfgHelper extends XmlValidationHelper {
    protected $nodeName     = 'CFG';//Nome do nó XML que contém as dados para a classe atual.
    protected $arrVldParams = array(            
        'NUM_FATURA:integer:0:0'
    );     
    
    function getNumFatura(){
        $numFatura = (int)$this->NUM_FATURA;  
        return $numFatura;
    }
}

?>
