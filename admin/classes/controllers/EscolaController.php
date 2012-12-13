<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolaModel;
    use \sys\classes\util\Date;
    use \sys\classes\util\Request;
    use \sys\classes\html\Combobox;

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
        
        /**
         * Inicializa a página de usuários de uma escola
         * Carrega dados de funções da escola
         */
        public function actionUsuarios(){
            try{
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/escola_usuarios');
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $rs         = $mdEscola->carregarFuncoesEscola(26436);
                
                //Atributos para combo
                $objAttr                = new stdClass();
                $objAttr->id            = "ID_AUTH_FUNCAO";
                $objAttr->name          = "ID_AUTH_FUNCAO";
                $objAttr->cls           = "required";
                $objAttr->field_name    = "Cargo/Função";
                
                //Inicia Combo Funções com parâmetros
                $objCb = new Combobox($objAttr);
                
                //Verifica retorno de Select
                if(!$rs->status){
                    $objCb->addOption(0, $rs->msg);
                }else{
                    $objCb->addOption(0, "Selecione um Cargo/Função");
                    foreach($rs->funcoes as $funcao){
                        $objCb->addOption($funcao->ID_AUTH_FUNCAO, utf8_decode($funcao->FUNCAO));
                    }
                }
                
                //Envia HTML de Combo para View
                $objViewPart->CB_FUNCOES = $objCb->render();
                
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
        
        /**
         * Lista informações pata grid jSon de Usuários da Escola
         */
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
                            utf8_decode($row['NOME_PRINCIPAL']),
                            utf8_decode($row['FUNCAO']),
                            $row['EMAIL'],
                            utf8_decode($row['LOGIN']),
                            $html_bloq,
                            Date::formatDate($row['DATA_REGISTRO']),
                            '<span class="icon gray" data-icon="m"></span>'
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
         * Altera status de bloquio de um usuário vindo do grid
         */
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
        
        /**
         * Salva dados do usuário
         */
        public function actionSalvarUsuario(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar bloqueio do usuário!";
                
                //Dados enviados
                $ID_CLIENTE             = Request::post("ID_CLIENTE", "NUMBER");
                $GERAR_SENHA            = Request::post("SENHA_SISTEMA", "NUMBER");
                $SENHA_NOVA_AUTOMATICA  = Request::post("SENHA_NOVA_AUTOMATICA", "NUMBER");
                $SENHA_NOVA_MANUAL      = Request::post("SENHA_NOVA_MANUAL", "NUMBER");
                $PASSWD                 = Request::post("PASSWD");
                $C_PASSWD               = Request::post("C_PASSWD");
                
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
                    
                    $arrDados['PASSWD_TMP'] = '';
                    $arrDados['PASSWD']     = md5($PASSWD);
                }else if($GERAR_SENHA == 1 || $SENHA_NOVA_AUTOMATICA == 1){
                    $arrDados['PASSWD']     = '';
                    $arrDados['PASSWD_TMP'] = md5("snPdSPRW" . date("Y"));
                }else if(trim($PASSWD) == '' && ($ID_CLIENTE <= 0 || $SENHA_NOVA_MANUAL == 1)){
                    $ret->msg = "O campo Senha é obrigatório!";
                    echo json_encode($ret);
                    die;
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
        
        public function actionCarregaDadosUsuario(){
            try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar dados do Usuário!";
                
                //Model de Escola
                $mdEscola = new EscolaModel();
                
                //Carrega dados do Usuário
                $ret = $mdEscola->carregarDadosUsuario(Request::post("idCliente", "NUMBER"));
                
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