<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolaModel;
    use \sys\classes\util\Date;
    use \sys\classes\util\Request;
    use \sys\classes\util\Component;
    use \sys\classes\security\Password;
    
    class Teste extends AdminController {
        
        function actionIndex(){
            
        }
        
        function actionMail(){            
            $objMail = Component::mail();    
            $objMail->smtpDebugOn();
            //$objMail->setTemplate('pasta/dsfdsf.txt');
            $objMail->setHtml('teste');
            
            $objMail->addAddress('claudio@supervip.com.br','Claudio');
            $objMail->setFrom('claudio@supervip.com.br','E-mail de contato');
            //$objMail->setHtml('<b>Teste</b>');
            $objMail->setSubject('Teste de e-mail');
            //$objMail->addAnexo();
            $objMail->printMsg();
            //if ($objMail->send()){
             //   echo 'mensagem enviada com sucesso.';
            //}
            //$objMail->setFrom('teste@supervip.com.br','Claudio');           
            
           
        }
        
        function actionPasswd(){
            echo Password::newPassword('llnnnlll');
        }
    }
?>
