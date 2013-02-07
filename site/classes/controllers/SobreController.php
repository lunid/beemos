<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Sobre extends mvc\ExceptionController {
        
        private function setPageContent($objViewPartPage,$layoutName){         
            //Cria uma viewPart com o arquivo template para o item "Sobre Nós":
            $objViewPart                = mvc\MvcFactory::getViewPart('tpl_sobre');
            $objViewPart->MENU_VERTICAL = $this->setMenuVertical();//Coloca conteúdo na coluna da esquerda (menu vertical)
            $objViewPart->CONTENT       = $objViewPartPage->render();//Coloca conteúdo na coluna da direita (maior)
            
            //Gera a página atual a partir da viewPart:
            $objView                    = mvc\MvcFactory::getView();
            $objView->TITLE             = 'Supervip - Sobre nós';
            $objView->MENU_MAIN         = Menu::main(__CLASS__);            
            
            $objView->setLayout($objViewPart);
            
            $listCss    = 'site.common,site.sobre';
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
                array('link'=>'sobre/politica/','page'=>'politica','text'=>'Política de Privacidade'),
                array('link'=>'sobre/contato/','page'=>'contato','text'=>'Entre em contato'),
                array('link'=>'sobre/trabalheconosco/','page'=>'cv','text'=>'Trabalhe conosco')
            );
            $menu = '<ul class="alt">';
            foreach($arrMenu as $arrItem) {
                $sel = ($arrItem['page'] == $page)?"class='active'":'';
                $menu .= "<li><a href='{$arrItem['link']}' {$sel}>{$arrItem['text']}</a></li>";
            }                
            $menu .= '</ul>';            
            return $menu;
        }
        
        public function actionIndex(){               
            $bodyHtmlName   = 'sobre';
            $objView        = mvc\MvcFactory::getView();
            $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                                             
            
            $this->setPageContent($objViewPart,$bodyHtmlName);                              
        }
        
        public function actionPolitica(){
                $bodyHtmlName   = 'politicaPrivacidade';
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
                $objView        = mvc\MvcFactory::getView();
                
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip - Política de Privacidade';
                $objView->MENU_MAIN = Menu::main(__CLASS__);

                $listCss    = 'site.home';
                $listJs     = '';
                $listCssInc = '';
                $listJsInc  = '';
                $listPlugin = '';
                
               
                $objView->setCss($listCss);
                
                /*
                $objView->setJs($listJs);
                $objView->setCssInc($listCssInc);
                $objView->setJsInc($listJsInc);
                $objView->setPlugin($listPlugin);
                */             
                $layoutName = 'sobre';
                $objView->render($layoutName);            
            
        }   

        public function actionContato(){
                $bodyHtmlName   = 'contato';
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
                $objView        = mvc\MvcFactory::getView();
                
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip - Entre em contato';                
                $objView->MENU_MAIN = Menu::main(__CLASS__);

                $listCss    = 'site.common,site.sobre';
                $listJs     = '';
                $listCssInc = '';
                $listJsInc  = '';
                $listPlugin = '';
                
               
                $objView->setCss($listCss);
                
                /*
                $objView->setJs($listJs);
                $objView->setCssInc($listCssInc);
                $objView->setJsInc($listJsInc);
                $objView->setPlugin($listPlugin);
                */             
                $layoutName = 'sobre';
                $objView->render($layoutName);            
            
        }
     }
?>
