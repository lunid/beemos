 <?php

    class Url {
        
        public static function siteUrlHttp($params){
            return self::siteUrl($params,'http');
        }
        
        
        public static function siteUrlHttps($params){
            return self::siteUrl($params,'https');
        }  
        
        public static function relativeUrl($uri){
           $path = $uri;
           if (strlen($uri) > 0) {
                $rootFolder  = \LoadConfig::rootFolder(); 
                $module      = \Application::getModule();
                $root        = '/'.$rootFolder.'/'.$module;
                $folderSys   = \LoadConfig::folderSys();
                echo $folderSys;
                die('s');
                $key         = strpos($uri,$folderSys);
                if ($key === FALSE) $path = $root.'/'.$uri;                   
           }
           return $path;
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
