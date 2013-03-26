<?php

namespace commerce\classes\helpers;
use \sys\classes\util as util;

class LoadXmlStatus extends util\Xml {
    private static $pathXml  = '/commerce/codStatus.xml';    
    
    /**
     * Localiza o código e a mensagem de status a partir de um código informado.
     * A consulta é feita em um arquivo XML contido, por padrão, na raíz da pasta commerce.
     * 
     * @param string $id Código de referência que serve para identificar um nó <status> a partir de seu atributo id.
     * @return \stdClass Objeto com duas propriedades: codigo e msg.
     */
    public static function getId($id){        
        $pathXml    = \Url::physicalPath(self::$pathXml);
        $objXml     = self::loadXml($pathXml); 
        if (is_object($objXml)) {            
            $nodes      = $objXml->status;            
            $nodeStatus = NULL;
            
            if (count($nodes)> 0) {           
                $nodeStatus  = self::valueForAttrib($nodes,'id',$id);  
            }
            
             if ($nodeStatus == NULL) {                              
                 $nodeStatus         = new \stdClass();
                 $nodeStatus->codigo = 101;
                 $nodeStatus->msg    = 'Erro desconhecido. Não é possível concluir a ação solicitada.';
             }
             return $nodeStatus;
        } else {                
            echo 'Impossível ler o arquivo '.$pathXml;                                            
            die();
        }        
    }
    
    function __get($id){
                  
    }       
}

?>
