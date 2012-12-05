<?php
    namespace sys\lib\soap\classes;
    
    class Soap {
        private $server; //Aramazena SoapServer
        private $uri; //Aramazena URI do serviço
        private $class; //Classe onde esta baseado o serviço
        private $arrMetodos; //Array de métodos para serem ignorados no WSDL
        
        /**
         * Constrói um SoapServer baseado em uma classe Controller em API
         * @param string $class Nome do Controller
         * @throws Exception
         */
        public function __construct($class = null){
            try{
                //Verifica o envio do nome de serviço
                if(is_null($class)){
                    throw new \Exception("Nome do serviço WS não definido no construtor de SOAP");
                }
                
                //Trata o vamos de class
                $this->class = trim(strtolower((string)$class));
                
                //Inicia valor de URI
                $this->uri = "http://localhost/interbits/api/{$this->class}/";
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Inicia o serviço SoapServer - WS de Usuários
         * 
         * @throws SoapFault
         */
        public function actionIndex(){
            try{
                //Inicia serviço SoapServer
                $this->server = new \SoapServer(null, 
                    array(
                        'uri'       => $this->uri,
                        'encoding'  => 'utf-8'
                    )
                );
                
                //Cadastra Classe com métodos a serem utilizados
                $this->server->setClass(ucfirst($this->class));  
                $this->server->handle();
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Valida o usuário que está acessando o serviço
         * 
         * @param mixed $dados
         * 
         * @return boolean
         * 
         * @throws Exception
         */
        protected function authenticate($token){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->erro      = 1;
                $ret->msg       = "Erro inesperado!";

                //Captura o Usuário e Senha enviados via HTTP - Basic (Base64)
                if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
                    $user = $_SERVER['PHP_AUTH_USER'];
                    $pass = $_SERVER['PHP_AUTH_PW'];

                    //Consulta usuário e senha na Base de dados
                    $sql = "SELECT
                                WS.ID_WS_USUARIO,
                                WS.ID_CLIENTE,
                                C.BLOQ
                            FROM
                                SPRO_WS_USUARIO WS
                            INNER JOIN
                                SPRO_CLIENTE C ON C.ID_CLIENTE = WS.ID_CLIENTE
                            WHERE
                                WS.LOGIN = '" . mysql_escape_string($user) . "'
                            AND
                                WS.SENHA = '" . $pass . "'
                            LIMIT
                                1
                            ";

                    $mysql  = new Mysql();
                    $rs     = $mysql->query($sql);

                    if(mysql_num_rows($rs) <= 0){
                        $ret->erro  = 2;
                        $ret->msg   = "Falha na autenticacao HTTP - Usuário inválido!";
                        return $ret;
                    }

                    //Transforma o ResultSet em objeto
                    $rs_user = mysql_fetch_object($rs);

                    if($rs_user->BLOQ == 1){
                        $ret->erro  = 3;
                        $ret->msg   = "Falha na autenticacao HTTP - Usuário bloqueado!";
                        return $ret;
                    }

                    //Verificação do TOKEN enviado.
                    if($token == null){
                        //Caso o $token seja NULL, um novo token é gerado

                        //Adquire um novo token para ser utilizado
                        return Security::geraToken($rs_user->ID_CLIENTE, $user);
                    }else{
                        //Caso o $token seja enviado o mesmo será validado      
                        return Security::validaToken($token, $rs_user->ID_CLIENTE);
                    }
                }else{
                    $ret->erro  = 2;
                    $ret->msg   = "Falha na autenticacao HTTP - Usuário inválido / não enviado!";
                    return $ret;
                }
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro inesperado! - " . $e->getMessage();
                $ret->erro      = 1;
                return $ret;
            }
        }
        
        /**
         * Função que gera um novo token ao cliente
         * 
         * @return string XMLResult
         */
        function getToken(){
            try{
                //Gera um novo TOKEN
                $rs = $this->authenticate(null);

                //Retorno da geração do Token
                $erro   = $rs->erro;
                $msg    = $rs->msg;

                if($rs->status){
                    $dados  = "<dados>";
                    $dados .= "<token>nnxnxnxnxnxnxnxnxnxnxnxnxnxnxnxnxnxnxnxnxnxnxnx</token>";
                    $dados .= "</dados>";
                }

                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>" . utf8_encode($msg) . "</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";
                
                return $ret;
            }catch(Exception $e){
                //Retorna erro em XML
                $this->xmlException($e->getMessage());
            }
        }
        
        /**
         * Retorn um XML com o Padrão de erro da documentação WS
         * 
         * @param string $msg
         * @return string
         */
        protected function xmlException($msg){
            $ret = "<root>";
            $ret .= "<status>";
            $ret .= "<erro>1</erro>";
            $ret .= "<msg>" . utf8_encode($msg) . "</msg>";
            $ret .= "</status>";
            $ret .= "</root>";

            return $ret;
        }

        /**
         * Adiciona um método a ser ignorado no WSDL
         * 
         * @param string $metodo Nome do método de Controller
         * @throws Exception
         */
        public function addIgnore($metodo){
            try{
                $this->arrMetodos[] = $metodo;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Monta o WSDL para o Controller de API
         * @throws Exception
         */
        public function actionWsdl(){
            try{
                //Inicia WSDL
                $wsdl = new Wsdl($this->class, $this->uri);

                //Ignora métodos enviados
                if(is_array($this->arrMetodos)){
                    foreach ($this->arrMetodos as $metodo) {
                        $wsdl->addIgnore($metodo);
                    }
                }

                //Imprime WSDL
                $wsdl->showWsdl();
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
