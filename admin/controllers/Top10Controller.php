<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        


    /**
    * Refere-se à página de avaliação de questões do Top10.
    */
    class Top10 extends Controller {

        /**
        * Conteúdo da página home do admin.
        */
        function questoes(){
            $objView        = new View('top10_questoes');
            $objView->TITLE = 'ADM | Top 10 | Avaliar Questões | SuperPro';

            $objView->setPlugin("abas");
            $objView->setPlugin("drop");
            $objView->setPlugin("menu_slider");
            
            $objView->setMinify(TRUE);
            
            $objView->render();            
        }               
    }
?>

