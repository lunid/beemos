<?php

    /**
     * Classe responsável por fazer a leitura do arquivo config.xml da aplicação
     * e também dos módulos do sistema, quando houver. 
     */
    require_once('sys/classes/util/Xml.php');  
    class LoadConfig extends sys\classes\util\Xml {
                
        private $nodes;
        const PREFIXO_VAR = 'GLB_';
        function __construct(){            
            //$this->loadConfigXml('config.xml');            
        }
        
        function loadConfigXml($pathXml){
            $msgErr         = '';           
            
            if (file_exists($pathXml)) {                
                $arrPath        = pathinfo($pathXml);                
                $extension      = $arrPath['extension'];
                if ($extension == 'xml') {                    
                    $objXml = self::loadXml($pathXml);  
                    if (is_object($objXml)) {
                        $this->objXml = $objXml;
                        $this->loadVars($objXml);
                    } else {                
                        $msgErr = 'Impossível ler o arquivo '.$pathXml;                                            
                    }
                } else {
                   $msgErr = 'O arquivo informado parece não ser um arquivo XML';                                                                 
                }
            } else {                
                $arrUrl         = explode('/',$pathXml);
                $tamPartsUrl    = count($arrUrl);                
                if ($tamPartsUrl <= 1) $msgErr = "Arquivo {$pathXml} não foi localizado.";                
            }                        
            if (strlen($msgErr) > 0) throw new \Exception( $msgErr );    
        }
        
        private function loadVars($objXml){                           
            $nodesHeader        = $objXml->header;
            $numItens           = count($nodesHeader);
            $pathTplFolder      = '';
            
            $idRootFolder       = 'rootFolder';
            $idBaseUrlHttp      = 'baseUrlHttp';
            $idBaseUrlHttps     = 'baseUrlHttps';
            $idDefaultModule    = 'defaultModule';            
            $idFolderSys        = 'folderSys';
            $idFolderViews      = 'folderViews';   
            
            if ($numItens > 0) {
                //Configurações da aplicação:
                $nodesApp               = $objXml->app->config;
                if (is_object($nodesApp)) {
                    $cfgRootFolder          = self::valueForAttrib($nodesApp,'id',$idRootFolder);   
                    $cfgBaseUrlHttp         = self::valueForAttrib($nodesApp,'id',$idBaseUrlHttp);   
                    $cfgBaseUrlHttps        = self::valueForAttrib($nodesApp,'id',$idBaseUrlHttps);   
                    $cfgFolderSys           = self::valueForAttrib($nodesApp,'id',$idFolderSys);   
                    $cfgFolderViews         = self::valueForAttrib($nodesApp,'id',$idFolderViews);   
                    $cfgDefaultModule       = self::valueForAttrib($nodesApp,'id',$idDefaultModule);                

                    $this->setGlobalVar($idBaseUrlHttp,$cfgBaseUrlHttp);
                    $this->setGlobalVar($idBaseUrlHttps,$cfgBaseUrlHttps);
                    $this->setGlobalVar($idFolderSys,$cfgFolderSys);
                    $this->setGlobalVar($idFolderViews,$cfgFolderViews);
                    $this->setGlobalVar($idRootFolder,$cfgRootFolder);
                    $this->setGlobalVar($idDefaultModule,$cfgDefaultModule);
                }
                
                //Configurações de módulo:
                $idFolderTpl            = 'folderTemplate';
                $idDefaultTpl           = 'defaultTemplate';
                $nodesModule            = $objXml->module->config; 
                
                if (is_object($nodesModule)) {
                    $cfgFolderTemplate      = self::valueForAttrib($nodesModule,'id',$idFolderTpl);                
                    $cfgDefaultTemplate     = self::valueForAttrib($nodesModule,'id',$idDefaultTpl);                
                    $pathTplFolder          = $cfgDefaultModule.'/'.$cfgFolderTemplate.'/';                               
                    
                    try {
                        $this->vldTemplate($pathTplFolder,$cfgDefaultTemplate);
                    } catch(\Exception $e) {
                        die('LoadConfig->loadVars(): '.$e->getMessage());
                    }

                    $this->setGlobalVar($idFolderTpl,$cfgFolderTemplate);
                    $this->setGlobalVar($idDefaultTpl,$cfgDefaultTemplate);                      
                }                                             
                                 
                //Configurações de cabeçalho (includes):
                //$nodesHeader        = $objXml->app->header;                                                                                                                                                           
                $nodesInclude       = $objXml->header->include;     
                $arrNodesInclude    = self::convertNode2Array($nodesInclude);
                
                foreach($arrNodesInclude as $arrItem){
                    $nodeValue  = (string)$arrItem['value'];    
                    $attrib     = $arrItem['attrib'];                    
                    $extension  = (string)$attrib['id'];//css,cssInc,js,jsInc,plugins                    
                    $concat     = (isset($attrib['concat']))?(int)$attrib['concat']:0;
                    
                    if (strlen($extension) > 0) {   
                        
                        //Gera o nome da constante a partir do atributo id.
                        $varName    = $extension;                      
                        $newValue   = $nodeValue;
                        $cacheValue = self::getGlobalVar($varName);                        
                        if ($concat == 1 && strlen($cacheValue) > 0) {
                            //Concatena o valor da propriedade atual 
                            //com a variável global caso já esteja definida.
                            $newValue   = $cacheValue.','.$nodeValue;
                            
                            //Retira valores duplicados, se houver:
                            $arrValue   = explode(',',$newValue);
                            $arrValue   = array_unique($arrValue);
                            $newValue   = join(',',$arrValue);
                        }
                        $this->setGlobalVar($varName,$newValue);   
                    }                    
                }                                   
            } else {
                //Nenhuma mensagem foi localizada no XML informado.
                echo 'O arquivo config.xml é válido, porém a estrutura XML epserada parece estar incorreta.';                    
            }            
        }
        
        /**
         * Valida o template padrão da aplicação e cria um arquivo novo 
         * na pasta de templatescaso ainda não exista.
         * 
         * @param string $pathTplFolder Path da pasta de templates
         * @param string $cfgDefaultTemplate nome do arquivo template padrão
         */
        private function vldTemplate($pathTplFolder,$cfgDefaultTemplate){
            $pathFileTplDefault = $pathTplFolder.$cfgDefaultTemplate;            
            if (!file_exists($pathFileTplDefault)) {
                //Arquivo template não existe
                if (!is_dir($pathTplFolder)) {
                    //Diretório de templates ainda não existe. Tenta criá-lo.
                    if (!mkdir($pathTplFolder, 0, true)) {
                        $msgErr = 'A tentativa de criar a pasta de templates em '.$pathTplFolder.' falhou.';
                        throw new \Exception( $msgErr );                           
                    }                  
                }   

                $date               = date('d/m/Y H:i:s'); 
                $pathFileTplDefault = str_replace('//','/',$pathFileTplDefault);
                $open               = fopen($pathFileTplDefault, "a+");

                //Conteúdo do novo arquivo template:
                $fileContent = "
                    <!-- Arquivo criado dinâmicamente em LoadConfig.php em {$date} -->".chr(13)."
                    <div>{BODY}</div>
                ";
                    
                if (fwrite($open, $fileContent) === false) {
                    $msgErr = "Um template padrão não foi definido no arquivo config.xml e a tentativa de 
                    gerar um novo arquivo ({$pathFileTplDefault}) falhou. Verifique a tag 
                    <fileName id='default'>nomeDoArquivoTemplate.html</fileName>";
                    $msgErr = htmlentities($msgErr);                     
                    throw new \Exception( $msgErr );                                                                  
                }
                fclose($open);                  
            }            
        }               
        
        private function setGlobalVar($varName,$value){
           $varName             = self::PREFIXO_VAR.$varName;  
           $value               = str_replace('./','',$value);
           $_SESSION[$varName]  = $value;           
        }
        
        private static function getGlobalVar($varName){
            $value = (isset($_SESSION[$varName]))?$_SESSION[$varName]:'';
            return $value;
        }
        
        private function getAttribId($valueAttrib){
            $nodes  = $this->nodes;
            $value  = '';
            if (is_object($nodes)) {
                $value      = self::valueForAttrib($nodes,'id',$valueAttrib);
                $arrAttrib  = self::getAttribsOneNode($nodes);
                if (count($arrAttrib) > 1)print_r($arrAttrib);
            }
            return $value;
        }
        
        /**
         * Destrói as variáveis globais de configuração.
         * 
         * return int Retorna o total de váriáveis destruídas. 
         */
        public static function unsetGlobalVars(){
            return self::listVars('unset');
        }        
                
        /**
         * Lista ($action='list')/destrói ($action='unset') as variáveis globais 
         * carregadas em LoadConfig a partir de um arquivo config.xml.
         * 
         * @param string $action Pode ser list = lista as variáveis globais de configuração, ou
         * então unset, que destrói as variáveis.
         * return int|string Retorna um valor numérico com o total de variáveis destruídas ($action='unset')
         * ou então uma string contendo a lista de variáveis globais que estão em memória.
         */
        public static function listVars($action='list'){
            $numVarsUnset   = 0;
            $listGlobalVars = '';
            
            foreach($_SESSION as $key=>$value){
                $indice = strpos($key,self::PREFIXO_VAR);
                if ($indice !== false) {
                    if ($action == 'list') {
                        $listGlobalVars .= "$key = $value <br>";
                    } elseif ($action == 'unset') {
                        unset($_SESSION[$key]);
                        $numVarsUnset++;
                    }
                }
            }      
            if($action == 'unset') return $numVarsUnset;
            return $listGlobalVars;
        }
        
        public static function __callStatic($varName,$args) {                      
            $varName = self::PREFIXO_VAR.$varName;           
            $value = (isset($_SESSION[$varName]))?$_SESSION[$varName]:'';
            return $value;                        
        }
    }
?>
