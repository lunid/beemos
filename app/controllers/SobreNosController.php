<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial da raíz do site.
    */
    class SobreNos extends Controller {
        /**
        *   Conteúdo da página Sobre Nós
        */
        function actionIndex(){
	    $this->actionAinterbits();
        }      
        
        function actionAinterbits(){          
            $objPartLayout          = new ViewPart('templates/navegacaoVertical');
            $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
            $objPartLayout->LOCAL   = "Conheça a Interbits";
            
            $objPartPg              = new ViewPart('sobreNos_aInterbits');            
            $objPartLayout->BODY    = $objPartPg->render();                                    
            
            $objView           = new View($objPartLayout);            
            $objView->TITLE    = 'SuperPro - A Interbits';
            $objView->setCssInc('pg_internas,menu_lateral');                      
            
            $objView->forceCssJsMinifyOn();
            
            $objView->render('aInterbits');    
        }
        
        function actionPolitica(){          
            $objPartLayout          = new ViewPart('templates/navegacaoVertical');
            $objPartLayout->IMG     = "<img src='app/views/images/testeira_politica.jpg'>";
            $objPartLayout->LOCAL   = "Conheça a nossa Política de Privacidade";
                    
            $objPartPg              = new ViewPart('sobreNos_Politica');            
            $objPartLayout->BODY    = $objPartPg->render();                                    
            
            $objView           = new View($objPartLayout);            
            $objView->TITLE    = 'SuperPro - A Interbits';
            $objView->setCssInc('pg_internas,menu_lateral');                      
            
            $objView->forceCssJsMinifyOn();
            
            $objView->render('politica');    
        }
        
        function actionContato(){          
            $objPartLayout          = new ViewPart('templates/navegacaoVertical');
            $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
            $objPartLayout->LOCAL   = "Entre em contato com a Interbits";
                    
            $objPartPg              = new ViewPart('sobreNos_Contato');            
            $objPartLayout->BODY    = $objPartPg->render();                                    
            
            $objView           = new View($objPartLayout);            
            $objView->TITLE    = 'SuperPro - A Interbits';
            
            $objView->setPlugin('tooltip');
            $objView->setCssInc('pg_internas,menu_lateral');                      
            $objView->setJsInc("sys:util.form,init_contato");
            
            $objView->forceCssJsMinifyOn();
            
            $objView->render('contato');    
        }
    }
?>

