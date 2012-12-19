<?php
    use \admin\classes\controllers\AdminController;
    use \admin\classes\models\EscolaModel;
    use \sys\classes\util\Date;
    use \sys\classes\util\Request;
    use \sys\classes\html\Combobox;
    use \sys\classes\util\Component;
    
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
                $objViewPart->CODIGO        = $rs->cliente->ID_CLIENTE;
                $objViewPart->ESCOLA        = $rs->cliente->NOME_PRINCIPAL;
                $objViewPart->CREDITOS      = $rs->saldo;
                $objViewPart->VALIDADE      = Date::formatDate($rs->validade);
                
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
                //Consulta dados do usuários
                $mdEscola   = new EscolaModel();
                $rs         = $mdEscola->carregaDadosClienteHome(26436);
                
                //Valida retorno
                if(!$rs->status){
                    throw new Exception($rs->msg);
                }
                
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/escola_usuarios');
                
                //Atribui valores para marcações do TPL
                $objViewPart->CREDITOS      = $rs->saldo;
                $objViewPart->VALIDADE      = Date::formatDate($rs->validade);
                
                //Model de Escola
                $rs = $mdEscola->carregarFuncoesEscola(26436);
                
                //Atributos para combo
                $objAttr                = new stdClass();
                $objAttr->id            = "SEL_ID_AUTH_FUNCAO";
                $objAttr->name          = "SEL_ID_AUTH_FUNCAO";
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
                $mdEscola = new EscolaModel();
                
                //Verifica filtros
                $where = " AND (DEL <> 1 OR DEL IS NULL) ";
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
                        $html_check = "<input type='checkbox' value='{$row['ID_CLIENTE']}' class='checkGrid' />";
                        $html_bloq  = (int)$row['BLOQ'] == 0 ? "<a href='javascript:void(0);' onclick='javascript:bloquearUsuario({$row['ID_CLIENTE']}, 1)'>Bloquear</a>" : "<a href='javascript:void(0);' onclick='javascript:bloquearUsuario({$row['ID_CLIENTE']}, 0)'>Desbloquear</a>";
                        
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
                $idCliente  = Request::post("idCliente");
                $status     = Request::post("status", "NUMBER");
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->alterarBloqueioUsuario(26436, $idCliente, $status);
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Exclui um usuário da escola
         */
        public function actionExcluirUsuario(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao excluir usuário!";
                
                //Dados enviados
                $idCliente  = Request::post("idCliente");
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->excluirUsuario(26436, $idCliente);
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Envia link de acesso para um ou mais usuarios
         */
        public function actionEnviarLinkAcesso(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao enviar links de acesso!";
                
                //Dados enviados
                $idCliente  = Request::post("idCliente");
                
                //Valida ID Cliente
                if($idCliente == ""){
                    $ret->msg = "Nenhum ID cliente enviado!";
                    echo json_encode($ret);
                    die;
                }
                
                //Array de IDs
                $arrId = explode(",", $idCliente);
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                
                foreach($arrId as $id){
                    $ret = $mdEscola->criarSenhaTmp(26436, $id);
                    
                    //Valida retorno
                    if($ret->status){
                        $objMail = Component::mail();    
                        $objMail->setHtml(utf8_decode("
                            <b>{$ret->cliente->NOME_PRINCIPAL}</b>, sua senha de acesso ao SuperProWeb precisa ser Redefinida.
                            <br /><br />
                            <b>Acese o sitema através do link abaixo e redefina sua senha agora mesmo!</b>
                            <br />
                            <a href='http://www.sprweb.com.br' target='_blank'>http://www.sprweb.com.br</a>
                        "));

                        $objMail->addAddress('prg.pacheco@interbits.com.br', $ret->cliente->NOME_PRINCIPAL);
                        $objMail->setFrom('prg.pacheco@interbits.com.br', 'SuperProWeb');
                        $objMail->setSubject('Redefina sua senha de acesso ao SuperProWeb');

                        if(!$objMail->send()){
                            $ret->msg = $ret->msg . "<br><font color='red'>Falha ao disparar e-mail de acesso! Tente mais tarde.</font>";
                        }
                    }
                }
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Links de acesso enviados com sucesso!";
                
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
                $ENVIAR_ACESSO          = Request::post("ENVIAR_ACESSO", "NUMBER");
                
                //Model de Escola
                $mdEscola = new EscolaModel();
                
                $arrDados = array(
                                "ID_CLIENTE"        => Request::post("ID_CLIENTE", "NUMBER"),
                                "NOME_PRINCIPAL"    => Request::post("NOME_PRINCIPAL"),
                                "APELIDO"           => Request::post("APELIDO"),
                                "EMAIL"             => Request::post("EMAIL"),
                                "LOGIN"             => Request::post("LOGIN"),
                                "ID_AUTH_FUNCAO"    => Request::post("SEL_ID_AUTH_FUNCAO", "NUMBER"),
                            );
                
                //Se houver senha, ela é inserida
                if(trim($PASSWD) != ''){
                    if($PASSWD != $C_PASSWD){
                        $ret->msg = "O campo Senha deve idêntico ao campo Confirmar Senha";
                        echo json_encode($ret);
                        die;
                    }
                    
                    $arrDados['PASSWD'] = md5($PASSWD);                    
                }else if($GERAR_SENHA == 1 || $SENHA_NOVA_AUTOMATICA == 1){
                    $PASSWD             = $mdEscola->criarSenhaTmp(); //Gera senha
                    $arrDados['PASSWD'] = md5($PASSWD);
                }else if(trim($PASSWD) == '' && ($ID_CLIENTE <= 0 || $SENHA_NOVA_MANUAL == 1)){
                    $ret->msg = "O campo Senha é obrigatório!";
                    echo json_encode($ret);
                    die;
                }
                
                //Salva e armazena retorno
                $ret = $mdEscola->salvarUsuario(26436, $arrDados);
                
                //Verifica retorno e envia acesso se solicitado
                if($ret->status && ($ENVIAR_ACESSO == 1 || $GERAR_SENHA == 1 || $SENHA_NOVA_AUTOMATICA == 1)){
                    $objMail = Component::mail();    
                    $objMail->setHtml(utf8_decode("
                        <b>Parabéns! Você acaba de ser cadastrado(a) no SuperProWeb!</b>
                        <br /><br />
                        <b>Dados de acesso:</b>
                        <br />
                        <b>Login:</b> {$arrDados['LOGIN']}
                        <br />
                        <b>Senha:</b> {$PASSWD}
                        <br /><br />
                        <b>Acese o sitema através do link abaixo:</b>
                        <br />
                        <a href='http://www.sprweb.com.br' target='_blank'>http://www.sprweb.com.br</a>
                    "));
                    
                    $objMail->addAddress('prg.pacheco@interbits.com.br', $arrDados['NOME_PRINCIPAL']);
                    $objMail->setFrom('prg.pacheco@interbits.com.br', 'SuperProWeb');
                    $objMail->setSubject('Dados de acesso ao sistema SuperProWeb');
                    
                    if(!$objMail->send()){
                        $ret->msg = $ret->msg . "<br><font color='red'>Falha ao disparar e-mail de acesso! Tente mais tarde.</font>";
                    }
                }
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Carrega dados de um usário e retorna jSon
         */
        public function actionCarregarDadosUsuario(){
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
        
        /**
         * Inicia a página de Escola -> Suporte
         */
        public function actionSuporte(){
            try{
                //View do Grid de Escolas
                $objViewPart            = $this->mkViewPart('admin/escola_suporte');
                $objViewPart->RETORNO   = "";
                
                //Verifica se o formulário foi enviado
                if($_POST){
                    //Armazena campos
                    $nome   = Request::post("suporte_nome");
                    $area   = Request::post("suporte_area", "NUMBER");
                    $msg    = Request::post("suporte_msg");
                    $anexo  = isset($_FILES['suporte_arquivo']) ? true : false;
                    
                    //Define destino
                    switch ($area) {
                        case 1:
                            //Suporte
                            $emailDestino = "prg.pacheco@interbits.com.br";
                            break;
                        case 2:
                            //Comercial
                            $emailDestino = "prg.pacheco@interbits.com.br";
                            break;
                        case 3:
                            //Outro
                            $emailDestino = "prg.pacheco@interbits.com.br";
                            break;
                        default:
                            $emailDestino = "prg.pacheco@interbits.com.br";
                            break;
                    }
                    
                    //Dados do usuário
                    $usuario = new common\db_tables\Cliente(26436);
                    
                    if($usuario->ID_CLIENTE <= 0){
                        $objViewPart->ERRO  = "Cliente não encontrado! Entre em contato com o suporte.";
                    }else{
                        //Component de e-mail
                        $objMail = Component::mail();    
                        $objMail->setHtml(utf8_decode("
                            <b>Data de Envio:</b> ".(date("d/m/Y H:i:s"))."
                            <br />    
                            <b>Nome/Contato:</b> {$nome}
                            <br />    
                            <b>Login Usuário:</b> {$usuario->LOGIN}
                            <br />
                            <b>E-mail usuário:</b> {$usuario->EMAIL}
                            <br />
                            <b>Código de acesso:</b> {$usuario->ID_CLIENTE}
                            <br /><br />
                            <b>Mensagem:</b>
                            <p><i>{$msg}</i></p>
                            <br /><br />
                            <a href='http://www.sprweb.com.br' target='_blank'>http://www.sprweb.com.br</a>
                        "));

                        $objMail->addAddress('prg.pacheco@interbits.com.br');
                        $objMail->setFrom('prg.pacheco@interbits.com.br', 'SuperProWeb - Suporte');
                        $objMail->setSubject("[ESCOLAS] Mensagem enviada por {$usuario->NOME_PRINCIPAL}");
                        
                        //Variável de verificação
                        $verEnvio = true;
                        
                        //Adiciona anexo
                        if($anexo){
                            //Dados do arquivo
                            if($_FILES['suporte_arquivo']['error'] == 1){
                                $verEnvio = false;
                                $objViewPart->RETORNO = "<font size='3' color='red'>O anexo não pode possuir mais de 5MB.</font>";
                            }else{
                                $arquivo    = $_SERVER['DOCUMENT_ROOT'] . "/interbits/tmp/" . $_FILES['suporte_arquivo']['name'];
                                $tam        = $_FILES['suporte_arquivo']['size'] / 1024000;
                                
                                if($tam > 5){
                                    $verEnvio = false;
                                    $objViewPart->RETORNO = "<font size='3' color='red'>O anexo não pode possuir mais de 5MB.</font>";
                                }else{
                                    //Transfere arquivo para TMP
                                    if(copy($_FILES['suporte_arquivo']['tmp_name'], $arquivo)){
                                        //Anexa o arquivo
                                        $objMail->addAnexo($arquivo);
                                    }else{
                                        $verEnvio               = false;
                                        $objViewPart->RETORNO   = "<font size='3' color='red'>Falha ao copiar arquivo de anexo! Entre em contato com o suporte.</font>";
                                    }
                                }
                            }
                        }
                        
                        //Verifica erros de anexo (caso exista) e envia
                        if($verEnvio){
                            if($objMail->send()){
                                if($anexo){
                                    @unlink($arquivo);
                                }
                                $objViewPart->RETORNO = "<font size='3' color='blue'>E-mail enviado com sucesso! Em breve responderemos sua dúvida.</font>";
                            }else{
                                $objViewPart->RETORNO = "<font size='3' color='red'>Falha ao disparar e-mail de suporte! Tente mais tarde.</font>";
                            }
                        }
                    }
                }
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro | Área da Escola | Suporte';
                $tpl->SUBTITLE      = 'Suporte';
                
                //Js
                $tpl->setJs("admin/escola_suporte");
                
                $tpl->render('escola_suporte');
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
         * Lista informações para grid jSon de Cargos e Funções da Escola
         */
        public function actionGridCargos(){
            try{
                //Objeto de retorno
                $ret = new stdClass();
                
                //Model de Escola
                $mdEscola = new EscolaModel();
                
                //Verifica filtros
                $where  = "";
                $search = Request::get('_search'); 
                if($search == true){
                    //Filtro Nome
                    $FUNCAO = Request::get('FUNCAO');
                    if($FUNCAO != ''){
                        $where = " AND FUNCAO LIKE '%" . $FUNCAO . "%'";  
                    }
                    
                    //Filtro Código
                    $CODIGO = Request::get('CODIGO');
                    if($CODIGO != ''){
                        $where = " AND CODIGO LIKE '%" . $CODIGO . "%'";  
                    }
                    
                    //Filtro Limite de créditos
                    $LIM_CREDITO = Request::get('LIM_CREDITO', 'NUMBER');
                    if($LIM_CREDITO > 0){
                        $where = " AND LIM_CREDITO = " . $LIM_CREDITO;  
                    }
                    
                    //Filtro Limite de créditos
                    $RECARGA_AUTO = Request::get('RECARGA_AUTO', 'NUMBER');
                    if($RECARGA_AUTO > 0){
                        $where = " AND RECARGA_AUTO " . ($RECARGA_AUTO == 1 ? " > 0" : " <= 0");  
                    }
                }
                
                //Carrega cargos e funções da escola
                $rs = $mdEscola->carregarCargosFuncoes(26436, $where);
                
                //Verifica se foram carregadas as listas
                if($rs->status){
                    $page           = Request::get('page', 'NUMBER'); 
                    $limit          = Request::get('rows', 'NUMBER'); 
                    $orderField     = Request::get('sidx'); 
                    $orderType      = Request::get('sord'); 
                            
                    if(!$orderField) $orderField = 1;

                    //Total de registros
                    $count          = sizeof($rs->cargos);
                    $total_pages    = ($count > 0 && $limit > 0) ? ceil($count/$limit) : 0;
                    $page           = $page > $total_pages ? $total_pages : $page;
                    $start          = $limit * $page - $limit;
                    
                    //Efetua select com ordenação e paginação
                    $rs = $mdEscola->carregarCargosFuncoes(
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
                    foreach($rs->cargos as $row) {
                        $html_recarga  = (int)$row->RECARGA_AUTO <= 0 ? "<a href='javascript:void(0);' onclick='javascript:alterarRecargaCargo({$row->ID_AUTH_FUNCAO}, 1)'>Liberar</a>" : "<a href='javascript:void(0);' onclick='javascript:alterarRecargaCargo({$row->ID_AUTH_FUNCAO}, 0)'>Bloquear</a>";
                        
                        $ret->rows[$i]['id']   = $row->ID_AUTH_FUNCAO;
                        $ret->rows[$i]['cell'] = array(
                            $row->ID_AUTH_FUNCAO,
                            $row->FUNCAO,
                            $row->CODIGO,
                            $row->LIM_CREDITO,
                            $html_recarga
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
         * Salva um novo cargo e devolve resposta em jSon
         */
        public function actionSalvarCargo(){
            try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar Cargo!";
                
                //Model de Escola
                $mdEscola = new EscolaModel();
                
                //Carrega dados do Usuário
                $ret = $mdEscola->salvarCargoEscola(26436, array(
                                "ID_AUTH_FUNCAO"    => Request::post("ID_AUTH_FUNCAO", "NUMBER"),
                                "FUNCAO"            => Request::post("FUNCAO"),
                                "CODIGO"            => Request::post("CODIGO"),
                                "LIM_CREDITO"       => Request::post("LIM_CREDITO", "NUMBER"),
                                "RECARGA_AUTO"      => Request::post("RECARGA_AUTO", "NUMBER")
                            )
                       );
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Carrega dados de um usário e retorna jSon
         */
        public function actionCarregarCargo(){
            try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar dados do Cargo/Função!";
                
                //Model de Escola
                $mdEscola = new EscolaModel();
                
                //Carrega dados do Usuário
                $ret = $mdEscola->carregarCargo(Request::post("idCargo", "NUMBER"));
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Altera status de recarga automática de um Cargo/Função
         */
        public function actionAlterarRecargaCargo(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar Recarga Automática do Cargo!";
                
                //Dados enviados
                $idCargo  = Request::post("idCargo");
                $status   = Request::post("status", "NUMBER");
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->alterarRecargaAutoCargo(26436, $idCargo, $status);
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Exclui um Cargo/Função da Escola
         */
        public function actionExcluirCargo(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao excluir Cargo/Função!";
                
                //Dados enviados
                $idCargo  = Request::post("idCargo");
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->excluirCargo(26436, $idCargo);
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Executa uma operação de crédito ou estorno de uma escola para um cliente
         */
        public function actionExecutaOperacaoCredito(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao executar a operação de crédito!";
                
                //Model de escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->operacaoCredito(
                                26436, 
                                Request::post("idCliente", "NUMBER"), 
                                Request::post("operacao", "NUMBER"), 
                                Request::post("creditos", "NUMBER")
                              );
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Inicializa a página de Escola
         */
        public function actionAcessos(){
            try{
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/escola_acessos');
                
                //Atributos para combo
                $objAttr                = new stdClass();
                $objAttr->id            = "SEL_ID_PERFIL";
                $objAttr->name          = "SEL_ID_PERFIL";
                $objAttr->cls           = "required";
                $objAttr->field_name    = "Perfil";
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $rs         = $mdEscola->carregarPerfisAcesso();
                
                //Inicia Combo de Perfis com parâmetros
                $objCb = new Combobox($objAttr);
                
                //Verifica retorno de Select
                if(!$rs->status){
                    $objCb->addOption(0, $rs->msg);
                }else{
                    $objCb->addOption(0, "Selecione um Perfil");
                    foreach($rs->perfis as $perfil){
                        $objCb->addOption($perfil->ID_PERFIL, $perfil->DESCRICAO);
                    }
                }
                
                //Envia HTML de Combo para View
                $objViewPart->CB_PERFIS = $objCb->render();
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro | Área da Escola | Contas de Acesso';
                $tpl->SUBTITLE      = 'Escola';
                
                //Instância de JS
                $tpl->setJs('admin/escola_acessos');
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('escola_acessos');
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function actionGridUsuariosAcesso(){
            try{
                //Objeto de retorno
                $ret = new stdClass();
                
                //Consulta dados do usuários
                $mdEscola = new EscolaModel();
                
                //Verifica filtros
                $where = " AND (DEL <> 1 OR DEL IS NULL) ";
                $search = Request::get('_search'); 
                if($search == true){
                    //Filtro Nome
                    $NOME = Request::get('NOME');
                    if($NOME != ''){
                        $where = " AND U.NOME LIKE '%" . $NOME . "%'";  
                    }
                    
                    //Filtro Perfil
                    $PERFIL = Request::get('PERFIL');
                    if($PERFIL != ''){
                        $where = " AND P.DESCRICAO LIKE '%" . $PERFIL . "%'";  
                    }
                    
                    //Filtro E-mail
                    $EMAIL = Request::get('EMAIL');
                    if($EMAIL != ''){
                        $where = " AND U.EMAIL LIKE '%" . $EMAIL . "%'";  
                    }
                    
                    //Filtro Login
                    $LOGIN = Request::get('LOGIN');
                    if($LOGIN != ''){
                        $where = " AND U.LOGIN LIKE '%" . $LOGIN . "%'";  
                    }
                    
                    //Filtro Bloqueio
                    $BLOQ = Request::get('BLOQ', 'NUMBER');
                    if($BLOQ > 0){
                        $where = " AND U.BLOQ = " . ($BLOQ == 1 ? 0 : 1);  
                    }
                    
                    //Filtro Bloqueio
                    $DATA_REGISTRO = Request::get('DATA_REGISTRO');
                    if($DATA_REGISTRO > 0){
                        $where = " AND DATE(U.DATA_REGISTRO) = '" . (Date::formatDate($DATA_REGISTRO, 'AAAA-MM-DD')) . "'";  
                    }
                }
                
                //Carrega usuários da escola
                $rs = $mdEscola->carregarUsuariosAcesso(26436, $where);
                
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
                    $rs = $mdEscola->carregarUsuariosAcesso(
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
                        $html_bloq  = (int)$row['BLOQ'] == 0 ? "<a href='javascript:void(0);' onclick='javascript:bloquearUsuario({$row['ID_USUARIO']}, 1)'>Bloquear</a>" : "<a href='javascript:void(0);' onclick='javascript:bloquearUsuario({$row['ID_USUARIO']}, 0)'>Desbloquear</a>";
                        
                        $ret->rows[$i]['id']   = $row['ID_USUARIO'];
                        $ret->rows[$i]['cell'] = array(
                            $row['ID_USUARIO'],
                            $row['NOME'],
                            $row['DESCRICAO'],
                            $row['EMAIL'],
                            $row['LOGIN'],
                            Date::formatDate($row['DATA_REGISTRO']),
                            $html_bloq
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
         * Salva um novo usuário de acesso da escola e devolve resposta em jSon
         */
        public function actionSalvarUsuarioAcesso(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar usuário de acesso!";
                
                //Dados para execução
                $arrDados = array(
                                "NOME"      => Request::post("NOME"),
                                "EMAIL"     => Request::post("EMAIL"),
                                "LOGIN"     => Request::post("LOGIN"),
                                "TELEFONE"  => Request::post("TELEFONE"),
                                "ID_PERFIL" => Request::post("SEL_ID_PERFIL", "NUMBER"),
                            );
                
                //Verificação de senha
                $SENHA      = Request::post("SENHA");
                $C_SENHA    = Request::post("C_SENHA");
                
                if($SENHA != ""){
                    if($SENHA != $C_SENHA){
                        $ret->msg = "Os campos Senha e Confirmar Senha devem ser igauis!";
                        echo json_encode($ret);
                        die;
                    }else{
                        $arrDados['SENHA'] = md5($SENHA);
                    }
                }
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->salvarUsuarioAcesso(26436, $arrDados, Request::post("ID_USUARIO", "NUMBER"));
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Carrega dados de um usário e retorna jSon
         */
        public function actionCarregarDadosUsuarioAcesso(){
            try{
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar dados do Usuário!";
                
                //Model de Escola
                $mdEscola = new EscolaModel();
                
                //Carrega dados do Usuário
                $ret = $mdEscola->carregarDadosUsuarioAcesso(26436, Request::post("idUsuario", "NUMBER"));
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Altera status de bloqueio de um usuário de acesso vindo do grid
         */
        public function actionBloquearUsuarioAcesso(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar bloqueio do usuário!";
                
                //Dados enviados
                $idUsuario  = Request::post("idUsuario");
                $status     = Request::post("status", "NUMBER");
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->alterarBloqueioUsuarioAcesso(26436, $idUsuario, $status);
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Exclui um usuário de acesso da escola
         */
        public function actionExcluirUsuarioAcesso(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao excluir usuário!";
                
                //Dados enviados
                $idUsuario  = Request::post("idUsuario");
                
                //Model de Escola
                $mdEscola   = new EscolaModel();
                $ret        = $mdEscola->excluirUsuarioAcesso(26436, $idUsuario);
                
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
