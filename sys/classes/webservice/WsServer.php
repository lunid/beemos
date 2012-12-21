<?php

namespace sys\classes\webservice;
use \sys\classes\util\Component;

class WsServer {
        private $wsInterfaceClass; //Classe a ser consumida no webservice.
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
                
                $objSoap->wsdlGenerate($wsInterfaceClass);
                                                
                
                //Trata caminhos de inclusão                
                $path                   = preg_replace("/(api)(.*)/", "", __DIR__);
          
                list($realPath,$uri)    = explode('\sys',$path);                
                $pathController         = $realPath."\api\classes\controllers\UsuariosController.php";                
                $pathController         = $realPath."\api\classes\controllers\TesteController.php";                
                $pathServer             = $path."\WsServer.php";
                                
                $objSoap->addFile($pathController, $wsInterfaceClass);
                //$objSoap->addFile($pathServer, $thisClassName);       
                
                //Ignora métodos da classe Server, mantendo apenas os globais
                $objSoap->addIgnore($wsInterfaceClass, "__construct");    
                //$objSoap->addIgnore($thisClassName, "__construct");                
                //$objSoap->addIgnore($thisClassName, "actionIndex");
                //$objSoap->addIgnore($thisClassName, "xmlException");
                //$objSoap->addIgnore($thisClassName, "actionWsdl");
                //$objSoap->addIgnore($thisClassName, "getXmlField");
                
                //Exibe WSDL
                $objSoap->showWsdl();
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
