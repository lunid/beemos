<?php

    use \sys\classes\mvc\Controller;    
    use \app\classes\ViewSite;        
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
        static $arrMenuVertical = array('aInterbits'=>'A Interbits','privacidade'=>'Política de Privacidade','contato'=>'Contato');                
        
        function actionIndex(){
	    $this->actionAinterbits();
        }      
        
        function actionAinterbits(){
            $objParams                  = new \stdClass();       
            $objParams->imgCab          = "<img src='app/views/images/testeira.jpg'>";
            $objParams->breadcumbs      = 'Conheça a Interbits';
            $objParams->htmlViewPart    = 'sobreNos_aInterbits';
            $objParams->title           = 'A Interbits';
            $objParams->layoutName      = 'aInterbits';
            
            $this->actionPgVertical($objParams);
        }
        
        function actionPrivacidade(){
            $objParams                  = new \stdClass();           
            $objParams->imgCab          = "<img src='app/views/images/testeira_politica.jpg'>";
            $objParams->breadcumbs      = 'Conheça nossa Política de Privacidade';
            $objParams->htmlViewPart    = 'sobreNos_politica';
            $objParams->title           = 'Política de Privacidade';
            $objParams->layoutName      = 'privacidade';
            
            $this->actionPgVertical($objParams);
        }        
                
        function actionContato(){
            $objParams                  = new \stdClass();           
            $objParams->imgCab          = "<img src='app/views/images/testeira.jpg'>";
            $objParams->breadcumbs      = 'Entre em contato com a Interbits';
            $objParams->htmlViewPart    = 'sobreNos_contato';
            $objParams->title           = 'Contato';
            $objParams->layoutName      = 'contato';
            
            $this->actionPgVertical($objParams);
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
        
        function actionPgVertical($objParams){
            try{
                if (is_object($objParams)) {                    
                    $imgCab          = $objParams->imgCab;
                    $breadcumbs      = $objParams->breadcumbs;
                    $htmlViewPart    = $objParams->htmlViewPart;
                    $title           = $objParams->title;
                    $layoutName      = $objParams->layoutName;
                    
                    $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                    $objPartLayout->IMG     = $imgCab;
                    $objPartLayout->LOCAL   = $breadcumbs;

                    $this->setMenuVertical($objPartLayout);

                    $objPartPg              = new ViewPart($htmlViewPart);            
                    $objPartLayout->BODY    = $objPartPg->render();                                    

                    $objView                = new ViewSite();
                    $objView->TITLE         = 'SuperPro - '.$title;
                    
                    $objView->setLayout($objPartLayout);                            
                    $objView->setCssInc('pg_internas,menu_lateral');                      

                    $objView->forceCssJsMinifyOn();

                    $objView->render($layoutName);
                }
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }        
        }
        
        private function setMenuVertical($objPartLayout){
            $objPartLayout->MENU_VERTICAL   = \HtmlComponent::menuVertical(self::$arrMenuVertical);                        
        }        
        
    }
?>
