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
        *Conteúdo da página Sobre Nós
        */
        function indexHome(){
	    $this->aInterbits();
            //$objPartPg  = new ViewPart('blank');  
            //$objView    = new View($objPartPg);         
            //$objView->render('index');
        }      
        
        function aInterbits(){          
            $objPartPg              = new ViewPart('blank');            
            $objPartLayout          = new ViewPart('templates/navegacaoVertical');
            $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
            $objPartLayout->BODY    = $objPartPg->render();                                    
            
            $objView           = new View($objPartLayout);            
            $objView->TITLE    = 'SuperPro - A Interbits';
            $objView->setCssInc('pg_internas,menu_lateral');                      
            
            $objView->forceCssJsMinifyOn();
            //$objView->onlyExternalCssJs();
                        
            $objView->render('aInterbits');    
        }
    }
?>

