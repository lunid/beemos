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
    private $onlyExternalCssJs      = FALSE;
    private $layoutName             = '';
    
    function __construct($layoutName=''){        
        self::$ROOT_VIEW_FILES = __MODULE__ . "/views";
        if (isset($layoutName) && strlen($layoutName) > 0) $this->layoutName = $layoutName;
    }   
    
    /**
     * Memoriza uma uri qualificada ($strFile) de acordo com a extensão informada (parâmetro $ext, que pode ser js ou css)
     * 
     * Como exemplo, seguem alguns valores possíveis para o parâmetro uri:
     *  - $ext='js', $strFile='home': o sistema entende como 'app/views/js/home.js'
     *  - $ext='css',$strFile='menu.init': o sistema entende como 'app/views/css/menu/init.css'
     * 
     * @param string $ext Valores possíveis: css,js,cssInc,jsInc
     * @param string $strFile String que contém o endereço de um arquivo
     * @param boolean $exception Se TRUE e o arquivo a ser memorizado não existir dispara uma exceção.
     * @return void
     * @throws \Exception Caso o arquivo físico não exista e o parâmetro $exception for TRUE.
     */    
    function memoSetFile($ext,$strFile,$exception=TRUE){
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
            $extFile    = $this->getExtFile($ext);
            $file       = $root.'/'.$extFile.'/'.$file;  	                                  
        }
	
        $extFile = $this->getExtFile($ext);            
        $file   .= '.'.$extFile;
        //$file   = str_replace('//','/',$file);
        //echo $file.'<br>';
        if (file_exists($file)){
            $this->arrMemoIncludeJsCss[$ext][]  = $file;                                           
        } elseif ($exception) {                
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'FILE_NOT_EXISTS'); 
            $msgErr = str_replace('{FILE}',$file,$msgErr);
            $msgErr = str_replace('{STR_FILE}',$strFile,$msgErr);
            throw new \Exception( $msgErr );                                  
        }                       
    } 
    
    function forceCssJsMinifyOn(){
        $this->forceNewIncMin = TRUE;
    }
    
    function forceCssJsMinifyOff(){
        $this->forceNewIncMin = FALSE;
    }
    
    function onlyExternalCssJs(){        
        $this->onlyExternalCssJs = TRUE;
    }
    
    /**
     * Recebe uma extensão de inclusão (js, jsInc, css ou cssInc) e retorna uma extensão 
     * de arquivo válida (css ou js). Caso o parâmetro $ext seja 'cssInc' retornará css e se 
     * for jsInc retornará js.
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
     * Checa se a extensão informada é válida (js | jsInc | css | cssInc).
     * 
     * @param string $ext
     * @return boolean
     * @throws \Exception Caso o parâmetro informado seja inválido.
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

    /**
     * Método usado para memorizar paths de inclusão para js ou css.
     * 
     * Usado também como suporte para os métodos addCss(), addCssInc(), addJs() e addJsInc(), 
     * chamados a partir do método mágico __call().
     * 
     * @param type $listInc
     * @param type $ext
     * @return type
     * @throws \Exception 
     */
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
            $msgErr = $this->showErr('Erro ao memorizar lista de includes ('.$ext.' -> '.$listInc.')',$e);
            throw new \Exception($msgErr);            
        }                              
    }  
   
    /**
     * Retorna o array dos paths de inclusão (js, css, jsInc e cssInc) da página atual.
     * Método de suporte a getTags();
     * 
     * @param string $ext Exensão do arquivo a ser incluído (css, js, cssInc ou jsInc)
     * @return string[] | FALSE
     */
    private function getMemo($ext){
        $this->checkExt($ext);       
        $out = (isset($this->arrMemoIncludeJsCss[$ext]))?$this->arrMemoIncludeJsCss[$ext]:FALSE;        
        return $out;
    }
    
    /**
     * Localiza as inclusões de uma determinada extensão (js,css), gera um arquivo compactado se necessário e
     * retorna a(s) tag(s) pronta(s) (<link...>, <script ...>).
     * 
     * @param string $ext (css|js|cssInc|jsInc)
     * @param string $layoutName
     * @return string
     * @throws \Exception Caso não exista lista memorizada para a extensão atual 
     * ou caso o nome da view ($layoutName) não tenha sido definido.
     */
    function getTags($ext,$layoutName=''){
        $arrMemo    = $this->getMemo($ext);  
        $arrTag     = array();
       
        try {
            $this->layoutName = $layoutName;                
            if (is_array($arrMemo)){                    
                //A extensão atual possui uma lista memorizada.                  
                if (($ext == $this::EXT_CSS || $ext == $this::EXT_JS) && !$this->onlyExternalCssJs){
                    //css ou js: faz a junção de todos os arquivos em um único arquivo compactado.
                    $arrTag[]  = $this->getOneTagMin($arrMemo,$ext);
                } else {
                    //Para cada arquivo gera uma tag de include (<script ...> ou <link ...>)
                    $arrTag = $this->getManyTagsMin($arrMemo, $ext);
                }

                $tags = (is_array($arrTag) && count($arrTag) > 0)?join(chr(13),$arrTag):'';
                return $tags;            
            }
        } catch(\Exception $e){            
             $msgErr = $this->showErr('Erro ao recuperar listas memorizadas de includes ('.$ext.')',$e); 
             throw new \Exception($msgErr);  
        }
    }    
    
    /**
     * Recebe um array contendo path(s) qualificado(s) de um ou mais arquivos (Ex.: path/folder/fileName.js) e
     * concatena o conteúdo de todos eles em um único arquivo.
     * 
     * @param string[] $arrMemo
     * @param string $ext Exensão do arquivo a ser gerado. Pode ser apenas css ou js
     * @return string Retorna a tag de inclusão do arquivo gerado. Pode ser <script> ou <link>
     * @throws \Exception Caso um nome de layout (layoutName) não for informado.
     */
    private function getOneTagMin($arrMemo,$ext){
        $layoutName     = $this->layoutName;
        $strScript      = '';
        $tag            = '';
        
        if (strlen($layoutName) > 0){
            $outFileMin  = self::getNameFileMin($ext);               
            if ($this->forceNewIncMin)@unlink($outFileMin);
            
            if (!file_exists($outFileMin)){
                foreach($arrMemo as $file){                
                    //O arquivo minify ainda não existe. Deve ser criado.
                    //Concatena o conteúdo de cada arquivo do $arrMemo e após o loop gera o arquivo _min.
                    $strScript .= file_get_contents($file);                   
                }
                
                if (strlen($strScript) > 0){
                    if (Component::yuiCompressor($strScript,$ext,$outFileMin)){                   
                        $tag = $this->setTag($outFileMin,$ext);                              
                    }                     
                }                
            } else {
                //Arquivo minify já existe. Ignorar o loop.
                $tag = $this->setTag($outFileMin,$ext);              
            }   
            return $tag;
        } else {
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_OUT_NAME');  
            throw new \Exception( $msgErr );               
        }
    }
    
    /**
     * Recebe um array contendo path(s) qualificado(s) de um ou mais arquivos (Ex.: path/folder/fileName.js)
     * e retorna uma tag de inclusão para cada arquivo.
     * 
     * @param string[] $arrMemo Inclusões memorizadas que devem ser convertidas tags: <script> ou <link>
     * @param string $ext Exensão do(s) arquivo(s) a ser(em) incluído(s). Pode ser apenas cssInc ou jsInc
     * @return string[] 
     */
    private function getManyTagsMin($arrMemo,$ext){       
        $arrTag = array();         
        if ($ext != $this::EXT_CSS_INC && $ext != $this::EXT_JS_INC) {
            $ext    = ($ext == $this::EXT_CSS)?$this::EXT_CSS_INC:(($ext == $this::EXT_JS)?$this::EXT_JS_INC:'');
        }
        
        try {
            $this->checkExt($ext); 
        } catch (\Exception $e){            
           $this->showErr(__FUNCTION__.'(): extensão inválida ('.$ext.')',$e);   
        }
        
        foreach($arrMemo as $file){
            //O arquivo deve ser incluído separadamente. Verifica se já está compactado                            
            $arrFile        = explode('/',$file);
            $indiceFile     = count($arrFile)-1;
            $fileName       = $arrFile[$indiceFile];
            $pos            = strpos($fileName,'_min.');
            $extFile        = ($ext == $this::EXT_CSS_INC)?'css':'js';            
       
            if ($pos === false) {
                //O arquivo solicitado não é compactado (sufixo _min). Deve ser gerado.
                
                //Modifica o path do arquivo. Ex.: path/fileName.js para path/fileName_min.js  
                $search                 = '.'.$extFile;
                $replace                = '_min.'.$extFile;                
                $nameMin                = str_replace($search,$replace,$fileName);               
                $arrFile[$indiceFile]   = $nameMin;                
                $outFileMin             = join('/',$arrFile);
                               
                if ($this->forceNewIncMin)@unlink($outFileMin);//Exclui o arquivo _min caso exista.
                if (!file_exists($outFileMin)){
                    //Arquivo ainda não existe. Gera um arquivo _min.
                    $strScriptInc = file_get_contents($file);
                                       
                    if (Component::yuiCompressor($strScriptInc,$extFile,$outFileMin)){                                                        
                        //Arquivo compactado com sucesso. Faz a inclusão do novo arquivo.
                        $file = $outFileMin;
                    } else {
                        //O arquivo gerado está vazio. Utiliza o arquivo sem compactação.
                        @unlink($outFileMin);//Exclui o arquivo _min caso exista.                        
                    }                     
                } else {
                    $file = $outFileMin;
                }
            }                        			                            
            $arrTag[] = $this->setTag($file,$ext);                  
        }
       
        return $arrTag;
    }        
 
    private function setTag($file,$ext){       
        $inc        = '';
        $extFile    = $this->getExtFile($ext);                         
        if (strlen($file) > 0){           
            if ($extFile == self::EXT_JS) {
                $inc = "<script type='text/javascript' src='".$file."'></script>".chr(13);
            } elseif ($extFile == self::EXT_CSS) {               
                $inc = "<link rel='stylesheet' href='".$file."' type='text/css' />".chr(13);
            }
        }            
        return $inc;
    }       

    
    /**
     * Gera o nome do arquivo que deve ser a versão compactada (sufixo _min) de $outFile.
     * 
     * Exemplo: 
     * Para $outFile='home', $ext='js' e $ROOT_VIEW_FILES = 'app/views'
     * o arquivo de saída será app/views/js/min/home_min.js
     * 
     * @param string $outFile Nome do arquivo sem compactação. Ex: home.
     * @param string $ext Exensão do arquivo (css,js).
     * @return string 
     */
    private function getNameFileMin($ext){        
        $rootOutFileMin = self::$ROOT_VIEW_FILES.'/'.$ext.'/min/'; 
        $outFileMin     = $rootOutFileMin.$this->layoutName.'_min.'.$ext;  
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
        return $msgErr.'<br/><br/>';
    }    
}
?>
