<?php

    /**
     * Classe responsável por fazer a leitura do arquivo config.xml da aplicação
     * e também dos módulos do sistema, quando houver. 
     */
    require_once('sys/classes/util/Xml.php');  
    class LoadConfig extends sys\classes\util\Xml {
                
        private $nodes;
        private $cfgGlobal = FALSE;//Se TRUE significa que o arquivo config.xml está na raíz do site.
        const PREFIXO_VAR = 'GLB_';
        function __construct(){            
            //$this->loadConfigXml('config.xml');            
        }
        
        function loadConfigXml($pathXml){
            $msgErr         = '';           
            
            if (file_exists($pathXml)) {   
                $this->cfgGlobal = FALSE;
                if (strcmp($pathXml,'config.xml') == 0) $this->cfgGlobal = TRUE;//config.xml global.
              
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
            
            if ($numItens > 0) {
                if ($this->cfgGlobal) {
                    /*
                     * O arquivo atual é o config.xml global que encontra-se na raíz da aplicação.
                     * Carrega as configurações específicas da aplicação e que não podem
                     * ser sobrepostas por um config.xml de módulo.
                     */
                    
                    //CARREGA AS CONFIGURAÇÕES GERAIS DA APLICAÇÃO:
                    //========================================================== 
                    $arrId = array('modules','rootFolder','baseUrlHttp','baseUrlHttps','folderSys','folderViews','defaultModule','langs','defaultLang');
                    $this->loadConfigId($objXml->app->config,$arrId);                        

                    //CARREGA AS CONFIGURAÇÕES DE ASSETS:
                    //==========================================================  
                    $arrId = array('folderPlugins','assetsFolderRoot');
                    $this->loadConfigId($objXml->assets->config,$arrId);                                                      

                    //CARREGA AS CONFIGURAÇÕES DE MÓDULOS:
                    //==========================================================
                    $arrId = array('folderTemplate','defaultTemplate');
                    $this->loadConfigId($objXml->module->config,$arrId);                    
                    
                    //CARREGA AS CONFIGURAÇÕES DE COMPONENTES:
                    //==========================================================   
                    $arrId = array('folderLib','folderComps');
                    $this->loadConfigId($objXml->components->config,$arrId);                      
                
                    //CARREGA AS CONFIGURAÇÕES DE ENVIO DE MENSAGEM (E-MAIL):
                    //==============================================================    
                    $arrId = array('emailFolder','emailFrom','nameFrom','emailReplyTo','nameReplyTo');
                    $this->loadConfigId($objXml->email->config,$arrId);

                    //CARREGA AS CONFIGURAÇÕES DE SMTP:
                    //==============================================================    
                    $arrId = array('smtpHost','smtpAuth','smtpPort','smtpUsername','smtpPassword');
                    $this->loadConfigId($objXml->smtp->config,$arrId);                            
                }        
                 
                //CARREGA AS CONFIGURAÇÕES DE HEADER (INCLUDES CSS E JS):
                //==============================================================                                                                                                                                                                    
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
         * Faz a leitura da tag <config id=''>...</config> a partir de um nó informado.
         * Serve como método de suporte para loadVars().
         * 
         * O(s) valor(es) lido(s) é(são) persistido(s) em variável de sessão.
         * 
         * @param Xml $node
         * @param string[] $arrNodes  Lista de id's que devem ser lidos e gravados em session.
         * @see loadVars()
         */
        private function loadConfigId($node,$arrId){
            if (is_object($node) && is_array($arrId)) {
                foreach($arrId as $id) {
                    $value = self::valueForAttrib($node,'id',$id);                    
                    $this->setGlobalVar($id,$value);
               }             
            } else {
                //O objeto não existe. Limpa as variáveis do objeto atual, se houver.
                foreach($arrId as $id) {                         
                    $this->setGlobalVar($id,'');
               }                 
            }            
            
        }
        
        private function setGlobalVar($varName,$value){
           $varName             = self::PREFIXO_VAR.$varName;  
           $value               = str_replace('./','',$value);
           $_SESSION[$varName]  = $value;           
        }
        
        private static function getGlobalVar($varName){
            $varName    = self::PREFIXO_VAR.$varName;
            $value      = (isset($_SESSION[$varName]))?$_SESSION[$varName]:'';
            return $value;
        }
        
        private function getAttribId($valueAttrib){
            $nodes  = $this->nodes;
            $value  = '';
            if (is_object($nodes)) {
                $value      = self::valueForAttrib($nodes,'id',$valueAttrib);
                $arrAttrib  = self::getAttribsOneNode($nodes);
                //if (count($arrAttrib) > 1)print_r($arrAttrib);
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
