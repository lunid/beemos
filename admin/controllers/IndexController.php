<?php

    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\util\Request;
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
                
                $objView        = new View('top10_questoes');
                $objView->TITLE = 'ADM | SuperPro';

                $objView->setPlugin("abas");
                $objView->setPlugin("drop");
                $objView->setPlugin("menu_slider");

                $objView->setMinify(TRUE);
                
                $id_materia             = Request::post("id_materia", "NUMBER");
                $id_fonte_vestibular    = Request::post("id_fonte_vestibular", "NUMBER");
                
                $objView->COMBO_MATERIAS    = HtmlComponent::select($m_top10->getMateriasSelectBox(), "id_materia", "Selecione uma matéria", null, $id_materia);
                $objView->COMBO_FONTES      = HtmlComponent::select($m_top10->getFontesSelectBox(), "id_fonte_vestibular", "Selecione uma fonte", null, $id_fonte_vestibular, null, null, true);
                $objView->TB_QUESTOES       = HtmlComponent::table($m_top10->listaQuestoesTop10($id_materia, $id_fonte_vestibular), 'table_questoes', 'table_top10');
                
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

