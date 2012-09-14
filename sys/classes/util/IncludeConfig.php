<?php

    namespace sys\classes\util;
    class IncludeConfig {
        protected static function setHeaderInc($atrib,$value,$arrHeaderInc=array()){        
            //$atrib = strtolower($atrib);        
            if (($atrib == 'js' || $atrib == 'css' || $atrib == 'jsInc' || $atrib == 'cssInc') && strlen($value) > 0){               
                $arrHeaderInc[$atrib]   = $value;
            }
            return $arrHeaderInc;
        }
        protected static function setUri($root,$inc){
            $inc    = $root.(string)$inc;//Concatena a raiz do plugin e converte o nó xml para string
            $inc    = str_replace(',',','.$root,$inc);//inclui a raiz no endereço de cada item da lista
            $inc    = 'plugin:'.str_replace('/','.',$inc);//inclui o prefixo 'plugin' no início da string
            $inc    = str_replace(',',',plugin:',$inc);//inclui o prefixo 'plugin' para os demais itens da lista
            return $inc;
        }
        protected static function loadConfig($rootXmlInstall=''){
            $rootXmlInstall .= '/';
            $rootXmlInstall = str_replace('//','/',$rootXmlInstall);             
            $xmlInstall     = $rootXmlInstall.'install.xml';
            if (file_exists($xmlInstall)){        
                $arrHeaderInc    = array();
                $objXml          = @simplexml_load_file($xmlInstall);   
                $arrNodes        = $objXml->config->include;
                    if (is_object($objXml) && (is_object($arrNodes) || is_array($arrNodes))){
                    // print_r($arrNodes);
                        //echo count($arrNodes);
                        if (count($arrNodes) > 1){
                            foreach($arrNodes as $inc){              
                                //Existe mais de um tipo de include (js e css)                                 
                                $atrib          = (string)$inc->attributes();
                                $inc            = self::setUri($rootXmlInstall,$inc);                                
                                $arrHeaderInc   = self::setHeaderInc($atrib,$inc,$arrHeaderInc);                           
                            }                             
                        } elseif (count($arrNodes) > 0) {
                            //Existe um único tipo de include (js ou css)
                            $atrib          = (string)$arrNodes->attributes();
                            $inc            = self::setUri($rootXmlInstall,$arrNodes);               
                            $arrHeaderInc   = self::setHeaderInc($atrib,$inc);                        
                        } else {
                            echo htmlentities(utf8_decode(__FUNCTION__.': O arquivo '.$xmlInstall.' está incorreto ou não possui tags <include>'));
                        }
                        return $arrHeaderInc;
                    }
            } else {
                echo __FUNCTION__.": Impossível carregar o plugin. Arquivo de configuração $xmlInstall não localizado.";
            }         
            return FALSE;
        }
    }
?>
