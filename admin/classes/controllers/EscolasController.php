<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolasTurmasModel;
    use \sys\classes\util\Request;
    
    class Escolas extends AdminController {
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
                        $where = " ID_ESCOLA = " . $ID_ESCOLA;  
                    }
                    
                    //Nome da escola
                    $NOME = Request::get('NOME');
                    if($NOME != ''){
                        if($where != ""){
                            $where .= " AND ";
                        }
                        $where .= " NOME LIKE '%" . mysql_escape_string($NOME) . "%'";  
                    }
                    
                    //Status da escola
                    $STATUS = Request::get('STATUS', 'NUMBER');
                    if($STATUS){
                        if($STATUS != -1){
                            if($where != ""){
                                $where .= " AND ";
                            }
                            $where .= " STATUS = " . $STATUS;  
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
                            utf8_decode($row->NOME),
                            "<input type='radio' name='status_{$row->ID_ESCOLA}' value='1' ".($row->STATUS == 1 ? "checked='checked'" : "")." onclick='javascript:alteraStatusEscola({$row->ID_ESCOLA}, this.value);' /> Ativa &nbsp; <input type='radio' name='status_{$row->ID_ESCOLA}' value='0' ".($row->STATUS == 0 ? "checked='checked'" : "")." onclick='javascript:alteraStatusEscola({$row->ID_ESCOLA}, this.value);' /> Inativa ",
                            "<input type='button' value='Turmas' onclick='javascript:abreModalTurma({$row->ID_ESCOLA});' />"        
                        );
                        $i++;
                    }
                }

                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro: " . utf8_decode($e->getMessage()) . " - Arquivo: " . $e->getFile() . " - Linha: " . $e->getFile();
                $ret->escolas   = array();
                
                echo json_encode($ret);
            }
        }
    }
?>
