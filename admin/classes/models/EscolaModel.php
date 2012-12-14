<?php
    namespace admin\classes\models;
    
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
         * @throws \admin\classes\models\Exception
         */
        public function alterarBloqueioUsuario($idMatriz, $idCliente, $status){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar blqoueio do usuário!";
                
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
    }
?>
