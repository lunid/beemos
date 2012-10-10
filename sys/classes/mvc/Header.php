<?php

namespace sys\classes\mvc;
use \sys\classes\util\Dic;
use \sys\classes\comps\Component;

class Header {

    const EXT_JS                    = 'js';
    const EXT_CSS                   = 'css';
    const EXT_JS_INC                = 'jsInc';
    const EXT_CSS_INC               = 'cssInc';    
    //static  $ROOT_VIEW_FILES        = '';
    //static  $ROOT_SYS_FILES         = '';        
    public static $arrExt          = array(self::EXT_CSS,self::EXT_CSS_INC,self::EXT_JS_INC,self::EXT_JS);     
    private $arrMemoIncludeJsCss    = array();
    var $arrIncDefault              = array();//Guarda as inclusÃµes default para todas as pÃ¡ginas (css, js, cssInc, jsInc).
    private $forceNewIncMin         = FALSE;
    private $onlyExternalCssJs      = FALSE;
    private $layoutName             = '';
    
    function __construct($layoutName=''){                       
        //self::$ROOT_VIEW_FILES      = \LoadConfig::folderViews();
        //self::$ROOT_SYS_FILES       = \LoadConfig::folderSys();
        
        //Inicializa a variável array que guarda todas as inclusões js e css da página atual
        $this->arrMemoIncludeJsCss = array(
            self::EXT_JS_INC=>array(),
            self::EXT_JS=>array(),            
            self::EXT_CSS_INC=>array(),
            self::EXT_CSS=>array()
        );
      
        if (isset($layoutName) && strlen($layoutName) > 0) $this->layoutName = $layoutName;
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
                        //Memoriza a string de includes atual de acordo com o tipo,
                        //que pode ser css, cssInc, js, jsInc                        
                        $this->memoSetFile($ext,$strFile);  
                    } catch(\Exception $e) {  
                        $msgErr = $this->showErr('memoIncludeJsCss()',$e,FALSE); 
                        throw new \Exception($msgErr);                        
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
        
        $extension  = $this->getExtFile($ext);
        $file       = str_replace('.','/',$strFile);
        
        //Verifica se a URL atual é um include de PLUGIN
        $keyPlugin      = strpos($strFile,\LoadConfig::folderPlugins());        
        if ($keyPlugin === FALSE) {
            $file = $extension.'/'.$file;//Não é PLUGIN          	
        }
                      
        $file       = 'assets/'.$file.'.'.$extension;           
        
        if (file_exists($file)){                        
            $this->arrMemoIncludeJsCss[$ext][]  = $file;                                           
        } elseif ($exception) {    
            if ($exception) {       
                $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'FILE_NOT_EXISTS'); 
                $msgErr = str_replace('{FILE}',$file,$msgErr);
                $msgErr = str_replace('{STR_FILE}',$strFile,$msgErr);
                throw new \Exception( $msgErr );                                  
            }
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
     * Imprime na tela todas as URLs de inclusão memorizadas até o momento e para a execução do script. 
     */
    function getMemos(){
        echo "<pre>";
        print_r($this->arrMemoIncludeJsCss);
        die("</pre>");
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
             throw $e;
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
    private function getOneTagMin($arrMemo,$extension){
        $layoutName     = $this->layoutName;
        $string         = '';
        $tag            = '';
        $arrMemo        = array_unique($arrMemo);//Elimina valores em duplicidade.      
        //echo "<pre>";
        //print_r($arrMemo);
        //echo "</pre>";
        
        if (strlen($layoutName) > 0){
            $outFileMin  = self::getNameFileMin($extension);                
            if ($this->forceNewIncMin)@unlink($outFileMin);
            
            if (!file_exists($outFileMin)){                
                foreach($arrMemo as $file){
                    //O arquivo minify ainda não existe. Deve ser criado.
                    //Concatena o conteúdo de cada arquivo do $arrMemo e após o loop gera o arquivo _min.
                    $string .= file_get_contents($file);                   
                }                
                $arrParams['string']        = $string;
                $arrParams['extension']     = $extension;
                $arrParams['fileNameMin']   = $outFileMin;                

                try {
                    $file = $this->geraMinify($arrParams);                                        
                } catch(\Exception $e) {
                    throw new \Exception($e);
                }
            }   
            
            $tag = $this->setTag($outFileMin,$extension);              
            return $tag;
        } else {
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_OUT_NAME');  
            throw new \Exception( $msgErr );               
        }
    }
    
    
    /**
     * Gera o nome do arquivo que deve ser a versão compactada (sufixo _min) da view atual.
     * Método de suporte ao método getOneTagMin().
     * 
     * Ao renderizara View da página atual um nome de layout deve ser fornecido (View->render($layoutName)). 
     * Esse nome é utilizado como nome do arquivo min da página a ser gerada.
     * 
     * Exemplo: 
     * Para $layoutName='home', $ext='js' e módulo app:
     * O arquivo de saída será assets/js/app/home_min.js
     *      
     * @param string $ext Exensão do arquivo (css,js).
     * @return string 
     */
    private function getNameFileMin($ext,$file=''){   
        $prefixo    = '_';
        $sufixo     = '_min';
        
        if (strlen($file) > 0){
            //Uma URL de arquivo foi informada. Apenas coloca o prefixo e sufixo no nome do arquivo.
            $pathInfo       = pathinfo($file);
            $filename       = $pathInfo['filename'];
            $search         = '.'.$ext;
            $replace        = $sufixo.'.'.$ext;                                                             
            $uri            = str_replace($search,$replace,$file);
            $uri            = str_replace($filename.$sufixo,$prefixo.$filename,$uri);            
        } else {
            //Um arquivo não foi informado. 
            //Cria um nome de arquivo a partir de $layoutName.
            $fileName       = $prefixo.$this->layoutName.$sufixo.'.'.$ext;
            $path           = $ext.'/'.$fileName;                         
            
            //Concatena o módulo no path:
            $assets         = \LoadConfig::assetsFolderRoot();
            $module         = \Application::getModule();            
            $pathInfo       = pathinfo($fileName);
            $basename       = $pathInfo['basename'];        
            $path           = str_replace($basename,$module.'/'.$basename,$path);            
            $uri            = $assets.'/'.$path;            
        }
        
        
        $pathFileMin    = \Url::physicalPath($uri);
    
        return $pathFileMin;
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
            if (strlen($file) == 0) continue;
            
            $arrInfo        = pathinfo($file);
            $filename       = $arrInfo['filename'];
            $extension      = $arrInfo['extension'];
          
            $pos            = strpos($file,'_min.');
            
            if ($pos === false) {
                //O arquivo solicitado não é compactado (sufixo _min). Deve ser gerado.                                
                $outFileMin = $this->getNameFileMin($extension,$file);
                
                if (!file_exists($outFileMin)){
                    //Arquivo ainda não existe. Gera um arquivo _min.
                    
                    $arrParams['string']        = file_get_contents($file);                    
                    $arrParams['extension']     = $extension;
                    $arrParams['fileNameMin']   = $outFileMin;
                    
                    try {
                        $file = $this->geraMinify($arrParams);                                        
                    } catch(\Exception $e) {
                        throw $e;
                    }
                } else {
                    //echo "$outFileMin - existe<br>";
                    $file = $outFileMin;
                }
            }                        			                            
            $arrTag[] = $this->setTag($file,$extension);                  
        }        
        return $arrTag;
    }        
    
    private function geraMinify($arrParams){
        
        $string         = '';
        $extension      = '';
        $fileNameMin    = ''; 
        $out            = FALSE;
        
        if (is_array($arrParams)) {
            $string         = $arrParams['string'];
            $extension      = $arrParams['extension'];
            $fileNameMin    = $arrParams['fileNameMin'];
        }
        
        //echo "<pre>".print_r($arrParams).'</pre>';
                
        try {            
            if (Component::yuiCompressor($string,$extension,$fileNameMin)){                                                        
                //Arquivo compactado com sucesso. Faz a inclusão do novo arquivo.
                $out = $fileNameMin;
            }  
        } catch(\Exception $e) {
            throw $e;
        }         
        return $out;
    }
 
    private function setTag($file,$ext){       
        $inc                = '';
        $cssJsExtension     = $this->getExtFile($ext);//Converte cssInc para css e jsInc para js, se necessário.                        
        if (strlen($file) > 0){   
            //$arrPath    = pathinfo($file);  
            //$dirname    = $arrPath['dirname'];
            //$basename   = $arrPath['basename'];
                        
            //Verifica se a URL atual é um include de PLUGIN
            //$plugins        = \LoadConfig::folderPlugins();//sub-pasta de assets onde ficam os plugins
            //$keyPlugin      = strpos($dirname,$plugins);
            $pathFile       = \Url::relativeUrl($file);
            
            if ($cssJsExtension == self::EXT_JS) {
                $inc = "<script type='text/javascript' src='".$pathFile."'></script>".chr(13);
            } elseif ($cssJsExtension == self::EXT_CSS) {               
                $inc = "<link rel='stylesheet' href='".$pathFile."' type='text/css' />".chr(13);
            }
        }            
        return $inc;
    }             
    
    function __call($fn,$args){
        //O parâmetro $fn deve ser coincidir com algum índice de $arrExt (js, jsInc, css, cssInc)
        $arrExt     = self::$arrExt;        
        $key        = array_search($fn,$arrExt);
        
        if ($key !== FALSE){    
            //Parâmetro encontrado. Memoriza a lista de include.
            $value  = (isset($args[0]))?$args[0]:'';             
            $ext    = $fn;// js | jsInc | css | cssInc
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
