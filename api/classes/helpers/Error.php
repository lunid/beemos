<?php
    namespace auth\classes\helpers;
    use \sys\classes\error\ErrorHandler;   
    
    class Error extends ErrorHandler {
        
        public static function eCreditos($codErr){
            $nameXmlFile    = __FUNCTION__;
            $msgErr         = self::getErrorString($nameXmlFile,$codErr);
            return $msgErr;
        }
    }
?>
