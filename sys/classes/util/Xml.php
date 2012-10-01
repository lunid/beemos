<?php

    namespace sys\classes\util;
    
    abstract class Xml {
        protected $objXml = NULL;
        
        public static function loadXml($pathXml=''){
            if (isset($pathXml)){
                if (!file_exists($pathXml)) {      
                    die('LoadXml(): Arquivo '.$pathXml.' não localizado.');
                } else {                         
                    return simplexml_load_file($pathXml);                                        
                }
            }
        }
        
        
        public static function valueForAttrib($nodes,$atribName,$atribValue){        
            foreach($nodes as $node){     
                foreach($node->attributes() as $name => $value){                    
                    if ($name == $atribName && $value == $atribValue) return $node;                    
                }                
            }
        }
        
        public static function getNode($arrNodes,$node){            
            if ((is_object($arrNodes) || is_array($arrNodes)) && strlen($node) > 0){
                //Arquivo xml carregado com sucesso.  
                //foreach($arrNodes->$node as $node){
                   // print_r($node);
                //}
                $nodes = $arrNodes;                
                if (count($arrNodes->$node) == 1) $nodes = $arrNodes->$node; 
                //echo $nodes;
                return $nodes;
            } else {
                die("Não foi possível carregar um objeto XML para ".print_r($arrNodes));
            }            
        }
    }
?>
