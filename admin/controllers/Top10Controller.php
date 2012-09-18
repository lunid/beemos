<?php
    
    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;
    use \sys\classes\util\Request;
    use \admin\models\Top10Model;


    /**
    * Refere-se à página de avaliação de questões do Top10.
    */
    class Top10 extends Controller {
        function questoes(){
            $objView        = new View('top10_questoes');
            $objView->TITLE = 'ADM | Top 10 | Avaliar Questões | SuperPro';

            $objView->setPlugin("abas");
            $objView->setPlugin("drop");
            $objView->setPlugin("menu_slider");
            
            $objView->setMinify(TRUE);
            
            $objView->render();            
        }               
        
        public function atualizaUsuarioQuestao(){
            try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Metódo de envio inválido!";
                
                if(Request::post("hdd_acao") == 'usuario_questao'){
                    $top10 = new Top10Model();
                    
                    if($top10->alteraUsuarioQuestao(Request::post("id_questao"), Request::post("id_usuario"))){
                        $ret->msg = 'Usuário alterado com sucesso';
                    }else{
                        $ret->msg = 'Falha na tentativa de alterar usuário';
                    }
                    
                    echo json_encode($ret);
                    die;
                }
            } catch (Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
                die;
            }
        }
    }
?>

