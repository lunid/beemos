<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    use \sys\classes\util\Request;
    use \app\models\tables\Categoria;
    
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
                $objView->setCssInc('pg_internas,menu_lateral,pg_tutoriais');                      
                
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
                $m_categoria            = new Categoria();
                //Opções do select
                $opts_sel                   = new stdClass();
                $opts_sel->id               = "categoria_id";
                $opts_sel->field_name       = "Categoria";
                $opts_sel->first_option     = "Selecione uma categoria";
                $opts_sel->class            = "required";
                $opts_sel->select_option    = Request::post("categoria_id", "NUMBER");
                
                $objPartPg->CATEGORIAS  = HtmlComponent::select($m_categoria->getCategoriasSelectBox(), $opts_sel);
                
                $objPartLayout->BODY    = $objPartPg->render();                                    
                
                
                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Ajuda - Fale com o Suporte';
                
                $objView->setPlugin('tooltip');
                $objView->setCssInc('pg_internas,menu_lateral');                      
                $objView->setJsInc("sys:util.form,init_suporte");
                
                $objView->forceCssJsMinifyOn();

                $objView->render('suporte');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionChat(){      
            try{
                $objPartLayout          = new ViewPart('templates/navegacaoVertical');
                $objPartLayout->IMG     = "<img src='app/views/images/testeira.jpg'>";
                $objPartLayout->LOCAL   = "Chat On-Line";

                $objPartPg              = new ViewPart('ajuda_chat');            
                $objPartLayout->BODY    = $objPartPg->render();                                    

                $objView           = new View($objPartLayout);            
                $objView->TITLE    = 'SuperPro - Ajuda - Chat On-line';
                $objView->setCssInc('pg_internas,menu_lateral');                      
                
                $objView->forceCssJsMinifyOn();

                $objView->render('chat');    
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
        
        function actionDisparaSuporte(){ 
            try{
                $ret            = new \stdClass();
                $ret->status    = true;
                $ret->msg       = "Mensagem enviada com sucesso! Em breve lhe responderemos.";
                
                //TODO Dispara e-mail
                $htmlUser   = HtmlComponent::emailSuporteUser($_POST);
                $htmlInter  = HtmlComponent::emailContatoSite($_POST);
                
                $m_categoria = new Categoria();
                if(!$ret_cat = $m_categoria->carregaEmailCategoria((int)Request::post("categoria_id"))){
                    throw new Exception($ret_cat->msg);
                }
                
                $headers = "From: interbits@interbits.com.br\r\n";
                $headers .= "Reply-To: interbits@interbits.com.br\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                
                if(($htmlUser == null || trim($htmlUser) == "") && ($htmlInter == null || trim($htmlInter) == "")){
                    $ret->status    = false;
                    $ret->msg       = "Falha ao processar HTML e disparar e-mail! Tente mais tarde.";
                }else if(@mail($ret_cat->email, "[Suporte] - Mensagem enviada via site", $htmlInter, $headers)){
                    if(!@mail(strtolower(trim(Request::post("email"))), "Interbits Suporte - Confirmação de recebimento", $htmlUser, $headers)){
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

