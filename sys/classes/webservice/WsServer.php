<?php

namespace sys\classes\webservice;

class WsServer {
        private $class; //Classe a ser consumida no webservice.
        private $arrIdgnoredMethods; //Métodos de $class que NÃO devem ser consumidos no webservice.
        private $wsComp;
        
        public function __construct($class, $arrIgnoredMethods = array()) {
            try{

                //Inicia o ServerSoap
                $this->wsComp   = Component::webservice();                                
                $this->class    = $class;
                
                //Armazena métodos a serem ignorados no WSDL
                $this->arrIdgnoredMethods = $arrIgnoredMethods;
            }catch(Exception $e){
                throw $e;
            }
        }   
        
        /**
         * Inicia o servico SOAP.
         * 
         * @throws Exception
         */
        public function actionIndex(){
            try{
                //Inicia o SoapServer
                $this->wsComp->index();
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
                $this->wsComp->wsdl("SuperProWeb");
                $className      = ucfirst($this->class);
                $thisClassName  = "WsServer";
                
                //Trata caminhos de inclusão
                $path               = preg_replace("/(api)(.*)/", "", __DIR__);
                $path_controller    = $path . "api/classes/controllers/" . $className . "Controller.php";
                $path_server        = $path . "api/classes/WsServer.php";
                
                //Inclui Arquivos no WSDL
                $this->wsComp->addFile($path_server, $thisClassName);
                $this->wsComp->addFile($path_controller, $this->class);
                
                //Ignora métodos da classe Server, mantendo apenas os globais
                $this->wsComp->addIgnore($className, "__construct");
                $this->wsComp->addIgnore($thisClassName, "__construct");                
                $this->wsComp->addIgnore($thisClassName, "actionIndex");
                $this->wsComp->addIgnore($thisClassName, "xmlException");
                $this->wsComp->addIgnore($thisClassName, "actionWsdl");
                $this->wsComp->addIgnore($thisClassName, "getXmlField");
                
                //Ignora métodos da classe
                foreach($this->classIdgnoredMethods as $method){
                    $this->wsComp->addIgnore($this->class, $method);
                }
                
                //Exibe WSDL
                $this->wsComp->showWsdl();
            }catch(Exception $e){
                throw $e;
            }
        }        
}

?>
