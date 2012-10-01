<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;  
    use \app\models\tables\AdmUsuario;
    use \sys\classes\util\Request;
    
    /**
    * Classe Controller usada para autenticação em redes sociais utilizando a biblioteca Hybridauth
    */
    class SocialAuth extends Controller {
        private $config = array(
            "base_url" => "http://localhost/interbits/socialauth", 
            "providers" => array ( 
                "Facebook" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "id" => "374046492672483", "secret" => "8d9c000f415ad128c10035a668c8a434" ),
                    // A comma-separated list of permissions you want to request from the user. See the Facebook docs for a full list of available permissions: http://developers.facebook.com/docs/reference/api/permissions.
                    "scope"   => "", 
                    // The display context to show the authentication page. Options are: page, popup, iframe, touch and wap. Read the Facebook docs for more details: http://developers.facebook.com/docs/reference/dialogs#display. Default: page
                    "display" => "" 
                )
            ),
            "debug_mode" => false,
            "debug_file" => "",
        );
        
        public function actionIndex(){
	    try{
                Hybrid_Endpoint::process();
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }   

        public function actionFacebook(){
            try{
                $auth       = new Hybrid_Auth($this->config);
                $profile    = $auth->authenticate('Facebook')->getUserProfile();
                
                if($profile->identifier == '' || $profile->identifier == null){
                    die("Usuário do Facebook não encontrado!");
                }
                
                //Procurando usuário do Facebook em nossa Base
                $m_admUsuario           = new AdmUsuario();
                $m_admUsuario->FB_ID    = $profile->identifier;
                $rs                     = $m_admUsuario->verificaUserFacebook();
                
                $viewPart           = new ViewPart("social_facebook");
                $viewPart->MSG      = $rs->msg;
                
                if($rs->status === true){
                    $viewPart->STATUS   = 1;
                    
                    $_SESSION['user_site'] = serialize($m_admUsuario);
                }else{
                    $viewPart->STATUS   = 0;
                }
                
                $view           = new View($viewPart, "socialauth");
                $view->TITLE    = "Autenticação Facebook";
                
                $view->render();
            }catch(Exception $e){
                echo "Erro<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo "Arquivo: " . $e->getFile() . "<br />\n";
                echo "Linha: " . $e->getLine() . "<br />\n";                
            }
        }
    }
?>
