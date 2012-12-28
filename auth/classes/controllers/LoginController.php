<?php
    
    //use \auth\classes\models\AuthModel;
    use \sys\classes\mvc\View; 
    use \sys\classes\mvc\ViewPart; 
    use \sys\classes\security\Token;
    use \sys\classes\util\Request;
    use \auth\classes\models\AuthModel;
    use \sys\classes\util\Dic;
    use \sys\classes\util\Redirect;
    
    class Login {
        
        public static function actionFormAuth(){
            //Formulário de autenticação
            $objViewPart = new ViewPart('formAuth');
                
            $objTk              = new Token(2);
            $tkField            = $objTk->protectForm();
            $errorMessage       = \Auth::getMessage();
            $divErrorMessage    = '';
            
            if (strlen($errorMessage) > 0) {
                $divErrorMessage = "<div class='notice error'>{$errorMessage}</div>";
                \Auth::unsetMessage();    
            }
            
            //Template
            $tpl                = new View();
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
        public static function actionProcessAuth(){
                $user           = Request::post('user');
                $passwd         = Request::post('passwd'); 
                $token          = Request::post('token');
                       
                $objRedirect    = new Redirect('auth/redirect.xml');
                $redirect       = $objRedirect->FORM;
                
                //Verifica se o token é válido.
                $objTk = new Token(1);
                if ($objTk->Check()) {
                   //Token válido.
                   if (strlen($user) > 0 && strlen($passwd) > 0) {
                        $objAuthModel   = new AuthModel(); 
                        $passwdMd5      = md5($passwd);
                        $admin          = $objAuthModel->passwdAdmin($passwdMd5);
                        $objUser        = $objAuthModel->authAcesso($user,$passwdMd5,$admin);
                        
                        if ($objUser !== FALSE && 1==0) {
                            //Autenticação feita com sucesso!
                            $idUser = $objUser->id;
                            $perfil = $objUser->perfil;
                            
                            \Auth::unsetMessage();//Limpa mensagens de erro que por ventura tenham ocorrido em tentativas anteriores.
                            \Auth::initSession($idUser);//Inicializa variáveis da sessão atual.
                            $redirect = $objRedirect->$perfil;                            
                        } else {
                            //Autenticação falhou.
                            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_LOGIN',FALSE);
                            \Auth::setMessage($msgErr);
                        }
                   } else {
                       //Parâmetros obrigatórios não informados.
                       $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_PARAMS_REQUIRED',FALSE);
                       \Auth::setMessage($msgErr);                        
                   }
                } else {
                    $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_TOKEN',FALSE);
                    \Auth::setMessage($msgErr);    
                }      
                
                Header('Location:'.$redirect);               
        }        
    }
?>
