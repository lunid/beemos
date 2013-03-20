<?php       
    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;    
    
    class Newsletter extends mvc\ExceptionController {
        
        public function actionNovo(){
            
                $email = Request::post('NEWSLETTER','STRING');
                
                if (strlen($email) > 0) {
                    //Faz o cadastro do novo e-mail.
                    
                }
                
                $layoutName     = 'newsletter_confirm';                
                $objViewPart    = mvc\MvcFactory::getViewPart($layoutName);
                
        
                $objView            = mvc\MvcFactory::getView();
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip';
                $objView->MENU_MAIN = Menu::main(__CLASS__);
                
                $objView->render($layoutName);
        }                
    }
?>
