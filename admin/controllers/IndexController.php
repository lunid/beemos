<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \admin\models\Top10Model;


    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial do admin.
    */
    class Index extends Controller {

        /**
        *Conteúdo da página home do admin.
        */
        function indexHome(){
            try{
                //Top10
                $m_top10 = new Top10Model();

                $m_top10->listaQuestoesTop10Materia();

                $objView        = new View('top10_questoes');
                $objView->TITLE = 'ADM | SuperPro';

                $objView->setPlugin("abas");
                $objView->setPlugin("drop");
                $objView->setPlugin("menu_slider");

                $objView->setMinify(TRUE);

                $objView->COMBO_MATERIAS = HtmlComponent::select(array(array("id" => '1', "text" => "Du"), array("id" => '2', "text" => "DuDu"), array("id" => '3', "text" => "Edu")));

                $objView->render();            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal - IndexController <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }               
    }
?>

