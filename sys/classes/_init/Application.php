<?php
    
    require_once('sys/classes/comps/files/YUICompressor.php');
    require_once('sys/classes/comps/HtmlComponent.php');
    require_once('sys/classes/comps/ChartComponent.php');
    require_once('sys/classes/db/Meekrodb_2_0.php');
    require_once('sys/classes/db/Conn.php');
    require_once('sys/classes/mvc/Controller.php');
    require_once('sys/classes/mvc/Model.php');           
    require_once('sys/classes/util/Xml.php');           
    
    class Application extends \sys\classes\util\Xml {
        /**
         * Classe de inicialização da aplicação.
         * 
         * Faz o carregamento dos arquivos comuns aos módulos do sistema (require_once),
         * identifica o módulo e seu respectivo Controller->action() a partir da URL e 
         * carrega as classes solicitadas na aplicação a partir de seu namespace.
        */               
        public static function load(){
            
            $arrModules   = array('app','admin');//Root dos módulos disponíveis
            $module       = $arrModules[0];//O primeiro módulo é sempre padrão.
            
            $controllerClass = 'index';
            $action          = 'index';
            
            $params     = (isset($_GET['PG']))?$_GET['PG']:'';    
            $arrParams  = explode('/',$params);            
            
            if (is_array($arrParams) && count($arrParams) > 0) {       
                $controller    = (isset($arrParams[0]) && $arrParams[0] != null)?$arrParams[0]:'';
                $keyModule     = array_search($controller,$arrModules);
                if ($controller != null && $keyModule === FALSE) {                    
                    //O primeiro parâmetro da URL não é um módulo.
                    //Portanto, define o Controller e o método (action), caso tenha sido informado.
                    $controllerClass = $controller;
                    if (isset($arrParams[1]) && $arrParams[1] != null) $action = $arrParams[1];
                } else {
                    //Acesso a um módulo específico.
                    $module = $arrModules[$keyModule];
                }                                
            }                     
            
            //Define a constante __MODULE__
            self::defineModule($module);
            //echo __MODULE__;                        
            
            self::loadConfig();
            
            //Faz o include do Controller atual
            $urlFileController = __MODULE__ . '/controllers/'.ucfirst($controllerClass).'Controller.php';
            if (!file_exists($urlFileController)) die('Arquivo de inclusão '.$urlFileController.' não localizado');
            require_once($urlFileController);           
            
            /*
             * Inicializa a conexão com o DB.
             * Necessário para evitar erro de conexão ao executar o Controller->action().
             */            
            Conn::init();

            //Carrega classes invocadas na aplicação, a partir de seu namespace.
            spl_autoload_register('self::loadClass');	         
            
            /*
             * Identifica o método (action) a ser chamado no Controller atual.
             * O $actionMethod deve iniciar sempre com o prefixo 'action' seguido do parâmetro
             * $action com inicial maiúscula.
             * 
             * Exemplo:
             * Para $action='faleConosco' o método do controle a ser chamado será 
             * actionFaleConosco().                         
             */            
            $method         = 'action'.ucfirst($action);
            $objController  = new $controllerClass;
            $objController->$method();//Executa o Controller->action()            
        }
        
        private static function loadConfig(){
            //Carrega a configuração do sistema
            $pathXml    = 'config.xml';
            $objXml     = self::loadXml($pathXml);   
            print_r($objXml);
            if (is_object($objXml)) {
                $nodes = $objXml->config->header;
                print_r($nodes);
                die();
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
        
        /**
         * Define qual o módulo chamado pelo usuário.
         * @param type $module 
         */
        private static function defineModule($module){
            define("__MODULE__", $module);            
        }
    }
?>
