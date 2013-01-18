<?php
    namespace sys\lib\comps\webservice\classes;
    
    class Soap extends Wsdl{
        private $server; //Aramazena SoapServer       
        
        /**
         * Constrói um SoapServer baseado em uma classe Controller (/api)
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
                                
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Inicia o serviço SoapServer
         * 
         * @throws SoapFault
         */
        public function index(){
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
    }
?>
