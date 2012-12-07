<?php
    namespace sys\lib\soap\classes;
    
    abstract class Soap extends Wsdl{
        private $server; //Aramazena SoapServer
        private $uri; //Aramazena URI do serviço
        private $class; //Classe onde esta baseado o serviço
        
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
         * Valida o usuário que está acessando o serviço - HTTP
         * 
         * @param mixed $args Argumento(s) para autenticação
         * 
         * Implemente sua validação
         */
        abstract function authenticate($args);
        
        /**
         * Monta o WSDL para o Controller de API
         * @throws Exception
         */
        public function wsdl($wsdlName){
            try{
                //Inicia WSDL
                parent::__construct($wsdlName, $this->uri);
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
