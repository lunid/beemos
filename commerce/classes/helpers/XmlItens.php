<?php

namespace commerce\classes\helpers;
use \sys\classes\commerce\XmlValidation;

class XmlItens extends XmlValidation {
   protected $nodeName     = 'ITEM';//Nome do nó XML que contém as dados para a classe atual.
   private $arrObjItem     = array();
   protected $arrVldParams = array(            
        'CATEGORIA:string:0:50',
        'CODIGO:string:0:20',
        'DESCRICAO:string:1:100',
        'QUANTIDADE:integer:1:0',
        'UNIDADE:string:0:5',
        'CAMPANHA:string:0:30',
        'PRECO:float:0:0',
        'SUBTOTAL:float:0:0',
    ); 
   
    function __construct($nodeXml){
        $this->nodeXml = $nodeXml;
        try {
           if (is_object($nodeXml)) {
               foreach($nodeXml as $nodeItem) {                   
                   $objItem = $this->getObjDadosXml($nodeItem);     
                   $this->arrObjItem[] = $objItem;                   
               }
           }                  
        } catch(\Exception $e) {
           throw $e;
        }        
    }           
}

?>
