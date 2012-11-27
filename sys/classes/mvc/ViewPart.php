<?php

    namespace sys\classes\mvc;
    use \sys\classes\util\File;
    
    class ViewPart {               
        
        protected   $bodyContent;
        protected   $viewFile;//path do arquivo estático usado como view.
        protected   $layoutName;
        protected   $params = array();
        
        function __construct($pathViewHtml=''){
            if (isset($pathViewHtml) && strlen($pathViewHtml) > 0) {            
                $arrParts           = explode('/',$pathViewHtml);
                $numParts           = count($arrParts);
                $this->layoutName   = (is_array($arrParts) && $numParts > 1)?$arrParts[$numParts-1]:$pathViewHtml; 
                $keyHtm             = strpos($pathViewHtml,'.htm');//Verifica se o path possui extensão .htm
                $keyHtml            = strpos($pathViewHtml,'.html');//Verifica se o path possui extensão .html
                $extHtml            = ($keyHtm !== false && $keyHtml !== false)?'':'.html';//Coloca a extensão html caso não tenha sido informada
                $lang               = \Application::getLanguage();
                $module             = \Application::getModule();
                $folderViews        = \LoadConfig::folderViews();      
                
                if (strlen($lang) > 0) $lang = $lang.'/';
                $viewFile = $module.'/'.$folderViews.'/'.$lang.$pathViewHtml.$extHtml;    
                
                try {                    
                    if (File::exists($viewFile)){
                        //Arquivo existe.
                        $this->bodyContent  = file_get_contents($viewFile);
                        $this->viewFile     = $viewFile;       
                    }
                } catch(\Exception $e){
                    $this->showErr('Erro ao instanciar a view solicitada -> '.$viewFile,$e);                    
                } 
            } else {
                //die('ViewPart(): Impossível continuar. O nome referente ao conteúdo HTML não foi informado'); 
            }
        }
        
        /**
         * Define uma string como conteúdo da viewPart.
         * Método utilizado geralmente quando não há um arquivo físico de conteúdo informado no construtor.
         *  
         * @param string $content Conteúdo da ViewPart.
         */
        function setContent($content){
            $this->bodyContent = (string)$content;
        }
        
        protected function showErr($msg,$e=NULL,$die=TRUE){
            $msgErr = "<b>".$msg.':</b><br/><br/>';
            if (is_object($e)) $msgErr .= $e->getMessage();
            if ($die) die($msgErr);
            echo $msgErr.'<br/><br/>';
        } 
        
        
        function render($layoutName=''){  
            if (isset($layoutName) && strlen($layoutName) > 0) $this->layoutName = $layoutName;
            $bodyContent    = $this->bodyContent;
            if (strlen($bodyContent) > 0) {
                $params          = $this->params;                      

                if (is_array($params)) {
                    foreach($params as $key=>$value){
                        $bodyContent = str_replace('{'.$key.'}',$value,$bodyContent);                
                    }
                }
            }
            return $bodyContent;            
        }        
        
        function __set($var,$value){
            $this->params[$var] = $value;
        }
        
    }
?>
