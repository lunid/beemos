<?php   
    
    namespace admin\classes\controllers;
    
    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;      
    use \sys\classes\util\Request;
    use \sys\classes\util\Date;
    use \admin\classes\views\ViewAdmin;                        
    
    class AdminController extends Controller {
        
        function mkViewPart($layoutName){
            $objViewPart = new ViewPart($layoutName);
            return $objViewPart;
        }
        
        function mkView(){
            $tpl = new ViewAdmin(); 
            return $tpl;
        }
    }

?>
