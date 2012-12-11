<?php
    class Index {
        public function actionIndex(){
            try{
                echo "WebService SuperPro";
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
