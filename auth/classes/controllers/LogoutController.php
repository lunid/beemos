<?php
use \sys\classes\util\Redirect;
class Logout {
        
        public function actionIndex(){
            \Auth::logout();
            $objRedirect    = new Redirect('auth/redirect.xml');
            $redirect       = $objRedirect->FORM;     
            header('Location:'.$redirect);
        }
}        
?>
