<?php
    
    use \sys\classes\error\XmlException;
    use \sys\classes\mvc as Mvc;
    class Error extends XmlException {
        
        function actionLogin() {
            if ($this->setFileXmlError('eLogin')){
                $msgErr      = $this->LOGIN;
                $objViewPart = Mvc\MvcFactory::getViewPart();
                $objViewPart->setContent($msgErr);
                $objView     = Mvc\MvcFactory::getView();
                //$objView->setTemplate();
                $objView->setLayout($objViewPart);  
                $objView->render('');   
            }
            die();
        }
    }
?>
