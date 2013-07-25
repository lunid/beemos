<?php
    namespace commerce\classes\helpers;
    use \sys\classes\error\ErrorHandler;   
    
    class Error extends ErrorHandler {
        public static function eRequest($codErr, $arrReplaceVars = null){
            try {
                $nameXmlFile    = __FUNCTION__;
                $msgErr         = self::getErrorString($nameXmlFile,$codErr,$arrReplaceVars);
                return $msgErr;
            } catch (\Exception $e) {  
                throw $e;
            }
        }
    }
?>
