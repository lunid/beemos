<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    
    class Sobre extends mvc\ExceptionController {
        
        private function setPageContent($objViewPartPage){
            $objView        = mvc\MvcFactory::getView();
            $objViewPart    = mvc\MvcFactory::getViewPart('sobre_tpl');
            $objViewPart->MENU_VERTICAL = $this->setMenuVertical();
            $objViewPart->CONTENT       = $objViewPartPage->render();
            $bodyContent                = $objViewPart->render('tpl');            
            
            $objView->setContent($bodyContent);                
            $objView->TITLE     = 'Supervip - Sobre Nós';                
            $objView->MENU_MAIN = Menu::main(__CLASS__);
            return $objView;
        }
        
        private function setMenuVertical(){
            $arrMenu = array(
                array('link'=>'sobre/','text'=>'Apresentação'),
                array('link'=>'sobre/politica/','text'=>'Política de Privacidade'),
                array('link'=>'sobre/contato/','text'=>'Entre em contato'),
                array('link'=>'sobre/trabalheconosco/','text'=>'Trabalhe conosco')
            );
            $menu = '<ul class="alt">';
            foreach($arrMenu as $arrItem) {
                $menu .= "<li><a href='{$arrItem['link']}'>{$arrItem['text']}</a></li>";
            }                
            $menu .= '</ul>';            
            return $menu;
        }
        
        public function actionIndex(){               
                $bodyHtmlName   = 'sobre';
                $objView        = mvc\MvcFactory::getView();
                $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
                $objViewTpl     = $this->setPageContent($objViewPart);
                
                $objView->BODY  = $objViewTpl->render();
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
