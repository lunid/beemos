<?php
namespace common\classes\helpers\commerce;

class XmlSacadoHelper extends XmlValidationHelper {
    protected $nodeName     = 'SACADO';//Nome do nó XML que contém as dados para a classe atual.
    protected $arrVldParams = array(            
        'NOME:string:1:100',
        'EMAIL:email:0:100',
        'ENDERECO:string:0:120',
        'CIDADE:string:0:100',
        'UF:string:0:2',
        'CPF_CNPJ:string:0:20',
    );        
}

?>
