<?php       
    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;    
    use \common\db_tables as TB;
    
    class Newsletter extends mvc\ExceptionController {
        
        public function actionNovo(){
            
                $email  = Request::post('NEWSLETTER','STRING');
                $msgOut = 'Uma mensagem de confirmação foi enviada para seu e-mail.';
                
                if (strlen($email) > 0) {
                    //Faz o cadastro do novo e-mail.
                    $objTb = new TB\Newsletter();
                    $objTb->EMAIL           = $email;
                    $objTb->DATA_REGISTRO   = date('Y-m-d H:i:s');
                    $idNewsletter           = (int)$objTb->save();
                    
                    //Envia mensagem com link de validação do e-mail
                    $objMail = Component::mail(); 
                    $objMail->addAddress($email);
                    $objMail->setCco('claudio@supervip.com.br');
                    $objMail->setSubject("Supervip: Por favor, confirme seu e-mail.");                    
                    $objMail->setHtml(utf8_decode(""));                    
                                        
                    if($objMail->send()){
                        //Grava a flag que indica o envio da mensagem de confirmação.
                        $objTb = new Newsletter($idNewsletter);
                        $objTb->ENV_MSG_CONFIRM = 1;
                        $objTb->save();
                    }else{
                        $msgOut = "<font size='3' color='red'>Ocorreu uma falha ao efetuar seu cadastro. Por favor, tente mais tarde.</font>";
                    }                                       
                } else {
                    $msgOut = 'Um e-mail válido não foi informado.';
                }
                
                $layoutName     = 'newsletter_confirm';                
                $objViewPart    = mvc\MvcFactory::getViewPart($layoutName);
                
        
                $objView            = mvc\MvcFactory::getView();
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Supervip'; 
                $objView->MSG       = $msgOut;
                
                $objView->render($layoutName);
        }                
    }
?>
