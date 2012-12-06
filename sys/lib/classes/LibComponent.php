<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Lib
 *
 * @author Supervip
 */
namespace sys\lib\classes;
use \sys\lib\classes\IComponent;

abstract class LibComponent implements IComponent{
    private $args           = array();
    private $exceptionFile  = '';
    private $return         = NULL;
    
    function __construct($folder,$args=array()){
        $folderSys = \LoadConfig::folderSys();
        echo $folderSys . 'fdsf';
        die();
        $exceptionFile = 'sys/lib/'.$folder.'/dic/exception.xml';
        if (file_exists($exceptionFile)){            
            $this->exceptionFile = $exceptionFile;
        }
        $this->setArgs($args);
    }
    
    function setArgs($args){
        if (is_array($args) && count($args) > 0) $this->args = $args[0];
    }
    
    /**
     * Retorna o xml referente às mensagens de Exception da lib atual.
     * 
     * @return string Caminho relativo do arquivo XML. 
     */
    function getXmlDic(){
        return $this->exceptionFile;
    }
    
    function __get($var){
        $args   = $this->args;
        $value  = '';             
        if (array_key_exists($var, $args)) {
            $value = $args[$var];
        }                
        return $value;
    }
    
    
    protected function setReturn($return){
        $this->return = $return;
    }
    
    /**
     * Retorna
     * @return type 
     */
    function getReturn(){
        return $this->return;
    }    
}

?>
