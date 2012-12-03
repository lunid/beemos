<?php

    namespace sys\classes\comps;   
    use \sys\classes\util\Dic;
    
    class Component {	
        
        public static function yuiCompressor($strInc,$ext,$outFileMin=''){	
            $root        = 'sys/components/yuicompressor-2_4_8/';                        
            //$pathJar     = $root.'build/yuicompressor-2.4.7.jar'; 
            $pathJar     = $root.'build/yuicompressor-2_4_8pre.jar'; 
            
            $ext         = strtolower($ext);
            $strInc      = (string)$strInc;            
            if (strlen($strInc) == 0) return FALSE;
            
            if (($ext == 'js' || $ext == 'css')) {            
                //Comprime a string:
                $strIncMin      = '';
                $pathTmp        = $root.'tmp/';
                \Minify_YUICompressor::$jarFile  = realpath($pathJar);
                \Minify_YUICompressor::$tempDir  = $pathTmp; 
                
                if (!is_dir($pathTmp)) mkdir($pathTmp);
                
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

                    $fp = @fopen($outFileMin, "wb+");   
                    if ($fp !== FALSE) {
                        fwrite($fp, $strIncMin);
                        fclose($fp);
                    } else {
                        $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'DIR_NOT_FOUND'); 
                        $msgErr = str_replace('{FILE}',$outFileMin,$msgErr);
                        throw new \Exception( $msgErr );                           
                    }
          
                    if (!file_exists($outFileMin)){
                        $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_FILE_INC'); 
                        $msgErr = str_replace('{FILE}',$outFileMin,$msgErr);
                        throw new \Exception( $msgErr );                           
                    }

                    $size = filesize($outFileMin);
                    if ($size == 0){
                        $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_FILE_SIZE_ZERO'); 
                        $msgErr = str_replace('{FILE}',$outFileMin,$msgErr);
                        $msgErr = str_replace('{STR_MIN}',$strIncMin,$msgErr);
                        throw new \Exception( $msgErr );                                                                         
                    }                      
                    return TRUE;
                } elseif (strlen($strIncMin) == 0 && $ext == 'js') {
                    $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_JS_COMPRESS'); 
                    $msgErr = str_replace('{FILE}',$outFileMin,$msgErr);
                    throw new \Exception( $msgErr );              
                }
                return $strIncMin;
            } else {
                $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_EXT'); 
                $msgErr = str_replace('{EXT}',$ext,$msgErr);
                throw new \Exception( $msgErr );     
            }
            return TRUE;
        }
    }
?>

