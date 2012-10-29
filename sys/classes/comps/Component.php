<?php

    namespace sys\classes\comps;   
    
    class Component {	
        
        public static function yuiCompressor($strInc,$ext,$outFileMin=''){	
            $root        = 'sys/components/yuicompressor-2_4_8/';                        
            //$pathJar     = $root.'build/yuicompressor-2.4.7.jar'; 
            $pathJar     = $root.'build/yuicompressor-2_4_8pre.jar'; 
            
            $ext         = strtolower($ext);
            $strInc      = (string)$strInc;            

            if (($ext == 'js' || $ext == 'css') && strlen($strInc) > 0) {            
                //Comprime a string:
                $strIncMin   = '';
                \Minify_YUICompressor::$jarFile  = realpath($pathJar);
                \Minify_YUICompressor::$tempDir  = $root.'tmp/'; 
                               
                try {
                    if ($ext == 'js'){
                        //Javascript                        
                        $strIncMin = \Minify_YUICompressor::minifyJs($strInc,array('nomunge' => true, 'line-break' => 1000));                   
                    } else {
                        //css
                        $strIncMin = \Minify_YUICompressor::minifyCss($strInc,array('nomunge' => true, 'line-break' => 2000));                   
                    }                                         
                } catch(\Exception $e){
                    die($e->getMessage()); 
                }         
                                
                if (strlen($strIncMin) > 0 && strlen($outFileMin) > 0){
                    //Gera um arquivo físico com o conteúdo compactado:                       
                    $dirName    = dirname($outFileMin);
                    $dirName    = \Url::relativeUrl($dirName);
     
                    if (!is_dir($dirName)) mkdir($dirName,'0777');
                    
                    $fp = fopen($outFileMin, "wb+");                
                    fwrite($fp, $strIncMin);
                    fclose($fp);
                
                    if (!file_exists($outFileMin)){
                        die('O arquivo de inclusão '.$outFileMin.' não pôde ser gerado.');
                    }

                    $size = filesize($outFileMin);
                    if ($size == 0){
                        $msgErr = 'Component->yuiCompressor(): O arquivo '.$outFileMin.' foi gerado porém está vazio.<br><br>'.$strIncMin;
                        throw new \Exception( $msgErr );                                                                        
                    }                      
                    return TRUE;
                } elseif (strlen($strIncMin) == 0 && $ext == 'js') {
                    $msgErr = 'Component->yuiCompressor(): Impossível comprimir o arquivo '.$outFileMin.' porque a compressão retornou vazio.';
                    throw new \Exception( $msgErr );          
                }
                return $strIncMin;
            } else {
                $msgErr = 'Component->yuiCompressor(): yuiCompressor não pôde ser executado porque o parâmetro $ext não foi informado corretamente.';
                throw new \Exception( $msgErr );         
            }
            return TRUE;
        }
    }
?>

