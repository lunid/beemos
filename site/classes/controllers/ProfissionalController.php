<?php
    include('IdentificacaoController.php');

    class Profissional extends Identificacao {
        const ID_PLANO = 2;
        function actionIndex(){
             $this->actionForm(self::ID_PLANO,'Profissional');           
        }
    }
?>
