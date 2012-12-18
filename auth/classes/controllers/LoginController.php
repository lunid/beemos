<?php
    
    //use \auth\classes\models\AuthModel;
    use \sys\classes\mvc\View; 
    use \sys\classes\mvc\ViewPart; 
    use \sys\classes\security\Token;
    use \sys\classes\util\Request;
    use \auth\classes\models\AuthModel;
    class Login {
        
        public static function actionFormAuth(){
            //Formulário de autenticação
            $objViewPart = new ViewPart('formAuth');
                
            $objTk      = new Token(2);
            $tkField    = $objTk->protectForm();
            
            //Template
            $tpl                = new View();
            $tpl->setLayout($objViewPart);
            $tpl->TITLE         = 'Autenticação';
            $tpl->TOKEN_FIELD   = $tkField;
            //Instância de JS
            //$tpl->setJs('criacao/javascript');
            $tpl->setCss('auth/formAuth');
            //$tpl->forceCssJsMinifyOn();
            
            $tpl->render('auth');                        
        }
        
        /**
         * Recebe dados de acesso (login, senha e token) e faz a autenticação.
         */
        public static function actionAuthLogin(){
                $user   = Request::post('user');
                $passwd = Request::post('passwd'); 
                $token  = Request::post('spack_token');
                
                //Verifica se o token é válido.
                $objTk = new Token(1);
                if ($objTk->Check()) {
                    //Token válido.
                    
                } else {
                    //
                }
                
                echo "$user - $passwd - $token";
        }
    }
?>
