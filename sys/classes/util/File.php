<?php

    namespace sys\classes\util;
    
    class File {
        
        /**
         * Verifica se o arquivo informado existe.
         * 
         * @param string $urlFile Path relativo do arquivo (não deve ser usado caminho absoluto com http://...)
         * @param boolean $exception Se o arquivo não existir: exception = TRUE dispara uma exceção, caso contrário retorna FALSE;
         * @return boolean
         * @throws \Exception Caso o arquivo não exista e o parâmetro $exception = TRUE.
         */
        public static function exists($urlFile,$exception=TRUE){
            if (!file_exists($urlFile)) {
                if ($exception){
                    $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'FILE_NOT_EXISTS'); 
                    $msgErr = str_replace('{FILE}',$urlFile,$msgErr);
                    throw new \Exception( $msgErr );  
                }
                return FALSE;
            }
            return TRUE;
        }        
    }
?>
