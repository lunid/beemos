<?php
    
    require_once('sys/classes/comps/files/YUICompressor.php');
    require_once('sys/classes/comps/HtmlComponent.php');
    require_once('sys/classes/comps/ChartComponent.php');
    require_once('sys/classes/db/Meekrodb_2_0.php');
    require_once('sys/classes/db/Conn.php');
    require_once('sys/classes/mvc/Controller.php');
    require_once('sys/classes/mvc/Model.php');               
    require_once('sys/classes/_init/LoadConfig.php');           
    require_once('sys/classes/util/Url.php');  
    
    class Application {
        
        /**
         * Classe de inicialização da aplicação.
         * 
         * Faz o carregamento dos arquivos comuns aos módulos do sistema (require_once),
         * identifica o módulo e seu respectivo Controller->action() a partir da URL e 
         * carrega as classes solicitadas na aplicação a partir de seu namespace.
        */   
        
        private static $sessionModuleName       =  'GLB_MODULE';
        private static $sessionControllerName   =  'GLB_CONTROLLER';
        private static $sessionActionName       = 'GLB_ACTION';       
        private static $sessionAbsolutePathIncludes      = 'GLB_ROOT_INCLUDES';
        private static $arrModules              = array('app','admin');
        
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
            
            $arrPartsUrl    = self::processaUrl();             
            $module         = $arrPartsUrl['module'];
            $controller     = $arrPartsUrl['controller'];
            $action         = $arrPartsUrl['action'];
            $method         = 'action'.ucfirst($action);                                         
                                
            $configModule   = $module.'/config.xml';
            
            $objLoadConfig = new LoadConfig();            
            $objLoadConfig->loadConfigXml('config.xml');
            $objLoadConfig->loadConfigXml($configModule);
                       
            //Define o root da pasta de includes baseado na pastaBase/modulo/pastaViews/            
            $arrRoot                = array(LoadConfig::root(),$module,LoadConfig::folderViews());
            $pathRootFolder         = join('/',$arrRoot).'/';
            $absolutePathIncludes   = $_SERVER['DOCUMENT_ROOT'].$pathRootFolder;
            
            self::setAbsolutePathIncludes($absolutePathIncludes);
           
            
            //echo $objLoadConfig->listVars();
            //Url::siteUrlHttps(array('part1','part2','part3'));
            //die();
            //Faz o include do Controller atual
            $urlFileController = $module . '/controllers/'.ucfirst($controller).'Controller.php';
            if (!file_exists($urlFileController)) {
                $msgErr = 'Arquivo de inclusão '.$urlFileController.' não localizado';
                throw new \Exception( $msgErr );  
            }
                
            require_once($urlFileController);                    
            
            /*
             * Inicializa a conexão com o DB.
             * Necessário para evitar erro de conexão ao executar o Controller->action().
             */            
            Conn::init();

            //Carrega classes invocadas na aplicação, a partir de seu namespace.
            spl_autoload_register('self::loadClass');	                           
            
            $objController  = new $controller;
            $objController->$method();//Executa o Controller->method()            
        }
        
        private static function processaUrl(){
            //$arrModules = self::$arrModules;
            //$module     = (isset($arrModules))?$arrModules[0]:'app';//O primeiro módulo é sempre padrão.  
            $module     = (isset($_GET['MODULE']))?$_GET['MODULE']:'app'; 
            $params     = (isset($_GET['PG']))?$_GET['PG']:''; 
            
            $pathParts  = explode('/',$params);            
            $controller = '';            
            $action     = self::getPartUrl(@$pathParts[1],'index');            
            if (is_array($pathParts) && count($pathParts) > 0) { 
                //A URL pode conter partes que representam o módulo, controller e action
                
                $controller = self::getPartUrl($pathParts[0]);
                //$keyModule  = array_search($controller,$arrModules);
                /*                
                if ($keyModule !== FALSE) {
                    //O primeiro parâmetro refere-se a um módulo
                    $module = $pathParts[0];  
                    
                    //Após extrair o módulo redefine as partes da URL sem o índice zero.
                    array_shift($pathParts);  
                    $controller = self::getPartUrl(@$pathParts[0],'index');
                }  */                                                                                          
            }   
              
            //Guarda o module, controller e action em variáveis de sessão.
            //Necessário para criar as URLs de navegação do site.            
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
        
        /*
        private static function loadConfigOld(){
            //Carrega a configuração do sistema
            $msgErr     = '';
            $pathXml    = 'config.xml';
            $objXml     = self::loadXml($pathXml);   
            //print_r($objXml);
            if (is_object($objXml)) {
                $nodesHeader = $objXml->header;
                $numItens    = count($nodesHeader);
                if ($numItens > 0) {
                    $rootFolderSys  = self::getAttrib($nodesHeader,'rootFolderSys');
                    $rootFolderView = self::getAttrib($nodesHeader,'rootFolderView');
                    
                    $nodesInclude   = $objXml->header->include;                  
                    $css            = self::valueForAttrib($nodesInclude,'id','css');
                    $cssInc         = self::valueForAttrib($nodesInclude,'id','cssInc');
                    $js             = self::valueForAttrib($nodesInclude,'id','js');
                    $jsInc          = self::valueForAttrib($nodesInclude,'id','jsInc');
                    $plugins        = self::valueForAttrib($nodesInclude,'id','plugins');
                    
                    echo $css.'<br>';
                    echo $cssInc.'<br>';
                    echo $js.'<br>';
                    echo $jsInc.'<br>';
                    
                } else {
                    //Nenhuma mensagem foi localizada no XML informado.
                    $msgErr  = 'O arquivo '.$pathXml.' existe, porém o Xml Node com a mensagem '.$codMsg.' não foi localizado.';                    
                }
                die($msgErr);
                $numItens    = count($nodes);
                
                if ($numItens > 0){
                    $value  = self::valueForAttrib($nodes,'id',$codMsg);
                    $msg    = (strlen($value) > 0)?$value:'Erro desconhecido';
                } else {
                    //Nenhuma mensagem foi localizada no XML informado.
                    $msgErr  = 'O arquivo '.$pathXml.' existe, porém o Xml Node com a mensagem '.$codMsg.' não foi localizado.';
                }
            } else {
                $msgErr = 'Impossível ler o arquivo '.$pathXml;
                die($msgErr);
            }            
        }
         */
         
        
        /**
        * Localiza a classe solicitada de acordo com o seu namespace e faz o include do arquivo.
        * @param String $class (nome da classe requisitada).
        * return void
        */             
        private static function loadClass($class){   
            //Tratamento para utilização do Hybridauth.
            if($class == 'FacebookApiException') return false;            
            
            $urlInc = str_replace("\\", "/" , $class . '.php');                
            if (isset($class) && file_exists($urlInc)){          
                require_once($urlInc);  
            } else {           
                die(" Classe $class não encontrada");
            }                      
        }                
    }
?>
