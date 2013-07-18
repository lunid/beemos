<?php

include('BmXmlInterface.php');
abstract class BmXml implements BmXmlInterface {
    
        protected $debug    = FALSE;
        protected $save     = FALSE;//TRUE = grava(persiste) o registro atual no servidor remoto.

        /**
         * Imprime na tela um array com as variáveis de envio ao chamar o método BmConn->send().
         * Muito útil para checar as variáveis de envio na fase de implementação.
         * 
         * Veja também o método printXml().
         * @see printXml()
         * 
         * @return void
         */
        function debugOn(){
            $this->debug = TRUE;
        }
        
        /**
         * Desabilita o debug.
         * 
         * @return void
         */
        function debugOff(){
            $this->debug = FALSE;
        }        
        

        /**
         * Salva os dados do sacado no servidor remoto.
         * Este recurso pode ser útil caso seja necessário efetuar uma nova cobrança 
         * para o mesmo sacado via painel de controle.
         * 
         * @return void     
         */
        public function persistOn(){
            $this->save= true;
        }  

        public function persistOff(){
            $this->save = FALSE;
        }          
        
        /**
         * Método auxiliar de getXml(), retira caracteres não permitidos antes de criar 
         * a tag PARAM com seu respectivo valor, retornando a tag <PARAM id=''>value</param>.
         * 
         * @param string $tag
         * @param mixed $value
         * @return string Tag que será usada para compor o XML de envio.
         */
        protected function getTagXml($tag,$value){
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
        
        /**
         * Armazena uma variável com seu respectivo valor em um array que será usado
         * posteriormente para gerar o XML de envio.
         * 
         * @param string $id Nome que será usado no atributo 'id' da tag XML 'PARAM'.
         * @param mixed $value Valor da tag.
         */
        protected function addParamXml($id,$value){
           if (strlen($id) > 0) {
               $name = strtoupper($id);
               $this->arrParamsXml[$name] = $value;
           }
        }   
        
        protected function getParamsXml(){
            return $this->arrParamsXml;
        }
         
        /**
         * Imprime a string XML a ser enviada para o servidor remoto.
         * A chamada interrompe a execução do script após a impressão do XML.
         *        
         * @return void
         * @throws Exception Caso alguma exceção seja levantada pelo método headerXml() no momento de gerar o XML
         */
        public function printXml(){
            try {
                $xml = $this->getXml();
                $this->headerXml($xml);
            } catch (\Exception $e) {
                throw $e;
            }
        }
        
        protected function getXmlFromObject($obj){
            if (is_object($obj)) { 
                if ($obj instanceof BmXml) {
                    return $obj->getXml();
                } else {
                    $msgErr = 'O objeto informado não é válido.';
                    throw new \Exception($msgErr);                    
                }
            }
        }
        
        public function getXml(){
            //Sobrescrever esse método nas classes filhas.
        }
        
        /**
         * Método de suporte ao método getXml();
         * Retorna as tags <PARAM id=''></PARAM> do módulo atual.
         * 
         * @return string String XML         
         */
        protected function getTagParams(){                               
            $xmlParams      = ''; 
            $arrParamsXml   = $this->getParamsXml();
            if (is_array($arrParamsXml)) {
                foreach($arrParamsXml as $param => $value) {
                    if (strlen($param) > 0) {
                        $xmlParams .= $this->getTagXml($param, $value);
                    }
                }
            } 
            return $xmlParams;
        }
}
?>
