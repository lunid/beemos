<?php
    namespace commerce\classes\helpers;
    use \sys\classes\error\ErrorHandler;   
    
    class ErrorHelper extends ErrorHandler {
        
        /**
         * Retorna os erros capturados em RequestController.
         * O arquivo xml que contém as mensagens de erro estão em dic/eRequest.xml.
         * 
         * @param string $codErr Exemplo 'ERR_HASH_NOT_EXISTS' Usado para localizar a mensagem de erro.
         * @param string[] $arrErrParams Array associativo contendo variáveis a substituir na mensagem do erro.
         * @return string
         * @throws Exception Caso o arquivo xml não seja localizado.
         */
        public static function eRequest($codErr, $arrErrParams = null){
            try {
                $nameXmlFile    = __FUNCTION__;
                $msgErr         = self::getErrorString($nameXmlFile,$codErr,$arrErrParams);
                return "[$codErr] $msgErr";
            } catch (\Exception $e) {  
                throw $e;
            }
        }
    }
?>
