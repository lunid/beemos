<?php   
    
    namespace admin\classes\controllers;
    
    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;      
    use \sys\classes\util\Request;
    use \sys\classes\util\Date;
    use \admin\classes\views\ViewAdmin;                        
    use \sys\classes\util\Redirect;
    
    class AdminController extends Controller {
        
        function mkViewPart($layoutName){
            $objViewPart = new ViewPart($layoutName);
            return $objViewPart;
        }
        
        function mkView(){
            $tpl = new ViewAdmin(); 
            return $tpl;
        }
        
        /**
         * Método executado antes da chamada da action informada na URL.
         * Na classe atual permite o acesso apenas para usuários autenticados. 
         */
        function before(){
            $objRedirect    = new Redirect('auth/redirect.xml');
            $redirect       = $objRedirect->LOGOUT;   
            \Auth::checkAuth($redirect);//Se a sessão NÃO estiver ativa redireciona para a página de $redirect.
        }
    }

?>
