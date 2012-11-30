<?php

    namespace sys\classes\mvc;
    use \sys\classes\util\Dic;
    use \sys\classes\util\Plugin;
    use \sys\classes\mvc\Header;
    use \sys\classes\util\File;
    use \sys\classes\mvc\ViewPart;
    
    class View extends ViewPart {

        private $objHeader          = NULL;        
        private $tplFile            = '';           
        private $forceNewIncMin     = FALSE;
        private $pathTpl            = '';
        private $arrIncludeCfgOff   = array();
        private $includeCfgAllOff   = FALSE;
        
        function __construct(){                            
            $this->init();
        } 
        
        /**
         * Inicializa o template definido em config.xml
         * 
         * Pode conter também outros recursos de inicialização da View (não implementados).
         * return void 
         */
        function init(){            
            $fileTpl = \LoadConfig::defaultTemplate();                 
            $this->setTemplate($fileTpl);
        }
        
        /**
         * Método usado para gerar um link para um controller/action no módulo atualmente ativo.
         *
         * @param string $controller Nome do controller. Ex,.: usuarios
         * @param string $action Nome do método (geralmente refere-se a uma página) a ser executado. Ex.: pedidos.
         *  
         * @return string
         */
        function setModuleUrl($controller='',$action=''){
            $module = \Application::getModule();
            return $this->setUrl($module,$controller,$action);
        }
        
        /**
         * Método usado para gerar um link para um module/controller/action.
         * 
         * @param string $module Nome do módulo.
         * @param string $controller Nome do controller. Ex,.: usuarios.
         * @param string $action Nome do método (geralmente refere-se a uma página) a ser executado. Ex.: pedidos.
         * 
         * @return string
         */        
        function setUrl($module='',$controller='',$action=''){           
           $arrUrl = array('module'=>$module,'controller'=>$controller,'action'=>$action);           
           $url = \Url::setUrl($arrUrl);               
           return $url;
        }        
        
        /**
         * Define um novo template html para a view atual.
         * O arquivo informado deve existir na pasta padrão de template, previamente definida no arquivo config.xml.
         * 
         * Exemplo:
         * $objView->setTemplate('novoModelo.html');
         * 
         * @param string $fileTpl Deve conter um nome de arquivo contendo a extensão (htm ou html)
         */
        function setTemplate($fileTpl=''){
            $pathTpl = '';
            if (strlen($fileTpl) > 0) {
                $folderTpl   = \LoadConfig::folderTemplate();                  
                $pathTpl     = $folderTpl.'/'.$fileTpl;                     
                $pathTpl     = str_replace('//', '/', $pathTpl);                            
            }
            
            $this->pathTpl  = $pathTpl;
        }
        
        
        /**
         * Retorna um template válido. Caso um arquivo de Template não tenha sido informado 
         * um template padrão (sys_blank.html) é criado no módulo atual, pasta de templates.
         * 
         * @return string
         * @throws \Exception Caso ocorra erro ao tentar criar um template padrão. 
         */
        private function getTemplate(){
            $pathTpl        = $this->pathTpl;
            $fileTplDefault = 'sys_blank.html';
            
            if (strlen($pathTpl) == 0) {
                //Um template não foi informado. Gera um arquivo template padrão.  
                $objModule  = new \Module();
                $newUrlTpl  = $objModule->tplLangFile($fileTplDefault);
                $folderTpl   = \LoadConfig::folderTemplate();                  
                $pathTpl     = $folderTpl.'/'.$fileTplDefault; 
                
                if (!file_exists($newUrlTpl)) {
                    $content = "<div>{BODY}</div>";
                    $fp = @fopen($newUrlTpl, "wb+");   
                    if ($fp !== FALSE) {
                        fwrite($fp, $content);
                        fclose($fp);                                             
                    } else {
                        $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_CREATE_TEMPLATE'); 
                        $msgErr = str_replace('{PATH_TPL}',$pathTpl,$msgErr);
                        throw new \Exception( $msgErr );                           
                    }                
                }
                $this->pathTpl = $pathTpl;
            } 
            return $pathTpl;
        }          
        
        /**
         * Desabilita a inclusão da lista de javascript definida em config.xml, conforme o nó abaixo:
         * <header><include id='js'></include></header>
         * 
         * IMPORTANTE: 
         * Este método deve ser chamado antes de setLayout().
         * 
         * @return void
         */
        function cfgJsOff(){
            $this->includeCfgOff(Header::EXT_JS);
        }
        
        /**
         * Desabilita a inclusão da lista de javascript (arquivos externos) definida em config.xml, conforme o nó abaixo:
         * <header><include id='jsInc'>...</include></header>
         * 
         * IMPORTANTE: 
         * Este método deve ser chamado antes de setLayout(). 
         * 
         * @return void
         */        
        function cfgJsIncOff(){
            $this->includeCfgOff(Header::EXT_JS_INC);
        }   
        
        /**
         * Desabilita a inclusão da lista de css definida em config.xml, conforme o nó abaixo:
         * <header><include id='css'>...</include></header>
         * 
         * IMPORTANTE: 
         * Este método deve ser chamado antes de setLayout().
         *          
         * @return void
         */        
        function cfgCssOff(){
            $this->includeCfgOff(Header::EXT_CSS);
        }        
        
        /**
         * Desabilita a inclusão da lista de css (arquivos externos) definida em config.xml, conforme o nó abaixo:
         * <header><include id='cssInc'>...</include></header>
         *          
         * IMPORTANTE: 
         * Este método deve ser chamado antes de setLayout().
         * 
         * @return void
         */        
        function cfgCssIncOff(){
            $this->includeCfgOff(Header::EXT_CSS_INC);
        }    
        
        /**
         * Desabilita a inclusão dos plugins definidos em config.xml, conforme o nó abaixo:
         * <header><include id='plugins'>...</include></header>
         * 
         * IMPORTANTE: 
         * Este método deve ser chamado antes de setLayout().
         * 
         * @return void
         */        
        function cfgPluginOff(){
            $this->includeCfgOff('plugin');
        } 
        
        /**
         * Desabilita um tipo específico de include (parâmetro $ext) ou todos os includes 
         * definidos em config.xml, conforme o nó abaixo:
         * <header><include id='$ext'></include></header>
         * 
         * IMPORTANTE: 
         * Este método deve ser chamado antes de setLayout().
         * Este método exclui o arquivo minify de inclusão para css/js e cria um novo, se necessário, a cada load da página.
         * 
         * @param string $ext Pode ser css, js, cssInc, jsInc (vide constantes da classe Header)
         * @return void
         */        
        function includeCfgOff($ext='all'){
            if ($ext == 'all') {
                $this->forceCssJsMinifyOn();//Força recriar o arquivo minify de inclusão para css/js
                $this->includeCfgAllOff = TRUE;                 
            } else {
                $this->arrIncludeCfgOff[] = $ext;
            }
        }
        
        /**
         * Faz a junção do conteúdo parcial (ViewPart) com o template atual.
         * Os includes definidos no config.xml são processados na seguinte ordem:
         *  - plugins
         *  - css/js 
         * 
         * @param ViewPart $objViewPart 
         */
        function setLayout(ViewPart $objViewPart){
            if (is_object($objViewPart)) {                       
                
                $this->getObjHeader();//Inicializa um objeto Header
                
                $pathTpl                        = $this->getTemplate();                  
                $objViewTpl                     = new ViewPart($pathTpl);
                $objViewTpl->BODY               = $objViewPart->render();                               
                $this->bodyContent              = $objViewTpl->render();
                $this->layoutName               = $objViewPart->layoutName;                
                
                if (strlen($pathTpl) > 0){    
                    
                    //Configurações lidas do arquivo config.xml:   
                    //$objHeader  = $this->getObjHeader(); 
                    //$objHeader  = new Header();            
                    $plugins    = '';                    
                        
                    if (!$this->includeCfgAllOff) {    
                        /*
                         * Inclusões css e js:   
                         * As configurações de include definidas em config.xml devem ser carregadas.
                         */                       
                        
                        //1º - Faz a inclusão de cada PLUGIN definido no config.xml:
                        $plugins            = \LoadConfig::plugins();  
                        
                        //Faz a inclusão de arquivos css e js padrão.
                        try {                                                                                                                                                                  

                            //Plugins         
                            if (strlen($plugins) > 0) {                               
                                $arrPlugins = explode(',',$plugins);
                                if (is_array($arrPlugins)) {
                                    foreach($arrPlugins as $plugin) {                
                                        $this->setPlugin($plugin);
                                    }
                                }
                            }                      
                        } catch(\Exception $e){
                            $this->showErr('View()',$e,FALSE); 
                        }  
                         
                        //2º - Faz a inclusão de cada CSS/JS definidos no config.xml:
                        $arrIncludeCfgOff   = $this->arrIncludeCfgOff;                    
                        $arrExt             = Header::$arrExt;
                        foreach($arrExt as $fn) {
                            $key = array_search($fn, $arrIncludeCfgOff);
                            if ($key === FALSE) {
                                echo $fn.'<br>';
                                //A extensão atual NÃO consta na lista de exclusão. 
                                //Portanto, os includes dessa extensão devem ser incluídos.
                                $list   = \LoadConfig::$fn();                                
                                $this->objHeader->$fn($list);                        
                            }
                        }        
                        
                        //Verifica se apenas os plugins defindos em config.xml foram desabilitados:
                        $key = array_search('plugin', $arrIncludeCfgOff);
                        if ($key !== FALSE) $plugins = '';
                    } else {
                        //Todos os includes de config.xml devem ser ignorados.                        
                    }                                                        
                } else {
                    $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'TEMPLATE_NOT_INFO'); 
                    throw new \Exception( $msgErr );                     
                }                
            } else {
                $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'VIEWPART_NOT_INFO'); 
                throw new \Exception( $msgErr );                                        
            }                        
        }
        
        private function getObjHeader(){
            $objHeader = $this->objHeader;            
            if (!is_object($objHeader)) $objHeader = new Header();
            $this->objHeader = $objHeader;    
            return $objHeader;
        }        
               
        function setPlugin($plugin){
           if (strlen($plugin) > 0){
               $arr = Plugin::$plugin();                 
               if (is_array($arr) && count($arr) > 0){ 
                   $objHeader = $this->getObjHeader();
                   try {                       
                       foreach($arr as $ext=>$listInc){                           
                           $objHeader->memoIncludeJsCss($listInc, $ext);
                       }
                       $this->objHeader = $objHeader;
                   } catch(\Exception $e){
                       $this->showErr('Erro ao incluir o Plugin solicitado ('.$plugin.')',$e);      
                   }
               } else {
                   echo 'Plugin não retornou dados de inclusão (css | js).';
               }
           } 
        }                         
        
        /**
         * Monta e retorna a saída HTML das partes processadas na camada View.
         * Carrega os includes definidos em config.xml (plugins, css, cssInc, js, jsInc) 
         * ao chamar o método setLayout e ao chamar também os métodos setCss(), setCssInc(), setJs(), setJsInc().
         * 
         * Permite fazer o cache da string resultante caso um objeto Cache (parâmetro $objMemCache)seja informado.
         *  
         * @param string $layoutName 
         * Se informado, $layoutName será usado para definir o nome do arquivo minify de js e css (método getIncludes()),
         * sobrepondo o que foi lido anteriormente ao carregar a ViewPart no método setLayout().
         * 
         * @param util\Cache $objMemCache 
         * Se for um objeto Cache válido faz o cache do conteúdo HTML gerado.
         * 
         * @return string Conteúdo HMTL
         */
        function render($layoutName='',$objMemCache=NULL){    
            //$this->objHeader->getMemos();
            if (isset($layoutName) && strlen($layoutName) > 0) {
                $this->layoutName   = $layoutName;                
            }            
            
            /*
             * Gera as tags de inclusões js e css.             
             */
            $css                       = $this->getIncludesCss();
            $js                        = $this->getIncludesJs();            
            
            $bodyContent               = trim($this->bodyContent);
            $params                    = $this->params;                                       
            $params['INCLUDE_CSS']     = $css;
            $params['INCLUDE_JS']      = $js;                                                                              
            
            if (is_array($params)) {
                foreach($params as $key=>$value){
                    $bodyContent = str_replace('{'.$key.'}',$value,$bodyContent);                
                }
            }
            if (is_object($objMemCache)) {
                //O cache foi ativado para o conteúdo atual. Armazena $bodyContent em cache.
                $objMemCache->setCache($bodyContent);
            }
            echo $bodyContent;
        } 
        
        /**
         * Gera/retorna as tags de inclusão de arquivos CSS.
         * 
         * IMPORTANTE: 
         * A ordem da extensão (EXT_CSS_INC e EXT_CSS) reflete a ordem em que as tags serão
         * incluídas no arquivo. Por exemplo, para incluir primeiro o arquivo minify 
         * (compactação de todos os arquivos css dentro de um único arquivo) e depois os arquivos com
         * inclusões separadas, faça a chamada conforme a ordem abaixo:
         * 
         * <code>
         *      $inc        = $this->getIncludes(Header::EXT_CSS); //Gera o minify em um único arquivo
         *      $inc        .= $this->getIncludes(Header::EXT_CSS_INC);         
         * </code>
         * 
         * Ou então, caso queira incluir os includes separados antes do arquivo minify,
         * siga o exemplo abaixo:
         * 
         * <code>
         *      $inc        .= $this->getIncludes(Header::EXT_CSS_INC); 
         *      $inc        = $this->getIncludes(Header::EXT_CSS);//Gera o minify em um único arquivo     
         * </code>
         * 
         * @return string 
         * Tags <link rel='stylesheet'...></script> 
         * Para consultar/alterar as tags de inclusão consulte mvc\Header->setTag().
         */
        private function getIncludesCss(){            
            $inc        = '';
            $inc        .= $this->getIncludes(Header::EXT_CSS_INC); 
            $inc        .= $this->getIncludes(Header::EXT_CSS);//Gera o minify em um único arquivo            
            return $inc;
        }
        
        /**
         * Gera/retorna as tags de inclusão de arquivos JS.
         * 
         * IMPORTANTE: 
         * A ordem da extensão (EXT_JS_INC e EXT_JS) reflete a ordem em que as tags serão
         * incluídas no arquivo. Por exemplo, para incluir primeiro o arquivo minify 
         * (compactação de todos os arquivos css dentro de um único arquivo) e depois os arquivos com
         * inclusões separadas, faça a chamada conforme a ordem abaixo:
         * 
         * <code>
         *      $inc        = $this->getIncludes(Header::EXT_JS); //Gera o minify em um único arquivo
         *      $inc        .= $this->getIncludes(Header::EXT_JS_INC);         
         * </code>
         * 
         * Ou então, caso queira incluir os includes separados antes do arquivo minify,
         * siga o exemplo abaixo:
         * 
         * <code>
         *      $inc        .= $this->getIncludes(Header::EXT_CSS_INC); 
         *      $inc        = $this->getIncludes(Header::EXT_CSS);//Gera o minify em um único arquivo     
         * </code>
         * 
         * @return string 
         * Tags <script type='text/javascript'...></script>
         * Para consultar/alterar as tags de inclusão consulte mvc\Header->setTag().
         */        
        private function getIncludesJs(){  
            $inc        = '';
            $inc        .= $this->getIncludes(Header::EXT_JS_INC); 
            $inc        .= $this->getIncludes(Header::EXT_JS); //Gera o minify em um único arquivo           
            return $inc;
        }
        
        /**
         * Método de suporte para getIncludesCss() e getIncludesJs().
         * 
         * @param string $ext Pode ser css, cssInc, js ou jsInc
         * @return string
         * @throws Exception Caso um problema ocorra ao executar Component::yuiCompressor()
         */
        private function getIncludes($ext){    
            try {                
                $objHeader = $this->getObjHeader();                               
                return $objHeader->getTags($ext,$this->layoutName);
            } catch(\Exception $e) {                                   
                throw $e;
            }
        }                
        
        function __call($fn,$args){
            $objHeader  = $this->getObjHeader(); 
            
            if (is_object($objHeader)){        
                $ext = '';                
                switch($fn){                
                    case 'setCss':
                        $ext = $objHeader::EXT_CSS;
                        break;
                    case 'setCssInc':
                        $ext = $objHeader::EXT_CSS_INC;
                        break;
                    case 'setJs':
                        $ext = $objHeader::EXT_JS;
                        break;
                    case 'setJsInc':
                        $ext = $objHeader::EXT_JS_INC;
                }
                                
                if (strlen($ext) > 0){
                    $listFiles = (isset($args[0]))?$args[0]:'';
                    if (strlen($listFiles) > 0) {                        
                        try {
                        $objHeader->memoIncludeJsCss($listFiles,$ext);  
                        } catch(\Exception $e){
                            $this->showErr('Erro ao memorizar arquivo(s) de inclusão(ões) css | js -> '.$listFiles,$e);                    
                        }
                    } else {
                       echo "Inclusão não realizada $listFiles<br>"; 
                    }
                } elseif ($fn == 'forceCssJsMinifyOn') {
                    //Força a compactação e junção dos includes (css e js), mesmo que o arquivo _min já exista.
                    $objHeader->forceCssJsMinifyOn();                       
                } elseif ($fn == 'forceCssJsMinifyOff') {
                    //Volta à situação padrão: apenas compacta e junta includes se o arquivo _min ainda não existir.
                    $objHeader->forceMinifyOff();     
                } elseif ($fn == 'onlyExternalCssJs') {
                    //Gera a página HTML com os includes (css e js) separados.
                    $objHeader->onlyExternalCssJs();  
                }
            }            
        }
    }
?>
