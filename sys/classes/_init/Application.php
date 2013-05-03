<?php
    
    require_once('sys/classes/db/Meekrodb_2_1.php');
    require_once('sys/classes/db/IConnInfo.php');
    require_once('sys/classes/db/ConnInfo.php');
    require_once('sys/classes/db/ConnConfig.php');
    require_once('sys/classes/db/Conn.php');
    require_once('sys/classes/db/ORM.php');
    require_once('sys/classes/db/Table.php');
    require_once('sys/classes/mvc/Controller.php');
    require_once('sys/classes/mvc/Model.php'); 
    require_once('sys/classes/mvc/Module.php');      
    require_once('sys/classes/_init/LoadConfig.php');
    require_once('sys/classes/util/Url.php');  
    require_once('sys/classes/util/DI.php');
    require_once('sys/classes/util/Dic.php');
    require_once('sys/classes/security/Token.php');
    require_once('sys/classes/security/Auth.php');      
    
    use sys\classes\util\DI;
    use sys\classes\mvc as MVC;
    use sys\classes\util as UTIL;
    
    class Application {
        
        /**
         * Classe de inicialização da aplicação.
         * 
         * Faz o carregamento dos arquivos comuns aos módulos do sistema (require_once),
         * identifica o módulo e seu respectivo Controller->action() a partir da URL e 
         * carrega as classes solicitadas na aplicação a partir de seu namespace.
        */           
        private static $sessionLangName                 = 'GLB_LANG';
        private static $sessionModuleName               = 'GLB_MODULE';
        private static $sessionControllerName           = 'GLB_CONTROLLER';
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
                $msgErr = 'Arquivo de inclusão '.$urlFileController.' não localizado, ou então o módulo solicitado não foi informado no item \'modules\' do arquivo global config.xml';
                throw new \Exception( $msgErr );  
            }
                
            require_once($urlFileController);                    
                        
            $objController  = new $controller;
            if (!method_exists($objController,$method)) die('Método '.$controller.'Controller->'.$method.'() não existe.');
            if (method_exists($objController, 'before')) {
                try {
                    $objController->before();//Executa o método before(), caso esteja implementado.
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
                 
            try {
                $objController->$method();//Executa o Controller->method()            
            } catch(\Exception $e) {          
                $objController->actionError($e);
            }
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
                
                $arrModulesDefault  = array('panel');//Módulos que não precisam constar no config.xml.
                $arrModules         = explode(',',$modules);
                $arrModules         = array_merge($arrModules,$arrModulesDefault);
                
                $controllerPart = $pathParts[0];
                
                $controllerPart = self::mapMagicModule($controllerPart);
                
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
        
        /**
         * Faz o mapeamento do controller informado para um nome diferente, 
         * caso o nome do controller informado esteja na lista magicModules de config.xml.
         * 
         * @param string $controller
         * @return string Nome do $controller altedo com o prefixo.
         */
        private static function mapMagicModule($controller=''){
            if (strlen($controller) > 0) {
                $magicModules   = LoadConfig::magicModules();
                if (strlen($magicModules) > 0) {
                    //Há configurações de módulos mágicos para o projeto atual.
                    $arrMagicModules = explode(';',$magicModules);
                    
                    if (is_array($arrMagicModules)) {
                        //Há uma ou mais listas de mósulos mapeados.
                        foreach($arrMagicModules as $groupMagicModules) {                        
                            /* Formato de $groupMagicModules:
                             * prefixo:modulo1,modulo2,modulo3...
                             * 
                             * EXEMPLO:
                             * app_:aluno,professor,escola;dev_:ambiente1,ambiente2
                             * 
                             * Neste caso, há dois mapeamentos separados por ponto-e-vírgula.
                             * Caso a URL servidor.com.br/aluno/ seja informada, o sistema deverá buscar o controller app_aluno.
                             * 
                             */                            
                            @list($prefixo,$listModules) = explode(':',$groupMagicModules);                         
                            $key = strpos($listModules,$controller);
                            if ($key !== false) {
                                //O controller atual refere-se a um módulo mapeado.
                                $controller = $prefixo.$controller;
                                break;
                            }                        
                        }
                    }
                }
            }
            return $controller;
        }
        
        /**
         * Define o nome da pasta, a partir da raíz do ambiente web, onde se localiza a aplicação.
         * 
         * @param string $folder 
         * @return void
         */
        public static function folder($folder) {
            //Define o path do diretório da aplicação:
            if (!defined('APPLICATION_PATH')) {
                $root = $folder;
                if (strlen($folder) > 0 && $folder != '/') $root = "/{$folder}/";
                define ('APPLICATION_PATH', $root);
            }            
        }
        
        /**
         * Faz a correção de uma URL para refletir o endereço correto a partir 
         * da pasta ROOT do projeto.
         * 
         * Considera como root padrão a pasta public_html.
         * 
         * @param $url
         * @return string Url com o contéudo de APPLICATION_PATH inserido após public_html/
         */
        public static function setRootProject($url){
            $uri = str_replace('public_html/','public_html'.APPLICATION_PATH,$url);
            return $uri;
        }
        
        /**
         * Habilita o ambiente de desenvolvimento.
         * 
         * @return void
         * @see setEnv() 
         */
        public static function dev(){
            self::setEnv('dev');
        }        
        
        /**
         * Habilita o ambiente de testes.
         * 
         * @return void
         * @see setEnv() 
         */
        public static function test(){
            self::setEnv('test');
        }
        
        /**
         * Habilita o ambiente de produção.
         * 
         * @return void
         * @see setEnv() 
         */
        public static function prod(){
            self::setEnv('prod');
        }        
        
        /**
         * Define o ambiente atual. Valores possíveis:
         * - test   = define o ambiente atual como ambiente de teste.
         * - dev    = define o ambiente atual como ambiente de desenvolvimento.
         * - prod   = define o ambiente atual como ambiente de produção
         * 
         * @param string $env Pode ser test | dev | prod
         */
        private static function setEnv($env){
            //Define o ambiente da aplicação caso a constante ainda não tenha sido definida
            if (defined('APPLICATION_ENV')) {
                echo "A constante APPLICATION_ENV não pode ser definida duas vezes.";
            } else {
                define ('APPLICATION_ENV',$env);
            }
        }
        
        public static function getEnv(){
            return APPLICATION_ENV;
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

            try {                
                $cfgFolderViews     = LoadConfig::folderViews();               
                $cfgFolderTemplate  = LoadConfig::folderTemplate();
                $pathTplFolder      = $module.'/'.$cfgFolderViews.'/'.self::getLanguage().'/'.$cfgFolderTemplate.'/'; 
                self::vldTemplate($pathTplFolder);
            } catch(\Exception $e) {
                die('LoadConfig->loadVars(): '.$e->getMessage());
            }            
        }
        
        
        /**
         * Valida o template padrão da aplicação e cria um arquivo novo 
         * na pasta de templates caso ainda não exista.
         * 
         * @param string $pathTplFolder Path da pasta de templates
         * @return void
         */
        private static function vldTemplate($pathTplFolder){
            $cfgDefaultTemplate = LoadConfig::defaultTemplate();
            $pathFileTplDefault = $pathTplFolder.$cfgDefaultTemplate;            
            if (!file_exists($pathFileTplDefault)) {
                //Arquivo template não existe                
                if (!is_dir($pathTplFolder)) {
                    //Diretório de templates ainda não existe. Tenta criá-lo.
                    if (!mkdir($pathTplFolder, 0, true)) {
                        $msgErr = 'A tentativa de criar a pasta de templates em '.$pathTplFolder.' falhou.';
                        throw new \Exception( $msgErr );                           
                    }                  
                }   

                $date               = date('d/m/Y H:i:s'); 
                $pathFileTplDefault = str_replace('//','/',$pathFileTplDefault);
                $open               = fopen($pathFileTplDefault, "a+");

                //Conteúdo do novo arquivo template:
                $fileContent = "<!-- Arquivo criado dinâmicamente em LoadConfig.php, em {$date} -->".chr(13)."<div>{BODY}</div>";
                    
                if (fwrite($open, $fileContent) === false) {
                    $msgErr = "Um template padrão não foi definido no arquivo config.xml e a tentativa de 
                    gerar um novo arquivo ({$pathFileTplDefault}) falhou. Verifique a tag 
                    <fileName id='default'>nomeDoArquivoTemplate.html</fileName>";
                    $msgErr = htmlentities($msgErr);                     
                    throw new \Exception( $msgErr );                                                                  
                }
                fclose($open);                  
            }            
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
            if($class == 'FacebookApiException' || $class == 'mysqli') return false; 
           
            
            $urlInc = str_replace("\\", "/" , $class . '.php');                           
            
            if (isset($class) && file_exists($urlInc)){          
                require_once($urlInc);            
            } else {                          
               die(" Classe $class não encontrada");
            }                      
        }                
    }
?>
