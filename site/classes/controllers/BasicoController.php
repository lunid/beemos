<?php
    include('IdentificacaoController.php');

    class Basico extends Identificacao {
        const ID_PLANO = 1;
        function actionIndex(){
             $this->actionForm(self::ID_PLANO,'Básico');           
        }
    }
?>
