<?php
    use \sys\classes\mvc\Controller;    
    use \sys\classes\util\Request;
    use \app\models\tables\AdmUsuario;
    
    /**
    * Classe Controller para área de Usuários
    */
    class Usuario extends Controller {
        /**
         * Efetua o Login do usuário utilizando Login e Senha
         */
        public function actionLogin(){
	    try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao efetuar login! Tente mais tarde.";
                //Obtendo valores
                $email = trim(strtolower(Request::post("login_email")));
                $senha = trim(Request::post("login_senha"));
                
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
        
        public function actionSair(){
            try{
                $ret            = new stdClass();
                $ret->status    = true;
                
                session_destroy();
                
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
