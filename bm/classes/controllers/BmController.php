<?php   
    
    namespace bm\classes\controllers;
    
    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;      
    use \sys\classes\util\Request;
    use \sys\classes\util\Date;
    use \bm\classes\views\ViewBm;                        
    
    class BmController extends Controller {
        
        function mkViewPart($layoutName){
            $objViewPart = new ViewPart($layoutName);
            return $objViewPart;
        }
        
        function mkView(){
            $tpl = new ViewBm(); 
            return $tpl;
        }
    }

?>
