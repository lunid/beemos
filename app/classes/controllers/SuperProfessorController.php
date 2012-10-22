<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    use \sys\classes\util\Request;
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página Assine Já do site.
    */
    class SuperProfessor extends Controller {
        /**
        *   Conteúdo da página Assine Já
        */
        function actionIndex(){
	    $this->actionSuperPro();
        }      
        
        function actionSuperPro(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
                $objPartLayout->LOCAL   = "Planos e Preços";

                $objPartPg              = new ViewPart('superpro_superpro');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Super Professor';
                $objView->setCssInc('pg_internas,menu_lateral');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('superpro');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionGerarProvas(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
                $objPartLayout->LOCAL   = "Gerador de Provas";

                $objPartPg              = new ViewPart('superpro_provas');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Super Professor - Gerador de Provas';
                $objView->setCssInc('pg_internas,menu_lateral');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('provas');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionGerarListas(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
                $objPartLayout->LOCAL   = "Gerador de Provas";

                $objPartPg              = new ViewPart('superpro_listas');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Super Professor - Gerador de Listas';
                $objView->setCssInc('pg_internas,menu_lateral');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('listas');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionRelatorios(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
                $objPartLayout->LOCAL   = "Gráficos e Relatórios";

                $objPartPg              = new ViewPart('superpro_relatorios');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Super Professor - Gráficos e Relatórios';
                $objView->setCssInc('pg_internas,menu_lateral');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('relatorios');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
    }
?>

