<?php
    include('IdentificacaoController.php');

    class Corporativo extends Identificacao {
        const ID_PLANO = 3;
        
        function actionIndex(){
             $this->actionForm(self::ID_PLANO,'Corporativo');           
        }
    }
?>
