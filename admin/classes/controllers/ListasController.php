<?php
    use \admin\classes\controllers\AdminController;
    use \sys\classes\util\Date;
    use \sys\classes\util\Request;
    use \admin\classes\models\ListasModel;
    
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
    }
?>
