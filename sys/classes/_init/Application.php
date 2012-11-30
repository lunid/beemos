<?php
    
    require_once('sys/classes/comps/files/YUICompressor.php');
    require_once('sys/classes/comps/HtmlComponent.php');
    require_once('sys/classes/comps/ChartComponent.php');
    require_once('sys/classes/db/Meekrodb_2_1.php');
    require_once('sys/classes/db/Conn.php');
    require_once('sys/classes/db/ORM.php');
    require_once('sys/classes/db/Table.php');
    require_once('sys/classes/mvc/Controller.php');
    require_once('sys/classes/mvc/Model.php'); 
    require_once('sys/classes/mvc/Module.php');      
    require_once('sys/classes/_init/LoadConfig.php');
    require_once('sys/classes/util/Url.php');  
    require_once('sys/classes/util/DI.php');
    
    use sys\classes\util\DI;
    
    class Application {
        
        /**
         * Classe de inicialização da aplicação.
         * 
         * Faz o carregamento dos arquivos comuns aos módulos do sistema (require_once),
         * identifica o módulo e seu respectivo Controller->action() a partir da URL e 
         * carrega as classes solicitadas na aplicação a partir de seu namespace.
        */           
        private static $sessionLangName                 =  'GLB_LANG';
        private static $sessionModuleName               =  'GLB_MODULE';
        private static $sessionControllerName           =  'GLB_CONTROLLER';
        private static $sessionActionName               = 'GLB_ACTION';       
        private static $sessionAbsolutePathIncludes     = 'GLB_ROOT_INCLUDES';
        private static $arrModules                      = array('app','admin');
        
      /**
       * Identifica o módulo, controller e action a partir da URL e faz a chamada
       * do método, como segue:
       * 
       * $objController  = new $controller;
       * $objController->method()
       * 
       * O $method deve iniciar sempre com o prefixo 'action' seguido do parâmetro
       * $action com inicial maiúscula.
       * 
       * Exemplo:
       * Para $action='faleConosco' a variável $method será 
       * actionFaleConosco().                         
       *  
       */          
        public static function setup(){
            
            //Faz a leitura dos parâmetros em config.xml na raíz do site
            $objLoadConfig  = new LoadConfig();            
            $objLoadConfig->loadConfigXml('config.xml');
            
            
            $arrPartsUrl    = self::processaUrl(); 
            $module         = $arrPartsUrl['module'];
            $controller     = $arrPartsUrl['controller'];
            $action         = $arrPartsUrl['action'];            
            $method         = 'action'.ucfirst($action);                                                                                    
            
            //Faz a leitura dos parâmetros em config.xml do módulo atual
            $configModule   = $module.'/config.xml';
            $objLoadConfig->loadConfigXml($configModule);                                             
                        
            /*
             * Inicializa a conexão com o DB.
             * Necessário para evitar erro de conexão ao executar o Controller->action().
             */            
            Conn::init();
           
            //Carrega, a partir do namespace, classes invocadas na aplicação.
            spl_autoload_register('self::loadClass');	                           
            
            //Faz o include do Controller atual
            $urlFileController = $module . '/classes/controllers/'.ucfirst($controller).'Controller.php';
            if (!file_exists($urlFileController)) {
                $msgErr = 'Arquivo de inclusão '.$urlFileController.' não localizado';
                throw new \Exception( $msgErr );  
            }
                
            require_once($urlFileController);                    
                        
            $objController  = new $controller;
            if (!method_exists($objController,$method)) die('Método '.$controller.'Controller->'.$method.'() não existe.');
            $objController->$method();//Executa o Controller->method()            
        }
        
        private static function processaUrl(){
            $arrPartsUrl    = array();
            $module         = LoadConfig::defaultModule(); 
            $params         = (isset($_GET['PG']))?$_GET['PG']:''; 
                                    
            $pathParts      = explode('/',$params);            
            $controller     = 'index';
            $language       = LoadConfig::defaultLang();            
            $action         = self::getPartUrl(@$pathParts[1]);            
            
            if (is_array($pathParts) && count($pathParts) > 0) { 
                //A URL pode conter partes que representam o módulo, controller e action
                $lang           = LoadConfig::langs();//Idiomas aceitos pelo sistema
                $modules        = LoadConfig::modules();
                $arrLangs       = explode(',',$lang); 
                $arrModules     = explode(',',$modules);
                $controllerPart = $pathParts[0];
                
                //Verifica se a primeira parte da URL é um idioma
                $keyLang        = FALSE; 
                if (strlen($arrLangs[0]) > 0) $keyLang = array_search($controllerPart,$arrLangs);

                if ($keyLang !== FALSE) {
                    //O primeiro parâmetro refere-se a um idioma específico
                    $language   = $controllerPart;
                    array_shift($pathParts); 
                    $controllerPart = (isset($pathParts[0]))?$pathParts[0]:'';
                }
                
                $keyModule      = array_search($controllerPart,$arrModules);
                if ($keyModule !== FALSE) {
                    //O primeiro parâmetro é um módulo
                    $module     = $controllerPart;
                    array_shift($pathParts);
                    $controller = self::getPartUrl(@$pathParts[0]);            
                    $action     = self::getPartUrl(@$pathParts[1]);            
                } else {                    
                    $controller = self::getPartUrl($controllerPart);                
                }
            }   
            
            //Guarda o idioma(language), module, controller e action em variáveis de sessão.
            //Necessário para criar as URLs de navegação do site.            
            self::setLanguage($language);  
            self::setModule($module);       
            self::setController($controller);
            self::setAction($action);  
            
            $arrPartsUrl['module']       = $module;
            $arrPartsUrl['controller']   = $controller;
            $arrPartsUrl['action']       = $action;
            return $arrPartsUrl;
        }
        
        private static function getPartUrl($pathPart,$default='index'){
           $value = (isset($pathPart) && $pathPart != null)?$pathPart:$default; 
           return $value;
        }
        
        private static function setAbsolutePathIncludes($rootIncludes){
            $_SESSION[self::$sessionAbsolutePathIncludes] = $rootIncludes;
        }
        
        static function getAbsolutePathIncludes(){
            return (isset($_SESSION[self::$sessionAbsolutePathIncludes]))?$_SESSION[self::$sessionAbsolutePathIncludes]:'';
        }
        
        private static function setLanguage($language){
            $_SESSION[self::$sessionLangName] = trim($language);
        }
        
        static function getLanguage(){
            return self::getVarApplication(self::$sessionLangName);                  
        }
        
        private static function setModule($module){
            $_SESSION[self::$sessionModuleName] = $module;
        }
        
        public static function getModule(){
            return self::getVarApplication(self::$sessionModuleName);      
        }        
        
        private static function setController($controller){
            $_SESSION[self::$sessionControllerName] = $controller;
        }
        
        public static function getController(){
            return self::getVarApplication(self::$sessionControllerName);           
        }        
        
        private static function setAction($action){
            $_SESSION[self::$sessionActionName] = $action;
        }
        
        public static function getAction(){
            return self::getVarApplication(self::$sessionActionName);
        }
        
        private static function getVarApplication($name){            
            $value = (isset($_SESSION[$name]))?$_SESSION[$name]:'';
            return $value;
        }
         
        
        /**
        * Localiza a classe solicitada de acordo com o seu namespace e faz o include do arquivo.
        * @param String $class (nome da classe requisitada).
        * return void
        */             
        public static function loadClass($class){   
            //Tratamento para utilização do Hybridauth.
            if($class == 'FacebookApiException') return false; 

            $urlInc = str_replace("\\", "/" , $class . '.php');                           
            
            if (isset($class) && file_exists($urlInc)){          
                require_once($urlInc);  
                //$obj = DI::loadMapXml($class);
                //die();
            } else {                          
               die(" Classe $class não encontrada");
            }                      
        }                
    }
?>
