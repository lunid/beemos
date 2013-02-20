<?php
    
use \sys\classes\mvc as mvc;   
    
class Controllers extends mvc\Controller {
    
        public function actionNovo(){
            foreach($_REQUEST as $var=>$value) {
                echo "$var = $value <br>";
            }
        }                    
}

?>
