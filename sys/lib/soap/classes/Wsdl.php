<?php
    namespace sys\lib\soap\classes;
    
    /**
     * Abstração da Lobrary WSDLCreator para geração de WSDL
     */
    class Wsdl {
        private $wsdl; //Objeto com a instância de WSDLCreator
        private $class; //Classe da qual será gerado o WSDL
        
        /**
         * Inicia a geração do WSDL com seus parâmetros básicos
         * 
         * @param string $class Controller que será utilizado para geração do WSDL
         * @param string $uri URI onde se encontra o serviço
         * 
         * @throws Exception
         */
        public function __construct($class = "SuperProWeb", $uri = null){
            try{
                //Trata caminhos de inclusão
                $path       = preg_replace("/(sys)(.*)/", "", __DIR__);
                $path_wsdl  = $path . "sys/lib/soap/src/wsdlcreator/WSDLCreator.php";
                $path_soap  = $path . "sys/lib/soap/classes/Soap.php";
                $path_class = $path . "api/classes/controllers/". ucfirst($class)."Controller.php";
                
                //Inclui bibilioteca WSDLCreator
                include($path_wsdl);
                
                //Instância a classe WSDLCreator e inicia funções primárias
                $this->wsdl = new \WSDLCreator("SuperProWeb", $uri . "/wsdl");
                
                $this->wsdl->addFile($path_soap); //Adiciona classe Soap - Padrão
                $this->wsdl->addFile($path_class); //Adiciona classe que será usada
                
                $this->wsdl->setClassesGeneralURL($uri); //Seta URL do serviço
                
                $this->wsdl->addURLToClass("Soap", $uri); //Seta url da classe
                $this->wsdl->addURLToClass(ucfirst($class), $uri); //Seta url da classe
                
                //Ignora métodos da classe SOAP, mantendo apenas os de WebService
                $this->wsdl->ignoreMethod(array("Soap" => "__construct"));
                $this->wsdl->ignoreMethod(array("Soap" => "actionWsdl"));
                $this->wsdl->ignoreMethod(array("Soap" => "actionIndex"));
                $this->wsdl->ignoreMethod(array("Soap" => "addIgnore"));
                $this->wsdl->ignoreMethod(array("Soap" => "validaUsuarioWS"));
                
                //Seta classe em uso
                $this->class = $class;
            }catch(Exception $e){
                throw $e;
            }   
        }
        
        /**
         * Adiciona um método para ser ignorado no WSDL
         * 
         * @param string $metodo nome do método (dentro do Controller setado no construct)
         * 
         * @throws Exception
         */
        public function addIgnore($metodo){
            try{
                //Adiciona método a ser ignorado
                $this->wsdl->ignoreMethod(array(ucfirst($this->class) => $metodo));
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Constroi o arquivo WSDL final e imprime a saída do mesmo
         * 
         * @throws Exception
         */
        public function showWsdl(){
            try{
                //Cria WSDL
                $this->wsdl->createWSDL();
                //Imprime Saída
                $this->wsdl->printWSDL(true);
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
