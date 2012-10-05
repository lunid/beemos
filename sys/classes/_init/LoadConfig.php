<?php

    /**
     * Classe responsável por fazer a leitura do arquivo config.xml da aplicação
     * e também dos módulos do sistema, quando houver. 
     */
    require_once('sys/classes/util/Xml.php');  
    class LoadConfig extends sys\classes\util\Xml {
                
        private $nodes;
        const PREFIXO_VAR = 'GLB_';
        function __construct($pathXml='config.xml'){            
            
            if (file_exists($pathXml)) {
                $arrPath    = pathinfo($pathXml);                
                $extension  = $arrPath['extension'];
                if ($extension == 'xml') {                    
                    $objXml     = self::loadXml($pathXml);  
                    if (is_object($objXml)) {
                        $this->objXml = $objXml;
                        $this->loadVars($objXml);
                    } else {                
                        $msgErr = 'Impossível ler o arquivo '.$pathXml;
                        die($msgErr);            
                    }
                } else {
                   echo 'O arquivo informado parece não ser um arquivo XML';
                }
            } else {
                echo "Arquivo {$pathXml} não foi localizado.";
            }
        }
        
        private function loadVars($objXml){    
            $msgErr             = '';
            $arrConfigCache     = array();
            $nodesHeader        = $objXml->header;
            $numItens           = count($nodesHeader);
            $module             = 'app';
            $idRoot             = 'root';
            $idDefaultModule    = 'defaultModule';            
            $idFolderViews      = 'folderViews';   
            
            if ($numItens > 0) {
                //Configurações da aplicação:
                $nodesApp               = $objXml->app->config; 
                $root                   = self::valueForAttrib($nodesApp,'id',$idRoot);   
                $cfgFolderViews         = self::valueForAttrib($nodesApp,'id',$idFolderViews);   
                $cfgDefaultModule       = self::valueForAttrib($nodesApp,'id',$idDefaultModule);                
                             
                $this->setGlobalVar($idFolderViews,$cfgFolderViews);
                $this->setGlobalVar($idRoot,$root);
                $this->setGlobalVar($idDefaultModule,$root);
                
                //Configurações de módulo:
                $idFolderTpl            = 'folderTemplate';
                $idDefaultTpl           = 'defaultTemplate';
                $nodesModule            = $objXml->module->config; 
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
                                 
                //Configurações de cabeçalho (includes):
                $nodesHeader            = $objXml->app->header;                 
                $rootFolderSys          = self::getAttrib($nodesHeader,'rootFolderSys');
                $rootFolderView         = self::getAttrib($nodesHeader,'rootFolderView');                            
                                
                $this->setGlobalVar('folderSys',$rootFolderSys);
                $this->setGlobalVar('folderView',$rootFolderView);
                
                $nodesInclude    = $objXml->header->include;     
                $arrNodesInclude = self::convertNode2Array($nodesInclude);
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
