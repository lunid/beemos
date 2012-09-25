<?php

    require_once('sys/classes/comps/files/YUICompressor.php');
    require_once('sys/classes/comps/HtmlComponent.php');
    require_once('sys/classes/comps/ChartComponent.php');
    require_once('sys/classes/db/Meekrodb_2_0.php');
    require_once('sys/classes/db/Conn.php');
    require_once('sys/classes/mvc/Controller.php');
    require_once('sys/classes/mvc/Model.php');           


    class Application {
               
        public static function load(){
            
            $arrModules   = array('app','admin');//Root dos módulos disponíveis
            $module       = $arrModules[0];
            
            $controllerClass = 'index';
            $actionMethod    = 'index';

            $var        = (isset($_GET['PG']))?$_GET['PG']:'';    
            $arrParams  = explode('/',$var);

            if (is_array($arrParams) && count($arrParams) > 0) {       
                $paramController    = (isset($arrParams[0]) && $arrParams[0] != null)?$arrParams[0]:'';
                $keyModule          = array_search($paramController,$arrModules);
                if ($paramController != null && $keyModule === NULL) {                    
                    $controllerClass = $arrParams[0];
                    if (isset($arrParams[1]) && $arrParams[1] != null) $actionMethod = $arrParams[1];
                } else {
                    //Acesso a um módulo específico.
                    $module = $arrModules[$keyModule];
                }                                
            }         

            //Define a constante __APP__
            self::defineApp($module);
            
            $urlFileController = __APP__ . '/controllers/'.ucfirst($controllerClass).'Controller.php';
            if (!file_exists($urlFileController)) die('Arquivo de inclusão '.$urlFileController.' não localizado');

            require_once($urlFileController);

            Conn::init();
        }
        
        private static function defineApp($module){
            define("__APP__", $module);            
        }
    }
?>
