<?php       
    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;    
    use \sys\classes\util\Component;
    use \common\db_tables as TB;
    use \common\classes\Menu;
    
    class Newsletter extends mvc\ExceptionController {
        
        public function actionNovo(){
            
                if (isset($_SESSION['NEWSLETTER_OK'])) {
                    unset($_SESSION['NEWSLETTER_OK']);
                    //header('Location:/site/');
                    //die();
                }
                
                $email  = Request::post('NEWSLETTER','STRING');
                $msgOut = 'Uma mensagem de confirmação foi enviada para seu e-mail.';
                                
                if (strlen($email) > 0 && 1==0) {
                    //Faz o cadastro do novo e-mail.
                    $objTb = new TB\Newsletter();
                    $objTb->EMAIL           = $email;
                    $objTb->DATA_REGISTRO   = date('Y-m-d H:i:s');
                    $idNewsletter           = (int)$objTb->save();
                    
                    //Envia mensagem com link de validação do e-mail
                    $msgHtml = "
                        
                    ";
                    
                    $objMail = Component::mail(); 
                    $objMail->addAddress($email);
                    $objMail->setCco('claudio@supervip.com.br');
                    $objMail->setSubject("Supervip: Confirmação ativa de cadastro");                    
                    $objMail->smtpDebugOn();
                    $objMail->setTextPlain('É necessário confirmar sua conta de e-mail.');
                    //$objMail->setHtml(utf8_decode("É necessário confirmar sua conta de e-mail."));                    
                                        
                    if($objMail->send()){
                        //Grava a flag que indica o envio da mensagem de confirmação.
                        $objTb = new TB\Newsletter($idNewsletter);
                        $objTb->ENV_MSG_CONFIRM = 1;
                        $objTb->save();
                        $_SESSION['NEWSLETTER_OK'] = 'S';
                    }else{
                        $msgOut = "<font size='3' color='red'>Ocorreu uma falha ao efetuar seu cadastro. Por favor, tente mais tarde.</font>";
                    }                                       
                } else {
                    $msgOut = 'Um e-mail válido não foi informado.';
                }
                
                $layoutName     = 'newsletter_confirm';                
                $objViewPart    = mvc\MvcFactory::getViewPart($layoutName);
                
        
                $objView            = mvc\MvcFactory::getView();
                $objView->setTemplate('/site/viewParts/br/templates/newsletter.html');
                $objView->setLayout($objViewPart);
                $objView->MENU_MAIN = Menu::main(__CLASS__);
                $objView->TITLE     = 'Supervip'; 
                $objView->MSG       = $msgOut;
                
                $objView->render($layoutName);
        }                
    }
?>
