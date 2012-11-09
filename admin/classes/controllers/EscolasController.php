<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolasTurmasModel;
    use \sys\classes\util\Request;
    use \sys\classes\html\Table;
    
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
                $rs = $mdEscolasTurmas->listaEscolasCliente(26436, $where);
                
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
                    $rs = $mdEscolasTurmas->listaEscolasCliente(
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
                            "<input type='button' value='Turmas' onclick='javascript:turmas({$row->ID_ESCOLA}, 26436, \"{$row->NOME}\");' />"        
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
                $ID_CLIENTE = Request::post("escolaIdCliente", "NUMBER");
                $NOME       = Request::post("escolaNome");
                
                //Executa chamada de model para salvar a nova escola
                $mEscolasTurmas = new EscolasTurmasModel();
                $ret            = $mEscolasTurmas->salvarEscola($ID_CLIENTE, $NOME);

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
        public function actionAlteraStatusEscola(){
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
                $ret            = $mEscolasTurmas->alteraStatusEscola($ID_ESCOLA, $ID_CLIENTE, $STATUS);

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
        public function actionListaTurmas(){
            try{
                //Obejto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listas Turmas!";
                
                //Recebe dados do POST
                $ID_ESCOLA  = Request::post("ID_ESCOLA", "NUMBER");
                $ID_CLIENTE = Request::post("ID_CLIENTE", "NUMBER");
                
                //Executa chamada de model para salvar a nova escola
                $mEscolasTurmas = new EscolasTurmasModel();
                $ret            = $mEscolasTurmas->listaTurmasCliente($ID_CLIENTE, $ID_ESCOLA);

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
                $rs = $mdEscolasTurmas->listaTurmasGrid(26436);
                
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
                    $rs = $mdEscolasTurmas->listaTurmasGrid(
                            26436, 
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
                        $ret->rows[$i]['id']   = $row->ID_TURMA;
                        $ret->rows[$i]['cell'] = array(
                            $row->ID_TURMA,
                            $row->CLASSE
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
    }
?>
