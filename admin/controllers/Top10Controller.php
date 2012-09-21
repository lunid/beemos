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
        function indexHome(){
            try{
                //Top10
                $m_top10 = new Top10Model();
                
                $objView = new ViewPart('top10');
                
                $id_materia             = Request::post("id_materia", "NUMBER");
                $id_fonte_vestibular    = Request::post("id_fonte_vestibular", "NUMBER");
                
                //Opções do <select> de matérias
                $cbo_materias_opts                  = new \stdClass();
                $cbo_materias_opts->id              = "id_materia";
                $cbo_materias_opts->first_option    = "Selecione uma matéria";
                $cbo_materias_opts->select_option   = $id_materia;
                
                $objView->COMBO_MATERIAS = HtmlComponent::select($m_top10->getMateriasSelectBox(), $cbo_materias_opts);
                
                //Opções do <select> de fontes
                $cbo_fontes_opts                  = new \stdClass();
                $cbo_fontes_opts->id              = "id_fonte_vestibular";
                $cbo_fontes_opts->first_option    = "Selecione uma fonte";
                $cbo_fontes_opts->select_option   = $id_fonte_vestibular;
                $cbo_fontes_opts->disabled        = true;
                
                $objView->COMBO_FONTES = HtmlComponent::select($m_top10->getFontesSelectBox(), $cbo_fontes_opts);
                
                //Opções do <table> de questões
                $tb_questos_opts                  = new \stdClass();
                $tb_questos_opts->id              = "table_questoes";
                $tb_questos_opts->disabled        = true;
                $tb_questos_opts->class           = "table_questoes";
                $tb_questos_opts->html_template   = "table_top10";
                
                $objView->TB_QUESTOES = HtmlComponent::table($m_top10->listaQuestoesTop10($id_materia, $id_fonte_vestibular), $tb_questos_opts);
                
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
                
                $objView->GR_TOP10 = ChartComponent::gerGraficoTop10($m_top10->graficoTop10($data_inicio, $data_final));
                
                //Template
                $tpl = new View($objView);
                
                $tpl->TITLE = 'ADM | SuperPro';
                
                $tpl->setCssJs('top10');
                $tpl->setPlugin("highcharts");
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('top10');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal - IndexController <<<<<<<<<<<<<<< <br />\n";
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
        function geraGrafico(){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Método indefinido";
                
                //Gráfico TOP10
                if(Request::post("hdd_acao") == 'filtar_grafico'){
                    //Top10
                    $m_top10 = new Top10Model();
                
                    $data_inicio    = Date::formatDate(Request::post("data_inicio"), "AAAA-MM-DD");
                    $data_final     = Date::formatDate(Request::post("data_final"), "AAAA-MM-DD");
                    
                    $retGr = $m_top10->graficoTop10($data_inicio, $data_final);
                    
                    if($retGr->status !== FALSE){
                        $ret->html      = ChartComponent::gerGraficoTop10($retGr);
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
    }
?>

