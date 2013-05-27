<?php
           
    use \sys\classes\mvc as Mvc;    
    use \sys\classes\security\Token;    
    use \auth\classes\models\AuthModel;
    use \auth\classes\helpers\Error;
    use \sys\classes\util as util;
    
    class Login extends Mvc\ExceptionController {
        
        /**
         * Gera um token de acesso para o site atual.
         * 
         * @return string Token de acesso.
         */
        function actionGetToken(){
            $token              = '';
            $msg                = 'Infelizmente não foi possível completar sua requisição. Por favor, tente novamente.';
            $success            = FALSE;
            
            $dominioOrig        = $_SERVER['HTTP_REFERER'];
            $dominioAutorizado  = $this->checkAuthDomain($dominioOrig);            
            
            if ($dominioAutorizado) {
                //O domínio que fez a requisição possui permissão para pegar um token válido.
                $objTk          = new Token(1);//Inicia um token passando o seu tempo de existência em minutos.
                $newToken       = $objTk->getToken();                
                $tokenLength    = strlen($newToken);
                if ($tokenLength > 0 && $tokenLength == $objTk->getTokenLength()) {
                    //Token gerado com sucesso.
                    $token      = $newToken;
                    $msg        = 'Token gerado com sucesso';
                    $success    = TRUE;                    
                }
            } else {
              $msg  = 'A origem da requisição não possui autorização para esta ação. Infelizmente não é possível prosseguir.';  
            }
            $arrToken   = array('tk'=>$token,'msg'=>$msg,'success'=>$success);
            echo json_encode($arrToken);
        }
        
        /**
         * Verifica se o domínio informado possui autorização para uma requisição no módulo auth.
         * Método de suporte para actionGetToken();
         * 
         * A lista de domínios autorizados deve existir como tag filha (id=DOMAIN_LIST_AUTH_GET_TOKEN) da tag auth, no arquivo urls.xml.
         * 
         * @see actionGetToken()
         * @param string $domain Domínio a ser verificado. Ex.: http://www.dominio.com
         * @return boolean.
         */
        private function checkAuthDomain($domain){            
            $objUrl     = new \Url('/auth/urlsAuthGetToken.xml');            
            $objUrl->domainsAuthToken();
            
            $autorizado     = FALSE;    
            $ultimoChar     = substr($domain, -1, 1);
            if ($ultimoChar == '/') $domain = substr($domain, 0, strlen($domain)-1);//Retira o último caractere (/) do domínio.
            $listAuth       =  trim($objUrl->LIST_AUTH);
            $listAuth       = str_replace(';',',',$listAuth);
            $arrListAuth    = explode(',',$listAuth);  
            $key            = array_search($domain, $arrListAuth);            
            if ($key !== FALSE) {
                $autorizado = TRUE;
            }
            return $autorizado;
        }
        
        function actionFormAuth(){
            //Formulário de autenticação
            $objViewPart = Mvc\MvcFactory::getViewPart('formAuth');
                
            $objTk              = new Token(1);//Inicia um token passando o seu tempo de existência em minutos.
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
                setcookie("interbits_login", $user, time()+60*60*24*30, "/", "dev.superproweb.com.br");
            }

            //$objRedirect    = new util\Redirect('auth/redirect.xml');
            //$redirect       = $objRedirect->FORM;

            //Verifica se o token é válido.
            $objTk = new Token(1);
            if ($objTk->Check()) {
               //Token válido.
               if (strlen($user) > 0 && strlen($passwd) > 0) {
                    $objAuthModel   = new AuthModel(); 
                    $passwdMd5      = md5($passwd);                    
                    $objUsuario     = $objAuthModel->authAcesso($user,$passwdMd5);

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
         * Efetua o Login com uma conta do facebook
         */
        function actionFb(){
            //Biblioteca Facebbook
            /* @var $fbLib Facebookapi */
            $codigoPerfil   = '';
            $fbLib          = util\Component::facebookapi();
            $fbCode         = util\Request::get("code");
                       
            //Verifica o retorno do Código de segurança do FB
            if($fbCode != "" && $fbCode != null){
                //Gera um Token de Acesso aos serviços FB
                $token_url  = "https://graph.facebook.com/oauth/access_token?client_id=".$fbLib->getAppId()."&redirect_uri=".$fbLib->getRedirectUrl()."&client_secret=".$fbLib->getSecretId()."&code=".$fbCode;
                $response   = $this->urlGetContents($token_url);
              
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
                            $objUsuario     = $objAuthModel->authAcesso($ret->user->EMAIL, $ret->user->PASSWD);
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
        
        function urlGetContents ($url) {
            if (!function_exists('curl_init')){ 
                die('CURL não está instalado!');
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output     = curl_exec($ch);
            $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE); //get the code of request
            
            curl_close($ch);

            if($httpCode == 400) 
               return FALSE;
            if($httpCode == 200) //is ok?
               return $output;
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
            $codigoPerfil   = '';
            $hash           = util\Request::get('hash');
            
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
            $codigoPerfil = '';
            if ($objUsuario !== FALSE && $objUsuario instanceof common\classes\helpers\Usuario) { 
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
            if (($codigoPerfil == 'PROF' || $codigoPerfil == 'PROF_ESC')) {
                $objUsuario = \Auth::getUsuario();
                $hash       = $objUsuario->HASH;
                $email      = $objUsuario->EMAIL;
                $params     = base64_encode("hash={$hash}&email={$email}");
                $https      = \LoadConfig::baseUrlHttps();                
                $redirect   = $https.'/adv090109/lab/mod_superpro/?PARAMS='.$params.'&PG=LOGON&';                
            } else {                                   
                $objRedirect    = new util\Redirect('auth/redirect.xml');
                $redirect       = $objRedirect->FORM;
                if (strlen($codigoPerfil) > 0) $redirect = $objRedirect->$codigoPerfil;   
                $redirect = '/'.$redirect;
            }
 
            //Redireciona para a área do perfil atual:
            Header('Location:'.$redirect);  
            die();
        }
    }
?>
