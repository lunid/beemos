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
                
                $m_admUsuario           = new AdmUsuario();
                $m_admUsuario->FB_ID    = $profile->identifier;
                $rs = $m_admUsuario->verificaUserFacebook();
                
                echo "<pre style='color:#FF0000;'>";
                print_r($profile->identifier);
                echo "</pre>";
                die;
                
                $viewPart   = new ViewPart("blank");
                $view       = new View($viewPart, "blank");
                
                
                
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
