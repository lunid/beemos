<?php

    namespace sys\classes\mvc;
    use \sys\classes\util\Dic;
    use \sys\classes\plugins\Plugin;
    use \sys\classes\mvc\Header;
    
    class View {
        const CSS       = 'site';
        const CSS_INC   = '';
        const JS        = 'sys:util.dictionary';
        const JS_INC    = '';
        
        private $objHeader      = NULL;
        private $viewAlias      = '';
        private $viewFile       = '';
        private $tplFile        = '';   
        private $tplContent     = '';
        private $bodyContent    = '';
        private $params         = array();
        private $forceNewIncMin = FALSE;
        
        function __construct($viewAlias,$tplName='padrao'){            
            $tplFile        = '';
            $viewFile       = '';
            if (strlen($viewAlias) > 0) {
                $viewFile    = __APP__ . '/views/'.$viewAlias.'.html';  
                try {
                    //Inicializa um objeto Header e define os includes padrão (js e css)
                    $objHeader = new Header($viewAlias);            
                    $objHeader->addCss(self::CSS);
                    $objHeader->addCssInc(self::CSS_INC);
                    $objHeader->addJs(self::JS);
                    $objHeader->addJsInc(self::JS_INC);
                    $this->checkUrlFile($viewFile);//Verifica se o arquivo existe.  
                    $this->viewAlias    = $viewAlias;
                    $this->bodyContent  = file_get_contents($viewFile);
		    
                    //Faz a inclusão de arquivos css e js com o mesmo nome da view atual, caso existam.
                    try {
                        $objHeader->memoSetFile($objHeader::EXT_CSS,$viewAlias);
                        $objHeader->memoSetFile($objHeader::EXT_JS,$viewAlias);                        
                    } catch(\Exception $e){
                        $this->showErr('View()',$e,FALSE); 
                    }   
                } catch(\Exception $e){
                    $this->showErr('Erro ao instanciar a view solicitada',$e);                    
                }              
            } else {
                $this->showErr('Impossível continuar. O nome da view não foi informado',$e);                
            }
            
            $tpl     = (strlen($tplName) == 0)?'padrao':$tplName; 
            $tplFile = __APP__ . '/views/templates/'.$tpl.'.html';  
            
            try {                                                 
                $this->checkUrlFile($tplFile); 
                $this->tplContent = file_get_contents($tplFile);
            } catch(\Exception $e){
                $this->showErr('Erro ao verificar o template solicitado ('.$tplFile.')',$e);                 
            }
            
            $this->objHeader    = $objHeader;
            $this->viewFile     = $viewFile;
            $this->tplFile      = $tplFile;
        }   
        
        /**
         * Define o comportamento para a criação dos arquivos de include (css e js).
         * 
         * Se $action = TRUE, força a criação dos arquivos de inclusão (sufixo _min) mesmo que já existam.
         * Se $action = FALSE, gera novos arquivos _min apenas se ainda não existirem.
         * 
         * @param boolean $action
         */
        function setMinify($action){
            $objHeader = $this->getObjHeader();
            if (is_object($objHeader)){
                if (is_bool($action)) {
                    $objHeader->forceMinifyOn();
                } else {
                    $objHeader->forceMinifyOff();
                }
            }
        }
        
        private function showErr($msg,$e,$die=TRUE){
            $msgErr = "<b>".$msg.':</b><br/><br/>'.$e->getMessage();
            if ($die) die($msgErr);
            echo $msgErr.'<br/><br/>';
        } 
        
        private function getObjHeader(){
            $objHeader = $this->objHeader;
            if (!is_object($objHeader)) die('View->getObjHeader(): O objeto Header solicitado não foi localizado.');
            return $objHeader;
        }
        
        private function checkUrlFile($urlFile=''){
            if (!file_exists($urlFile)) {
                $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'FILE_NOT_EXISTS'); 
                $msgErr = str_replace('{FILE}',$urlFile,$msgErr);
                throw new \Exception( $msgErr );  
            }
            return TRUE;
        }
        
        function setPlugin($plugin){
           if (strlen($plugin) > 0){
               $arr = Plugin::$plugin();                 
               if (is_array($arr) && count($arr) > 0){ 
                   $objHeader = $this->getObjHeader();
                   try {
                        foreach($arr as $ext=>$listInc){
                            $objHeader->memoIncludeJsCss($listInc, $ext);
                        }
                   } catch(\Exception $e){
                       $this->showErr('Erro ao incluir o Plugin solicitado ('.$plugin.')',$e);      
                   }
               } else {
                   echo 'Plugin não retornou dados de inclusão (css | js).';
               }
           } 
        }  
        
        private function getIncludesCss(){
            $objHeader  = $this->getObjHeader();
            $inc        = $this->getIncludes($objHeader::EXT_CSS);
            $inc        .= $this->getIncludes($objHeader::EXT_CSS_INC,FALSE);
            return $inc;
        }
        
        private function getIncludesJs(){
            $objHeader  = $this->getObjHeader();
            $inc        = $this->getIncludes($objHeader::EXT_JS);
            $inc        .= $this->getIncludes($objHeader::EXT_JS_INC,FALSE);
            return $inc;
        }
        
        private function getIncludes($ext){               
           $objHeader   = $this->getObjHeader();           
           return $objHeader->getTags($ext);
        } 
        
        function render(){            
            $css                       = $this->getIncludesCss();
            $js                        = $this->getIncludesJs();
            $tplContent                = $this->tplContent;
            $bodyContent               = $this->bodyContent;
            $params                    = $this->params;
            $params['INCLUDE_CSS']     = $css;
            $params['INCLUDE_JS']      = $js;
            $tplContent                = str_replace('{BODY}',$bodyContent,$tplContent);     
            if (is_array($params)) {
                foreach($params as $key=>$value){
                    $tplContent = str_replace('{'.$key.'}',$value,$tplContent);                
                }
            }            
            echo $tplContent;
        }
        
        function __set($var,$value){
            $this->params[$var] = $value;
        }
    }
?>
