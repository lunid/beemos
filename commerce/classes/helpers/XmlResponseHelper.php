<?php

namespace commerce\classes\helpers;

class XmlResponseHelper {
    
    private $arrParams       = array();
    private $arrMsgErr       = array();
    private $codStatus       = 0;
    private $msgStatus       = '';
    
    function __construct($codStatus='',$msgStatus=''){
        $this->setStatus($codStatus, $msgStatus);
    }
    
    function setStatus($codStatus,$msgStatus){
        $this->codStatus    = $codStatus;
        $this->msgStatus    = $msgStatus;
    }
    
    function addParam($name,$value){
        $name                   = strtoupper($name);
        $this->arrParams[$name] = $value;        	        
    }
    
    function render(){
        $arrParams      = $this->arrParams;        
        $xml            = "<?xml version='1.0' encoding='UTF-8'?><ROOT>";
        $nodeParams     = '';
        $codigoStatus   = 0;
        $msgStatus      = '';
        
        if (is_array($arrParams)){
            foreach($arrParams as $key=>$value) {
                $nodeParams .= "<PARAM><NAME>$key</NAME><VALUE>$value</VALUE></PARAM>";
            }                                                                               
        } else {
            $codigoStatus  = 2;
            $msgStatus     = 'Erro ao gerar parâmetros de retorno (parâmetros inexistentes).';
        } 
        $xml .= "<STATUS><CODIGO>{$this->codStatus}</CODIGO><MSG>{$this->msgStatus}</MSG></STATUS>";
        $xml .= "<PARAMS>".$nodeParams."</PARAMS>";
        $xml .= "</ROOT>";     
        
        return $xml;
    }
        
}

?>
