<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolasTurmasModel;
    use \admin\classes\models\ListasModel;
    use \sys\classes\util\Request;
    use \sys\classes\util\Date;
    
    class Escolas extends AdminController {
        /**
         * Inicializa a página de Escola & Turmas
         * Inicializa as Abas da Página
         */
        public function actionIndex(){
            try{
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/escolas_turmas');
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro';
                $tpl->SUB_TITULO    = 'Escolas & Turmas';
                
                $tpl->setJs('admin/escolas_turmas');
                $tpl->setCss('admin/escolas_turmas');
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('escolas_turmas');
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
         * Traz o array jSon para que seja possível montar o grid de escolas & turmas
         */
        public function actionGridEscolas(){
            try{
                //Obejto de retorno
                $ret = new stdClass();
                
                //Model de Escolas e Turmas
                $mdEscolasTurmas = new EscolasTurmasModel();
                
                //Verifica filtros
                $where  = "";
                $search = Request::get('_search'); 
                if($search == true){
                    //Código da escola
                    $ID_ESCOLA = Request::get('ID_ESCOLA', 'NUMBER');
                    if($ID_ESCOLA > 0){
                        $where = " AND ID_ESCOLA = " . $ID_ESCOLA;  
                    }
                    
                    //Nome da escola
                    $NOME = Request::get('NOME');
                    if($NOME != ''){
                        $where .= " AND NOME LIKE '%" . $NOME . "%'";  
                    }
                    
                    //Status da escola
                    $STATUS = Request::get('STATUS', 'NUMBER');
                    if($STATUS){
                        if($STATUS != -1){
                            $where .= " AND STATUS = " . $STATUS;  
                        }
                    }
                }

                //Lista todas escolas encontradas
                $rs = $mdEscolasTurmas->listarEscolasCliente(26436, $where);
                
                if($rs->status){
                    $page           = Request::get('page', 'NUMBER'); 
                    $limit          = Request::get('rows', 'NUMBER'); 
                    $orderField     = Request::get('sidx'); 
                    $orderType      = Request::get('sord'); 

                    if(!$orderField) $orderField = 1;

                    //Total de registros
                    $count          = sizeof($rs->escolas);
                    $total_pages    = $count > 0 ? ceil($count/$limit) : 0;
                    $page           = $page > $total_pages ? $total_pages : $page;
                    $start          = $limit * $page - $limit;
                    
                    //Efetua select com ordenação e paginação
                    $rs = $mdEscolasTurmas->listarEscolasCliente(
                            26436, 
                            $where, 
                            array(
                                "campoOrdenacao"    => $orderField, 
                                "tipoOrdenacao"     => $orderType, 
                                "inicio"            => $start, 
                                "limite"            => $limit
                            )
                    );
                    
                    $ret->page      = $page;
                    $ret->total     = $total_pages;
                    $ret->records   = $count;

                    $i=0;
                    foreach($rs->escolas as $row) {
                        $ret->rows[$i]['id']   = $row->ID_ESCOLA;
                        $ret->rows[$i]['cell'] = array(
                            $row->ID_ESCOLA,
                            $row->NOME,
                            "<input type='radio' name='status_{$row->ID_ESCOLA}' value='1' ".($row->STATUS == 1 ? "checked='checked'" : "")." onclick='javascript:alteraStatusEscola({$row->ID_ESCOLA}, this.value);' /> Ativa &nbsp; <input type='radio' name='status_{$row->ID_ESCOLA}' value='0' ".($row->STATUS == 0 ? "checked='checked'" : "")." onclick='javascript:alteraStatusEscola({$row->ID_ESCOLA}, this.value);' /> Inativa ",
                            "<input type='button' value='Turmas' onclick='javascript:turmas({$row->ID_ESCOLA}, 26436, \"{$row->NOME}\");' style='padding: 0; font-size: 12px; font-weight: bold; height: 20px; width: 80px; margin: 1px;' />"        
                        );
                        $i++;
                    }
                }else{
                    $ret                    = new stdClass();
                    $ret->rows[0]['id']     = 0;
                    $ret->rows[0]['cell']   = array($rs->msg);
                }

                echo json_encode($ret);
            }catch(Exception $e){
                $ret                    = new stdClass();
                $ret->rows[0]['id']     = 0;
                $ret->rows[0]['cell']   = array('Erro: ' . $e->getMessage() . " <br /> Arquivo: " . $e->getFile() . " <br /> Linha: " . $e->getLine());
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Função para incluir uma nova Escola na base de dados
         */
        public function actionSalvarEscola(){
            try{
                //Obejto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar nova Escola!";
                
                //Recebe dados do POST
                $NOME       = Request::post("escolaNome");
                
                //Executa chamada de model para salvar a nova escola
                $mEscolasTurmas = new EscolasTurmasModel();
                $ret            = $mEscolasTurmas->salvarEscola(26436, $NOME);

                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro: " . utf8_decode($e->getMessage()) . " - Arquivo: " . $e->getFile() . " - Linha: " . $e->getFile();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Função que atualiza o status de ATIVO e INATIVO de uma escola
         */
        public function actionAlterarStatusEscola(){
            try{
                //Obejto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar status Escola!";
                
                //Recebe dados do POST
                $ID_ESCOLA  = Request::post("escolaId", "NUMBER");
                $ID_CLIENTE = Request::post("escolaIdCliente", "NUMBER");
                $STATUS     = Request::post("escolaStatus", "NUMBER");
                
                //Executa chamada de model para salvar a nova escola
                $mEscolasTurmas = new EscolasTurmasModel();
                $ret            = $mEscolasTurmas->alterarStatusEscola($ID_ESCOLA, $ID_CLIENTE, $STATUS);

                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro: " . utf8_decode($e->getMessage()) . " - Arquivo: " . $e->getFile() . " - Linha: " . $e->getFile();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Lista as turmas de um cliente e uma escola
         */
        public function actionListarTurmas(){
            try{
                //Obejto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listas Turmas!";
                
                //Recebe dados do POST
                $ID_ESCOLA  = Request::post("ID_ESCOLA", "NUMBER");
                
                //Executa chamada de model para salvar a nova escola
                $mEscolasTurmas = new EscolasTurmasModel();
                $ret            = $mEscolasTurmas->listarTurmasCliente(26436, $ID_ESCOLA);

                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro: " . utf8_decode($e->getMessage()) . " - Arquivo: " . $e->getFile() . " - Linha: " . $e->getFile();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Função para salvar os dados de uma Turma (nova ou existente)
         */
        public function actionSalvarTurma(){
            try{
                //Obejto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar dados da Turma!";
                
                //Recebe dados do POST
                //Executa chamada de model para salvar a nova escola
                $mEscolasTurmas = new EscolasTurmasModel();
                $ret            = $mEscolasTurmas->salvarTurma(
                                        Request::post("turmaId", "NUMBER"),
                                        Request::post("turmaIdEscola", "NUMBER"),
                                        Request::post("turmaClasse"),
                                        Request::post("turmaEnsino"),
                                        Request::post("turmaAno", "NUMBER"),
                                        Request::post("turmaPeriodo")
                                  );

                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro: " . utf8_decode($e->getMessage()) . " - Arquivo: " . $e->getFile() . " - Linha: " . $e->getFile();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Monta o jSon para criação do Grid de Turmas
         */
        public function actionGridTurmas(){
            try{
                //Obejto de retorno
                $ret = new stdClass();
                
                //Model de Escolas e Turmas
                $mdEscolasTurmas = new EscolasTurmasModel();
                
                //Verifica filtros
                $where  = "";
                $search = Request::get('_search'); 
                if($search == true){
                    //Filtro de ensino
                    $ENSINO = Request::get('ENSINO');
                    if($ENSINO != 'T' && $ENSINO != ''){
                        $where = " AND T.ENSINO = '" . $ENSINO . "'";  
                    }
                    
                    //Filtro de Ano
                    $ANO = Request::get('ANO');
                    if($ANO != 'T' && $ANO != ''){
                        $where .= " AND T.ANO = " . $ANO;  
                    }
                    
                    //Filtro de Periodo
                    $PERIODO = Request::get('PERIODO');
                    if($PERIODO != 'TO' && $PERIODO != ''){
                        $where .= " AND T.PERIODO = '" . $PERIODO . "'";  
                    }
                    
                    //Filtro de Escola
                    $ESCOLA = Request::get('ESCOLA');
                    if($ESCOLA != ''){
                        $where .= " AND E.NOME LIKE '%" . $ESCOLA . "%'";  
                    }
                    
                    //Filtro de classe
                    $CLASSE = Request::get('CLASSE');
                    if($CLASSE != ''){
                        $where .= " AND T.CLASSE LIKE '%" . $CLASSE . "%'";  
                    }
                    
                    //Filtro de Turma
                    $ID_TURMA = Request::get('ID_TURMA', 'NUMBER');
                    if($ID_TURMA > 0){
                        $where .= " AND T.ID_TURMA = " . $ID_TURMA;  
                    }
                }
                
                //Verifica se foi enviado o ID_LISTA e o filtro de Utilizadas
                $ID_HISTORICO_GERADOC   = Request::get('ID_LISTA', 'NUMBER');
                $utilizadas             = Request::get('utilizadas', 'NUMBER');
                
                //Lista todas escolas encontradas
                $rs = $mdEscolasTurmas->listarTurmasCliente(26436, 0, $utilizadas, ($utilizadas == 1 ? $ID_HISTORICO_GERADOC : 0), $where);
                
                //Variável que armazena IDs encontrados no Select
                $ids = "";
                
                if($rs->status){
                    $page           = Request::get('page', 'NUMBER'); 
                    $limit          = Request::get('rows', 'NUMBER'); 
                    $orderField     = Request::get('sidx'); 
                    $orderType      = Request::get('sord'); 

                    if(!$orderField) $orderField = 1;

                    //Total de registros
                    $count          = sizeof($rs->turmas);
                    $total_pages    = $count > 0 ? ceil($count/$limit) : 0;
                    $page           = $page > $total_pages ? $total_pages : $page;
                    $start          = $limit * $page - $limit;
                    
                    //Efetua select com ordenação e paginação
                    $rs = $mdEscolasTurmas->listarTurmasCliente(
                            26436,
                            0,
                            $utilizadas, 
                            $ID_HISTORICO_GERADOC,
                            $where,
                            array(
                                "campoOrdenacao"    => $orderField, 
                                "tipoOrdenacao"     => $orderType, 
                                "inicio"            => $start, 
                                "limite"            => $limit
                            )
                    );
                    
                    $ret->page      = $page;
                    $ret->total     = $total_pages;
                    $ret->records   = $count;

                    $i=0;
                    foreach($rs->turmas as $row) {
                        //Concatena IDs encontrados
                        if($ids != ""){
                            $ids .= ",";
                        }
                        $ids .= $row['ID_TURMA'];
                        
                        $ret->rows[$i]['id']   = $row['ID_TURMA'];
                        $ret->rows[$i]['cell'] = array(
                            "<input type='checkbox' value='{$row['ID_TURMA']}' class='check_turma' ".($row['ID_HISTORICO_GERADOC'] == $ID_HISTORICO_GERADOC ? "checked='checked'" : "")." onclick='javascript:salvaRelacaoTurma(this);' />",
                            $row['ID_TURMA'],
                            $row['CLASSE'],
                            EscolasTurmasModel::traduzirEnsino($row['ENSINO']),
                            $row['ANO'],
                            EscolasTurmasModel::traduzirPeriodo($row['PERIODO']),
                            $row['ESCOLA']
                        );
                        $i++;
                    }
                    //Armazena IDs no retorno
                    $ret->idsTurmas = $ids;
                }else{
                    $ret                    = new stdClass();
                    $ret->rows[0]['id']     = 0;
                    $ret->rows[0]['cell']   = array($rs->msg);
                }

                echo json_encode($ret);
            }catch(Exception $e){
                $ret                    = new stdClass();
                $ret->rows[0]['id']     = 0;
                $ret->rows[0]['cell']   = array('Erro: ' . $e->getMessage() . " <br /> Arquivo: " . $e->getFile() . " <br /> Linha: " . $e->getLine());
                
                echo json_encode($ret);
            }  
        }
        
        /**
         * Função que listas as turmas para um Grid JSon
         */
        public function actionGridListas(){
            try{
                //Objeto de retorno
                $ret = new stdClass();
                
                //Model de Listas de Exercícios
                $mdListas = new ListasModel();
                
                //Verifica filtros
                $where  = "";
                $search = Request::get('_search'); 
                if($search == true){
                    //Filtro Código da Lista
                    $COD_LISTA = Request::get('COD_LISTA');
                    if($COD_LISTA != ''){
                        $where = " AND L.COD_LISTA LIKE '%" . $COD_LISTA . "%'";  
                    }
                    
                    //Filtro Descrição/Nome da Lista
                    $DESCR_ARQ = Request::get('DESCR_ARQ');
                    if($DESCR_ARQ != ''){
                        $where .= " AND L.DESCR_ARQ LIKE '%" . $DESCR_ARQ . "%'";  
                    }
                }

                //Parâmetro fala filtro de turmas
                $ID_TURMA   = Request::get('ID_TURMA', 'NUMBER');
                //Parâmetro que verifica a busca de listas utilizadas
                $utilizadas = Request::get('utilizadas', 'NUMBER');
                
                //Carrega todas listas de um cliente + escola
                $rs = $mdListas->carregarListasCliente(26436, $where, $utilizadas, ($utilizadas == 1 ? $ID_TURMA : 0));
                
                //Variável que armazena IDs encontrados no Select
                $ids = "";
                
                //Verifica se foram carregadas as listas
                if($rs->status){
                    $page           = Request::get('page', 'NUMBER'); 
                    $limit          = Request::get('rows', 'NUMBER'); 
                    $orderField     = Request::get('sidx'); 
                    $orderType      = Request::get('sord'); 
                            
                    if(!$orderField) $orderField = 1;

                    //Total de registros
                    $count          = sizeof($rs->listas);
                    $total_pages    = $count > 0 ? ceil($count/$limit) : 0;
                    $page           = $page > $total_pages ? $total_pages : $page;
                    $start          = $limit * $page - $limit;
                    
                    //Efetua select com ordenação e paginação
                    $rs = $mdListas->carregarListasCliente(
                            26436,
                            $where,
                            $utilizadas,
                            $ID_TURMA,
                            array(
                                "campoOrdenacao"    => $orderField, 
                                "tipoOrdenacao"     => $orderType, 
                                "inicio"            => $start, 
                                "limite"            => $limit
                            )
                    );
                    
                    $ret->page      = $page;
                    $ret->total     = $total_pages;
                    $ret->records   = $count;

                    $i=0;
                    foreach($rs->listas as $row) {
                        //Concatena IDs encontrados
                        if($ids != ""){
                            $ids .= ",";
                        }
                        $ids .= $row['ID_HISTORICO_GERADOC'];
                        
                        $ret->rows[$i]['id']   = $row['ID_HISTORICO_GERADOC'];
                        $ret->rows[$i]['cell'] = array(
                            "<input type='checkbox' value='{$row['ID_HISTORICO_GERADOC']}' class='check_lista' ".($row['ID_TURMA'] == Request::get('ID_TURMA', 'NUMBER') ? "checked='checked'" : "")." onclick='javascript:salvaRelacaoLista(this);' />",
                            $row['COD_LISTA'],
                            $row['DESCR_ARQ'],
                            Date::formatDate($row['DATA_REGISTRO']),
                            $row['NUM_QUESTOES']
                        );
                        $i++;
                    }
                    //Armazena IDs no retorno
                    $ret->idsListas = $ids;
                }else{
                    $ret                    = new stdClass();
                    $ret->rows[0]['id']     = 0;
                    $ret->rows[0]['cell']   = array($rs->msg);
                }

                echo json_encode($ret);
            }catch(Exception $e){
                $ret                    = new stdClass();
                $ret->rows[0]['id']     = 0;
                $ret->rows[0]['cell']   = array('Erro: ' . $e->getMessage() . " <br /> Arquivo: " . $e->getFile() . " <br /> Linha: " . $e->getLine());
                
                echo json_encode($ret);
            }  
        }
        
        /**
         * Salva alteração de relacionamento entre listas e turmas
         */
        public function actionSalvarTurmaLista(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar listas da turma!";
                
                //Salva operação enviada
                $mdListas   = new ListasModel();
                $ret        = $mdListas->salvarListasTurmas(
                                    Request::post('idsTurmas'), 
                                    Request::post('idsListas'), 
                                    Request::post('tipo')
                                );
                
                echo json_encode($ret);
            }catch(Exception $e){
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }  
        }
        
        /**
         * Carrega as informações sobre o disparo de convites:
         * Total de Alunos e Total de Celulares
         */
        public function actionCarregarInfoConvite(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao disparar notificação aos Alunos!";
                
                //Captura variáveis enviadas
                $id             = Request::post('id');
                $tipo           = Request::post('tipo');
                //Instância Model
                $mdEscolaTurma  = new EscolasTurmasModel();
                                
                switch ($tipo) {
                    case 'T':
                        $ret            = $mdEscolaTurma->carregarContatosTurma($id);
                        $ret->idsTurmas = $id; //Retorna IDs de Turma utilizados
                        break;
                    case 'L':
                        //Busca turmas relacionadas a Lista
                        $rsTurmas = $mdEscolaTurma->listarTurmasCliente(26436, 0, 1, $id);
                        
                        //Verifica se houve retorno
                        if(!$rsTurmas->status){
                            $ret = $rsTurmas;
                        }else{
                            //Cancatena IDs de Turma encontradas
                            $ids = "";                            
                            foreach ($rsTurmas->turmas as $turma) {
                                if($ids != ""){
                                    $ids .= ",";
                                }
                                $ids .= $turma["ID_TURMA"];
                            }
                            
                            //Verifica informações de alunos para convites
                            $ret            = $mdEscolaTurma->carregarContatosTurma($ids);
                            $ret->idsTurmas = $ids; //Retorna IDs de Turma utilizados
                        }
                        break;
                }
                               
                echo json_encode($ret);
            }catch(Exception $e){
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Função que salva a lista de Turmas e Listas para onde devem ser disparados os convites.
         */
        public function actionDispararConvites(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao disparar notificação aos Alunos!";
                $txtErro        = ""; //Armazena erros caso existam
                $qtdDisparos    = 0; //Contador de disparos
                $verOk          = 0; //Contador de disparos OK
                
                //Captura variáveis enviadas
                $idsTurmas  = Request::post('idsTurmas');
                $idLista    = Request::post('idLista', 'NUMBER');
                $sms        = Request::post('sms');
                
                //Array de turmas
                $arrId = explode(",", $idsTurmas);
                //Armazena quantidade de turmas
                $ret->qtdTurmas = sizeof($arrId);
                
                //Instância do Model de Listas
                $mdListas   = new ListasModel();
                
                //Verifica se será enviado convite apenas para uma lista
                if($idLista > 0){
                    //Seta Total de Disparos
                    $qtdDisparos = sizeof($arrId);
                    //Insere registros para disparo apenas para a lista desejada
                    foreach($arrId as $idTurma){
                        //Insere registros para disparo via cron
                        $rs = $mdListas->salvarConvites(
                                26436,
                                $idTurma,
                                $idLista,
                                $sms
                        );
                        
                        if(!$rs->status){
                            $txtErro .= "<br />Falha ao disparar convites para Turma: " . $idTurma;
                        }else{
                            $verOk++;
                        }
                    }
                }else{
                    //Caso seja um disparo de todas as listas de uma ou mais turmas...
                    
                    //Varre Turma enviadas
                    foreach($arrId as $idTurma){
                        //Busca listas ta turma
                        $retListas = $mdListas->carregarListasCliente(26436, '', 1, $idTurma);
                        
                        //Se houver listas para turma
                        if($retListas->status){
                            //Seta Total de Disparos
                            $qtdDisparos = sizeof($retListas->listas);
                            //Insere um registro de convite para cada lista
                            foreach($retListas->listas as $lista){
                                //Insere registros para disparo via cron
                                $rs = $mdListas->salvarConvites(
                                        26436,
                                        $idTurma,
                                        $lista['ID_HISTORICO_GERADOC'],
                                        $sms
                                );
                                
                                //Caso exista erro no INSERT o erro é anotado
                                if(!$rs->status){
                                    $txtErro .= "<br />Falha ao disparar convites para Turma: " . $idTurma;
                                }else{
                                    $verOk++;
                                }
                            }
                        }else{
                            $ret = $retListas;
                        } //Verificação de listas
                    } //Loop de Turmas
                } //Verificação de convite é para lista ou turmas
                
                if($verOk == $qtdDisparos){
                    $ret->status = true;
                    $ret->msg = "Convites enviados com sucesso! Em breve seus alunos receberão as informações para acesso à(s) Lista(s) de Exercícios";
                }else if($verOk == 0){
                    $ret->msg = "Falha ao disparar convites, tente mais tarde!";
                }else{
                    $ret->status    = false;
                    $ret->msg       = "Falha ao disparar convites:<br />" . $txtErro;
                }
                
                echo json_encode($ret);
            }catch(Exception $e){
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
    }
?>
