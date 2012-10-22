<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    use \sys\classes\util\Request;
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página Assine Já do site.
    */
    class Assine extends Controller {
        /**
        *   Conteúdo da página Assine Já
        */
        function actionIndex(){
	    $this->actionPlanos();
        }      
        
        function actionPlanos(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira_politica.jpg'>";
                $objPartLayout->LOCAL   = "Planos e Preços";

                $objPartPg              = new ViewPart('assine_planos');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Assine já - Planos e Preços';
                $objView->setCssInc('pg_internas,menu_assineja,pg_assineja');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('planos');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionRecursos(){        
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
                $objPartLayout->LOCAL   = "Principais Recursos";

                $objPartPg              = new ViewPart('assine_recursos');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Assine Já - Principais Recursos';
                $objView->setCssInc('pg_internas,menu_lateral');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('recursos');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionPagamento(){ 
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira_pagamento.jpg'>";
                $objPartLayout->LOCAL   = "Formas de Pagamento";

                $objPartPg              = new ViewPart('assine_pagamento');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Assine Já - Formas de Pagamento';

                $objView->setCssInc('pg_internas,menu_lateral');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('pagamento');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
    }
?>

