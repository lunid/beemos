<?php


class BmXml {
    

        /**
         * Método auxiliar de getXml(), retira caracteres não permitidos antes de criar 
         * a tag PARAM com seu respectivo valor.
         * 
         * @param string $tag
         * @param mixed $value
         * @return string Tag que será usada para compor o XML de envio.
         */
        protected function setTagXml($tag,$value){
            $value  = str_replace('"', '', $value);
            $value  = str_replace('<', '', $value);
            $value  = str_replace('>', '', $value);
            $tagXml = "<PARAM id='{$tag}'>{$value}</PARAM>";
            return $tagXml;
        }    
        
        
        /**
         * Método que imprime o XML de envio para o servidor remoto.
         * Use-o para checar o XML a ser enviado.
         * 
         * @return void
         */
        protected function headerXml($xml){
            header("Content-type: text/xml; charset=ISO-8859-1");
            echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
            echo $xml;
            die();
        }        
                
}

?>
