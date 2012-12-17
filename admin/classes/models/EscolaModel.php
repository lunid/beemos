<?php
    namespace admin\classes\models;
    
    use \sys\classes\security\Password;
    use \sys\classes\mvc\Model;    
    use \common\db_tables as TB;
    use \common\classes\models as MD;
    
    class EscolaModel extends Model {
        /**
         * Carrega dados do cliente que serão utilizados na Home de Escolas
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool        $ret->status        - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string      $ret->msg           - Armazena mensagem ao usuário                      <br />
         *  stdClass    $ret->cliente       - Dados do cliente logado                           <br />
         *  int         $ret->saldo         - Saldo atual do cliente                            <br />
         *  datetime    $ret->validade      - String com a validade dos créditos AAAA-MM-DD     <br />
         * </code>
         * @throws Exception
         */
        public function carregaDadosClienteHome($idCliente){
            try{
                //Obejto de Retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao consultar dados do cliente!";
                
                //Consulta dados do cliente
                $cliente = new TB\Cliente($idCliente);
                
                if($cliente->ID_CLIENTE <= 0){
                    $ret->msg = "Cliente não encontrado!";
                    return $ret;
                }
                
                //Model de usuários
                $mdUsuarios = new MD\UsuariosModel();
                
                //Consulta de Saldo
                $saldo = $mdUsuarios->calcularSaldo($idCliente);
                
                if(!$saldo->status){
                    $ret->msg = $saldo->msg;
                    return $ret;
                }
                
                //Consulta de Operação para pegar validade dos créditos
                $ultimaOperacao = $mdUsuarios->consultarUltimaOperacaoCliente($idCliente);
                
                if(!$ultimaOperacao->status){
                    $ret->msg = $ultimaOperacao->msg;
                    return $ret;
                }
                
                //Retrono OK
                $ret->status        = true;
                $ret->msg           = "Dados carregados com sucesso!";
                $ret->cliente       = $cliente;
                $ret->saldo         = $saldo->saldo;
                $ret->validade      = $ultimaOperacao->operacao->VENCIMENTO;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Lista os usuários de uma determinada Escola usando WHERE
         * 
         * @param int $idMatriz ID da Escola logada
         * @param string $where String com a cláusula WHERE. Ex: ID_CLIENTE = 9
         * @param array $arrPg Array com parâmetros para Ordenação e Paginação
         * <code>
         * array(
         *   "campoOrdenacao"    => 'DATA_REGISTRO', 
         *   "tipoOrdenacao"     => 'DESC', 
         *   "inicio"            => 1, 
         *   "limite"            => 10
         * )
         * </code>
         *  
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->usuarios  - Array com usuários encontrados                    <br />
         * </code>
         * @throws Exception
         */
        public function carregarUsuariosEscola($idMatriz, $where = '', $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao cunsultar usuários!";
                
                //Valida ID
                if((int)$idMatriz <= 0){
                    $ret->msg = "ID da Escola não definido ou nulo!";
                    return $ret;
                }
                
                //Model de usuários
                $mdUsuarios = new MD\UsuariosModel();
                
                //Consulta usuários da Escola
                return $mdUsuarios->listarUsuarios($idMatriz, $where, $arrPg);
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Bloqueia ou Desbloqueia um usuário da escola
         * 
         * @param int $idMatriz ID da Escola (Cliente)
         * @param int $idCliente ID do Usuário (Cliente)
         * @param int $status 0 - Desbloqueado 1 - Bloqueado
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * @throws Exception
         */
        public function alterarBloqueioUsuario($idMatriz, $idCliente, $status){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar bloqueio do usuário!";
                
                //Validações
                if((int)$idMatriz <= 0){
                    $ret->msg = "ID Matriz inválido ou nulo!";
                    return $ret;
                }
                
                if((int)$idCliente <= 0){
                    $ret->msg = "ID Cliente inválido ou nulo!";
                    return $ret;
                }
                
                //Tabela de clientes
                $tbCliente = new TB\Cliente();
                
                //Ewxwcuta UPDATE
                $tbCliente->query("UPDATE SPRO_CLIENTE SET BLOQ = " . ((int)$status) . " WHERE ID_CLIENTE IN ({$idCliente}) AND ID_MATRIZ = " . ((int)$idMatriz));
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Bloqueio atualizado com sucesso!";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        
        /**
         * Cria uma senha temporária para o usuário
         * 
         * @param int $idMatriz ID da Escola (Cliente)
         * @param int $idCliente ID do Usuário (Cliente)
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  string  $ret->senha     - Senha gerada                                      <br />
         * </code>
         * @throws Exception
         */
        public function criarSenhaTmp($idMatriz = 0, $idCliente = 0){
            try{
                //Gera senha
                $senha = Password::newPassword();
                
                //Se não forem enviados dados, apenas retorna a senha
                if((int)$idCliente <= 0 && (int)$idCliente <= 0){
                    return $senha;
                }
                
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao criar senhas temporárias!";
                
                //Validações
                if((int)$idMatriz <= 0){
                    $ret->msg = "ID Matriz inválido ou nulo!";
                    return $ret;
                }
                
                if((int)$idCliente <= 0){
                    $ret->msg = "ID Cliente inválido ou nulo!";
                    return $ret;
                }
                
                if($senha == ''){
                    $ret->msg = "Falha ao gerar senha!";
                    return $ret;
                }
                
                //Tabela de clientes
                $tbCliente = new TB\Cliente($idCliente);
                
                //Ewxwcuta UPDATE
                $tbCliente->query("UPDATE SPRO_CLIENTE SET PASSWD_TMP = '" . ($senha) . "' WHERE ID_CLIENTE IN ({$idCliente}) AND ID_MATRIZ = " . ((int)$idMatriz));
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Senha criada com sucesso!";
                $ret->senha     = $senha;
                $ret->cliente   = $tbCliente;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Salva novo usuário de uma escola
         * 
         * @param int $idMatriz ID da Escola
         * @param array $arrDados Array com dados para cadastro
         * <code>
         * <br />
         * $arrDados['NOME_PRINCIPAL']  = 'Interbits Informática';  <br />
         * $arrDados['APELIDO']         = 'Interbits';              <br />
         * $arrDados['EMAIL']           = 'intertis@sistema.com';   <br />
         * </code>
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * @throws Exception
         */
        public function salvarUsuario($idMatriz, $arrDados){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar usuários!";
                
                //Validações
                if((int)$idMatriz <= 0){
                    $ret->msg = "ID Matriz inválido ou nulo!";
                    return $ret;
                }
                
                if(!is_array($arrDados)){
                    $ret->msg = "Array de Dados não é um vetor!";
                    return $ret;
                }
                
                //Tabela de clientes
                $tbCliente = new TB\Cliente();
                
                //Atribui campos e valores para inserção ou update
                foreach($arrDados as $campo => $valor){
                    $tbCliente->$campo = $valor;
                }
                
                //Define Matriz
                $tbCliente->ID_MATRIZ = $idMatriz;
                
                //Valida e-mail
                $mdUsusarios = new MD\UsuariosModel();
                
                if(!$mdUsusarios->validarEmailUsuarioMatriz($arrDados['EMAIL'], $arrDados['ID_CLIENTE'])){
                    $ret->msg = "Esse e-mail já possui cadastro!";
                    return $ret;
                }
                
                if(!$mdUsusarios->validarLoginUsuario($arrDados['LOGIN'], $arrDados['ID_CLIENTE'])){
                    $ret->msg = "Esse login já existe!";
                    return $ret;
                }
                    
                //Verifica se é um usuário novo ou não
                if($arrDados['ID_CLIENTE'] <= 0){
                    //Novo usuário
                    $tbCliente->DATA_REGISTRO = date("Y-m-d H:i:s");
                    
                    $tbCliente->save();
                }else{
                    //Usuário existente
                    $tbCliente->update(array("ID_CLIENTE = %i AND ID_MATRIZ = %i", (int)$arrDados['ID_CLIENTE'], $idMatriz));
                }
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Usuário salvo com sucesso!";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Carregar Cargos e Funções de uma Escola
         * 
         * @param int $idCliente ID da Escola (Cliente)
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->funcoes   - Array com os dados encontrados no banco de dados  <br />
         * </code>
         * @throws Exception
         */
        public function carregarFuncoesEscola($idCliente){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar Cargos / Funções!";
                
                //Table APRO_AUTH_FUNCAO
                $tbAuthFuncao = new TB\AuthFuncao();
                
                //Consulta funções da escola
                $rs = $tbAuthFuncao->findAll("ID_CLIENTE = {$idCliente}");
                
                //Verifica retorno
                if($rs->count() <= 0){
                    $ret->msg = "Nenhuma Função encontrada!";
                    return $ret;
                }
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Funções carregadas com sucesso!";
                $ret->funcoes   = $rs->getRs();
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Carrega dados de um determinado usuário no sistema
         * 
         * @param type $idCliente ID do Usuário (Cliente)
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->usuario   - Objeto com os dados carregado do Usuário          <br />
         * </code>
         * @throws Exception
         */
        public function carregarDadosUsuario($idCliente){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar dados do Usuário!";
                
                //Table APRO_AUTH_FUNCAO
                $tbCliente = new TB\Cliente($idCliente);
                
                //Verifica retorno
                if($tbCliente->ID_CLIENTE <= 0){
                    $ret->msg = "Nenhum Usuário encontrado!";
                    return $ret;
                }
                
                //Objeto para dados de retorno
                $objUsuario                 = new \stdClass();
                $objUsuario->ID_CLIENTE     = $tbCliente->ID_CLIENTE;
                $objUsuario->NOME_PRINCIPAL = $tbCliente->NOME_PRINCIPAL;
                $objUsuario->APELIDO        = $tbCliente->APELIDO;
                $objUsuario->EMAIL          = $tbCliente->EMAIL;
                $objUsuario->LOGIN          = $tbCliente->LOGIN;
                $objUsuario->ID_AUTH_FUNCAO = $tbCliente->ID_AUTH_FUNCAO;
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Usuário carregado com sucesso!";
                $ret->usuario   = $objUsuario;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Exclui um ou mais usuários de uma escola
         * 
         * @param int $idMatriz ID da Escola (Cliente)
         * @param int $idCliente ID do Usuário (Cliente) ou string com vários IDs ex: 23,44,67 
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * @throws \admin\classes\models\Exception
         */
        public function excluirUsuario($idMatriz, $idCliente){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao excluir usuário!";
                
                //Validações
                if((int)$idMatriz <= 0){
                    $ret->msg = "ID Matriz inválido ou nulo!";
                    return $ret;
                }
                
                if((int)$idCliente <= 0){
                    $ret->msg = "ID Cliente inválido ou nulo!";
                    return $ret;
                }
                
                //Tabela de clientes
                $tbCliente = new TB\Cliente();
                
                //Ewxwcuta UPDATE
                $tbCliente->query("UPDATE SPRO_CLIENTE SET DEL = 1 WHERE ID_CLIENTE IN ({$idCliente}) AND ID_MATRIZ = " . ((int)$idMatriz));
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Usuário excluido com sucesso!";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Carrega Cargos/Funções de uma escola
         * 
         * @param int $idMatriz ID da Escola (Cliente)
         * @param string $where String com a cláusula WHERE. Ex: ID_CLIENTE = 9
         * @param array $arrPg Array com parâmetros para Ordenação e Paginação
         * <code>
         * array(
         *   "campoOrdenacao"    => 'FUNCAO', 
         *   "tipoOrdenacao"     => 'DESC', 
         *   "inicio"            => 1, 
         *   "limite"            => 10
         * )
         * </code>
         *  
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->cargos    - Array com cargos encontrados                      <br />
         * </code>
         * @throws Exception
         */
        public function carregarCargosFuncoes($idMatriz, $where = '', $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao consultar Cargos e Funções!";
                
                //Valida ID
                if((int)$idMatriz <= 0){
                    $ret->msg = "ID da Escola não definido ou nulo!";
                    return $ret;
                }
                
                //Tabela Funções
                $tbFuncoes = new TB\AuthFuncao();
                
                //Paginação e Ordenação
                $order = "";
                $limit = "";                
                if($arrPg != null){
                    //Monta ordenação
                    if(isset($arrPg['campoOrdenacao']) && isset($arrPg['tipoOrdenacao'])){
                        $tbFuncoes->setOrderBy($arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao']);
                    }
                    
                    //Monta paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                        $tbFuncoes->setLimit($arrPg['limite'], $arrPg['inicio']);
                    }
                }else{
                    $tbFuncoes->setOrderBy("FUNCAO");
                }
                
                //Efetua consulta
                $rs = $tbFuncoes->findAll("ID_CLIENTE = {$idMatriz} {$where}");
                
                if($rs->count() > 0){
                    $ret->status    = true;
                    $ret->msg       = "Usuários listados com sucesso!";
                    $ret->cargos    = $rs;
                }else{
                    $ret->msg = "Nenhum Cargo/Função encontrado!";
                }
                
                //Consulta usuários da Escola
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Salva novo Cargo/Função de uma escola
         * 
         * @param int $idMatriz ID da Escola
         * @param array $arrDados Array com dados para cadastro
         * <code>
         * <br />
         * $arrDados['NOME_PRINCIPAL']  = 'Interbits Informática';  <br />
         * $arrDados['APELIDO']         = 'Interbits';              <br />
         * $arrDados['EMAIL']           = 'intertis@sistema.com';   <br />
         * </code>
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * @throws Exception
         */
        public function salvarCargoEscola($idMatriz, $arrDados = array()){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar Cargo!";
                
                //Validações
                if($idMatriz <= 0){
                    $ret->msg = "ID matriz é inválido ou nulo!";
                    return $ret;
                }
                
                if(sizeof($arrDados) <= 0){
                    $ret->msg = "Dados de cadatsro não enviados!";
                    return $ret;
                }
                
                if(!isset($arrDados['FUNCAO'])){
                    $ret->msg = "Nome do Cargo/Função não definido!";
                    return $ret;
                }
                
                if(!isset($arrDados['CODIGO'])){
                    $ret->msg = "Código do Cargo não definido!";
                    return $ret;
                }
                
                if(!isset($arrDados['LIM_CREDITO'])){
                    $ret->msg = "Limite de Créditos não definido!";
                    return $ret;
                }
                
                if((int)$arrDados['LIM_CREDITO'] <= 0){
                    $ret->msg = "Limite de Créditos deve ser maior que zero!";
                    return $ret;
                }
                
                //Tabela de Funções
                $tbFuncoes = new TB\AuthFuncao();
                
                //Valida nome do cargo
                $rs = $tbFuncoes->findAll("FUNCAO = '".trim($arrDados['FUNCAO'])."' AND ID_CLIENTE = {$idMatriz} " . (isset($arrDados['ID_AUTH_FUNCAO']) ? " AND ID_AUTH_FUNCAO != " . $arrDados['ID_AUTH_FUNCAO'] : ""));
                
                if($rs->count() > 0){
                    $ret->msg = "Nome do Cargo/Função já existe!";
                    return $ret;
                }
                
                //Valida código do cargo
                $rs = $tbFuncoes->findAll("CODIGO = '".trim($arrDados['CODIGO'])."' AND ID_CLIENTE = {$idMatriz} " . (isset($arrDados['ID_AUTH_FUNCAO']) ? " AND ID_AUTH_FUNCAO != " . $arrDados['ID_AUTH_FUNCAO'] : ""));
                
                if($rs->count() > 0){
                    $ret->msg = "Esse Código de Cargo já existe!";
                    return $ret;
                }
                
                //Monta dados para o INSERT / UPDATE
                foreach($arrDados as $campo => $valor){
                    $tbFuncoes->$campo = $valor;
                }
                $tbFuncoes->ID_CLIENTE = $idMatriz;
                
                //Verifica qual a ação
                if(isset($arrDados['ID_AUTH_FUNCAO']) && $arrDados['ID_AUTH_FUNCAO'] > 0){
                    $tbFuncoes->update(array("ID_AUTH_FUNCAO = %i", $arrDados['ID_AUTH_FUNCAO']));
                }else{
                    $tbFuncoes->save();
                }
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Cargo/Função salvo com sucesso!";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Carrega dados de um determinado cargo/função no sistema
         * 
         * @param type $idCargo ID do Cargo/Função
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->cargo     - Objeto com os dados carregado do Cargo/Função     <br />
         * </code>
         * @throws Exception
         */
        public function carregarCargo($idCargo){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar dados do Cargo/Função!";
                
                //Table SPRO_AUTH_FUNCAO
                $tbCargo = new TB\AuthFuncao($idCargo);
                
                //Verifica retorno
                if($tbCargo->ID_AUTH_FUNCAO <= 0){
                    $ret->msg = "Nenhum Cargo/Função encontrado!";
                    return $ret;
                }
                
                //Objeto para dados de retorno
                $objCargo                   = new \stdClass();
                $objCargo->ID_AUTH_FUNCAO   = $tbCargo->ID_AUTH_FUNCAO;
                $objCargo->FUNCAO           = $tbCargo->FUNCAO;
                $objCargo->CODIGO           = $tbCargo->CODIGO;
                $objCargo->LIM_CREDITO      = $tbCargo->LIM_CREDITO;
                $objCargo->RECARGA_AUTO     = $tbCargo->RECARGA_AUTO;
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Cargo/Função carregado com sucesso!";
                $ret->cargo     = $objCargo;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Bloqueia ou Desbloqueia recarga automática de um carga doa escola
         * 
         * @param int $idMatriz ID da Escola (Cliente)
         * @param int $idCargo ID do Cargo/Função
         * @param int $status 0 - Desbloqueado 1 - Bloqueado
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * @throws Exception
         */
        public function alterarRecargaAutoCargo($idMatriz, $idCargo, $status){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar Recarga Automática do Cargo/Função!";
                
                //Validações
                if((int)$idMatriz <= 0){
                    $ret->msg = "ID Matriz inválido ou nulo!";
                    return $ret;
                }
                
                if((int)$idCargo <= 0){
                    $ret->msg = "ID Cargo/Função inválido ou nulo!";
                    return $ret;
                }
                
                //Tabela de Cargos/Funções
                $tbCargo = new TB\AuthFuncao($idCargo);
                
                //Executa UPDATE
                $tbCargo->RECARGA_AUTO = $status;
                
                $tbCargo->update(array("ID_CLIENTE = %i AND ID_AUTH_FUNCAO = %i", (int)$idMatriz, (int)$idCargo));
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Recarga Automática atualizada com sucesso!";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
