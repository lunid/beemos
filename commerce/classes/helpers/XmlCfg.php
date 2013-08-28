<?php
namespace commerce\classes\helpers;
use \sys\classes\commerce\XmlValidation;

class XmlCfg extends XmlValidation {
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
