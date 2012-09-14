<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \app\models\HomeModel;


    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial da raíz do site.
    */
    class SobreNos extends Controller {

        /**
        *Conteúdo da página Sobre Nós
        */
        function indexHome(){
	    $this->aInterbits();//  
        }      
        
        function aInterbits(){
            //$objModel           = new HomeModel();	    
            
            $objView           = new View('sobreNos_aInterbits');
            $objView->TITLE    = 'SuperPro - A Interbits';
            
            $objView->setPlugin('sliderBanner');
            $objView->setPlugin('menuHorizontal');
            $objView->setPlugin('menuIdiomas');
            $objView->render();    
        }
    }
?>

