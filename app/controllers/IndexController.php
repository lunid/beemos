<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \app\models\HomeModel;


    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial da raíz do site.
    */
    class Index extends Controller {

        /**
        *Conteúdo da página home do site.
        */
        function indexHome(){
            $objModel           = new HomeModel();	    
            $totalQuestoesDb    = $objModel->getTotalQuestoesDb();
            $totalQuestoesEnem  = $objModel->getTotalQuestoesEnem(); 

            $objView                        = new View('home');
            //$objView->setMinify(TRUE);

            $objView->TITLE                 = 'Bem-vindo ao SuperPro';
            $objView->TOTAL_QUESTOES_DB     = $totalQuestoesDb;
            $objView->TOTAL_QUESTOES_ENEM   = $totalQuestoesEnem;

            $objView->setPlugin('sliderBanner');
            $objView->setPlugin('menuHorizontal');
            $objView->setPlugin('menuIdiomas');
            $objView->render();            
        }               
    }
?>

