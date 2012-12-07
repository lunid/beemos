<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Setup
 *
 * @author Supervip
 */
use \sys\lib\classes\LibComponent;
use \sys\classes\util\Dic;

require_once('sys/lib/yuiCompressor/classes/YUICompressor.php');

class YuiCompressor extends LibComponent {
    
    /**
     * Faz a compactação de uma string gravando o resultado em um arquivo externo.
     * Os formatos 
     *      
     * @return void
     * 
     * @throws \Exception Se uma extensão válida não for informada (valores permitidos: css, js).
     * @throws \Exception Se após a compactação de uma string válida de javascript o resultado for vazio.
     * @throws \Exception Se a tentativa de criar o arquivo de saída falhar.
     * @throws \Exception Se após a sua criação, o arquivo de saída possuir tamanho 0kb.
     */
    function init(){	
            $pathXmlDic     = $this->getXmlDic();
            $ext            = $this->extension;
            $strInc         = $this->string; 
            $outFileMin     = $this->fileNameMin;
            
            $root           = 'sys/lib/yuiCompressor/src/yuicompressor-2_4_8/';                                    
            $pathJar        = $root.'build/yuicompressor-2_4_8pre.jar'; 
            
            $ext         = strtolower($ext);//Extensão do arquivo (css ou cs)
            $strInc      = (string)$strInc;//String a ser compactada.
           
            $this->setReturn(FALSE);           
            
            if (strlen($strInc) == 0) return;                       
            
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
                        //Não foi possível gerar o arquivo compactado.
                        $msgErr = Dic::loadMsgForXml($pathXmlDic,__METHOD__,'DIR_NOT_FOUND');
                        $msgErr = str_replace('{FILE}',$outFileMin,$msgErr);
                        throw new \Exception( $msgErr );                           
                    }
          
                    if (!file_exists($outFileMin)){
                        $msgErr = Dic::loadMsgForXml($pathXmlDic,__METHOD__,'ERR_FILE_INC');
                        $msgErr = str_replace('{FILE}',$outFileMin,$msgErr);
                        throw new \Exception( $msgErr );                           
                    }

                    $size = filesize($outFileMin);
                    if ($size == 0){
                        $msgErr = Dic::loadMsgForXml($pathXmlDic,__METHOD__,'ERR_FILE_SIZE_ZERO');
                        $msgErr = str_replace('{FILE}',$outFileMin,$msgErr);
                        $msgErr = str_replace('{STR_MIN}',$strIncMin,$msgErr);
                        throw new \Exception( $msgErr );                                                                         
                    }                                          
                } elseif (strlen($strIncMin) == 0 && $ext == 'js') {
                    //O conteúdo compactado está vazio e o trata-se de um conteúdo de javascript.
                    $msgErr = Dic::loadMsgForXml($pathXmlDic,__METHOD__,'ERR_JS_COMPRESS');
                    $msgErr = str_replace('{FILE}',$outFileMin,$msgErr);
                    throw new \Exception( $msgErr );              
                }
                //return $strIncMin;
            } else {                            
                $msgErr     = Dic::loadMsgForXml($pathXmlDic,__METHOD__,'ERR_EXT');
                $msgErr     = str_replace('{EXT}',$ext,$msgErr);
                throw new \Exception( $msgErr );     
            }
            
            $this->setReturn(TRUE);   
        }
}

?>
