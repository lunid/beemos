<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    use \sys\classes\util\Request;
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial da raíz do site.
    */
    class SobreNos extends Controller {
        /**
        *   Conteúdo da página Sobre Nós
        */
        static $arrMenuVertical = array('A Interbits','Política de Privacidade','Contato');
        function actionIndex(){
	    $this->actionAinterbits();
        }      
        
        function actionAinterbits(){      
            try{
                $objPartLayout                  = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG             = "<img src='app/views/images/testeira.jpg'>";
                $objPartLayout->LOCAL           = "Conheça a Interbits";
                $objPartLayout->MENU_VERTICAL   = \HtmlComponent::menuVertical(self::$arrMenuVertical);
                        
                $objPartPg                      = new ViewPart('sobreNos_aInterbits');            
                $objPartLayout->BODY            = $objPartPg->render();                                    

                $objView                        = new View($objPartLayout);            
                $objView->TITLE                 = 'SuperPro - A Interbits';
                
                $objView->setCssInc('pg_internas,menu_lateral');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('aInterbits');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionPolitica(){        
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira_politica.jpg'>";
                $objPartLayout->LOCAL   = "Conheça a nossa Política de Privacidade";

                $objPartPg              = new ViewPart('sobreNos_politica');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView                = new View($objPartLayout);            
                $objView->TITLE         = 'SuperPro - A Interbits';
                $objView->setCssInc('pg_internas,menu_lateral');                      

                $objView->forceCssJsMinifyOn();

                $objView->render('politica');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionContato(){ 
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
                $objPartLayout->LOCAL   = "Entre em contato com a Interbits";

                $objPartPg              = new ViewPart('sobreNos_contato');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView                = new View($objPartLayout);            
                $objView->TITLE         = 'SuperPro - A Interbits';

                $objView->setPlugin('tooltip');
                $objView->setCssInc('pg_internas,menu_lateral');                      
                $objView->setJsInc("init_contato");

                $objView->forceCssJsMinifyOn();

                $objView->render('contato');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionDisparaContato(){ 
            try{
                $ret            = new \stdClass();
                $ret->status    = true;
                $ret->msg       = "Mensagem enviada com sucesso! Em breve lhe responderemos.";
                
                //TODO Dispara e-mail
                $htmlUser   = HtmlComponent::emailContatoUser($_POST);
                $htmlInter  = HtmlComponent::emailContatoSite($_POST);
                
                $headers    = "From: interbits@interbits.com.br\r\n";
                $headers    .= "Reply-To: interbits@interbits.com.br\r\n";
                $headers    .= "MIME-Version: 1.0\r\n";
                $headers    .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                
                if(($htmlUser == null || trim($htmlUser) == "") && ($htmlInter == null || trim($htmlInter) == "")){
                    $ret->status    = true;
                    $ret->msg       = "Falha ao processar HTML e disparar e-mail! Tente mais tarde.";
                }else if(@mail("prg.pacheco@interbits.com.br", "[Contato] - Mensagem de contato enviada via site", $htmlInter, $headers)){
                    if(!@mail(strtolower(trim(Request::post("email"))), "Interbits - Confirmação de recebimento", $htmlUser, $headers)){
                        $ret->status    = false;
                        $ret->msg       = "Falha ao disparar e-mail! Tente mais tarde.";
                    }
                }else{
                    $ret->status    = false;
                    $ret->msg       = "Falha ao disparar e-mail! Tente mais tarde.";
                }
                
                echo json_encode($ret);
                die;    
            }catch(Exception $e){
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                echo json_encode($ret);
                die;    
            }
        }
    }
?>
