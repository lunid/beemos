<?php
    
    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;      
    use \sys\classes\util\Request;
    use \sys\classes\util\Date;
    use \admin\models\Top10Model;


    /**
    * Refere-se à página de questões do Top10.
    */
    class Top10 extends Controller {
        /**
        * Conteúdo da página home do Top10.
        */
        function actionIndex(){
            try{
                //Top10
                $m_top10 = new Top10Model();
                
                $objView = new ViewPart('top10');
                
                $id_materia             = Request::post("id_materia", "NUMBER");
                $id_materia_gr          = Request::post("id_materia_gr", "NUMBER");
                
                $id_fonte_vestibular    = Request::post("id_fonte_vestibular", "NUMBER");
                $id_fonte_vestibular_gr = Request::post("id_fonte_vestibular_gr", "NUMBER");
                
                $rs_materias            = $m_top10->getMateriasSelectBox();
                $rs_fonte_vestibular    = $m_top10->getFontesSelectBox();
                
                //Opções do <select> de matérias
                $cbo_materias_opts                  = new \stdClass();
                $cbo_materias_opts->id              = "id_materia";
                $cbo_materias_opts->first_option    = "Selecione uma matéria";
                $cbo_materias_opts->select_option   = $id_materia;
                
                $objView->COMBO_MATERIAS = HtmlComponent::select($rs_materias, $cbo_materias_opts);
                
                $cbo_materias_opts->id              = "id_materia_gr";
                $cbo_materias_opts->first_option    = "Todas as matérias";
                $cbo_materias_opts->select_option   = $id_materia_gr;

                $objView->COMBO_MATERIAS_GR = HtmlComponent::select($rs_materias, $cbo_materias_opts);
                
                //Opções do <select> de fontes
                $cbo_fontes_opts                  = new \stdClass();
                $cbo_fontes_opts->id              = "id_fonte_vestibular";
                $cbo_fontes_opts->first_option    = "Selecione uma fonte";
                $cbo_fontes_opts->select_option   = $id_fonte_vestibular;
                $cbo_fontes_opts->disabled        = true;
                
                
                
                $objView->COMBO_FONTES = HtmlComponent::select($rs_fonte_vestibular, $cbo_fontes_opts);
                
                $cbo_fontes_opts->id              = "id_fonte_vestibular_gr";
                $cbo_fontes_opts->first_option    = "Todas as fontes";
                $cbo_fontes_opts->select_option   = $id_fonte_vestibular_gr;
                
                $objView->COMBO_FONTES_GR = HtmlComponent::select($rs_fonte_vestibular, $cbo_fontes_opts);
                
                //Opções do <table> de questões
                $tb_questoes_opts                  = new \stdClass();
                $tb_questoes_opts->id              = "table_questoes";
                $tb_questoes_opts->disabled        = true;
                $tb_questoes_opts->class           = "table_questoes";
                $tb_questoes_opts->html_template   = "table_top10";
                
                $objView->TB_QUESTOES = HtmlComponent::table($m_top10->listaQuestoesTop10($id_materia, $id_fonte_vestibular), $tb_questoes_opts);
                
                //Gráfico TOP10
                if(Request::post("hdd_acao") == 'filtar_grafico'){
                    $data_inicio    = Date::formatDate(Request::post("data_inicio"), "AAAA-MM-DD");
                    $data_final     = Date::formatDate(Request::post("data_final"), "AAAA-MM-DD");
                }else{
                    $data_inicio    = date("Y-m-d", mktime(0, 0, 0, date("m"), (date("d")-10), date("Y")));
                    $data_final     = date("Y-m-d");
                }
                
                $objView->DATA_INICIO   = Date::formatDate($data_inicio);
                $objView->DATA_FINAL    = Date::formatDate($data_final);
                
                $objView->GR_TOP10 = ChartComponent::geraGraficoTop10($m_top10->graficoTop10($data_inicio, $data_final));
                
                //Template
                $tpl = new View($objView);
                
                $tpl->TITLE = 'ADM | SuperPro';
                
                $tpl->setCssJs('top10');
                $tpl->setPlugin("highcharts");
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('top10');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        } 
        
        /**
        * Solicitação Ajax de Gráfico Top10
        */
        function actionGeraGrafico(){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Método indefinido";
                
                //Gráfico TOP10
                if(Request::post("hdd_acao") == 'filtar_grafico'){
                    //Top10
                    $m_top10 = new Top10Model();
                
                    $data_inicio            = Date::formatDate(Request::post("data_inicio"), "AAAA-MM-DD");
                    $data_final             = Date::formatDate(Request::post("data_final"), "AAAA-MM-DD");
                    $id_materia             = Request::post("id_materia", "NUMBER");
                    $id_fonte_vestibular    = Request::post("id_fonte_vestibular", "NUMBER");
                    $selecao                = Request::post("selecao", "NUMBER");
                    $cor                    = Request::post("cor");
                    
                    $retGr = $m_top10->graficoTop10($data_inicio, $data_final, $id_materia, $id_fonte_vestibular, $selecao, $cor);
                    
                    if($retGr->status !== FALSE){
                        $ret->html      = ChartComponent::geraGraficoTop10($retGr);
                        $ret->status    = true;
                        $ret->msg       = "";
                    }else{
                        $ret->html      = null;
                        $ret->status    = true;
                        $ret->msg       = $retGr->msg;
                    }
                }else{
                    $ret->msg = "HDD Ação inválida!";
                }           
                
                echo json_encode($ret);
                die;
            }catch(Exception $e){
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
                die;
            }
        }
        
        /**
         * Função para implementação AJAX que altera o usuário responsável em avaliar uma determinada questão.
         * 
         * @return json $ret
         */
        public function actionAtualizaUsuarioQuestao(){
            try{
                $ret            = new \stdClass();
                $ret->status    = FALSE;
                $ret->msg       = "Dados para alteração de usuário inválidos!";
                
                if(Request::post("id_questao") > 0 && Request::post("id_usuario")){
                    $m_top10    = new Top10Model();
                    $ret        = $m_top10->alteraUsuarioQuestao(Request::post('id_questao'), Request::post('id_usuario'));
                }
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new \stdClass();
                $ret->status    = FALSE;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        public function actionAvaliarQuestao(){
            try{
                //Armazena ID da questão enviado
                $id_questao = (int)Request::get("id_questao");
                
                if($id_questao <= 0){
                    throw new Exception("ID da Questão inválido!");
                }
                
                $objView = new ViewPart('top10_avaliar');
                
                //Template
                $tpl = new View($objView);
                
                $tpl->TITLE = 'ADM | SuperPro | TOP 10 | Avaliar Questão';
                
                $tpl->setCssJs('top10');
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('top10_avaliar');
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

