<?php

    namespace sys\classes\comps;
    
    class Component {	
        
        public static function yuiCompressor($strInc,$ext,$outFileMin=''){	
            $root        = 'sys/components/yuicompressor-2_4_7/';            
            $pathJar     = $root.'build/yuicompressor-2.4.7.jar';    
            $ext         = strtolower($ext);
            $strInc      = (string)$strInc;            

            if ($ext == 'js' || $ext == 'css' && strlen($strInc) > 0) {            
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

                if (strlen($outFileMin) > 0){
                    //Gera um arquivo físico com o conteúdo compactado:
                    $fp     = fopen($outFileMin, "wb+");                
                    fwrite($fp, $strIncMin);
                    fclose($fp);

                    if (!file_exists($outFileMin)){
                        die('O arquivo de inclusão '.$outFileMin.' não pôde ser gerado.');
                    }

                    $size = filesize($outFileMin);
                    if ($size == 0){
                        die('O arquivo '.$outFileMin.' foi gerado porém está vazio.<br><br>'.$strIncMin);
                        return FALSE;
                    }
                    return TRUE;
                } 
                return $strIncMin;
            } else {
                die('yuiCompressor não pôde ser executado porque o parâmetro $ext não foi informado corretamente.');
            }
        }
    }
?>

