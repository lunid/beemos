<?php

    namespace sys\classes\mvc;
    use \sys\classes\util\Dic;
    use \sys\classes\plugins\Plugin;
    use \sys\classes\mvc\Header;
    use \sys\classes\util\File;
    use \sys\classes\mvc\ViewPart;
    
    class View extends ViewPart {
        //Site
//        const CSS       = 'sys:skeleton.stylesheets.base,sys:skeleton.stylesheets.skeleton,sys:skeleton.stylesheets.layout,site';
//        const CSS_INC   = '';
//        const JS        = 'init,site,sys:util.dictionary';
//        const JS_INC    = 'sys:util.form';
//        const PLUGINS   = 'modal,menuHorizontal,menuIdiomas';
        
        //Admin
        const CSS       = 'site';
        const CSS_INC   = '';
        const JS        = 'init,sys:util.dictionary';
        const JS_INC    = '';        
        const PLUGINS   = 'jquery_ui,abas,drop,menu_slider';
        
        private $arrMenuOpts = array(
            "menu_home" => array( //menu_home será o ID do elemento HTML
                "href"      => "/",
                "titulo"    => "Home",
                "subTitulo" => "Bem vindo",
                "ativo"     => false
            ),  
            "menu_sobre" => array(
                "href"      => "sobreNos",
                "titulo"    => "Sobre Nós",
                "subTitulo" => "Conheça a InterBits",
                "ativo"     => false
            ),  
            "menu_assine" => array(
                "href"      => "assine",
                "titulo"    => "Assine Já",
                "subTitulo" => "Planos & Preços",
                "ativo"     => false
            ),
            "menu_super" => array(
                "href"      => "superprofessor",
                "titulo"    => "SuperPro",
                "subTitulo" => "Principais Recursos",
                "ativo"     => false
            ),
            "menu_ajuda" => array(
                "href"      => "ajuda",
                "titulo"    => "Ajuda",
                "subTitulo" => "FAQ & Tutoriais",
                "ativo"     => false
            ),
        );
        
        private $objHeader      = NULL;        
        private $tplFile        = '';           
        private $forceNewIncMin = FALSE;
        
        function __construct(ViewPart $objViewPart,$tplName='padrao'){                            
            if (is_object($objViewPart)) {                                 
                $objViewTpl                     = new ViewPart('templates/'.$tplName);
                $objViewTpl->BODY               = $objViewPart->render();
                
                if($tplName == 'padrao'){
                    $objViewTpl->BARRA_TOPO = \HtmlComponent::barraTopo();
                }
                
                $this->bodyContent              = $objViewTpl->render();
                $this->layoutName               = $objViewPart->layoutName;                
                
                if (strlen($tplName) > 0){
                    $objHeader = new Header();            
                    $objHeader->addCss(self::CSS);
                    $objHeader->addCssInc(self::CSS_INC);
                    $objHeader->addJs(self::JS);
                    $objHeader->addJsInc(self::JS_INC);                      
                    
                    //Faz a inclusão de arquivos css e js padrão.
                    try {                        
                        $objHeader->memoSetFile($objHeader::EXT_CSS,self::CSS,FALSE);
                        $objHeader->memoSetFile($objHeader::EXT_JS,self::JS,FALSE);                        
                        $this->objHeader = $objHeader;                                                                   
                        
                        //Plugins                        
                        $plugins    = $this::PLUGINS;
                        $arrPlugins = explode(',',$plugins);
                        if (is_array($arrPlugins)) {
                            foreach($arrPlugins as $plugin) {                                 
                                $this->setPlugin($plugin);
                            }
                        }
                    } catch(\Exception $e){
                        $this->showErr('View()',$e,FALSE); 
                    }                                                           
                }                
            } else {
                die('Impossível continuar. O objeto View não foi informado ou não é um objeto válido.');                
            }            
        }               
        
        private function getObjHeader(){
            $objHeader = $this->objHeader;            
            if (!is_object($objHeader)) $objHeader = new Header();
            return $objHeader;
        }        
               
        function setPlugin($plugin){
           if (strlen($plugin) > 0){
               $arr = Plugin::$plugin();                 
               if (is_array($arr) && count($arr) > 0){ 
                   $objHeader = $this->getObjHeader();
                   try {                       
                       foreach($arr as $ext=>$listInc){
                           //echo $listInc.'<br>';
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
        
        private function getIncludesCss(){            
            $inc        = $this->getIncludes(Header::EXT_CSS);
            $inc        .= $this->getIncludes(Header::EXT_CSS_INC);
            return $inc;
        }
        
        private function getIncludesJs(){            
            $inc        = $this->getIncludes(Header::EXT_JS);
            $inc        .= $this->getIncludes(Header::EXT_JS_INC);
            return $inc;
        }
        
        private function getIncludes($ext,$exception=TRUE){    
           try {
               $objHeader = $this->getObjHeader();           
               return $objHeader->getTags($ext,$this->layoutName);
           } catch(\Exception $e) {
               if ($exception) {
                   $this->showErr('Erro ao ao gerar os includes de '.$ext.' para a página atual',$e);  
               }
           }
        }                
        
        function render($layoutName=''){            
            if (isset($layoutName) && strlen($layoutName) > 0) {
                $this->layoutName   = $layoutName;
                $objHeader          = $this->getObjHeader();
                if (is_object($objHeader)) {
                    //Faz a inclusão de arquivos css e js com o mesmo nome da view atual, caso existam.                
                    $objHeader->memoSetFile(Header::EXT_CSS,$layoutName,FALSE);
                    $objHeader->memoSetFile(Header::EXT_JS,$layoutName,FALSE);
                }
            }
           
            $css                       = $this->getIncludesCss();
            $js                        = $this->getIncludesJs();            
            $bodyContent               = trim($this->bodyContent);
            $params                    = $this->params;                                       
            $params['INCLUDE_CSS']     = $css;
            $params['INCLUDE_JS']      = $js;                                                      
            
            //Controle de Menu Horizontal
            switch($layoutName){
                case 'index':
                case 'home':
                    $menu_ativo = 'menu_home';
                    break;
                case 'sobre':
                case 'politica':
                case 'contato':
                case 'sobreNos':
                case 'aInterbits':
                    $menu_ativo = 'menu_sobre';
                    break;
                case 'assine':
                case 'planos':
                case 'recursos':
                case 'pagamento':
                    $menu_ativo = 'menu_assine';
                    break;
                case 'superpro':
                case 'provas':
                case 'listas':
                case 'relatorios':
                    $menu_ativo = 'menu_super';
                    break;
                case 'faq':
                case 'tutoriais':
                case 'suporte':
                case 'chat':
                    $menu_ativo = 'menu_ajuda';
                    break;
                default:
                    $menu_ativo = 'menu_home';
            }
            
            //Setando o menuque deve ser selecionado
            $this->arrMenuOpts[$menu_ativo]["ativo"] = true;
            
            //Renderizando HTML do menu
            $params['MENU_HORIZONTAL'] = \HtmlComponent::menuHorizontal($this->arrMenuOpts);
            
            if (is_array($params)) {
                foreach($params as $key=>$value){
                    $bodyContent = str_replace('{'.$key.'}',$value,$bodyContent);                
                }
            }
            
            echo $bodyContent;
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
                        $this->objHeader->memoIncludeJsCss($listFiles,$ext);  
                        } catch(\Exception $e){
                            $this->showErr('Erro ao memorizar arquivo(s) de inclusão(ões) css | js -> '.$listFiles,$e);                    
                        }
                    } else {
                       echo "Inclusão não realizada $listFiles<br>"; 
                    }
                } elseif ($fn == 'forceCssJsMinifyOn') {
                    $objHeader->forceCssJsMinifyOn();                                          
                } elseif ($fn == 'forceCssJsMinifyOff') {
                    $objHeader->forceMinifyOff();     
                } elseif ($fn == 'onlyExternalCssJs') {
                    $objHeader->onlyExternalCssJs();  
                }
            }            
        }
    }
?>
