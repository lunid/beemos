<?php
    namespace api\classes;
    
    use \sys\lib\soap\classes\Soap;
    use \api\classes\models\ServerModel;
    
    class Server extends Soap {
        private $class; //Classe que está utilizando o Server
        private $classIdgnoredMethods; //Métodos a serem ignorados no WSDL pela classe que utiliza Server
        
        public function __construct($class, $metodos = array()) {
            try{
                //Inicia o ServerSoap
                parent::__construct($class);
                
                //Aramazena classe
                $this->class = $class;
                
                //Armazena métodos a serem ignorados no WSDL
                $this->classIdgnoredMethods = $metodos;
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
                parent::wsdl("SuperProWeb");
                
                //Trata caminhos de inclusão
                $path               = preg_replace("/(api)(.*)/", "", __DIR__);
                $path_controller    = $path . "api/classes/controllers/" . ucfirst($this->class) . "Controller.php";
                $path_server        = $path . "api/classes/Server.php";
                
                //Inclui Arquivos no WSDL
                $this->addFile($path_server, "Server");
                $this->addFile($path_controller, $this->class);
                
                //Ignora métodos da classe Server, mantendo apenas os globais
                $this->addIgnore("Server", "__construct");
                $this->addIgnore("Server", "xmlException");
                $this->addIgnore("Server", "actionWsdl");
                $this->addIgnore("Server", "getXmlField");
                
                //Ignora métodos da classe
                foreach($this->classIdgnoredMethods as $metodo){
                    $this->addIgnore($this->class, $metodo);
                }
                
                //Exibe WSDL
                $this->showWsdl();
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
