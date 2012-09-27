<?php
    session_start();
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);    
    
    /*
    define("__APP__", "app");

    
    $controllerClass = 'index';
    $actionMethod    = 'index';
    
    $var        = (isset($_GET['PG']))?$_GET['PG']:'';    
    $arrParams  = explode('/',$var);

    if (is_array($arrParams) && count($arrParams) > 0) {       
        if (isset($arrParams[0]) && $arrParams[0] != null) $controllerClass  = $arrParams[0];
        if (isset($arrParams[1]) && $arrParams[1] != null) $actionMethod     = $arrParams[1];
    } else {
        
    }         
    
    $urlFile = __APP__ . '/controllers/'.ucfirst($controllerClass).'Controller.php';
    if (!file_exists($urlFile)) die('Arquivo de inclusão '.$urlFile.' não localizado');

    require_once('sys/classes/comps/files/YUICompressor.php');
    require_once('sys/classes/comps/HtmlComponent.php');
    require_once('sys/classes/comps/ChartComponent.php');
    require_once('sys/classes/db/Meekrodb_2_0.php');
    require_once('sys/classes/db/Conn.php');
    require_once('sys/classes/mvc/Controller.php');
    require_once('sys/classes/mvc/Model.php');    
    require_once($urlFile);
    
    Conn::init();
    
    */
    
    /**
     *Localiza a classe solicitada de acordo com o seu namespace e faz o include do arquivo.
     * @param String $class (nome da classe requisitada).
     * Não retorna valor.
     */
    spl_autoload_register('loadClass');	
    
    function loadClass($class) {	
        $urlInc = str_replace("\\", "/" , $class . '.php');                
        if (isset($class) && file_exists($urlInc)){          
            require_once($urlInc);  
        } else {           
            die(" Classe $class não encontrada");
        }       
    }    
    
    $actionMethod   = 'action'.ucfirst($actionMethod);
    $objPg          = new $controllerClass;
    $objPg->$actionMethod();
