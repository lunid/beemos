<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    
    class Index extends Controller {

        /**
        *Conteúdo da página home
        */
        function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('home');

                //Template
                $tpl        = new ViewSite();
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web';

                $tpl->setPlugin('diapo');
                $tpl->setJs('app/home');                
                
                $tpl->render('home');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function actionExperimente(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao cadastrar Usuário! Tente mais tarde.";
                
                //Recebe valores
                $nome       = Request::post("nome");
                $email      = Request::post("email");
                $celular    = Request::post("celular");
                
                //Salva usuário no banco de dados
                if(true){
                    $para = "mpcbarone@gmail.com";
                
                    //Componente para disparo de e-mail
                    $objMail = Component::mail();

                    $objMail->setFrom("prg.pacheco@interbits.com.br", "Contato Site");
                    $objMail->addAddress($para);
                    $objMail->setSubject(utf8_decode($assuntoTxt));

                    $html = "<b>Nome:</b> " . $nome . "<br />";
                    $html .= "<b>E-mail:</b> " . $email . "<br /><br />";
                    $html .= "<b>Mensagem</b><br />" . utf8_decode($msg);

                    $objMail->setHtml($html);
                    
                    $objMail->send();
                    
                    //Retorno OK
                    $ret->status    = true;
                    $ret->msg       = "E-mail cadastrado com sucesso!";
                }else{
                    $ret->msg = "Falha ao cadastrar usuário! Tente mais tarde.";
                }
                
                echo json_encode($ret);
            }catch(Exception $e){
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro: " . $e->getMessage() . " - Arquivo: " . $e->getFile() . " - Linha: " . $e->getFile();
                
                echo json_encode($ret);
            }
        }
    }

?>
