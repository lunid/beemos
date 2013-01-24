<?php
           
    use \sys\classes\mvc as Mvc;    
    use \sys\classes\security\Token;    
    use \auth\classes\models\AuthModel;
    use \auth\classes\helpers\Error;
    use \sys\classes\util;
    
    class Login extends Mvc\ExceptionController {
        
        function actionFormAuth(){
            //Formulário de autenticação
            $objViewPart = Mvc\MvcFactory::getViewPart('formAuth');
                
            $objTk              = new Token(2);
            $tkField            = $objTk->protectForm();
            $errorMessage       = \Auth::getMessage();
            $divErrorMessage    = '';
            
            if (strlen($errorMessage) > 0) {
                $divErrorMessage = "<div class='notice error'>{$errorMessage}</div>";
                \Auth::unsetMessage();    
            }
            
            //Template
            $tpl                = Mvc\MvcFactory::getView();
            $tpl->setLayout($objViewPart);
            $tpl->TITLE         = 'Autenticação';
            $tpl->ERROR_MESSAGE = $divErrorMessage;
            $tpl->TOKEN_FIELD   = $tkField;
            
            //Instância de JS
            $tpl->setJs('auth/submitAuth');
            $tpl->setCss('auth/formAuth');
            //$tpl->forceCssJsMinifyOn();
            
            $tpl->render('auth');                        
        }
        
        /**
         * Recebe dados de acesso (login, senha e token) e faz a autenticação.
         */
       function actionProcessAuth(){
                $codigoPerfil   = '';
                $user           = util\Request::post('user');
                $passwd         = util\Request::post('passwd'); 
                $memorizar      = util\Request::post('memorizar', 'NUMBER');
                
                if($memorizar == 1){
                    //Cookie de email - 30 dias
                    setcookie("interbits_login", $user, time()+60*60*24*30, "/", "dev.interbits.com");
                }                                       
                
                //Verifica se o token é válido.
                $objTk = new Token(1);
                if ($objTk->Check()) {
                   //Token válido.
                   if (strlen($user) > 0 && strlen($passwd) > 0) {
                        $objAuthModel   = new AuthModel(); 
                        $passwdMd5      = md5($passwd);
                        $admin          = $objAuthModel->passwdAdmin($passwdMd5);
                        $objUsuario     = $objAuthModel->authAcesso($user,$passwdMd5,$admin);
                        $codigoPerfil   = $this->initLogon($objUsuario);          
                   } else {
                        $msgErr = Error::eLogin('PARAMS_REQUIRED');   
                        \Auth::setMessage($msgErr);
                   }
                } else {
                    $msgErr = Error::eLogin('TOKEN');  
                    \Auth::setMessage($msgErr);
                }      
                $this->redirect($codigoPerfil);                
        }
        
        /**
         * Recebe dados de acesso (login, senha e token) e faz a autenticação de um aluno.
         */
       function actionProcessAuthAluno(){
                $codigoPerfil   = '';
                $user           = util\Request::post('user');
                $passwd         = util\Request::post('passwd'); 
                $codLista       = util\Request::post('codLista');//Opcional
                $memorizar      = util\Request::post('memorizar', 'NUMBER');
                
                if($memorizar == 1){
                    //Memoriza o login/email do usuário em cookie - 30 dias
                    setcookie("interbits_login", $user, time()+60*60*24*30, "/", "dev.interbits.com");
                }
                       
                //$objRedirect    = new util\Redirect('auth/redirect.xml');
                //$redirect       = $objRedirect->FORM_ALUNO;
                
                //Verifica se o token é válido.
                $objTk = new Token(1);
                if ($objTk->Check()) {
                   //Token válido.
                   if (strlen($user) > 0 && strlen($passwd) > 0) {
                        $objAuthModel   = new AuthModel(); 
                        $passwdMd5      = md5($passwd);
                        $admin          = $objAuthModel->passwdAdmin($passwdMd5);                        
                        $objUsuario     = $objAuthModel->authAcesso($user,$passwdMd5,$admin);
                        
                        if ($objUsuario !== FALSE) {
                            $codigoPerfil = $objUsuario->CODIGO_PERFIL;
                            if($codigoPerfil != 'ALUNO'){
                                //Autenticação falhou.      
                                $msgErr = Error::eLogin('LOGIN_ALUNO');    
                                \Auth::setMessage($msgErr);
                            }else{
                                if($codLista != "") $_SESSION['COD_LISTA'] = $codLista;                                
                                $codigoPerfil = $this->initLogon($objUsuario);                                     
                            }   
                        } else {
                            //Autenticação falhou.      
                            $msgErr = Error::eLogin('LOGIN');    
                            \Auth::setMessage($msgErr);
                        }
                   } else {
                        $msgErr = Error::eLogin('PARAMS_REQUIRED');   
                        \Auth::setMessage($msgErr);
                   }
                } else {
                    $msgErr = Error::eLogin('TOKEN');  
                    \Auth::setMessage($msgErr);
                }      
                
                if (strlen($codigoPerfil) == 0) $codigoPerfil = 'FORM_ALUNO';
                $this->redirect($codigoPerfil);                
        }
        
        /**
         * Efetua o Login com uma conta do facebook
         */
        function actionFacebook(){
            //Biblioteca Facebbook
            /* @var $fbLib Facebookapi */
            $codigoPerfil   = '';
            $fbLib          = util\Component::facebookapi();
            $fbCode         = util\Request::get("code");                        
                
            //Verifica o retorno do Código de segurança do FB
            if($fbCode != "" && $fbCode != null){
                //Gera um Token de Acesso aos serviços FB
                $token_url  = "https://graph.facebook.com/oauth/access_token?client_id=".$fbLib->getAppId()."&redirect_uri=".$fbLib->getRedirectUrl()."&client_secret=".$fbLib->getSecretId()."&code=".$fbCode;
                $response   = @file_get_contents($token_url);
                
                //Verifica a geração do token
                if($response){
                    $params     = null;
                    parse_str($response, $params);
                    
                    //Busca dados do usuário no Facebook
                    $user = $fbLib->getUser($params['access_token']);

                    if(isset($user) && is_array($user)){
                        $_SESSION['fb_user'] = $user;
                        
                        //Busca Usuário em nossa Base
                        $objAuthModel   = new AuthModel(); 
                        $ret            = $objAuthModel->buscarUserFacebook($user);
                        
                        if($ret->status){                            
                            $objUsuario     = $objAuthModel->authAcesso($ret->user->EMAIL,$ret->user->PASSWD);
                            $codigoPerfil   = $this->initLogon($objUsuario);       
                        }else{
                            \Auth::setMessage($ret->msg);
                        }
                    }else{
                        $msgErr = Error::eLogin('FB_USER');   
                        \Auth::setMessage($msgErr);
                    }
                }else{
                    $msgErr = Error::eLogin('FB_TOKEN');   
                    \Auth::setMessage($msgErr);
                }
            }else{
                $msgErr = Error::eLogin('FB_CODE');   
                \Auth::setMessage($msgErr);
            }
            
            //Redireciona usuário
            $this->redirect($codigoPerfil);
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
            $codigoPerfil   = '';
            
            //Verifica se o token é válido.
            $objTk = new Token(1);
            if ($objTk->Check()) {
               //Token válido.
               if (strlen($hash) > 0) {
                    $objAuthModel   = new AuthModel(); 
                    $objUsuario     = $objAuthModel->carregarUsuarioHash($hash);                    
                    $codigoPerfil   = $this->initLogon($objUsuario);                    
               } else {
                    $msgErr = Error::eLogin('PARAMS_REQUIRED');   
                    \Auth::setMessage($msgErr);
               }
            } else {
                $msgErr = Error::eLogin('TOKEN');  
                \Auth::setMessage($msgErr);
            }      
            $this->redirect($codigoPerfil);
        }
        
    
        /**
         * Inicializa e persiste o objeto do usuário logado.
         * 
         * @return string Retorna o código do perfil do usuário, que servirá para redirecioná-lo para a área de destino.
         */
        private function initLogon($objUsuario){
            //Redirecionamento
            $codigoPerfil = '';
      
            if ($objUsuario !== FALSE && $objUsuario instanceof auth\classes\helpers\Usuario) { 
                //Autenticação feita com sucesso!            
                \Auth::persistUsuario($objUsuario);     
                \Auth::unsetMessage();//Limpa mensagens de erro que por ventura tenham ocorrido em tentativas anteriores.

                $codigoPerfil   = $objUsuario->CODIGO_PERFIL;                        
            } else {
                //Autenticação falhou.      
                $msgErr = Error::eLogin('LOGIN');    
                \Auth::setMessage($msgErr);                
            }
            return $codigoPerfil;
        } 
        
        /**
         * Após processar uma requisição de autenticação, o método de origem deve chamar
         * o método atual para que o usuário seja redirecionado para a área específica de seu perfil.
         * 
         * @param $codigoPerfil Este parâmetro deve ter um nó correspondente no arquivo redirect.xml. 
         * @return void
         */
        private function redirect($codigoPerfil=''){
            $objRedirect    = new util\Redirect('auth/redirect.xml');
            $redirect       = $objRedirect->FORM;
            if (strlen($codigoPerfil) > 0) $redirect = $objRedirect->$codigoPerfil;   
            
            //Redireciona para a área do perfil atual:
            Header('Location: /'.$redirect);  
        }
    }
?>
