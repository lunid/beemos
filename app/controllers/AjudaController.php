<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    use \sys\classes\util\Request;
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página Assine Já do site.
    */
    class Ajuda extends Controller {
        /**
        *   Conteúdo da página Assine Já
        */
        function actionIndex(){
	    $this->actionFaq();
        }      
        
        function actionFaq(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira_duvidas.jpg'>";
                $objPartLayout->LOCAL   = "F.A.Q.";

                $objPartPg              = new ViewPart('ajuda_faq');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Ajuda - F.A.Q.';
                $objView->setCssInc('pg_internas,menu_lateral');                      
                
                $objView->setPlugin('accordeon');
                
                $objView->forceCssJsMinifyOn();

                $objView->render('faq');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionTutoriais(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira_video.jpg'>";
                $objPartLayout->LOCAL   = "Assista nossos Tutoriais";

                $objPartPg              = new ViewPart('ajuda_tutoriais');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Ajuda - Tutoriais';
                $objView->setCssInc('pg_internas,menu_lateral');                      
                
                $objView->forceCssJsMinifyOn();

                $objView->render('tutoriais');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionSuporte(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira_video.jpg'>";
                $objPartLayout->LOCAL   = "Fale com o Suporte";

                $objPartPg              = new ViewPart('ajuda_suporte');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Ajuda - Fale com o Suporte';
                $objView->setCssInc('pg_internas,menu_lateral');                      
                
                $objView->forceCssJsMinifyOn();

                $objView->render('suporte');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
    }
?>

