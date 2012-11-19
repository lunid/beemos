<?php
    use \admin\classes\controllers\AdminController;
    use \sys\classes\util\Date;
    use \sys\classes\util\Request;
    use \admin\classes\models\ListasModel;
    use \admin\classes\html\AbaLista;
    
    class Listas extends AdminController {
        /**
         * Inicializa a página de Minhas Listas
         * Inicializa as Abas da Página
         */
        public function actionIndex(){
            try{
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/minhas_listas');
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro';
                $tpl->SUB_TITULO    = 'Minhas Listas';
                
                $tpl->setJs('admin/minhas_listas');
                $tpl->setCss('admin/minhas_listas');
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('minhas_listas');
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
         * Função que carrega as listas em formato jSon para JQGrid
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

                //Carrega todas listas de um cliente + escola
                $rs = $mdListas->carregaListasCliente(26436, $where);
                
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
                    $rs = $mdListas->carregaListasCliente(
                            26436,
                            $where,
                            0, //Utilizadas
                            0, //ID Turma
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
                        //Verifica Status
                        if($row['LISTA_ATIVA_DT_HR_INI'] == null || $row['LISTA_ATIVA_DT_HR_FIM'] == null){
                            $status = "Ativa";
                        }else{
                            $status = $row['STATUS'];
                        }
                        
                        $ret->rows[$i]['id']   = $row['ID_HISTORICO_GERADOC'];
                        $ret->rows[$i]['cell'] = array(
                            $row['COD_LISTA'],
                            $row['DESCR_ARQ'],
                            Date::formatDate($row['DATA_REGISTRO']),
                            $row['VER_IMPRESSA'] == 1 ? 'Sim' : 'Não',
                            "-",
                            $status,
                            "<a href='javascript:void(0);' onclick='abreLista({$row['ID_HISTORICO_GERADOC']}, \"{$row['DESCR_ARQ']}\")'><img src='/interbits/assets/images/editar_icone.png' border='0' style='width:17px;height:17px;' /></a>"
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
         * Carrega o html para montar uma nova aba de Lista seleciona no grid
         */
        public function actionCarregaHtmlAbaLista(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar dados da Lista! Tente mais tarde.";
                
                //Pega as variáveis enviadas
                $idLista    = Request::post("idLista", "NUMBER");
                
                //Carrega dados da lista solicitada
                $mdListas   = new ListasModel();
                $ret        = $mdListas->carregaDadosLista($idLista);
                
                //Se forem carregados os dados
                if($ret->status){
                    //Objeto HTML para montar Aba
                    $aba        = new AbaLista();
                    //Dados a serem repassados ao HTML
                    $aba->setAttr('ID_HISTORICO_GERADOC', $idLista);
                    $aba->setAttr('ANTICOLA', $ret->lista->ANTICOLA);
                    $aba->setAttr('PERIODO_INICIO', Date::formatDate($ret->lista->LISTA_ATIVA_DT_HR_INI));
                    $aba->setAttr('PERIODO_FINAL', Date::formatDate($ret->lista->LISTA_ATIVA_DT_HR_FIM));
                    $aba->setAttr('ST_RESULTADO_ALUNO', $ret->lista->ST_RESULTADO_ALUNO);
                    $aba->setAttr('ST_GABARITO_ALUNO', $ret->lista->ST_GABARITO_ALUNO);
                    $aba->setAttr('TEMPO_VIDA', $ret->lista->TEMPO_VIDA);
                    //HTML final
                    $ret->html  = $aba->render();
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
         * Altera o status de Anticola de uma lista
         */
        public function actionAlteraAnticola(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao tentar alterar status de Anticola! Tente mais tarde.";
                
                //Pega as variáveis enviadas
                $idLista    = Request::post("idLista", "NUMBER");
                $status     = Request::post("status", "NUMBER");
                
                if($idLista <= 0){
                    $ret->msg = "ID_HISTORICO_GERADOC inválido ou nulo!";
                }else{
                    //Instancia o Objeto Model e efetua alteração
                    $mdListas   = new ListasModel();
                    $ret        = $mdListas->alteraAnticola($idLista, $status);
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
         * Altera o Período de Vida de uma Lista
         */
        public function actionAlteraPeriodo(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao tentar alterar período de validade! Tente mais tarde.";
                
                //Pega as variáveis enviadas
                $idLista    = Request::post("idLista", "NUMBER");
                $data       = Request::post("data");
                $tipo       = Request::post("tipo");
                
                if($idLista <= 0){
                    $ret->msg = "ID_HISTORICO_GERADOC inválido ou nulo!";
                }else{
                    //Instancia o Objeto Model e efetua alteração
                    $mdListas   = new ListasModel();
                    $ret        = $mdListas->alteraPeriodo($idLista, $data, $tipo);
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
         * Altera a permissão de um aluno visualizar (ou não) o seu resultado final após finalizar a lista
         */
        public function actionAlteraResultadoAluno(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao tentar alterar status de Resultado do Aluno! Tente mais tarde.";
                
                //Pega as variáveis enviadas
                $idLista    = Request::post("idLista", "NUMBER");
                $status     = Request::post("status", "NUMBER");
                
                if($idLista <= 0){
                    $ret->msg = "ID_HISTORICO_GERADOC inválido ou nulo!";
                }else{
                    //Instancia o Objeto Model e efetua alteração
                    $mdListas   = new ListasModel();
                    $ret        = $mdListas->alteraResultadoAluno($idLista, $status);
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
         * Altera a permissão de um aluno visualizar (ou não) o Gabarito da Listas
         */
        public function actionAlteraGabaritoAluno(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao tentar alterar status de Gabarito do Aluno! Tente mais tarde.";
                
                //Pega as variáveis enviadas
                $idLista    = Request::post("idLista", "NUMBER");
                $status     = Request::post("status", "NUMBER");
                
                if($idLista <= 0){
                    $ret->msg = "ID_HISTORICO_GERADOC inválido ou nulo!";
                }else{
                    //Instancia o Objeto Model e efetua alteração
                    $mdListas   = new ListasModel();
                    $ret        = $mdListas->alteraGabaritoAluno($idLista, $status);
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
         * Altera o tempo limite que o aluno possui paa iniciar e finalizar a lista de questões. Ex: 00:45
         */
        public function actionAlteraTempoVida(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao tentar alterar status de Gabarito do Aluno! Tente mais tarde.";
                
                //Pega as variáveis enviadas
                $idLista    = Request::post("idLista", "NUMBER");
                $tempo      = Request::post("tempo");
                
                if($idLista <= 0){
                    $ret->msg = "ID_HISTORICO_GERADOC inválido ou nulo!";
                }else{
                    //Instancia o Objeto Model e efetua alteração
                    $mdListas   = new ListasModel();
                    $ret        = $mdListas->alteraTempoVida($idLista, $tempo);
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
