<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolaModel;
    use \sys\classes\util\Date;
    use \sys\classes\util\Request;

    class Escola extends AdminController {
        /**
         * Inicializa a página de Escola
         */
        public function actionIndex(){
            try{
                //Consulta dados do usuários
                $mdEscola   = new EscolaModel();
                $rs         = $mdEscola->carregaDadosClienteHome(26436);
                
                //Valida retorno
                if(!$rs->status){
                    throw new Exception($rs->msg);
                }
                
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/escola');
                
                //Atribui valores para marcações do TPL
                $objViewPart->CODIGO    = $rs->cliente->ID_CLIENTE;
                $objViewPart->ESCOLA    = $rs->cliente->NOME_PRINCIPAL;
                $objViewPart->CREDITOS  = $rs->saldo;
                $objViewPart->VALIDADE  = Date::formatDate($rs->validade);
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro | Área da Escola';
                $tpl->SUBTITLE      = 'Escola';
                
                $tpl->render('escola');
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function actionUsuarios(){
            try{
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/escola_usuarios');
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro | Escola | Usuários';
                
                //Instância de JS
                $tpl->setJs('admin/escola_usuarios');
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('escola_usuarios');
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function actionGridUsuarios(){
            try{
                //Objeto de retorno
                $ret = new stdClass();
                
                //Consulta dados do usuários
                $mdEscola   = new EscolaModel();
                
                //Verifica filtros
                $where  = "";
                $search = Request::get('_search'); 
                if($search == true){
                    //Filtro Nome
                    $NOME_PRINCIPAL = Request::get('NOME_PRINCIPAL');
                    if($NOME_PRINCIPAL != ''){
                        $where = " AND C.NOME_PRINCIPAL LIKE '%" . $NOME_PRINCIPAL . "%'";  
                    }
                    
                    //Filtro Função
                    $FUNCAO = Request::get('FUNCAO');
                    if($FUNCAO != ''){
                        $where = " AND F.FUNCAO LIKE '%" . $FUNCAO . "%'";  
                    }
                    
                    //Filtro E-mail
                    $EMAIL = Request::get('EMAIL');
                    if($EMAIL != ''){
                        $where = " AND C.EMAIL LIKE '%" . $EMAIL . "%'";  
                    }
                    
                    //Filtro Login
                    $LOGIN = Request::get('LOGIN');
                    if($LOGIN != ''){
                        $where = " AND C.LOGIN LIKE '%" . $LOGIN . "%'";  
                    }
                    
                    //Filtro de Bloqueados
                    $BLOQ = Request::get('BLOQ');
                    if((int)$BLOQ > 0){
                        $where = " AND C.BLOQ = " . ((int)$BLOQ == 1 ? 1 : 0) . "";  
                    }
                    
                    //Filtro de Data
                    $DATA_REGISTRO = Request::get('DATA_REGISTRO');
                    if($DATA_REGISTRO != ''){
                        $where = " AND DATE(C.DATA_REGISTRO) = '" . (Date::formatDate($DATA_REGISTRO, 'AAAA-MM-DD')) . "'";  
                    }
                }
                
                //Carrega usuários da escola
                $rs = $mdEscola->carregarUsuariosEscola(26436, $where);
                
                //Verifica se foram carregadas as listas
                if($rs->status){
                    $page           = Request::get('page', 'NUMBER'); 
                    $limit          = Request::get('rows', 'NUMBER'); 
                    $orderField     = Request::get('sidx'); 
                    $orderType      = Request::get('sord'); 
                            
                    if(!$orderField) $orderField = 1;

                    //Total de registros
                    $count          = sizeof($rs->usuarios);
                    $total_pages    = ($count > 0 && $limit > 0) ? ceil($count/$limit) : 0;
                    $page           = $page > $total_pages ? $total_pages : $page;
                    $start          = $limit * $page - $limit;
                    
                    //Efetua select com ordenação e paginação
                    $rs = $mdEscola->carregarUsuariosEscola(
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
                    foreach($rs->usuarios as $row) {
                        $html_check = "<input type='checkbox' value='{$row['ID_CLIENTE']}' />";
                        $html_bloq  = (int)$row['BLOQ'] == 0 ? "<a href='javascript:void(0);' onclick='javascript:bloquearUsuario(26436, {$row['ID_CLIENTE']}, 1)'>Bloquear</a>" : "<a href='javascript:void(0);' onclick='javascript:bloquearUsuario(26436, {$row['ID_CLIENTE']}, 0)'>Desbloquear</a>";
                        
                        $ret->rows[$i]['id']   = $row['ID_CLIENTE'];
                        $ret->rows[$i]['cell'] = array(
                            $html_check,
                            $row['NOME_PRINCIPAL'],
                            $row['FUNCAO'],
                            $row['EMAIL'],
                            $row['LOGIN'],
                            $html_bloq,
                            Date::formatDate($row['DATA_REGISTRO'])
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
        
        public function actionBloquearUsuario(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar bloqueio do usuário!";
                
                //Dados enviados
                $idMatriz   = Request::post("idMatriz", "NUMBER");
                $idCliente  = Request::post("idCliente", "NUMBER");
                $status     = Request::post("status", "NUMBER");
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->alterarBloqueioUsuario($idMatriz, $idCliente, $status);
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        public function actionSalvarUsuario(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar bloqueio do usuário!";
                
                //Dados enviados
                $GERAR_SENHA    = Request::post("SENHA_SISTEMA");
                $PASSWD         = Request::post("PASSWD");
                $C_PASSWD       = Request::post("C_PASSWD");
                
                //Model de Escola
                $mdEscola = new EscolaModel();
                
                $arrDados = array(
                                "ID_CLIENTE"        => Request::post("ID_CLIENTE", "NUMBER"),
                                "NOME_PRINCIPAL"    => Request::post("NOME_PRINCIPAL"),
                                "APELIDO"           => Request::post("APELIDO"),
                                "EMAIL"             => Request::post("EMAIL"),
                                "LOGIN"             => Request::post("LOGIN"),
                                "ID_AUTH_FUNCAO"    => Request::post("ID_AUTH_FUNCAO", "NUMBER"),
                            );
                
                //Se houver senha, ela é inserida
                if(trim($PASSWD) != ''){
                    if($PASSWD != $C_PASSWD){
                        $ret->msg = "O campo Senha deve idêntico ao campo Confirmar Senha";
                        echo json_encode($ret);
                        die;
                    }
                    
                    $arrDados['PASSWD'] = md5($PASSWD);
                }else if($GERAR_SENHA == "on"){
                    $arrDados['PASSWD']     = '';
                    $arrDados['PASSWD_TMP'] = md5("snPdSPRW" . date("Y"));
                }
                
                //Salva e armazena retorno
                $ret = $mdEscola->salvarUsuario(26436, $arrDados);
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
    }
?>
