<?php

    namespace sys\classes\mvc;
    use \sys\classes\util\Dic;
    use \sys\classes\util\Plugin;
    use \sys\classes\mvc\Header;
    use \sys\classes\util\File;
    use \sys\classes\mvc\ViewPart;
    
    class View extends ViewPart {

        private $objHeader      = NULL;        
        private $tplFile        = '';           
        private $forceNewIncMin = FALSE;
        private $pathTpl        = '';
        
        function __construct(){                            
            $this->init();
        } 
        
        /**
         * Inicializa o template definido em config.xml
         * 
         * Pode conter também outros recursos de inicialização da View.
         * return void 
         */
        function init(){            
            $fileTpl = \LoadConfig::defaultTemplate();                 
            $this->setTemplate($fileTpl);
        }
        
        /**
         * Método usado para gerar um link para um controller/action no módulo atualmente ativo.
         *
         * @param string $controller Nome do controller. Ex,.: usuarios
         * @param string $action Nome do método (geralmente refere-se a uma página) a ser executado. Ex.: pedidos.
         *  
         * @return string
         */
        function setModuleUrl($controller='',$action=''){
            $module = \Application::getModule();
            return $this->setUrl($module,$controller,$action);
        }
        
        /**
         * Método usado para gerar um link para um module/controller/action.
         * 
         * @param string $module Nome do módulo.
         * @param string $controller Nome do controller. Ex,.: usuarios.
         * @param string $action Nome do método (geralmente refere-se a uma página) a ser executado. Ex.: pedidos.
         * 
         * @return string
         */        
        function setUrl($module='',$controller='',$action=''){           
           $arrUrl = array('module'=>$module,'controller'=>$controller,'action'=>$action);           
           $url = \Url::setUrl($arrUrl);               
           return $url;
        }        
        
        function setTemplate($fileTpl=''){
            $pathTpl = '';
            if (strlen($fileTpl) > 0) {
                $folderTpl   = \LoadConfig::folderTemplate();                  
                $pathTpl     = $folderTpl.'/'.$fileTpl;                
                $pathTpl     = str_replace('//', '/', $pathTpl);                            
            }
            
            $this->pathTpl  = $pathTpl;
        }
        
        function setLayout(ViewPart $objViewPart){
            if (is_object($objViewPart)) {                       
                $pathTpl                        = $this->pathTpl;                                                
                $objViewTpl                     = new ViewPart($pathTpl);
                $objViewTpl->BODY               = $objViewPart->render();
                
                //if($tplName == 'padrao'){
                    //$objViewTpl->BARRA_TOPO = \HtmlComponent::barraTopo();
                //}
                
                $this->bodyContent              = $objViewTpl->render();
                $this->layoutName               = $objViewPart->layoutName;                
          
                if (strlen($pathTpl) > 0){    
                    
                    //Configurações lidas do arquivo config.xml:                    
                    $plugins    = \LoadConfig::plugins();                     
                    $objHeader  = new Header();            
                     
                    //Inclusões css e js:
                    $arrExt     = $objHeader::$arrExt;
                    foreach($arrExt as $fn) {
                        $list   = \LoadConfig::$fn();                
                        $objHeader->$fn($list);                        
                    }                  
                                                           
                    //Faz a inclusão de arquivos css e js padrão.
                    try {                        
                        //$objHeader->memoSetFile($objHeader::EXT_CSS,self::CSS,FALSE);
                        //$objHeader->memoSetFile($objHeader::EXT_JS,self::JS,FALSE);                        
                        $this->objHeader = $objHeader;                                                                                           
                        
                        //Plugins                                               
                        $arrPlugins = explode(',',$plugins);
                        if (is_array($arrPlugins)) {
                            foreach($arrPlugins as $plugin) {                                 
                                $this->setPlugin($plugin);
                            }
                        }
                    } catch(\Exception $e){
                        $this->showErr('View()',$e,FALSE); 
                    }                                                           
                }                
            } else {
                die('Impossível continuar. O objeto View não foi informado ou não é um objeto válido.');                
            }                        
        }
        
        private function getObjHeader(){
            $objHeader = $this->objHeader;            
            if (!is_object($objHeader)) $objHeader = new Header();
            return $objHeader;
        }        
               
        function setPlugin($plugin){
           if (strlen($plugin) > 0){
               $arr = Plugin::$plugin();                 
               if (is_array($arr) && count($arr) > 0){ 
                   $objHeader = $this->getObjHeader();
                   try {                       
                       foreach($arr as $ext=>$listInc){
                           //echo $listInc.'<br>';
                           $objHeader->memoIncludeJsCss($listInc, $ext);
                       }
                       $this->objHeader = $objHeader;
                   } catch(\Exception $e){
                       $this->showErr('Erro ao incluir o Plugin solicitado ('.$plugin.')',$e);      
                   }
               } else {
                   echo 'Plugin não retornou dados de inclusão (css | js).';
               }
           } 
        } 
        
        private function getIncludesCss(){            
            $inc        = $this->getIncludes(Header::EXT_CSS);
            $inc        .= $this->getIncludes(Header::EXT_CSS_INC);
            return $inc;
        }
        
        private function getIncludesJs(){            
            $inc        = $this->getIncludes(Header::EXT_JS);
            $inc        .= $this->getIncludes(Header::EXT_JS_INC);
            return $inc;
        }
        
        private function getIncludes($ext,$exception=TRUE){    
            try {
                $objHeader = $this->getObjHeader();           
                return $objHeader->getTags($ext,$this->layoutName);
            } catch(\Exception $e) {                                   
                throw $e;
            }
        }                
        
        function render($layoutName='',$objMemCache=NULL){            
            if (isset($layoutName) && strlen($layoutName) > 0) {
                $this->layoutName   = $layoutName;                
            }
            //$this->objHeader->getMemos();                        
            $css                       = $this->getIncludesCss();
            $js                        = $this->getIncludesJs();            
            $bodyContent               = trim($this->bodyContent);
            $params                    = $this->params;                                       
            $params['INCLUDE_CSS']     = $css;
            $params['INCLUDE_JS']      = $js;                                                                              
            
            if (is_array($params)) {
                foreach($params as $key=>$value){
                    $bodyContent = str_replace('{'.$key.'}',$value,$bodyContent);                
                }
            }
            if (is_object($objMemCache)) {
                //O cache foi ativado para o conteúdo atual. Armazena $bodyContent em cache.
                $objMemCache->setCache($bodyContent);
            }
            echo $bodyContent;
        } 
        
        function __call($fn,$args){
            $objHeader  = $this->getObjHeader(); 
            
            if (is_object($objHeader)){        
                $ext = '';                
                switch($fn){                
                    case 'setCss':
                        $ext = $objHeader::EXT_CSS;
                        break;
                    case 'setCssInc':
                        $ext = $objHeader::EXT_CSS_INC;
                        break;
                    case 'setJs':
                        $ext = $objHeader::EXT_JS;
                        break;
                    case 'setJsInc':
                        $ext = $objHeader::EXT_JS_INC;
                }
                                
                if (strlen($ext) > 0){
                    $listFiles = (isset($args[0]))?$args[0]:'';
                    if (strlen($listFiles) > 0) {
                        try {
                        $this->objHeader->memoIncludeJsCss($listFiles,$ext);  
                        } catch(\Exception $e){
                            $this->showErr('Erro ao memorizar arquivo(s) de inclusão(ões) css | js -> '.$listFiles,$e);                    
                        }
                    } else {
                       echo "Inclusão não realizada $listFiles<br>"; 
                    }
                } elseif ($fn == 'forceCssJsMinifyOn') {
                    //Força a compactação e junção dos includes (css e js), mesmo que o arquivo _min já exista.
                    $objHeader->forceCssJsMinifyOn();                       
                } elseif ($fn == 'forceCssJsMinifyOff') {
                    //Volta à situação padrão: apenas compacta e junta includes se o arquivo _min ainda não existir.
                    $objHeader->forceMinifyOff();     
                } elseif ($fn == 'onlyExternalCssJs') {
                    //Gera a página HTML com os includes (css e js) separados.
                    $objHeader->onlyExternalCssJs();  
                }
            }            
        }
    }
?>
