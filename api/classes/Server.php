<?php
    namespace api\classes;
    
    use \sys\classes\util\Component;
    use \api\classes\models\ServerModel;
    
    class Server {
        private $class; //Classe que está utilizando o Server
        private $classIdgnoredMethods; //Métodos a serem ignorados no WSDL pela classe que utiliza Server
        private $ws;
        
        public function __construct($class, $ignoredMetodos = array()) {
            try{
                //Parâmetros para inicio do Serviço
                $arrParams = array(
                    "class"             => $class,
                    "ignoredMetodos"    => $ignoredMetodos
                );
                
                //Inicia o ServerSoap
                $this->ws = Component::webservice($arrParams);
                
                //Aramazena classe
                $this->class = $class;
                
                //Armazena métodos a serem ignorados no WSDL
                $this->classIdgnoredMethods = $ignoredMetodos;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Inicia o servico SOAP
         * 
         * @throws Exception
         */
        public function actionIndex(){
            try{
                //Inicia o SoapServer
                $this->ws->index();
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Valida o usuário que está acessando o serviço
         * 
         * @param mixed $dados
         * @return boolean
         */
        public function authenticate($token){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->erro      = 1;
                $ret->msg       = "Erro inesperado!";
                
                //Captura o Usuário e Senha enviados via HTTP - Basic (Base64)
                if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
                    $usuario    = $_SERVER['PHP_AUTH_USER'];
                    $senha      = $_SERVER['PHP_AUTH_PW'];

                    //Model de Soap
                    $mdSoap = new ServerModel();
                    $rs     = $mdSoap->validarUsuario($usuario, $senha);

                    //Se houver falha na autenticação o usuário é retornado
                    if(!$rs->status){
                        return $rs;
                    }

                    //Verificação do TOKEN enviado.
                    if($token == null){
                        //Caso o $token seja NULL, um novo token é gerado
                        //Adquire um novo token para ser utilizado
                        return Security::gerarToken($rs->user['ID_CLIENTE'], $usuario);
                    }else{
                        //Caso o $token seja enviado o mesmo será validado      
                        return Security::validarToken($rs->user['ID_CLIENTE'], $token);
                    }
                }else{
                    $ret->erro  = 2;
                    $ret->msg   = "Falha na autenticacao HTTP - Usuário inválido ou Não enviado!";
                    return $ret;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que gera um novo token ao cliente
         * 
         * @return string
         */
        public function getToken(){
            try{
                //Gera um novo TOKEN
                $rs = $this->authenticate(null);

                //Retorno da geração do Token
                $erro   = $rs->erro;
                $msg    = $rs->msg;

                if($rs->status){
                    $dados  = "<dados>";
                    $dados .= "<token>".$rs->token."</token>";
                    $dados .= "</dados>";
                }
                
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>{$msg}</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
        * Função que varre o array XML de parâmetros e retorna o valor do campo solicitado
        * 
        * @param array $xmlParams
        * @param string $fielName
        * 
        * @return string $value
        * @throws Exception
        */
        protected function getXmlField($xmlParams, $fielName){
            try{
                //Varre o array de campos para capturar o valor solicitado.
                if(is_object($xmlParams) || is_array($xmlParams)){
                    foreach($xmlParams as $param){
                        $id = (string)$param['id'];

                        if($id == $fielName){
                            $tmp = mysql_escape_string(trim((string)$param));

                            //Se depois das tratativas se o valor for vazio retorna NULL
                            if($tmp == '' || $tmp == null){
                                return null;
                            }else{
                                return mysql_escape_string(trim((string)$param));
                            }
                        }
                    }
                }

                return null;
            }catch(Exception $ex){
                throw $ex;
            }
        }
        
        /**
         * Gera o WSDL do Serviço
         * 
         * @throws Exception
         */
        public function actionWsdl(){
            try{
                //Inicia WSDL
                $this->ws->wsdl("SuperProWeb");
                
                //Trata caminhos de inclusão
                $path               = preg_replace("/(api)(.*)/", "", __DIR__);
                $path_controller    = $path . "api/classes/controllers/" . ucfirst($this->class) . "Controller.php";
                $path_server        = $path . "api/classes/Server.php";
                
                //Inclui Arquivos no WSDL
                $this->ws->addFile($path_server, "Server");
                $this->ws->addFile($path_controller, $this->class);
                
                //Ignora métodos da classe Server, mantendo apenas os globais
                $this->ws->addIgnore("Server", "__construct");
                $this->ws->addIgnore("Server", "actionIndex");
                $this->ws->addIgnore("Server", "xmlException");
                $this->ws->addIgnore("Server", "actionWsdl");
                $this->ws->addIgnore("Server", "getXmlField");
                
                //Ignora métodos da classe
                foreach($this->classIdgnoredMethods as $metodo){
                    $this->ws->addIgnore($this->class, $metodo);
                }
                
                //Exibe WSDL
                $this->ws->showWsdl();
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
