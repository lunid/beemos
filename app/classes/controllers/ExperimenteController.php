<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    use \sys\classes\util\Request;
    use \common\classes\Util;
    use \common\classes\models\UsuariosModel;
    use \sys\classes\util\Component;
    
    class Experimente extends Controller {
        /**
         * Conteúdo da página Experimente Grátis
         */
        function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('experimente_gratis');
                
                //Template
                $tpl        = new ViewSite();
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | Experimente Grátis agora Mesmo!';
                
                //Js para inclusão
                $tpl->setJs('app/experimente_gratis');            
                
                $tpl->render('experimente_gratis');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function actionSalvarVisitante(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao cadastrar Usuário! Tente mais tarde.";
                
                //Recebe valores
                $nome       = Request::post("nome");
                $email      = strtolower(Request::post("email"));
                $celular    = Util::limpaTel(Request::post("celular"));
                $senha      = md5(Request::post("senha"));
                $c_senha    = md5(Request::post("c_senha"));
                
                //Valida senha
                if($senha !== $c_senha){
                    $ret->msg = "O campo Senha e Confirmar Senha são diferentes!";
                    echo json_encode($ret);
                    die;
                }
                
                //Salva usuário no banco de dados
                if(true){
                    //Model de Usuários
                    $mdUsuarios = new UsuariosModel();
                    
                    //Dados a serem cadastrados
                    $arrDados = array(
                        "NOME"      => $nome,
                        "EMAIL"     => strtolower($email),
                        "CELULAR"   => Util::limpaTel($celular),
                        "SENHA"     => $senha
                    );
                    
                    //Efetua cadastro
                    $rs = $mdUsuarios->salvarUsuarioVistante($arrDados);
                    
                    if(!$rs->status){
                        echo json_encode($rs);
                        die;
                    }
                    
                    //Componente para disparo de e-mail
                    $objMail = Component::mail();

                    $objMail->setFrom("prg.pacheco@interbits.com.br", "Interbits - SuperPro Web");
                    $objMail->addAddress($email);
                    $objMail->setSubject("Bem-vindo ao SuperProWeb");

                    $html = "Olá <b>{$nome}</b>!<br /><br />";
                    
                    $html .= "Sua conta no <b>SuperPro® Web</b> foi criada com sucesso.<br />";
                    $html .= "Por favor, clique no link abaixo para confirmar seu e-mail e definir sua senha de acesso.<br /><br />";
                    
                    $html .= "<a href=''>Confirmar meu e-mail e definir senha de acesso</a><br /><br />";
                    
                    $html .= "Obrigado<br /><br />";
                    
                    $html .= "-----------------------------------------------------------------------------<br />";
                    $html .= "<b>Equipe SuperPro®</b><br />";
                    $html .= "Mensagem automática - não é necessário respondê-la.<br />";

                    $objMail->setHtml(utf8_decode($html));
                    
                    $objMail->send();
                    
                    //Retorno OK
                    $ret = $rs;
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
