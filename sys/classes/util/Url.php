 <?php

    class Url {
             
        public static function physicalPath($uri){
            $path = $uri;
            if (strlen($uri) > 0) {
                $absolutePathIncludes   = \Application::getAbsolutePathIncludes();        
                $path                   = $absolutePathIncludes.$uri; 
                $path                   = str_replace('//','/',$path);    
            }
            return $path;
        }
        
        public static function absolutePath($uri){
           $path    = $uri;
           $root    = $_SERVER['DOCUMENT_ROOT'];
           $path    = str_replace($root,'/',$uri);           
           return $path;            
        }

        public static function relativeUrl($uri){
            $path = $uri;                             
           if (strlen($uri) > 0) {     
                $folderSys  = \LoadConfig::folderSys();          
                $keySys     = strpos($uri,$folderSys);
                if ($keySys === FALSE) {
                    $rootFolder  = \LoadConfig::rootFolder(); 
                    $module      = \Application::getModule();   
                    $root        = $rootFolder.'/'.$module.'/';
                    $keyRoot     = strpos($uri,$root);
                    $path        = $_SERVER['DOCUMENT_ROOT'];
                    if ($keyRoot !== false) {
                        $path .= $uri;                                    
                    } else {
                        $path .= $root.$uri;
                    }
                }
           }
           $path = str_replace('//','/',$path);
           return $path;
        }
        
        public static function siteUrlHttp($params){
            return self::siteUrl($params,'http');
        }
        
        
        public static function siteUrlHttps($params){
            return self::siteUrl($params,'https');
        }          
        
        /**
         * Método de suporte a siteUrlHttp e siteUrlHttps.
         * 
         * Retorna a URL do site conforme os parâmetros baseUrlHttp, baseUrlHttps e rootFolder do arquivo config.xml.
         * 
         * O index.php será incluído na URL. Por exemplo:
         * <code>
         *  echo Url::siteUrlHttp('catalogo/produto/abcd');
         *</code>
         * Imprimirá: http://www.seudominio.com.br/rootFolder/index.php/catalogo/produto/abcd
         * 
         */
        private static function siteUrl($params,$protocol='http'){
            if (is_array($params)) $params = join('/',$params);
            $baseUrl      = ($protocol == 'http')?\LoadConfig::baseUrlHttp():\LoadConfig::baseUrlHttps();
            $baseUrl      = str_replace($protocol.'://','',$baseUrl);
            $rootFolder   = \LoadConfig::rootFolder();
            $uri          = $protocol.'//'.$baseUrl.'/'.$rootFolder.'/'.$params;
            
            return $uri;
        }
    }
?>