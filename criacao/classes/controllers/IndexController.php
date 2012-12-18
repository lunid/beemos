<?php
    use \sys\classes\mvc\View;
    use \sys\classes\mvc\ViewPart;
    use \sys\classes\util\Request;
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial do admin.
    */
    use \sys\classes\mvc\Controller;
    
    class Index extends Controller {
        /**
        * Conteúdo da página home de criação.
        */
        function actionIndex(){
            try{
                //Parâmetro de página
                $pag = trim(Request::get("pag")) != "" ? trim(Request::get("pag")) : "home";
                
                //Home
                $objViewPart = new ViewPart($pag);
                
                //Template
                $tpl = new View();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'Index | Criação';
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('index');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }          
    }
?>

