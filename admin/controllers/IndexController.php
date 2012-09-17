<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \asmin\models\HomeModel;


    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial do admin.
    */
    class Index extends Controller {

        /**
        *Conteúdo da página home do admin.
        */
        function indexHome(){
            $objView                        = new View('home');

            $objView->TITLE                 = 'ADM | SuperPro';

            $objView->render();            
        }               
    }
?>

