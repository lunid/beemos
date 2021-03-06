<?php   
    use \sys\classes\webservice\WsServer;
    use \api\classes\Util;
    use \api\classes\Security;
    use \common\classes\models\UsuariosModel;
    use \app_escola\classes\models\EscolaModel;
    use \common\classes\helpers\Usuario;
    
    class Usuarios extends WsServer {
        
        public function __construct() {
            try{      
                $this->setWsInterfaceClass(__CLASS__);   
            }catch(Exception $e){
                die(utf8_decode("<b>Erro Fatal:</b> " . $e->getMessage() . " - Entre em contato com suporte!"));
            }
        }
        
        /**
         * Função que exclui um usuário da escola logada no WS
         * 
         * @param string $xmlParams String com o XML dos campos de entrada
         * @return string $xmlResult
         */
        public function excluiUsuario($xmlParams){
            try{
                $erro   = 0;
                $msg    = "Usuário excluido com sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token      = $this->getXmlField($params, 'token');
                        $idUsuario  = $this->getXmlField($params, 'id_usuario');
                        
                        if(!isset($token) || $token == null || $token == ""){
                            $erro   = 4;
                            $msg    = "Token inválido!";
                        }else{
                            //Autentica usuário e token
                            $ret = $this->authenticate($token);
                            
                            if(!$ret->status){
                                $erro   = $ret->erro;
                                $msg    = $ret->msg;
                            }else{
                                //Valida envio do id_usuario
                                if($idUsuario <= 0 || $idUsuario == $ret->ID_USER){
                                    if($idUsuario <= 0){
                                        $erro   = 91;
                                        $msg    = "ID_USUARIO inválido ou nulo!";
                                    }else{
                                        $erro   = 92;
                                        $msg    = "Sem permissão para excluir seu próprio usuário!";
                                    }
                                }else{
                                    //Model de Usuarios
                                    $mdUsuarios = new UsuariosModel();
                                    
                                    //Valida se o usuário é dependente do usuário logado
                                    if(!$mdUsuarios->validarUsuarioMatriz($idUsuario, $ret->ID_USER)){
                                        $erro   = 83;
                                        $msg    = "Usuário não é seu dependente ou não existe!";
                                    }else{
                                        $rs = $mdUsuarios->atualizarStatusUsuario($idUsuario, $ret->ID_USER, 'EXCLUIR');

                                        //VErifica se o status foi atualizado
                                        if(!$rs->status){
                                            $erro   = 84;
                                            $msg    = $rs->msg;
                                        }
                                    } //Verifica dependencia do usuário
                                } //Valida envio de id_usuario
                            }
                        }
                    }else{
                        $erro   = 6;
                        $msg    = "XML de entrada fora do padrão!";
                    }
                }else{
                    $erro   = 5;
                    $msg    = "XML Params inválido ou nulo!";
                }

                //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>{$msg}</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";

                return $ret;
            }catch(Exception $e){
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>1</erro>";
                $ret .= "<msg>". $e->getMessage() ."</msg>";
                $ret .= "</status>";
                $ret .= "</root>";

                return $ret;
            }
        }
        
        /**
         * Função que atualiza os dados de um cliente através de um cliente logado
         * Apenas clientes já cadastrados e dependentes do cliente podem ser alterados
         * 
         * @param string $xmlParams String com campos de entrada
         * @return string
         */
        public function atualizaUsuario($xmlParams){
            try {
                $erro   = 0;
                $msg    = "Consulta efetuada com sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token      = $this->getXmlField($params, 'token');
                        $idUsuario  = $this->getXmlField($params, 'id_usuario');
                        $nome       = $this->getXmlField($params, 'nome');
                        $email      = $this->getXmlField($params, 'email');
                        $login      = $this->getXmlField($params, 'login');
                        $senha      = $this->getXmlField($params, 'passwd_md5');

                        if(!isset($token) || $token == null || $token == ""){
                            $erro   = 4;
                            $msg    = "Token inválido!";
                        }else{
                            //Autentica usuário e token
                            $ret = $this->authenticate($token);

                            if(!$ret->status){
                                $erro   = $ret->erro;
                                $msg    = $ret->msg;
                            }else{
                                //Array de campos a serem atualizados
                                $update = null;
                                
                                //Valida campos
                                if($nome != null && $nome != ''){
                                    $update['NOME'] = $nome;
                                }

                                if($email != null && $email != ''){
                                    $update['EMAIL'] = $email;
                                }

                                if($login != null && $login != ''){
                                    $update['LOGIN'] = $login;
                                }

                                if($senha != null && $senha != ''){
                                    $update['PASSWD'] = $senha;
                                }

                                if($idUsuario == null || $idUsuario == '' || (int)$idUsuario <= 0){
                                    $erro   = 21;
                                    $msg    = "O campo ID USUARIO é obrigatório!";
                                }else if($email != null && !Util::validaEmail($email)){
                                    $erro   = 22;
                                    $msg    = "Preencha um e-mail válido!";
                                }else if($update == null){
                                    $erro   = 23;
                                    $msg    = "Pelo menos um parâmetro opcional deve ser informado!";
                                }else{
                                    //Carrega Prefixo da escola
                                    $mdEscola   = new EscolaModel();
                                    $rsPrefixo  = $mdEscola->carregarPrefixoEscola($ret->ID_USER);

                                    //Prepara login com prefixo
                                    $login = $rsPrefixo->prefixo . "_" . strtolower(str_replace($rsPrefixo->prefixo, "", $login));
                                    
                                    //Instância do Model Usuarios
                                    $mdUsuarios = new UsuariosModel();

                                    //Valida Email
                                    if($email != null && $email != ''){
                                        $ver_email  = $mdUsuarios->validarEmailUsuarioMatriz($email, $idUsuario);
                                    }else{
                                        $ver_email = true;
                                    }

                                    if(!$ver_email){
                                        $erro   = 24;
                                        $msg    = "Este e-mail já possui cadastro!";
                                    }else{
                                        //Valida login
                                        if($email != null && $email != ''){
                                            $ver_login  = $mdUsuarios->validarLoginUsuario($login, $idUsuario);
                                        }else{
                                            $ver_login = true;
                                        }

                                        if(!$ver_login){
                                            $erro   = 25;
                                            $msg    = "Este login já existe!";
                                        }else if(!$mdUsuarios->validarUsuarioMatriz($idUsuario, $ret->ID_USER)){
                                            //Valida a dependência do cliente que vai ser alterado com a MATRIZ logada no WS
                                            $erro   = 28;
                                            $msg    = "Este cliente não pertence a sua matriz!";
                                        }else{
                                            //Se o ID_CLIENTE estiver difinido (através da autenticação HTTP) o usuário é atualizado
                                            if($ret->ID_USER > 0){
                                                $id = $mdUsuarios->atualizarUsuario($idUsuario, $ret->ID_USER, $update);
                                                
                                                if($id < 0){
                                                    $erro   = 27;
                                                    $msg    = "Falha na atualização do cliente! {$sql}";
                                                }else{
                                                    $dados  = "<dados>";
                                                    $dados .= "<id_usuario>{$idUsuario}</id_usuario>";
                                                    $dados .= "</dados>";
                                                }
                                            }else{
                                                $erro   = 26;
                                                $msg    = "ID_MATRIZ não encontrado!";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $erro   = 6;
                        $msg    = "XML de entrada fora do padrão!";
                    }
                }else{
                    $erro   = 5;
                    $msg    = "XML Params inválido ou nulo!";
                }

                //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>{$msg}</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";

                return $ret;
            }catch(Exception $e){
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>1</erro>";
                $ret .= "<msg>".$e->getMessage()."</msg>";
                $ret .= "</status>";
                $ret .= "</root>";

                return $ret;
            }
        }
        
        /**
         * Função que bloqueia o acesso de um usuário da escola logada no WS
         * 
         * @param string $xmlParams String com o XML dos campos de entrada
         * @return string $xmlResult
         */
        public function bloqueiaUsuario($xmlParams){
             try{
                 $erro   = 0;
                 $msg    = "Usuário bloqueado com sucesso!";
                 $dados  = "";

                 if($xmlParams){
                     $xml = new SimpleXMLElement($xmlParams);

                     if($xml){
                         //Array de parâmetros
                         $params = $xml->params->param;

                         //Campos utilizados
                         $token      = $this->getXmlField($params, 'token');
                         $idUsuario  = $this->getXmlField($params, 'id_usuario');

                         if(!isset($token) || $token == null || $token == ""){
                             $erro   = 4;
                             $msg    = "Token inválido!";
                         }else{
                             //Autentica usuário e token
                             $ret = $this->authenticate($token);
                             
                             if(!$ret->status){
                                 $erro   = $ret->erro;
                                 $msg    = $ret->msg;
                             }else{
                                 //Valida envio do id_usuario
                                 if($idUsuario <= 0 || $idUsuario == $ret->ID_USER){
                                     if($idUsuario <= 0){
                                         $erro   = 71;
                                         $msg    = "ID_USUARIO inválido ou nulo!";
                                     }else{
                                         $erro   = 72;
                                         $msg    = "Sem permissão para bloquear seu próprio usuário!";
                                     }
                                 }else{
                                     //Model de Usuários
                                     $mdUsuarios = new UsuariosModel();
                                     
                                     //Valida se o usuário é dependente do usuário logado
                                     if(!$mdUsuarios->validarUsuarioMatriz($idUsuario, $ret->ID_USER)){
                                         $erro   = 73;
                                         $msg    = "Usuário não é dependente de sua matriz!";
                                     }else{
                                         $rs = $mdUsuarios->atualizarStatusUsuario($idUsuario, $ret->ID_USER, 'BLOQ');

                                         //VErifica se o status foi atualizado
                                         if(!$rs->status){
                                             $erro   = 74;
                                             $msg    = $rs->msg;
                                         }
                                     } //Verifica dependencia do usuário
                                 } //Valida envio de id_usuario
                             }
                         }
                     }else{
                         $erro   = 6;
                         $msg    = "XML de entrada fora do padrão!";
                     }
                 }else{
                     $erro   = 5;
                     $msg    = "XML Params inválido ou nulo!";
                 }

                 //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
                 $ret = "<root>";
                 $ret .= "<status>";
                 $ret .= "<erro>{$erro}</erro>";
                 $ret .= "<msg>" . $msg . "</msg>";
                 $ret .= "</status>";
                 $ret .= $dados;
                 $ret .= "</root>";

                 return $ret;
             }catch(Exception $e){
                 $ret = "<root>";
                 $ret .= "<status>";
                 $ret .= "<erro>1</erro>";
                 $ret .= "<msg>". $e->getMessage() ."</msg>";
                 $ret .= "</status>";
                 $ret .= "</root>";

                 return $ret;
             }
        }
    
        /**
         * Função que desbloqueia o acesso de um usuário da escola logada no WS
         * 
         * @param string $xmlParams String com o XML dos campos de entrada
         * @return string $xmlResult
         */
        public function desbloqueiaUsuario($xmlParams){
            try{
                $erro   = 0;
                $msg    = "Usuário desbloqueado com sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token      = $this->getXmlField($params, 'token');
                        $idUsuario  = $this->getXmlField($params, 'id_usuario');

                        if(!isset($token) || $token == null || $token == ""){
                            $erro   = 4;
                            $msg    = "Token inválido!";
                        }else{
                            //Autentica usuário e token
                            $ret = $this->authenticate($token);

                            if(!$ret->status){
                                $erro   = $ret->erro;
                                $msg    = $ret->msg;
                            }else{
                                //Valida envio do id_usuario
                                if($idUsuario <= 0 || $idUsuario == $ret->ID_USER){
                                    if($idUsuario <= 0){
                                        $erro   = 81;
                                        $msg    = "ID_USUARIO inválido ou nulo!";
                                    }else{
                                        $erro   = 82;
                                        $msg    = "Sem permissão para desbloquear seu próprio usuário!";
                                    }
                                }else{
                                    //Model de Usuários
                                    $mdUsuarios = new UsuariosModel();

                                    //Valida se o usuário é dependente do usuário logado
                                    if(!$mdUsuarios->validarUsuarioMatriz($idUsuario, $ret->ID_USER)){
                                        $erro   = 83;
                                        $msg    = "Usuário não é dependente de sua matriz!";
                                    }else{
                                        $rs = $mdUsuarios->atualizarStatusUsuario($idUsuario, $ret->ID_USER, 'DESBLOQ');

                                        //VErifica se o status foi atualizado
                                        if(!$rs->status){
                                            $erro   = 84;
                                            $msg    = $rs->msg;
                                        }
                                    } //Verifica dependencia do usuário
                                } //Valida envio de id_usuario
                            }
                        }
                    }else{
                        $erro   = 6;
                        $msg    = "XML de entrada fora do padrão!";
                    }
                }else{
                    $erro   = 5;
                    $msg    = "XML Params inválido ou nulo!";
                }

                //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>". $msg ."</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";

                return $ret;
            }catch(Exception $e){
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>1</erro>";
                $ret .= "<msg>". utf8_encode($e->getMessage()) ."</msg>";
                $ret .= "</status>";
                $ret .= "</root>";

                return $ret;
            }
        }
        
        /**
         * Função que lista os usuários do cliente logado ao Webservice utilizando
         * os filtros enviados
         *  
         * @param string $xmlParams XML de parâmetros
         * @return string
         */
        public function listaUsuarios($xmlParams){
            try{
                $erro   = 0;
                $msg    = "Consulta efetuada com sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token      = $this->getXmlField($params, 'token');
                        $filtro     = $this->getXmlField($params, 'filtro');
                        $dataIni    = $this->getXmlField($params, 'dataIni');
                        $dataFim    = $this->getXmlField($params, 'dataFim');

                        if(!isset($token) || $token == null || $token == ""){
                            $erro   = 4;
                            $msg    = "Token inválido!";
                        }else{
                            //Autentica usuário e token
                            $ret = $this->authenticate($token);

                            if(!$ret->status){
                                $erro   = $ret->erro;
                                $msg    = $ret->msg;
                            }else{
                                //Valida se foi enviado algum filtro
                                $where      = "";
                                $ver_filtro = false;

                                if($filtro != null){
                                    preg_match("/id_usuario=([0-9]?[0-9]?[0-9]?[0-9]?[0-9]?[0-9]?[0-9]?[0-9]?[0-9]?[0-9])/", $filtro, $flt_id_usuario);
                                    preg_match("/nomeLIKE(.*?)&/", $filtro, $flt_nome); if(sizeof($flt_nome) == 0){ preg_match("/nomeLIKE(.*)$/", $filtro, $flt_nome); }
                                    preg_match("/emailLIKE(.*?)&/", $filtro, $flt_email); if(sizeof($flt_email) == 0){ preg_match("/emailLIKE(.*)$/", $filtro, $flt_email); }

                                    if(sizeof($flt_id_usuario) > 0){
                                        $where .= " AND U.ID_USER = " . ((int)$flt_id_usuario[1]) . " ";
                                    }

                                    if(sizeof($flt_nome) > 0){
                                        $nome   = str_replace("&", "", $flt_nome[1]);
                                        $where  .= " AND U.NOME LIKE '%" . (mysql_escape_string($nome)) . "%' ";
                                    }

                                    if(sizeof($flt_email) > 0){
                                        $email  = str_replace("&", "", $flt_email[1]);
                                        $where  .= " AND U.EMAIL LIKE '%" . (mysql_escape_string($email)) . "%' ";
                                    }

                                    $ver_filtro = true;
                                }

                                if($dataIni != null){
                                    $where  .= " AND DATE(U.DATA_REGISTRO) >= '" . (mysql_escape_string($dataIni)) . "' ";
                                    $ver_filtro = true;
                                }

                                if($dataFim != null){
                                    $where  .= " AND DATE(U.DATA_REGISTRO) <= '" . (mysql_escape_string($dataFim)) . "' ";
                                    $ver_filtro = true;
                                }

                                //Caso não exista nenhum filtro
                                if(!$ver_filtro){
                                    $erro   = 31;
                                    $msg    = "Nenhum campo de filtro foi preenchido!";
                                }else{
                                    //Model Usuários
                                    $mdUsuarios = new UsuariosModel();
                                    $rs         = $mdUsuarios->listarUsuarios($ret->ID_USER, $where);
                                    
                                    //Verifica se houve retorno 
                                    if(!$rs->status){
                                        $erro   = 32;
                                        $msg    = $rs->msg;
                                    }else{
                                        $dados = "<dados>";

                                        foreach($rs->usuarios as $usuario){
                                            //Carrega saldo do Usuário
                                            $userTmp    = new Usuario();
                                            $userTmp->carregarUsuarioId($usuario['ID_USER']);
                                            $rsSaldo    = $userTmp->calcSaldo();

                                            if($usuario['EXCLUIDO_EM'] != NULL && $usuario['EXCLUIDO_EM'] != "0000-00-00 00:00:00"){
                                                $status = "Excluido";
                                            }else if($usuario['BLOQUEADO_EM'] != NULL && $usuario['BLOQUEADO_EM'] != "0000-00-00 00:00:00"){
                                                $status = "Bloqueado";
                                            }else{
                                                $status = "Ativo";
                                            }
                                            
                                            $dados .= "<usuario>";
                                                $dados .= "<id_usuario>";
                                                    $dados .= $usuario['ID_USER'];
                                                $dados .= "</id_usuario>";
                                                $dados .= "<nome>";
                                                    $dados .= utf8_encode($usuario['NOME']);
                                                $dados .= "</nome>";
                                                $dados .= "<email>";
                                                    $dados .= utf8_encode($usuario['EMAIL']);
                                                $dados .= "</email>";
                                                $dados .= "<login>";
                                                    $dados .= utf8_encode($usuario['LOGIN']);
                                                $dados .= "</login>";
                                                $dados .= "<status>";
                                                    $dados .= utf8_encode($status);
                                                $dados .= "</status>";
                                                $dados .= "<dataRegistro>";
                                                    $dados .= Util::formataData($usuario['DATA_REGISTRO'], 'DD/MM/AAAA HH:MM:SS');
                                                $dados .= "</dataRegistro>";
                                                $dados .= "<saldo>";
                                                    $dados .= $rsSaldo->saldo;
                                                $dados .= "</saldo>";
                                                $dados .= "<consumo>";
                                                    $dados .= (int)$usuario['CONSUMO'];
                                                $dados .= "</consumo>";
                                            $dados .= "</usuario>";
                                        }
                                        $dados .= "</dados>";
                                    } //Verificação do record set de clientes
                                } //Verifica filtro
                            }
                        }
                    }else{
                        $erro   = 6;
                        $msg    = "XML de entrada fora do padrão!";
                    }
                }else{
                    $erro   = 5;
                    $msg    = "XML Params inválido ou nulo!";
                }

                //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>". $msg."</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";

                return $ret;
            }catch(Exception $e){
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>1</erro>";
                $ret .= "<msg>". $e->getMessage() ."</msg>";
                $ret .= "</status>";
                $ret .= "</root>";

                return $ret;
            }
        }
        
        /**
        * Função que cadastra novos clientes através de um cliente logado
        * Por isso, o novo cliente será atrelado ao cliente logado através do ID_MATRIZ
        * 
        * @param string $xmlParams String XML com campos de entrada
        * @return string
        */
        public function novoUsuario($xmlParams){
            try{
                $erro   = 0;
                $msg    = "Consulta efetuada com sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token  = $this->getXmlField($params, 'token');
                        $nome   = $this->getXmlField($params, 'nome');
                        $email  = $this->getXmlField($params, 'email');
                        $login  = $this->getXmlField($params, 'login');
                        $senha  = $this->getXmlField($params, 'passwd_md5');


                        if(!isset($token) || $token == null || $token == ""){
                            $erro   = 4;
                            $msg    = "Token inválido!";
                        }else{
                            //Autentica usuário e token
                            $ret = $this->authenticate($token);

                            if(!$ret->status){
                                $erro   = $ret->erro;
                                $msg    = $ret->msg;
                            }else{
                                //Valida campos
                                if($nome == null || $nome == ''){
                                    $erro   = 11;
                                    $msg    = "O campo nome é obrigatório!";
                                }else if($email == null || $email == ''){
                                    $erro   = 12;
                                    $msg    = "O campo e-mail é obrigatório!";
                                }else if(!Util::validaEmail($email)){
                                    $erro   = 13;
                                    $msg    = "Preencha um e-mail válido!";
                                }else if($login == null || $login == ''){
                                    $erro   = 14;
                                    $msg    = "O campo login é obrigatório!";
                                }else if($senha == null || $senha == ''){
                                    $erro   = 15;
                                    $msg    = "O campo senha é obrigatório!";
                                }else{
                                    //Carrega Prefixo da escola
                                    $mdEscola   = new EscolaModel();
                                    $rsPrefixo  = $mdEscola->carregarPrefixoEscola($ret->ID_USER);

                                    //Prepara login com prefixo
                                    $login = $rsPrefixo->prefixo . "_" . strtolower(str_replace($rsPrefixo->prefixo, "", $login));
                                    
                                    //Model de Usuários
                                    $mdUsuarios = new UsuariosModel();
                                    
                                    //Consulta usuário por e-mail
                                    $rs = $mdUsuarios->consultarUsuarioMatriz($email);

                                    if($rs->status){
                                        //Armazena dados do usuário
                                        $cliente = $rs->usuario;;
                                        
                                        //Se for um cliente existente da MATRIZ e estiver INATIVO, o mesmo será ativado
                                        if($ret->ID_USER == $cliente->ID_MATRIZ && ($cliente->EXCLUIDO_EM !== NULL && $cliente->EXCLUIDO_EM !== '0000-00-00 00:00:00')){
                                            //Valida login
                                            if(!$mdUsuarios->validarLoginUsuario($login, $cliente->ID_CLIENTE)){
                                                $erro   = 17;
                                                $msg    = "Este login já existe!";
                                            }else{
                                                $arrCampos = array(
                                                    'NOME'              => $nome,
                                                    'LOGIN'             => $login,
                                                    'PASSWD'            => $senha,
                                                    'ID_MATRIZ'         => $ret->ID_USER,
                                                    'HASH'              => Security::geraHashId(),
                                                    'EXCLUIDO_EM'       => '0000-00-00 00:00:00',
                                                    'BLOQUEADO_EM'      => '0000-00-00 00:00:00',
                                                    'ID_USER_PERFIL'    => 3
                                                );
                                                
                                                $mdUsuarios->atualizarUsuario($cliente->ID_USER, $ret->ID_USER, $arrCampos);
                                                                                                
                                                $dados  = "<dados>";
                                                $dados .= "<id_usuario>{$cliente->ID_USER}</id_usuario>";
                                                $dados .= "</dados>";
                                            }
                                        }else{
                                            $erro   = 16;
                                            $msg    = "Este e-mail já possui cadastro!";
                                        }
                                    }else{
                                        //Valida login
                                        if(!$mdUsuarios->validarLoginUsuario($login)){
                                            $erro   = 17;
                                            $msg    = "Este login já existe!";
                                        }else{
                                            //Se o ID_CLIENTE estiver difinido (através da autenticação HTTP) o usuário é cadastrado
                                            if($ret->ID_USER > 0){
                                                $arrCampos = array(
                                                    'NOME'              => $nome,
                                                    'EMAIL'             => $email,
                                                    'LOGIN'             => $login,
                                                    'PASSWD'            => $senha,
                                                    'ID_MATRIZ'         => $ret->ID_USER,
                                                    'HASH'              => Security::geraHashId(),
                                                    'ID_USER_PERFIL'    => 3,
                                                    'DATA_REGISTRO'     => date('Y-m-d H:i:s')
                                                );
                                                
                                                $id = $mdUsuarios->cadastrarUsuario($ret->ID_USER, $arrCampos);

                                                if($id <= 0){
                                                    $erro   = 19;
                                                    $msg    = "Falha na inserção do cliente!";
                                                }else{
                                                    $dados  = "<dados>";
                                                    $dados .= "<id_usuario>{$id}</id_usuario>";
                                                    $dados .= "</dados>";
                                                }
                                            }else{
                                                $erro   = 18;
                                                $msg    = "ID_MATRIZ não encontrado!";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $erro   = 6;
                        $msg    = "XML de entrada fora do padrão!";
                    }
                }else{
                    $erro   = 5;
                    $msg    = "XML Params inválido ou nulo!";
                }

                //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>" . $msg . "</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";

                return $ret;
            }catch(Exception $e){
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>1</erro>";
                $ret .= "<msg>". $e->getMessage() ."</msg>";
                $ret .= "</status>";
                $ret .= "</root>";

                return $ret;

                //return new soap_fault("Server", null, $e->getMessage());
            }
        }
        
        
    }
?>
