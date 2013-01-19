<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    use \sys\classes\util\Request;
    use \sys\classes\util\Component;
    
    class Contato extends Controller {
        /**
         * Conteúdo da página Empresa
         */
        function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('contato');
                
                //Template
                $tpl = new ViewSite();
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | Contato';
                
                $tpl->setJs('app/contato');
                
                $tpl->render('contato');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        /**
         * Envia a mensagem do usuário via E-mail para o setor escolhido
         */
        public function actionEnviar(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao enviar mensagem! Tente mais tarde.";
                
                //Recebe valores
                $nome       = Request::post("nome");
                $email      = Request::post("email");
                $assunto    = Request::post("assunto", "NUMBER");
                $msg        = Request::post("msg");
                
                //Define destino
                switch ($assunto){
                    case 1:
                        $para       = "duvidas@interbits.com.br";
                        $assuntoTxt = "[Contato Site] Dúvidas";
                        break;
                    case 2:
                        $para       = "suporte@interbits.com.br";
                        $assuntoTxt = "[Contato Site] Suporte Técnico";
                        break;
                    case 3:
                        $para       = "cobranca@interbits.com.br";
                        $assuntoTxt = "[Contato Site] Cobrança / Financeiro";
                        break;
                    default :
                        $para       = "interbits@interbits.com.br";
                        $assuntoTxt = "[Contato Site] Dúvidas";
                        break;
                }
                
                $para = "mpcbarone@gmail.com";
                
                //Componente para disparo de e-mail
                
                /* @var $objMail Mail */
                $objMail = Component::mail();
                
                $objMail->setFrom("prg.pacheco@interbits.com.br", "Contato Site");
                $objMail->addAddress($para);
                $objMail->setSubject(utf8_decode($assuntoTxt));
                
                $html = "<b>Nome:</b> " . $nome . "<br />";
                $html .= "<b>E-mail:</b> " . $email . "<br /><br />";
                $html .= "<b>Mensagem</b><br />" . utf8_decode($msg);
                
                $objMail->setHtml($html);
                
                if (!$objMail->send()){
                    $ret->msg = "Falha ao disparar e-mail! Tente mais tarde.";
                    echo json_encode($ret);
                }else{
                    //Retorno OK
                    $ret->status    = true;
                    $ret->msg       = "E-mail enviado com sucesso!<br />Em breve entraremos em contato com você.";
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
