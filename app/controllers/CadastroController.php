<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;        
    use \sys\classes\util\Request;
    use \app\models\tables\Categoria;
    
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página Assine Já do site.
    */
    class Cadastro extends Controller {
        /**
        *   Conteúdo da página Assine Já
        */
        function actionIndex(){
	    $objPartPg  = new ViewPart('cadastro');  
            $objView    = new View($objPartPg);

            $objView->setJsInc('init_cadastro');
            $objView->forceCssJsMinifyOn();
            
            $objView->TITLE = 'SuperPro - Cadastre-se';
            
            $objView->render('cadastro');    
        }      
    }
?>

