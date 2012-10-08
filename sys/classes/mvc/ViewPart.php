<?php

    namespace sys\classes\mvc;
    use \sys\classes\util\File;
    
    class ViewPart {               
        
        protected   $bodyContent;
        protected   $viewFile;
        protected   $layoutName;
        protected   $params = array();
        
        function __construct($pathViewHtml=''){
            if (isset($pathViewHtml) && strlen($pathViewHtml) > 0) {            
                $arrParts           = explode('/',$pathViewHtml);
                $numParts           = count($arrParts);
                $this->layoutName   = (is_array($arrParts) && $numParts > 1)?$arrParts[$numParts-1]:$pathViewHtml; 
                $keyHtm             = strpos($pathViewHtml,'.htm');
                $keyHtml            = strpos($pathViewHtml,'.html');
                $extHtml            = ($keyHtm !== false && $keyHtml !== false)?'':'.html';//Coloca a extensão html caso não tenha sido informada
                $module             = \Application::getModule();
                $folderViews        = \LoadConfig::folderViews();                
                $viewFile           = $module.'/'.$folderViews.'/'.$pathViewHtml.$extHtml;    
                
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
                die('ViewPart(): Impossível continuar. O nome referente ao conteúdo HTML não foi informado'); 
            }
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
            $params          = $this->params;                      
            
            if (is_array($params)) {
                foreach($params as $key=>$value){
                    $bodyContent = str_replace('{'.$key.'}',$value,$bodyContent);                
                }
            }            
            return $bodyContent;
        }        
        
        function __set($var,$value){
            $this->params[$var] = $value;
        }
        
    }
?>
