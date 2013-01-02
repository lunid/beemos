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
                $user           = util\Request::post('user');
                $passwd         = util\Request::post('passwd'); 
                $token          = util\Request::post('token');
                       
                $objRedirect    = new util\Redirect('auth/redirect.xml');
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
                        
                        if ($objUser !== FALSE) {
                            //Autenticação feita com sucesso!
                            $idUser = $objUser->id;
                            $perfil = $objUser->perfil;
                            
                            \Auth::unsetMessage();//Limpa mensagens de erro que por ventura tenham ocorrido em tentativas anteriores.
                            \Auth::initSession($idUser);//Inicializa variáveis da sessão atual.
                            $redirect = $objRedirect->$perfil;                            
                        } else {
                            //Autenticação falhou.      
                            $msgErr = Error::eLogin('LOGIN');                                                             
                            throw new \Exception($msgErr);
                        }
                   } else {
                        $msgErr = Error::eLogin('PARAMS_REQUIRED');                                                             
                        throw new \Exception($msgErr);                                            
                   }
                } else {
                    $msgErr = Error::eLogin('TOKEN');                                                             
                    throw new \Exception($msgErr);                      
                }      
                Header('Location:'.$redirect);               
        }        
    }
?>
