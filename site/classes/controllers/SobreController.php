<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Sobre extends mvc\ExceptionController {
        
        private $tituloPage     = '';
        private $subtituloPage  = '';
        
        private function setPageContent($objViewPartPage,$layoutName,$page=''){         
            //Cria uma viewPart com o arquivo template para o item "Sobre Nós":
            if (strlen($page) == 0) $page = $layoutName;
            
            $objViewPart                = mvc\MvcFactory::getViewPart('tpl_sobre');
            $objViewPart->MENU_VERTICAL = $this->setMenuVertical($page);//Coloca conteúdo na coluna da esquerda (menu vertical)
            $objViewPart->CONTENT       = $objViewPartPage->render();//Coloca conteúdo na coluna da direita (maior)
            
            //Gera a página atual a partir da viewPart:
            $objView                    = mvc\MvcFactory::getView();
            $objView->TITULO_PAGE       = $this->tituloPage;
            $objView->SUBTITULO_PAGE    = $this->subtituloPage;
            $objView->TITLE             = 'Supervip - Sobre nós';
            $objView->MENU_MAIN         = Menu::main(__CLASS__);            
            
            $objView->setLayout($objViewPart);
            
            $listCss    = 'common.formulario,site.common,site.sobre';
            $listJs     = '';
            $listCssInc = '';
            $listJsInc  = '';
            $listPlugin = '';

            $objView->setCss($listCss);            
           
            $objView->render($layoutName);      
        }
        
        private function setMenuVertical($page='sobre'){
            $arrMenu = array(
                array('link'=>'sobre/','page'=>'sobre','text'=>'Apresentação'),
                array('link'=>'sobre/politicaPrivacidade/','page'=>'politicaPrivacidade','text'=>'Política de Privacidade'),
                array('link'=>'sobre/termosDeUso/','page'=>'termosDeUso','text'=>'Termos de Uso'),
                array('link'=>'sobre/contato/','page'=>'contato','text'=>'Entre em contato'),
                array('link'=>'sobre/trabalheconosco/','page'=>'cv','text'=>'Trabalhe conosco')
            );
            $menu = '<ul class="alt">';
            foreach($arrMenu as $arrItem) {
                $sel = ($arrItem['page'] == $page)?"class='active'":'';
                $menu .= "<li><a href='/{$arrItem['link']}' {$sel}>{$arrItem['text']}</a></li>";
            }                
            $menu .= '</ul>';            
            return $menu;
        }
        
        public function actionIndex(){               
            $bodyHtmlName           = 'sobre';
            $objView                = mvc\MvcFactory::getView();
            $objViewPart            = mvc\MvcFactory::getViewPart($bodyHtmlName);                                             
            $this->tituloPage       = 'Sobre nós';
            $this->subtituloPage    = 'e-Commerce inteligente, negócios sem fronteiras.';
                    
            $this->setPageContent($objViewPart,$bodyHtmlName);                              
        }
        
        public function actionPoliticaPrivacidade(){
            $bodyHtmlName           = 'politicaPrivacidade';
            $objView                = mvc\MvcFactory::getView();
            $objViewPart            = mvc\MvcFactory::getViewPart($bodyHtmlName);                                             
            $this->tituloPage       = 'Política de Privacidade';
            $this->subtituloPage    = 'Entenda como lidamos com suas informações pessoais';
            
            $this->setPageContent($objViewPart,$bodyHtmlName);                      
        }   

        public function actionContato(){
            $bodyHtmlName           = 'contato';
            $objView                = mvc\MvcFactory::getView();
            $objViewPart            = mvc\MvcFactory::getViewPart($bodyHtmlName);                                             
            $this->tituloPage       = 'Entre em contato!';
            $this->subtituloPage    = 'Envie-nos sua dúvida. Estamos aqui para ajudar.';
            
            $this->setPageContent($objViewPart,$bodyHtmlName);          
            
        }
     }
?>
