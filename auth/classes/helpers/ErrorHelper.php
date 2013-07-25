<?php
    namespace auth\classes\helpers;
    use \sys\classes\error\ErrorHandler;   
    
    class ErrorHelper extends ErrorHandler {
        
        public static function eAssinatura($codErr,$arrParams=NULL){
            $nameXmlFile    = '/auth/dic/'.__FUNCTION__;
            $msgErr         = self::getErrorString($nameXmlFile,$codErr,$arrParams);
            return $msgErr;
        }
    }
?>
