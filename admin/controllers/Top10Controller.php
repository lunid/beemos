<?php
    
    use \sys\classes\mvc\Controller;    
    use \sys\classes\mvc\View;        
    use \sys\classes\mvc\ViewPart;      
    use \sys\classes\util\Request;
    use \sys\classes\util\Date;
    use \sys\classes\html\Combobox;
    use \sys\classes\html\Table;
    use \admin\models\Top10Model;
    use \admin\models\tables\AvaliacaoQuestao;
    
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
                
                $idMateria                  = Request::post("idMateria", "NUMBER");
                $idMateriaGrafico           = Request::post("idMateriaGrafico", "NUMBER");
                $idFonteVestibular          = Request::post("idFonteVestibular", "NUMBER");
                $idFonteVestibularGrafico   = Request::post("idFonteVestibularGrafico", "NUMBER");
                
                $rsMaterias           = $m_top10->getMateriasSelectBox();
                $rsFontesVestibular   = $m_top10->getFontesSelectBox();
                
                //===== COMBOBOX MATERIAS ======================================
                $objCbxMateria      = new Combobox();
                $objCbxMateria->id  = 'idMateria';
                $objCbxMateria->addOption('0','Todas as Matérias');                
                $objCbxMateria->addOptions($rsMaterias);           
                $objCbxMateria->selected($idMateria);                
                                
                $objView->CBX_MATERIAS = $objCbxMateria->render();
                
                //===== COMBOBOX MATERIAS/GRÁFICO ==============================
                $objCbxMateriaGrafico       = new Combobox();
                $objCbxMateriaGrafico->id   = 'idMateriaGrafico';
                $objCbxMateria->addOption('0','Todas as Matérias');
                $objCbxMateria->addOptions($rsMaterias);           
                $objCbxMateria->selected($idMateriaGrafico);                
                                
                $objView->CBX_MATERIAS_GRAFICO = $objCbxMateriaGrafico->render();
                
                //===== COMBOBOX FONTES/VESTIB =================================
                $objCbxFonteVestib          = new Combobox();
                $objCbxFonteVestib->id      = 'idFonteVestibular';
                $objCbxFonteVestib->addOption('0','Selecione uma fonte');
                $objCbxFonteVestib->addOptions($rsFontesVestibular);           
                $objCbxFonteVestib->disabledOff();
                $objCbxFonteVestib->selected($idFonteVestibular);                
                
                $objView->CBX_FONTES = $objCbxFonteVestib->render();
                                        
                //==== COMBOBOX FONTES/VESTIB/GRÁFICO ==========================
                $objCbxFonteVestibGrafico          = new Combobox();
                $objCbxFonteVestibGrafico->id      = 'idFonteVestibularGrafico';
                $objCbxFonteVestibGrafico->addOption('0','Todas as fontes');
                $objCbxFonteVestibGrafico->addOptions($rsFontesVestibular);                           
                $objCbxFonteVestibGrafico->selected($idFonteVestibularGrafico);                                                
                
                $objView->COMBO_FONTES_GRAFICO = $objCbxFonteVestibGrafico->render();
                
                
                //===== TABELA QUESTÕES ========================================
                $arrDados                   = $m_top10->listaQuestoesTop10($idMateria, $idFonteVestibular);
                //print_r($arrDados);
                //die();
                $objTable                   = new Table();
                $objTable->setColumns(array('ID/Questão','Fonte/Vestibular','Uso'));
                $objTable->id               = 'table_questoes';
                $objTable->cls              = 'table_top10';                
                echo $objTable->render();
                die();
               
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
                $tpl = new View();
                $tpl->setLayout($objView);                
                
                $tpl->TITLE         = 'ADM | SuperPro';
                $tpl->SUB_TITULO    = 'TOP 10';
                
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
                    
                    $objRet = $m_top10->graficoTop10($data_inicio, $data_final, $id_materia, $id_fonte_vestibular, $selecao, $cor);
                    
                    if($objRet->status !== FALSE){
                        print_r($objRet);
                        die();
                        $objRet->html      = ChartComponent::geraGraficoTop10($objRet);
                        $objRet->status    = true;
                        $objRet->msg       = "";
                    }else{
                        $objRet->html      = null;
                        $objRet->status    = true;
                        $objRet->msg       = $retGr->msg;
                    }
                }else{
                    $objRet->msg = "HDD Ação inválida!";
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
                
                //Carrega dados da avaliação caso exista
                $m_avaliacao                    = new AvaliacaoQuestao();
                $m_avaliacao->ID_BCO_QUESTAO    = $id_questao;
                $rs                             = $m_avaliacao->carregaAvaliacaoQuestao();
                
                $objView                = new ViewPart('top10_avaliar');
                $objView->id_questao    = $id_questao;
                
                $objView->NOTA_ENUNCIADO                = isset($rs->NOTA_ENUNCIADO) && $rs->NOTA_ENUNCIADO > 0 ? $rs->NOTA_ENUNCIADO : 0;
                $objView->SOBRE_ENUNCIADO               = isset($rs->SOBRE_ENUNCIADO) && $rs->SOBRE_ENUNCIADO != '' ? $rs->SOBRE_ENUNCIADO : "";
                $objView->NOTA_ABRANGENCIA              = isset($rs->NOTA_ABRANGENCIA) && $rs->NOTA_ABRANGENCIA > 0 ? $rs->NOTA_ABRANGENCIA : 0;
                $objView->SOBRE_ABRANGENCIA             = isset($rs->SOBRE_ABRANGENCIA) && $rs->SOBRE_ABRANGENCIA != '' ? $rs->SOBRE_ABRANGENCIA : "";
                $objView->NOTA_ILUSTRACAO               = isset($rs->NOTA_ILUSTRACAO) && $rs->NOTA_ILUSTRACAO > 0 ? $rs->NOTA_ILUSTRACAO : 0;
                $objView->SOBRE_ILUSTRACAO              = isset($rs->SOBRE_ILUSTRACAO) && $rs->SOBRE_ILUSTRACAO != '' ? $rs->SOBRE_ILUSTRACAO : "";
                $objView->NOTA_INTERDISCIPLINARIDADE    = isset($rs->NOTA_INTERDISCIPLINARIDADE) && $rs->NOTA_INTERDISCIPLINARIDADE > 0 ? $rs->NOTA_INTERDISCIPLINARIDADE : 0;
                $objView->SOBRE_INTERDISCIPLINARIDADE   = isset($rs->SOBRE_INTERDISCIPLINARIDADE) && $rs->SOBRE_INTERDISCIPLINARIDADE != '' ? $rs->SOBRE_INTERDISCIPLINARIDADE : "";
                $objView->NOTA_HABILIDADE_COMPETENCIA   = isset($rs->NOTA_HABILIDADE_COMPETENCIA) && $rs->NOTA_HABILIDADE_COMPETENCIA > 0 ? $rs->NOTA_HABILIDADE_COMPETENCIA : 0;
                $objView->SOBRE_HABILIDADE_COMPETENCIA  = isset($rs->SOBRE_HABILIDADE_COMPETENCIA) && $rs->SOBRE_HABILIDADE_COMPETENCIA != '' ? $rs->SOBRE_HABILIDADE_COMPETENCIA : "";
                $objView->NOTA_ORIGINALIDADE            = isset($rs->NOTA_ORIGINALIDADE) && $rs->NOTA_ORIGINALIDADE > 0 ? $rs->NOTA_ORIGINALIDADE : 0;
                $objView->SOBRE_ORIGINALIDADE           = isset($rs->SOBRE_ORIGINALIDADE) && $rs->SOBRE_ORIGINALIDADE != '' ? $rs->SOBRE_ORIGINALIDADE : "";
                $objView->DATA_AVALIACAO                = isset($rs->DATA_AVALIACAO) && $rs->DATA_AVALIACAO != '' ? Date::formatDate($rs->DATA_AVALIACAO) : "";
                $objView->AVALIADOR                     = isset($rs->NOME_USUARIO) && $rs->NOME_USUARIO != '' ? $rs->NOME_USUARIO : "";
                
                
                //Template
                $tpl = new View($objView);
                
                $tpl->TITLE         = 'ADM | SuperPro | TOP 10 | Avaliar Questão';
                $tpl->SUB_TITULO    = 'TOP 10';
                
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
        
        public function actionSalvarAvaliacao(){
            try{
                $ret            = new \stdClass();
                $ret->status    = FALSE;
                $ret->msg       = "Falha ao salvar avaliação. Tente mais tarde";
                
                $m_avaliacao = new AvaliacaoQuestao();
                
                $m_avaliacao->ID_USUARIO                    = 1; //Pegar quando implementada a sessão do usuário
                $m_avaliacao->ID_BCO_QUESTAO                = Request::post('id_questao');
                $m_avaliacao->NOTA_ENUNCIADO                = Request::post('nota_enunciado');
                $m_avaliacao->SOBRE_ENUNCIADO               = Request::post('sobre_enunciado');
                $m_avaliacao->NOTA_ABRANGENCIA              = Request::post('nota_abrangencia');
                $m_avaliacao->SOBRE_ABRANGENCIA             = Request::post('sobre_abrangencia');
                $m_avaliacao->NOTA_ILUSTRACAO               = Request::post('nota_ilustracao');
                $m_avaliacao->SOBRE_ILUSTRACAO              = Request::post('sobre_ilustracao');
                $m_avaliacao->NOTA_INTERDISCIPLINARIDADE    = Request::post('nota_interdisciplinaridade');
                $m_avaliacao->SOBRE_INTERDISCIPLINARIDADE   = Request::post('sobre_interdisciplinaridade');
                $m_avaliacao->NOTA_HABILIDADE_COMPETENCIA   = Request::post('nota_habilidadecompetencia');
                $m_avaliacao->SOBRE_HABILIDADE_COMPETENCIA  = Request::post('sobre_habilidadecompetencia');
                $m_avaliacao->NOTA_ORIGINALIDADE            = Request::post('nota_originalidade');
                $m_avaliacao->SOBRE_ORIGINALIDADE           = Request::post('sobre_originalidade');
                
                $ret = $m_avaliacao->salvaAvaliacaoQuestao();
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new \stdClass();
                $ret->status    = FALSE;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
    }
?>

