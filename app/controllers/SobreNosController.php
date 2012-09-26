<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    use \app\models\HomeModel;       
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial da raíz do site.
    */
    class SobreNos extends Controller {
        
        /**
        * Conteúdo da página Sobre Nós        
         * Teste xxx
        */
        function actionIndex(){
	    $this->actionAinterbits();
        }      
        
        function actionAinterbits(){          
            $objPartLayout          = new ViewPart('templates/navegacaoVertical');
            $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
            
            $objPartPg              = new ViewPart('sobreNos_aInterbits');            
            $objPartLayout->BODY    = $objPartPg->render();                                    
            
            $objView           = new View($objPartLayout);            
            $objView->TITLE    = 'SuperPro - A Interbits';
            $objView->setCssInc('pg_internas,menu_lateral');                      
            
            $objView->forceCssJsMinifyOn();
            
            $objView->render('aInterbits');    
        }
    }
?>

