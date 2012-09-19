<?php
    
    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;
    use \sys\classes\util\Request;
    use \sys\classes\util\Date;
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
        
        public function geraGrafico(){
            try{
                if(Request::post("data_inicio") != "" && Request::post("data_final") != ""){
                    $data_inicio    = Date::formatDate(Request::post('data_inicio'), "AAAA-MM-DD");
                    $data_final     = Date::formatDate(Request::post('data_final'), "AAAA-MM-DD");
                }else{
                    $data_inicio    = date("Y-m-d", mktime(0, 0, 0, date("m"), (date("d")-7), date("Y")));
                    $data_final     = date("Y-m-d");
                }
                
                $rs = Date::dateDiff($data_inicio, $data_final);
                
                echo "<pre style='color:#FF0000;'>";
                print_r($rs);
                echo "</pre>";
                die;
                
                $m_top10 = new Top10Model();
                $m_top10->graficoTop10($data_inicio, $data_final);
                
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

