<?php

/*
 * Classe abastrata que contÃ©m os recursos comuns a todos os Controllers
 * @abstract
 */
    namespace sys\classes\mvc;
    use \sys\classes\util\Cache;
    
    abstract class Controller {
        
        private $memCache;
        private $nameCache;
        
        function __construct(){
             $this->nameCache = NULL;
        }                
        
        /*
         * MÃ©todo que recebe o nome do arquivo e um objeto cujos atributos 
         * representam as variÃ¡veis a concatenar.
         * 
         * @param $fileName (nome do arquivo HTML que servirÃ¡ de matriz para a pÃ¡gina solicitada).
         * @param $objParams (os atributos representam os parÃ¢metros usados para concatenar com o HTML de $fileName)
         * @return String
        */            
        protected function view($fileName,$objParams = NULL){
           
            $tpl            = $this->templateFile;            
            $arrPlugin      = $this->arrPlugin;
            $urlFile        = 'app/views/'.$fileName.'View.html';
            $urlTpl         = 'app/views/templates/'.$tpl.'.html';
            $arrJsCssInc    = $this->arrJsCssInc;
            
            if (count($arrJsCssInc['css']) == 0) $this->incCssJsFileName($fileName,'css');//Tenta incluir o css de $fileName            
            if (count($arrJsCssInc['js']) == 0) $this->incCssJsFileName($fileName,'js');//Tenta incluir o js de $fileName
            
            $this->checkUrlFile($urlFile);
            $this->checkUrlFile($urlTpl);                       
            
            $htmlTpl            = file_get_contents($urlTpl);
            $objParams->BODY    = utf8_encode(file_get_contents($urlFile));            
            if (is_object($objParams)) {
                foreach($objParams as $key=>$value){
                    $htmlTpl = str_replace('{'.$key.'}',$value,$htmlTpl);                
                }
            }
            
            //Faz a inclusÃ£o de JS e CSS dos plugins da pÃ¡gina:
            //==================================================================
            //Array multidimensional usado para agrupar os tipos de todos os plugins (extensÃ£o: js|css|jsInc|cssInc)
            //Ex: arrVarInc['css'][], arrVarInc['js'][]...
            $arrVarInc = array();
            
            //Faz a leitura de cada plugin e separa as strings de cada extensÃ£o. 
            //Guarda em $arrVarIn.
            if (is_array($arrPlugin) && count($arrPlugin) > 0){
                foreach($arrPlugin as $plugin){                    
                    foreach($plugin as $var=>$value){
                       $arrVarInc[$var][] = $value;   
                    }
                }                
            }
            
            //Agora, para cada extensÃ£o, faz o include e/ou concatena em um Ãºnico arquivo.
            if (count($arrVarInc) > 0){
                $this->outFileMin = get_class($this).'Plugin';
                foreach($arrVarInc as $ext => $arrValue){  
                    $strValue   = join(ViewInclude::$separadorList,$arrValue);
                    $this->$ext = $strValue;
                }
            }
            //==================================================================
            
            $arrHeaderInc   = $this->arrHeaderInc;
            if (is_array($arrHeaderInc) && count($arrHeaderInc) > 0){                
                foreach($arrHeaderInc as $key=>$value) {
                    $htmlTpl = str_replace('{'.$key.'}',$value,$htmlTpl);
                }
            }
            
            header( "Expires: ".gmdate("D, d M Y H:i:s", time() + (24 * 60 * 60)) . " GMT");//adiciona 1 dia ao tempo de expiração
            echo $htmlTpl;
        } 
        
        function cacheOn($action,$period='DAY',$time=30){            
            try {
                $nameCache          = $this->setNameCache($action);                
                $this->nameCache    = $nameCache;
                $memCache           = new Cache($nameCache);                   
                $content            = $memCache->getCache();
                $memCache->setTime($period,$time);            
                $this->memCache     = $memCache;
                if (strlen($content) > 0) {
                    //Um conteúdo ref. ao parâmetro action foi localizado.
                    die($content);
                }
            }catch(Exception $e){
                throw $e;
            }                        
        }
        
        function getMemCache(){
            return $this->memCache;
        }
        
        private function setNameCache($action){
            $nameCache      = \Application::getModule().'_'.$action;
            $nameCache      = str_replace('::','_',$nameCache); 
            return $nameCache;
        }        

        function cacheOff($action){
            if (strlen($action) > 0) {
                $nameCache  = $this->setNameCache($action);                    
                if (strlen($nameCache) > 0) {                    
                    $memCache = new Cache($nameCache);                    
                    $memCache->delete();
                }
                $this->memCache = NULL;
            }
        }
        
        function __set($var,$value){
           if (
                isset($value) && is_string($value) && 
                ($var == 'js' || $var == 'css' || $var == 'jsInc' || $var == 'cssInc')
           ){       
               if (strlen($value) == 0) return;
               
               //Identifica qual a inclusão solicitada (js ou css):
               //===============================================================
               $ext         = 'js';
               $tag         = 'INCLUDE_JS';
               $outFileMin  = $this->outFileMin;               
               $outFile     = (strlen($outFileMin) == 0)?get_class($this):$outFileMin;

               if ($var == 'css' || $var == 'cssInc'){
                   $ext = 'css';
                   $tag = 'INCLUDE_CSS';
               }
               //===============================================================
               
               $sep           = ViewInclude::$separadorList;
               $arrIncDefault = $this->arrIncDefault;
               if (isset($arrIncDefault[$var])) $value = $arrIncDefault[$var].$sep.$value;
               
               $include   = ($var == 'jsInc' || $var == 'cssInc')?TRUE:FALSE;                              
               $objInc    = new ViewInclude();
               
               $objInc->setInclude($include);//Se TRUE retorna o(s) include(s) do(s) arquivo(s) solicitado(s)
               $objInc->setList($value,$ext);
               
               $headerInc = $objInc->convert($outFile);//Retorna string com as tags <script> ou <link> para cada arquivo.         
               if ($headerInc !== FALSE) {
                   $this->arrHeaderInc[$tag]    .= $headerInc;
                   $arrInc                      = explode(',',$value);
                   foreach($arrInc as $inc)  $this->arrJsCssInc[$ext][] = $inc;
               }
           }
        }
    }
?>
