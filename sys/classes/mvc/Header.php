<?php

namespace sys\classes\mvc;
use \sys\classes\util\Dic;
use \sys\classes\comps\Component;

class Header {

    const EXT_JS                    = 'js';
    const EXT_CSS                   = 'css';
    const EXT_JS_INC                = 'jsInc';
    const EXT_CSS_INC               = 'cssInc';    
    static  $ROOT_VIEW_FILES        = 'app/views';
    static  $ROOT_SYS_FILES         = 'sys';        
    private static $arrExt          = array(self::EXT_CSS,self::EXT_CSS_INC,self::EXT_JS,self::EXT_JS_INC);     
    private $arrMemoIncludeJsCss    = array(self::EXT_JS=>array(),self::EXT_JS_INC=>array(),self::EXT_CSS_INC=>array(),self::EXT_CSS=>array());//Guarda todas as inclusÃµes js e css da pÃ¡gina atual
    var $arrIncDefault              = array();//Guarda as inclusÃµes default para todas as pÃ¡ginas (css, js, cssInc, jsInc).
    private $forceNewIncMin         = FALSE;
    
    function __construct($viewAlias){        
        $this->viewAlias = $viewAlias;
    }
    
    /**
     * Memoriza uma uri qualificada ($strFile) de acordo com a extensÃ£o ($ext) informada (js ou css)
     * 
     * Como exemplo, seguem alguns valores possÃ­veis para o parÃ¢metro uri:
     *  - home, $ext = js: o sistema entende como 'app/views/js/home.js'
     *  - menu.init, $ext = css: o sistema entende como 'app/views/css/menu/init.css'
     * 
     * @param string $ext Valores possÃ­veis: css,js,cssInc,jsInc
     * @param stirng $strFile String que contÃ©m o endereÃ§o de um arquivo
     * @return void
     * @throws \Exception Caso o arquivo fÃ­sico nÃ£o exista.
     */
    
    function memoSetFile($ext,$strFile){
        if (strlen($strFile) == 0) return;

        $root = self::$ROOT_VIEW_FILES;
        $arrUrlFile = explode(':',$strFile);
        $uri        = $strFile;
        if (is_array($arrUrlFile) && count($arrUrlFile) == 2){
            $root    = $arrUrlFile[0]; 
            $uri     = $arrUrlFile[1];
        }

        $file = str_replace('.','/',$uri);

        if ($root != 'plugin'){
            if ($root == 'sys') $root = self::$ROOT_SYS_FILES;                                            
            $file       = $root.'/'.$ext.'/'.$file;  	                                  
	}
	
        $extFile = $this->getExtFile($ext);            
        $file   .= '.'.$extFile;

        if (file_exists($file)){
            $this->arrMemoIncludeJsCss[$ext][]  = $file;                                           
        } else {                
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'FILE_NOT_EXISTS'); 
            $msgErr = str_replace('{FILE}',$file,$msgErr);
            $msgErr = str_replace('{STR_FILE}',$strFile,$msgErr);
            throw new \Exception( $msgErr );                                  
        }                       
    } 
    
    function forceMinifyOn(){
        $this->forceNewIncMin = TRUE;
    }
    
    function forceMinifyOff(){
        $this->forceNewIncMin = FALSE;
    }
    
    /**
     * Recebe uma extensÃ£o de inclusÃ£o (js, jsInc, css ou cssInc) e retorna uma extensÃ£o 
     * de arquivo vÃ¡lida (css ou js). Caso o parÃ¢metro $ext seja cssInc retornarÃ¡ css e se 
     * for jsInc retornarÃ¡ js.
     * 
     * @param string $ext (css, cssInc, js ou jsInc)
     * @return string Retorna 'css' ou 'js' 
     */
    private function getExtFile($ext){
        $extFile = $ext;
        if (strlen($ext) > 0){
            $this->checkExt($ext);
            $extFile = self::EXT_JS;
            if ($ext == self::EXT_CSS_INC || $ext == self::EXT_CSS) $extFile = self::EXT_CSS;                                                    
        }
        return $extFile;
    }
    
    /**
     * Checa se a extensÃ£o informada Ã© vÃ¡lida (js | jsInc | css | cssInc).
     * 
     * @param string $ext
     * @return boolean
     * @throws \Exception Caso o parÃ¢metro informado seja invÃ¡lido.
     */    
    private function checkExt($ext){
        $key = array_search($ext,self::$arrExt);            
        if ($key === FALSE){
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'EXTENSION_NOT_EXISTS');
            $msgErr = str_replace('{EXT}',$ext,$msgErr);
            throw new \Exception( $msgErr );                  
        }
        return TRUE;
    }

    function memoIncludeJsCss($listInc,$ext){
        if (strlen($listInc) == 0) return;
        try {
            $this->checkExt($ext);
            if (isset($listInc) && is_string($listInc)){
                $arrInclude = explode(',',$listInc);
                foreach($arrInclude as $strFile){                        
                    if (strlen($strFile) == 0) continue;
                    try {
                        $this->memoSetFile($ext,$strFile);  
                    } catch(\Exception $e) {
                        $this->showErr('memoIncludeJsCss()',$e,FALSE); 
                    }                                
                }                    
            } else {
                $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_INCLUDE'); 
                $msgErr = str_replace('{EXT}',$ext,$msgErr);
                $msgErr = str_replace('{LIST}',$listInc,$msgErr);
                throw new \Exception( $msgErr );                 
            }               
        } catch(\Exception $e){
            $this->showErr('Erro ao memorizar lista de includes ('.$ext.' -> '.$listInc.')',$e);      
        }                              
    }  
   
    private function getMemo($ext){
        $this->checkExt($ext);       
        $out = (isset($this->arrMemoIncludeJsCss[$ext]))?$this->arrMemoIncludeJsCss[$ext]:FALSE;        
        return $out;
    }
    
    /**
     * Localiza as inclusÃµes de uma extensÃ£o, gera um arquivo minificado se necessÃ¡rio e
     * retorna a(s) tag(s) pronta(s) (<link...>, <script ...>) para mesclar com o template atual.
     * 
     * @param string $ext (css|js|cssInc|jsInc)
     * @return string
     * @throws \Exception Caso nÃ£o exista lista memorizada para a extensÃ£o atual 
     * ou caso o nome da view ($viewAlias) nÃ£o tenha sido definido.
     */
    function getTags($ext){
        $listInc     = '';
        $strScript   = '';//Guarda o conteÃºdo de todos os arquivos memorizados da extensÃ£o atual.
        $arrTag      = array();        
        
        try {               
            $arrMemo = $this->getMemo($ext);           
            if (is_array($arrMemo)){
                //A extensÃ£o atual possui uma lista memorizada. 
                //print_r($arrMemo);
                $viewAlias = $this->viewAlias;
                if (strlen($viewAlias) > 0){
                    $outFileMin  = self::getNameFileMin($viewAlias,$ext);                    
      
                    foreach($arrMemo as $file){
                        if ($ext == $this::EXT_CSS || $ext == $this::EXT_JS){
                            //O arquivo atual deve ser concatenado com os demais da lista
                            if ($this->forceNewIncMin)@unlink($outFileMin);
                            if (!file_exists($outFileMin)){
                                //O arquivo minify ainda nÃ£o existe. Deve ser criado:                        
                                $strScript .= file_get_contents($file);//Concatena os arquivos lidos em uma Ãºnica string                                   
                            } else {
                                //Arquivo minify jÃ¡ existe. Ignorar o loop.
                                $arrTag[] = $this->setTag($outFileMin,$ext);
                                break;
                            }
                        } else {
                            //O arquivo deve ser incluÃ­do separadamente. Verifica se jÃ¡ estÃ¡ compactado                            
                            $arrFile        = explode('/',$file);
                            $indiceFile     = count($arrFile)-1;
                            $fileName       = $arrFile[$indiceFile];
                            $pos            = strpos($fileName,'_min.');
                            $extFile        = ($ext == $this::EXT_CSS_INC)?'css':'js';
                            $fileNameMin    = str_replace('.'.$extFile,'_min.'.$extFile,$fileName);                            
                            
                            //Cria o nome do arquivo de saÃ­da para o conteÃºdo compactado:
                            $arrFile[$indiceFile]   = $fileNameMin;
                            $fileMin                = join('/',$arrFile);
                            //==============================================
                                                            
                            if ($this->forceNewIncMin)@unlink($fileMin);
                            if ($pos === false && !file_exists($fileMin)) {
                                //O arquivo ainda nÃ£o estÃ¡ compactado. Gera uma versÃ£o compactada.
                                $strScriptInc = file_get_contents($file);
                                if (Component::yuiCompressor($strScriptInc,$extFile,$fileMin)){                                                        
                                    //Arquivo compactado com sucesso. Faz a inclusÃ£o do novo arquivo.
                                    $file = $fileMin;
                                }                                  
                            } elseif (file_exists($fileMin)){
                                $file = $fileMin;
                            }
				
                            
                            $arrTag[] = $this->setTag($file,$ext);                             
                        }
                    } 

                    if (strlen($strScript) > 0){
                        if (Component::yuiCompressor($strScript,$ext,$outFileMin)){                   
                            $arrTag[] = $this->setTag($outFileMin,$ext);                              
                        }                     
                    }
                } else {
                    $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_VIEW_ALIAS');  
                    throw new \Exception( $msgErr );                       
                }
            }
            $tags = join(chr(13),$arrTag);
            return $tags;
        } catch(\Exception $e) {
            $this->showErr('Erro ao recuperar listas memorizadas de includes ('.$ext.')',$e); 
        }        
    }
    
        
 
    private function setTag($file,$ext){       
        $inc        = '';
        $extFile    = $this->getExtFile($ext);                          
	$date       = date('YmdHis');
        if (strlen($file) > 0){           
            if ($extFile == self::EXT_JS) {
                $inc = "<script type='text/javascript' src='".$file."'></script>".chr(13);
            } elseif ($extFile == self::EXT_CSS) {               
                $inc = "<link rel='stylesheet' href='".$file."' type='text/css' />".chr(13);
            }
        }            
        return $inc;
    }       

    
    public static function getNameFileMin($outFile,$ext){
        $rootOutFileMin = self::$ROOT_VIEW_FILES.'/'.$ext.'/min/'; 
        $outFileMin     = $rootOutFileMin.$outFile.'_min.'.$ext;  
        return $outFileMin;
    }         
    
    function __call($fn,$args){
        $arrFnAdd   = array('addCss','addCssInc','addJs','addJsInc');
        $key        = array_search($fn,$arrFnAdd);
        
        if ($key !== FALSE){            
            $ext    = self::$arrExt[$key];            
            $value  = (isset($args[0]))?$args[0]:''; 
            if (strlen($value) > 0) $this->memoIncludeJsCss($value,$ext);
        }
    }
    
    private function showErr($msg,$e,$die=TRUE){
        $msgErr = "<b>".$msg.':</b><br/><br/>'.$e->getMessage();
        if ($die) die($msgErr);
        echo $msgErr.'<br/><br/>';
    }    

}

?>
