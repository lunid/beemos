<?php

/*
 * Classe que trata mensagens de erro da aplicação disparadas com trhow.
 * O arquivo de dicionário é o XML /app/dic/backend_exception.xml.
 * 
 */
    namespace sys\classes\util;    
    
    class Dic extends Xml {
        
        /**
         * Recebe um nome de classe ou método na variável $item, retira o namespace e retorna 
         * o conteúdo de $item sem o namespace.
         * 
         * @param string $item Pode ser o nome de uma classe com namespace (Ex:) ou 
         * o nome de um método com namespace
         * @param string $ns Namespace a ser excluído da variável $item
         * @return string
         */
        public static function getNameItem($item,$ns){
            $out = str_replace($ns,'',$item);
            return $out;            
        }
 
        public static function loadMsg($class,$func,$ns,$codMsg=''){
            $msgErr  = ''; 
            $msg     = '';
            
            //Retira o namespace da variável $class e $func
            $class          = self::getNameItem($class,$ns.'\\');
            $func           = ($func == NULL)?'default':self::getNameItem($func,$ns.'\\'.$class.'::');            
            $fileException  = 'app/dic/e'.$class.'.xml';
           
            $method = __CLASS__.'\\'.__FUNCTION__."()";//Monta uma string ref. ao método atual. Usado para mostrar erro do método setErr()
            
            $pathXml = $fileException;
            if (!file_exists($fileException)) {
                //Verifica na pasta sys
                $fileException  = str_replace('app/','sys/',$fileException);
                $pathXml        = (!file_exists($fileException))?'sys/dic/exception.xml':$fileException;
            }            
           
            $objXml = self::loadXml($pathXml);
            if (is_object($objXml)) {
                $nodes      = $objXml->$class->$func->msg;
                $numItens    = count($nodes);
                
                if ($numItens > 0){
                    $value  = self::valueForAttrib($nodes,'id',$codMsg);
                    $msg    = (strlen($value) > 0)?$value:'Erro desconhecido';
                } else {
                    //Nenhuma mensagem foi localizada no XML informado.
                    $msgErr  = 'O arquivo '.$pathXml.' existe, porém o Xml Node com a mensagem '.$codMsg.' não foi localizado.';
                }
            } else {
                $msgErr = 'Impossível ler o arquivo '.$pathXml;
            }
            
            if (strlen($msgErr) > 0) self::setErr($method, $msgErr);
            return $msg;                                    
        }
        
        /**
         * Mostra um erro de execução originário na classe Dic.
         * 
         * @param string $method Nome do método que fez a chamada
         * @param type $msgErr Mensagem de erro a mostrar na tela
         */
        public static function setErr($method,$msgErr){
            die($method." : $msgErr");
        }
    }
?>
