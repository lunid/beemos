<?php
           
    use \sys\classes\mvc as Mvc;    
    use \sys\classes\security\Token;    
    use \auth\classes\models\AuthModel;
    use \auth\classes\helpers\ErrorHelper;
    use \sys\classes\util;
    
    class Cadastro extends Mvc\ExceptionController {
        /**
         * Efetua o Login com uma conta do facebook
         */
        function actionFacebook(){
            //Biblioteca Facebbook
            /* @var $fbLib Facebookapi */
            $fbLib  = util\Component::facebookapi();
            $fbCode = util\Request::get("code");
            $local  = util\Request::get("local");
            
            //Redirecionamento
            $objRedirect    = new util\Redirect('auth/redirect.xml');
            
            switch ($local){
                case 'experimente';
                        $redirect = $objRedirect->FORM_EXPERIMENTE;
                        break;
                case 'aluno';
                        $redirect = $objRedirect->FORM_ALUNO;
                        break;
                default:
                        $redirect = $objRedirect->FORM_EXPERIMENTE;
                        break;
            }
            
            //Verifica o retorno do Código de segurança do FB
            if($fbCode != "" && $fbCode != null){
                //Gera um Token de Acesso aos serviços FB
                $token_url  = "https://graph.facebook.com/oauth/access_token?client_id=".$fbLib->getAppId()."&redirect_uri=".urlencode($fbLib->getRedirectUrlCadastro()."/?local={$local}")."&client_secret=".$fbLib->getSecretId()."&code=".$fbCode;
                $response   = @file_get_contents($token_url);
                
                //Verifica a geração do token
                if($response){
                    $params = null;
                    parse_str($response, $params);
                    
                    //Busca dados do usuário no Facebook
                    $user = $fbLib->getUser($params['access_token']);

                    if(isset($user) && is_array($user)){
                        //Limpa session
                        unset($_SESSION['fb_user_cadastro']);
                        
                        //Busca Usuário em nossa Base
                        $objAuthModel   = new AuthModel(); 
                        $ret            = $objAuthModel->buscarUserFacebook($user);
                        
                        \Auth::unsetMessage();//Limpa mensagens de erro que por ventura tenham ocorrido em tentativas anteriores.
                        
                        if($ret->status){
                            \Auth::setMessage(ErrorHelper::eLogin('FB_USER_EXISTS'));
                        }else{
                            //Inicia sessão
                            $_SESSION['fb_user_cadastro'] = $user;
                        }
                    }else{
                        $msgErr = ErrorHelper::eLogin('FB_USER');   
                        \Auth::setMessage($msgErr);
                    }
                }else{
                    $msgErr = ErrorHelper::eLogin('FB_TOKEN');   
                    \Auth::setMessage($msgErr);
                }
            }else{
                $msgErr = ErrorHelper::eLogin('FB_CODE');   
                \Auth::setMessage($msgErr);
            }
            
            //Redireciona usuário
            Header('Location: /'.$redirect);   
        }
        
        /**
         * Efetua o logoff do usuário
         */
        public function actionLogoff(){
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao efetuar Logoff! Tente mais tarde.";
            
            \Auth::unsetMessage();
            \Auth::logout();
            
            $ret->status    = true;
            $ret->msg       = "Logoff efetuado com sucesso!";
            
            echo json_encode($ret);
            die;
        }
        
        /**
         * Efetua o login do usuário através de um 
         */
        public function actionAuthHash(){
            $hash           = util\Request::get('hash');

            $objRedirect    = new util\Redirect('auth/redirect.xml');
            $redirect       = $objRedirect->FORM;

            //Verifica se o token é válido.
            $objTk = new Token(1);
            if ($objTk->Check()) {
               //Token válido.
               if (strlen($hash) > 0) {
                    $objAuthModel   = new AuthModel(); 
                    $objUser        = $objAuthModel->carregarUsuarioHash($hash);
                    
                    if ($objUser !== FALSE) {
                        //Autenticação feita com sucesso!
                        $idUser = $objUser->id;
                        $perfil = $objUser->perfil;

                        //Armazena objeto em sessão
                        \Auth::setUserSession($objUser);

                        \Auth::unsetMessage();//Limpa mensagens de erro que por ventura tenham ocorrido em tentativas anteriores.
                        \Auth::initSession($idUser);//Inicializa variáveis da sessão atual.
                        $redirect = $objRedirect->$perfil;
                    } else {
                        //Autenticação falhou.      
                        $msgErr = ErrorHelper::eLogin('LOGIN');    
                        \Auth::setMessage($msgErr);
                    }
               } else {
                    $msgErr = ErrorHelper::eLogin('PARAMS_REQUIRED');   
                    \Auth::setMessage($msgErr);
               }
            } else {
                $msgErr = ErrorHelper::eLogin('TOKEN');  
                \Auth::setMessage($msgErr);
            }      
            Header('Location: /'.$redirect);  
        }
    }
?>
