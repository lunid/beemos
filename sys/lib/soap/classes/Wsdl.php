<?php
    namespace sys\lib\soap\classes;
    
    /**
     * Abstração da Lobrary WSDLCreator para geração de WSDL
     */
    class Wsdl {
        private $wsdl; //Objeto com a instância de WSDLCreator
        private $uri; //URL do WSDL
        
        /**
         * Inicia a geração do WSDL com seus parâmetros básicos
         * 
         * @param string $class Controller que será utilizado para geração do WSDL
         * @param string $uri URI onde se encontra o serviço
         * 
         * @throws Exception
         */
        public function __construct($wsdlName = "SuperProWeb", $uri = "http://localhost/interbits/api/"){
            try{
                //Aramazena URI
                $this->uri = $uri . "/wsdl";
                
                //Trata caminhos de inclusão
                $path       = preg_replace("/(sys)(.*)/", "", __DIR__);
                $path_wsdl  = $path . "sys/lib/soap/src/wsdlcreator/WSDLCreator.php";
                
                //Inclui bibilioteca WSDLCreator
                include($path_wsdl);
                
                //Instância a classe WSDLCreator e inicia funções primárias
                $this->wsdl = new \WSDLCreator($wsdlName, $this->uri);
                
                $this->wsdl->setClassesGeneralURL($this->uri); //Seta URL do serviço
            }catch(Exception $e){
                throw $e;
            }   
        }
        
        public function addFile($file, $className){
            try{
                //Adiciona Arquivo
                $this->wsdl->addFile($file); //Adiciona classe ao WSDL
                $this->wsdl->addURLToClass(ucfirst($className), $this->uri); //Seta url da classe
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
        public function addIgnore($className, $metodo){
            try{
                //Adiciona método a ser ignorado
                $this->wsdl->ignoreMethod(array(ucfirst($className) => $metodo));
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
