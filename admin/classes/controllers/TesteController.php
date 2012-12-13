<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolaModel;
    use \sys\classes\util\Date;
    use \sys\classes\util\Request;
    use \sys\classes\util\Component;

    class Teste extends AdminController {
        
        function actionIndex(){
            
        }
        
        function actionMail(){
            $arrParams['address'] = 'claudio@supervip.com.br';
            $objMail = Component::mail();
            $objMail->addAddress('claudio@supervip.com.br','Claudio');
            $objMail->addAddress('teste@supervip.com.br');
            $objMail->getAddrress();
        }
    }
?>
