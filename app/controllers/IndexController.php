<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\ViewPart;      
    use \app\classes\ViewSite;           
    use \app\models\HomeModel;

    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial da raíz do site.
    */
    class Index extends Controller {

        /**
        *Conteúdo da página home do site.
        */
        function actionIndex(){
            $objModel                           = new HomeModel();	    
            $objViewPart                        = new ViewPart('home');
            $objViewPart->TOTAL_QUESTOES_DB     = $objModel->getTotalQuestoesDb();
            $objViewPart->TOTAL_QUESTOES_ENEM   = $objModel->getTotalQuestoesEnem(); 
            
            $objView = new ViewSite($objViewPart);
            $objView->setLayout($objViewPart);
            $objView->forceCssJsMinifyOn();
            $objView->setPlugin('sliderBanner');            
            
            $objView->TITLE = 'Bem-vindo ao Super Professor Web';
            $objView->render('home');            
        }               
    }
?>

