<?php
    namespace api\classes\controllers;
    
    //Include de Bibliotecas
    include "/api/nusoap/nusoap.php";

    //Depêndencias
    use \sys\classes\mvc\Controller;
    
    /**
    * Classe Controller usada com default para criação se um servidor SOAP.
    */
    class ServerController extends Controller {
        //Armazena o SERVER NuSoap criado no contrutor
        protected $server;
        
        /**
         * Cria um servidor WSDL (NuSoap) para utilização
         */
        public function __construct(){
            try{
                //Inicia servidor WSDL e suas configuirações
                $this->server = new \nusoap_server();
                $this->server->configureWSDL("SuperProWeb", "urn:superproweb");
            }catch(Exception $e){
                $this->imprimirErro($e);
            }
        }
        
        /**
         * Devolve o erro em forma XML
         * 
         * @param Exception $e
         * @return string XML com o padrão de erros
         */
        protected function imprimirErro($e){
            $ret = "<root>";
            $ret .= "<status>";
            $ret .= "<erro>1</erro>";
            $ret .= "<msg>". utf8_encode($e->getMessage()) ."</msg>";
            $ret .= "</status>";
            $ret .= "</root>";

            return $ret;
        }
    }
?>
