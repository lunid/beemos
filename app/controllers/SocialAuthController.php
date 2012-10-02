<?php
    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;  
    use \app\models\tables\AdmUsuario;
    
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
                ),
                "Google" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "id" => "830321440557.apps.googleusercontent.com", "secret" => "3P_BKWwE40vR0K4rXXO2zSiO" ),
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
        
        public function actionGoogle(){
            try{
                $auth       = new Hybrid_Auth($this->config);
                $profile    = $auth->authenticate('Google')->getUserProfile();
                
                if($profile->identifier == '' || $profile->identifier == null){
                    die("Usuário do Google não encontrado!");
                }
                
                //Procurando usuário do Facebook em nossa Base
                $m_admUsuario               = new AdmUsuario();
                $m_admUsuario->GOOGLE_ID    = $profile->identifier;
                $rs                         = $m_admUsuario->verificaUserGoogle();
                
                $viewPart           = new ViewPart("social_facebook");
                $viewPart->MSG      = $rs->msg;
                
                if($rs->status === true){
                    $viewPart->STATUS   = 1;
                    
                    $_SESSION['user_site'] = serialize($m_admUsuario);
                }else{
                    $viewPart->STATUS   = 0;
                }
                
                $view           = new View($viewPart, "socialauth");
                $view->TITLE    = "Autenticação Google";
                
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
