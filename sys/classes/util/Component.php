<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Classe responsável por inicializar um componente.
 * O endereço padrão para 
 *
 * @author Supervip
 */
namespace sys\classes\util;
use \sys\classes\util\Dic;

class Component {
    
    /**
     * Chama um método estático do componente solicitado.
     * 
     * IMPORTANTE:
     * Caso o Memcache esteja ativo no php o objeto será guardado em cache.
     * O tempo de vida padrão estipulado para o cache é de 30 dias.
     * 
     * Exemplo que mostra a utilização do componente YuiCompressor:
     * <code>     
     *  $arrParams['string']        = 'Texto de exemplo';
     *  $arrParams['extension']     = 'js';
     *  $arrParams['fileNameMin']   = 'assets/app/js/_teste_min.js';
     * 
     *  //O nome do método estático deve ser a pasta onde se encontra o componente em sys/lib/.
     *  if (Component::yuiCompressor($arrParams)){
     *      echo 'Arquivo gerado com sucesso.';     
     *  } else {
     *      echo 'Erro ao comprimir o arquivo';
     *  }
     * </code>
     * 
     * @param string $folder Pasta que contém o componente a ser inicializado.
     * @param mixed $args Array associativo contendo as variáveis usadas no método init() do objeto.
     * @return mixed
     * 
     * @throws \Exception Caso o arquivo de inicialização do Componente não seja localizado.
     */
    public static function __callStatic($folder,$args=array()){
        $class      = 'YuiCompressor';
        $classPath  = 'sys/lib/'.$folder.'/classes/Lib'.$class.'.php';              
        if (file_exists($classPath)){
            include_once($classPath);
            $cacheName  = $folder.'_'.$class;
            $objCache   = new Cache($cacheName);
            $objComp    = $objCache->getCache();
            //var_dump($objComp);
            if (!is_object($objComp)) {
                $objComp = new $class($folder,$args);
                $objCache->setDay(30);//30 dias de cache
                $objCache->setCache($objComp);
            } else {
                $objComp->setArgs($args);                
            }
            $objComp->init();           
            return $objComp->getReturn();
        } else {
            //Arquivo não existe
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'FILE_NOT_EXISTS');
            $msgErr = str_replace('{FILE}',$classPath,$msgErr);
            throw new \Exception( $msgErr );                
        }
    }
}

?>
