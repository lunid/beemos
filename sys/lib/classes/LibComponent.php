<?php
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
        $folderSys      = \LoadConfig::folderSys();
        $exceptionFile  = $folderSys.'/lib/'.$folder.'/dic/exception.xml';
        
        //Define o path do arquivo XML usado como dicionário das mensagens de Exception.
        if (file_exists($exceptionFile)) $this->exceptionFile = $exceptionFile;
        
        $this->setArgs($args);
    }
    
    /**
     * Armazena um array associativo de parâmetros a ser usados como variáveis
     * na execução do método init(), da classe de inicialização do componente.
     * 
     * Estes parâmetros são opcionais
     * 
     * @param mixed[] $args 
     */
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
    
    /**
     * Método mágico para acessar os parâmetros recebidos em setArgs() como variáveis de objeto.
     * 
     * @param string $var Nome da variável requisitada.
     * @return mixed Valor da variável. 
     */
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
     * Retorna o resultado gerado no método init() da classe de inicialização do componente.
     * 
     * @return mixed 
     */
    function getReturn(){
        return $this->return;
    }    
}

?>
