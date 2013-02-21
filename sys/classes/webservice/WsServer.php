<?php

namespace sys\classes\webservice;
use \sys\classes\util\Component;

class WsServer {
        private $wsInterfaceClass; //Classe a ser consumida no webservice.
        private $local = "api"; //Local que está utilizando WSDL
        private $arrIgnoredMethods; //Métodos de $class que NÃO devem ser consumidos no webservice.
        
        
        public function __construct($class, $arrIgnoredMethods = array()) {
            try{                
                $this->setClass($class);    
                $this->setArrIgnoredMethods($arrIgnoredMethods); //Armazena métodos a serem ignorados no WSDL   
            }catch(Exception $e){
                throw $e;
            }
        } 
        
        protected function setWsInterfaceClass($wsInterfaceClass){
            $this->wsInterfaceClass = $wsInterfaceClass;
        }
        
        protected function setLocal($local){
            $this->local = $local;
        }
        
        protected function setArrIgnoredMethods($arrIgnoredMethods){
            $this->arrIgnoredMethods = $arrIgnoredMethods;
        }
        
        /**
         * Inicia o servico SOAP.
         * 
         * @throws Exception
         */
        public function actionIndex(){
            try{
                //Inicia o SoapServer
                $this->getSoap()->index();
            }catch(Exception $e){
                throw $e;
            }
        }      
        
        /**
         * Gera o WSDL do Serviço.
         * 
         * @throws Exception Caso uma falha ocorra ao tentar gerar o WSDL.
         */
        public function actionWsdl(){
            try{
                //Inicia WSDL                   
                $wsInterfaceClass   =  $this->wsInterfaceClass;
                $objSoap            = $this->getSoap();
                
                //Inicia WSDL da Classe
                $objSoap->wsdlGenerate($wsInterfaceClass);
                
                //Trata caminhos de inclusão                
                $pathController = preg_replace("/(sys)(.*)/", "", __DIR__) . $this->local . "\classes\controllers\\" . $wsInterfaceClass . "Controller.php";
                $pathWsServer   = __DIR__ . "\WsServer.php";

                if(file_exists($pathController)){
                    //Adiciona Arquivo ao WSDL
                    $objSoap->addFile($pathWsServer, "WsServer");
                    $objSoap->addFile($pathController, $wsInterfaceClass);
                    
                    //Ignora métodos da classe Enviada
                    $objSoap->addIgnore($wsInterfaceClass, "__construct");
                    
                    //Verifica array de métodos para serem ifnorados
                    if(is_array($this->arrIgnoredMethods) && sizeof($this->arrIgnoredMethods)){
                        //Caso existam métodos, o loop ignora os mesmos
                        foreach($this->arrIgnoredMethods as $method){
                            $objSoap->addIgnore($wsInterfaceClass, $method);
                        }
                    }
                    
                    //Ignora métodos da classe WebService
                    $objSoap->addIgnore("WsServer", "__construct");                
                    $objSoap->addIgnore("WsServer", "actionIndex");
                    $objSoap->addIgnore("WsServer", "xmlException");
                    $objSoap->addIgnore("WsServer", "actionWsdl");
                    $objSoap->addIgnore("WsServer", "getXmlField");
                    $objSoap->addIgnore("WsServer", "setArrIgnoredMethods");
                    $objSoap->addIgnore("WsServer", "setLocal");
                    $objSoap->addIgnore("WsServer", "setWsInterfaceClass");
                    
                    //Exibe WSDL
                    $objSoap->showWsdl();
                }
            }catch(Exception $e){
                throw $e;
            }
        }     
        
        /**
         * Retorna um objeto SOAP a partir da chamada do componente webservice().
         * 
         * @return Soap
         */
        private function getSoap(){
            $objSoap = Component::webservice();                                
            return $objSoap;
        }           
}

?>
