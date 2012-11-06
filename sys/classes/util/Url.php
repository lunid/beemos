 <?php

    class Url {
        
        /**
         * Retorna o caminho físico da URI informada.
         * 
         * Exemplo: c:/serverFolder/projectFolder/...
         * 
         * @param string $uri Exemplo: app/phtml/table.phtml
         * @return string
         */
        public static function physicalPath($uri){
            $path = $uri;
            if (strlen($uri) > 0) {
                $root           = $_SERVER['DOCUMENT_ROOT'];
                $rootFolder     = \LoadConfig::rootFolder();
                $path           = $root.'/'.$rootFolder.'/'.$uri;
                $path           = str_replace('//','/',$path);
            }
            return $path;
        }
        
       
        /**
         * Define o caminho absoluto do path informado retirando o caminho físico, se houver.
         * 
         * Por exemplo, caso o caminho ($path) informado seja c:/projetos/folder/...
         * path de retorno será /folder/...
         * 
         * @param string $path
         * @return string
         */
        public static function absolutePath($path){           
           $root    = $_SERVER['DOCUMENT_ROOT'];
           $path    = str_replace($root,'/',$path);           
           return $path;            
        }

        public static function relativeUrl($uri){
            $path = $uri;                             
           if (strlen($uri) > 0) {                    
               $root            = $_SERVER['DOCUMENT_ROOT'];
               $rootFolder      = \LoadConfig::rootFolder();               
               $physicalPath    = $root.'/'.$rootFolder.'/';
               $path            = str_replace($physicalPath,'',$uri);               
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
