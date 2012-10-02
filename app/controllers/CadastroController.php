<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    use \sys\classes\util\Request;
    use \app\models\tables\AdmUsuario;
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página Assine Já do site.
    */
    class Cadastro extends Controller {
        /**
        *   Conteúdo da página Assine Já
        */
        function actionIndex(){
	    $objPartPg  = new ViewPart('cadastro');  
            $objView    = new View($objPartPg);

            $objView->setJsInc('init_cadastro');
            $objView->forceCssJsMinifyOn();
            
            $objView->TITLE = 'SuperPro - Cadastre-se';
            
            $objView->render('cadastro');    
        }
        
        /**
         * Efetua o Login do usuário utilizando Login e Senha
         */
        public function actionLogin(){
	    try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao efetuar login! Tente mais tarde.";
                //Obtendo valores
                $email = trim(strtolower(Request::post("email_login")));
                $senha = trim(Request::post("senha_login"));
                
                if($email == "" && $senha == ""){
                    $ret->msg = "Preencha os campos Login e Senha";                    
                }else{
                    $m_admUsuario           = new AdmUsuario();
                    $m_admUsuario->EMAIL    = $email;
                    $m_admUsuario->SENHA    = $senha;
                    
                    $ret = $m_admUsuario->validaUsuarioSenha();
                    
                    if($ret->status == true){
                        $_SESSION['user_site'] = serialize($m_admUsuario);
                    }
                }
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        
        /**
         * Efetua o Login do usuário utilizando Login e Senha
         */
        public function actionEsqueci(){
	    try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao recuperar senha! Tente mais tarde.";
                
                //Obtendo valores
                $email = trim(strtolower(Request::post("email_esqueci")));
                
                if($email == ""){
                    $ret->msg = "Preencha os campo E-mail";                    
                }else{
                    $m_admUsuario           = new AdmUsuario();
                    $m_admUsuario->EMAIL    = $email;
                    
                    $ret = $m_admUsuario->esqueciSenha();
                }
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        public function actionNovo(){
	    try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao recuperar senha! Tente mais tarde.";
                
                //Obtendo valores
                $nome       = trim(Request::post("nome_cadastro"));
                $email      = trim(strtolower(Request::post("email_cadastro")));
                $telefone   = trim(strtolower(Request::post("telefone_cadastro")));
                $fb_id      = trim(Request::post("fb_id"));
                $google_id  = trim(Request::post("google_id"));
                $id_perfil  = Request::post("id_perfil", "NUMBER");
                
                if($email == ""){
                    $ret->msg = "Preencha os campo E-mail";                    
                }if($id_perfil != 2 && $id_perfil != 9 && $id_perfil != 10){
                    $ret->msg = "O perfil selcionado não é válido";
                }else{
                    $m_admUsuario                   = new AdmUsuario();
                    $m_admUsuario->NOME             = $nome;
                    $m_admUsuario->EMAIL            = $email;
                    $m_admUsuario->TELEFONE         = $telefone;
                    $m_admUsuario->SENHA            = md5(123123);
                    $m_admUsuario->ID_PERFIL        = $id_perfil;
                    $m_admUsuario->FB_ID            = $fb_id != '' ? $fb_id : NULL;
                    $m_admUsuario->GOOGLE_ID        = $google_id != '' ? $google_id : NULL;
                    $m_admUsuario->STATUS           = 'I';
                    $m_admUsuario->DATA_REGISTRO    = date("Y-m-d H:i:s");
                    
                    $ret = $m_admUsuario->cadastrarUsuarioSite();
                }
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
    }
?>

