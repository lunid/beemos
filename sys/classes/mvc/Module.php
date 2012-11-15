<?php

    class Module {
        
        private $module;
        
        function __construct(){
            $this->module = \Application::getModule();
        }

        function viewPartsLangFile($path){
            $rootModule     = $this->viewPartsFile('');
            $lang           = \Application::getLanguage();
            if (strlen($lang) > 0) $rootModule = $rootModule.$lang.'/';
            
            $url            = $rootModule.$path;
            
            return $url;
        }
        
        function viewPartsFile($path){
            $module       = $this->module;
            $folderViews  = \LoadConfig::folderViews();//viewParts
            $url          = $module.'/'.$folderViews.'/'.$path;
            return $url;
        }
    }
?>
