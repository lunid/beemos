<?php    
    namespace sys\classes\error;
    use \sys\classes\util\Xml;
    use \sys\classes\mvc\MvcFactory;
    
    class XmlException extends Xml {
        
        private $_physicalPathXml;
        
        
        /**
         * Retorna o erro solicitado no formato JSON.
         * 
         * @param string $nameFileXml Nome do arquivo XML que contém a mensagem de erro.
         * @param string $codErr Nome do nó XML que contém a mensagem de erro solicitada.
         * @return string
         */
        public static function getErrorJson($nameFileXml,$codErr,$arrParams=array()){
            $msgErr = self::getErrorString($nameFileXml, $codErr,$arrParams);
            $arrErr = array('error'=>$msgErr);
            $json   = json_encode($arrErr);
            return $json;
        }
        
        /**
         * Retorna o erro solicitado no fomrato string.
         * 
         * @param type $nameFileXml Nome do arquivo XML que contém a mensagem de erro.
         * @param type $codErr Nome do nó XML que contém a mensagem de erro solicitada.
         * @return string
         * @throws \Exception Caso o arquivo XML solicitado não seja localizado.
         */
        public static function getErrorString($nameFileXml,$codErr,$arrParams=array()){
            $objXmlException = new XmlException();
            if ($objXmlException->findFileXmlError($nameFileXml)){
                $msgErr = $objXmlException->$codErr;
                if (is_array($arrParams) && count($arrParams) > 0) {
                    foreach($arrParams as $key=>$value) {
                        $msgErr = str_replace("{{$key}}",$value,$msgErr);
                    }
                }
                return $msgErr;
            } else {                 
                $msgErr = __METHOD__.'() Arquivo '.$nameFileXml.' não localizado.';
                throw new \Exception($msgErr);            
            }
        }
        
        /**
         * Localiza e valida o caminho físico do arquivo XML informado.
         * Procura primeiro no módulo de origem. Caso não encontre, procura na pasta sys/dic/.
         * 
         * @param string $nameFileXml Nome do arquivo Xml a ser loclaizado. Não é necessário informar extensão.
         * @return boolean
         */
        function findFileXmlError($nameFileXml){
            //Verifica se o arquivo XML existe na pasta dic/ do módulo de origem.
            $fileExists         = FALSE;
            $module             = \Application::getModule();
            $physicalPathXml    = $this->getPhysicalPathXml($nameFileXml,$module);           
            
            if (!file_exists($physicalPathXml)) {
                //Arquivo não existe no módulo de origem. Verifica se existe no módulo sys.
                $module             = \LoadConfig::folderSys();
                $physicalPathXml    = $this->getPhysicalPathXml($nameFileXml,$module);        
            }
            
            if (file_exists($physicalPathXml)) {
                $this->_physicalPathXml = $physicalPathXml;
                $fileExists             = TRUE;
            }           
            return $fileExists;
        }      
        
        /**
         * Monta o caminho físico a partir do nome do arquivo XML e do módulo de origem.
         * Os arquivos XML devem ser armazenados, por padrão, na pasta dic/.
         * 
         * Método de suporte para setFileXmlError();
         * @see setFileXmlError
         * 
         * @param string $nameFileXml Nome do arquivo XML. Não é necessário informar a extensão do arquivo.
         * @param string $module Nome do módulo onde o arquivo XML deve existir.
         * @return string
         */
        private function getPhysicalPathXml($nameFileXml,$module){
            $nameFileXml        = str_replace('.xml','',$nameFileXml);
            $pathXml            = '/'.$module.'/dic/'.$nameFileXml.'.xml';
            $physicalPathXml    = \Url::physicalPath($pathXml);  
            
            return $physicalPathXml;
        }

        /**
         * Faz o mapeamento do nó XML que contém a mensagem de erro com o
         * parâmetro $codErr informado.
         * 
         * @param string $codErr Nome referente ao nó do arquivo XML contendo a respectiva mensagem de erro.
         * @return string 
         */
        function __get($codErr){
            $_physicalPathXml   = $this->_physicalPathXml;
            $objXml             = self::loadXml($_physicalPathXml);  
            $msgErr             = "Erro {$codErr} não localizado em {$_physicalPathXml}";
            
            if (is_object($objXml)) {               
                $nodesRoot  = $objXml->errors->error;//Guarda o nó que contém mensagens de erro               
                if (is_object($nodesRoot)) {
                    //Carrega os parâmetros do nó atual
                    $codErrOk = FALSE;
                    foreach($nodesRoot as $nodeError){
                        $idValue = $this->getAttrib($nodeError,'id'); 
                        if ($idValue == $codErr) {
                            //Encontrou a configuração do servidor informado:                                
                            $msgErr = (string)$nodeError;            
                            $codErrOk = TRUE;
                            break;
                        }                  
                    }                       
                    
                    if (!$codErrOk) $msgErr = "Erro desconhecido. Não foi possível localizar a mensagem do erro {$codErr}.";
                } else {
                    $msgErr = "O nó <errors><error ...></error></errors> não foi localizado em {$_physicalPathXml}";
                }                
            }
            return $msgErr;
        }
    }
?>
