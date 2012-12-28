<?php 
    
    use sys\classes\util\Dic;
    
    class Url {
        /**
         * Recebe um array associativo que será convertido em URL no formato
         * rootFolder/modulo/controller/action/...onde rootFolder é lido do arquivo config.xml.
         *          
         * @param array $arrUrl Array associativo. 
         * Exemplo: 
         * O array('module'=>'admin','controller'=>'escolas','action'=>'home','id'=>11) retornará
         * /rootFolder/admin/escola/home/id/11
         * 
         * @return string 
         */                
        public static function setUrl(array $arrOptions,$langPath=TRUE){   
            $url            = '/';
            $rootFolder     = \LoadConfig::rootFolder();
            $lang           = \Application::getLanguage();            
            
            if (strlen($rootFolder) > 0) $url .= $rootFolder.'/';
            if (strlen($lang) > 0 && $langPath) $url .= $lang.'/';
            
            foreach($arrOptions as $key=>$value) {
                $value = trim($value);
                if (($key == 'module' || $key == 'controller' || $key == 'action')) {                   
                    if (strlen($value) > 0) $url .= $value.'/';
                } else {
                    $url .= $key.'/'.$value;                
                }
            }
            return $url;
        }
        
        public static function mvcForModel($controller,$action){
            $module = \Application::getModule();
            return self::mvc($module,$controller,$action);                       
        }
        
        public static function mvc($module='',$controller='',$action=''){
            $arrOptions = array('module'=>$module,'controller'=>$controller,'action'=>$action);
            $langPath   = FALSE;
            return self::setUrl($arrOptions,$langPath);
        }
        
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
               $physicalPath    = str_replace('//','/',$physicalPath);
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
        
        public static function getUrlLibInitComponent($folderComponent){
            $folderSys      = \LoadConfig::folderSys();
            $folderLib      = \LoadConfig::folderLib();
            $folderComps    = \LoadConfig::folderComps(); 
            
            if (strlen($folderLib) == 0 || strlen($folderComps) == 0) {
                //Arquivo não existe
                $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'COMPS_UNDEFINED');
                throw new \Exception( $msgErr );              
            }
            $class          = ucfirst($folderComponent);
            $classPath      = "{$folderSys}/{$folderLib}/{$folderComps}/{$folderComponent}/classes/Lib{$class}.php";
            return $classPath;
        }
        
        public static function getUrlEmailTpl(){
            $folderSys      = \LoadConfig::folderSys();
            $folderLib      = \LoadConfig::folderLib();
            $folderComps    = \LoadConfig::folderComps();       
            $classPath      = "{$folderSys}/common/{$folderComps}/{$folderComponent}/classes/Lib{$class}.php";
        }
    }
?>
