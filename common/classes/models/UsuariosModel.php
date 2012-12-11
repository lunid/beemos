<?php
    namespace common\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \common\db_tables as TB;
    
    class UsuariosModel extends Model{
        /**
         * Verifica se o usuário enviado pertence a matriz utilizada pelo WS
         * 
         * @param int $idUsuario ID do usuário
         * @param int $idCliente ID da matriz (Cliente) logado no WS
         * 
         * @return boolean
         * @throws Exception
         */
        public function validarUsuarioMatriz($idUsuario, $idCliente){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                $tbCliente->setLimit(1); //Define LIMIT 1
                
                //Consulta usuário
                $rs = $tbCliente->findAll("ID_MATRIZ = " . (int)$idCliente . " AND ID_CLIENTE = " . (int)$idUsuario);
                
                //Se houver retorno, retorno TRUE
                if($rs->count() > 0){
                    return true;
                }else{
                    return false;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função interna para atualização dos Status de um usuário
         * 
         * @param int $idCliente
         * @param int $idMatriz 
         * @param string $status (BLOQ, DESBLOQ e EXCLUIR)
         * 
         * @return \stdClass
         */
        function atualizarStatusUsuario($idCliente, $idMatriz, $status){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao atualizar usuário";
                
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                
                //Define Campo do Update
                switch(strtoupper($status)){
                    case 'BLOQ':
                        $tbCliente->BLOQ = 1;
                        break;
                    case 'DESBLOQ':
                        $tbCliente->BLOQ = 0;
                        break;
                    case 'EXCLUIR':
                        $tbCliente->DEL  = 1;
                        break;
                    default :
                        $ret->msg = "Status não identificado!";
                        return $ret;
                        break;
                }

                //Executa UPDATE
                $tbCliente->update(array("ID_CLIENTE = %i AND ID_MATRIZ = %i", $idCliente, $idMatriz));
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Usuário atualizado com sucesso!";

                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Valida a existência de um e-mail em outra Matriz
         * 
         * @param string $email E-mail a ser validado
         * @param int $idUsuario ID do usuário (para ser retirado do select)
         * 
         * @return boolean
         * @throws Exception
         */
        public function validarEmailUsuarioMatriz($email, $idUsuario){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                $tbCliente->setLimit(1); //Define LIMIT 1
                
                //Consulta usuário
                $rs = $tbCliente->findAll("EMAIL = '{$email}' AND ID_CLIENTE != {$idUsuario} AND ID_MATRIZ > 0");
                
                //Se não houver retorno, retorno TRUE
                if($rs->count() <= 0){
                    return true;
                }else{
                    return false;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Valida a existência de um LOGIN na tabela de Clientes
         * 
         * @param type $login Login a ser verificado
         * @param type $idUsuario ID do usuário (para ser retirado do select)
         * 
         * @return boolean
         * @throws Exception
         */
        public function validarLoginUsuario($login, $idUsuario = 0){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                $tbCliente->setLimit(1); //Define LIMIT 1
                
                //Consulta usuário
                $rs = $tbCliente->findAll("LOGIN = '{$login}' ".($idUsuario > 0 ? "AND ID_CLIENTE != {$idUsuario}" : ""));
                
                //Se não houver retorno, retorno TRUE
                if($rs->count() <= 0){
                    return true;
                }else{
                    return false;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Atualiza os dados de um usuário
         * 
         * @param int $idUsuario ID do usuário que será atualizado
         * @param int $idMatriz ID da Matriz logada no WS (Cliente)
         * @param array $arrCampos Array com os campos para atualização
         * <br />
         * <code>
         * Ex: $arrCampos['NOME_PRINCIPAL'] = 'Cláudio Rubens';
         * </code>
         * 
         * @return int
         * @throws Exception
         */
        public function atualizarUsuario($idUsuario, $idMatriz, $arrCampos = array()){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                
                //Implementa campos a serem alterados
                foreach($arrCampos as $campo => $valor){
                    $tbCliente->$campo = $valor;
                }
                
                //Consulta usuário
                return $tbCliente->update(array("ID_CLIENTE = %i AND ID_MATRIZ = %i", $idUsuario, $idMatriz));
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Cadastra um novo usuário para a matriz logada no WS
         * 
         * @param int $idMatriz ID da Matriz logada no WS (Cliente)
         * @param array $arrCampos Array com os campos para inserção
         * <br />
         * <code>
         * Ex: $arrCampos['NOME_PRINCIPAL'] = 'Cláudio Rubens';
         * </code>
         * 
         * @return int
         * @throws Exception
         */
        public function cadastrarUsuario($idMatriz, $arrCampos = array()){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                
                //Implementa campos a serem alterados
                foreach($arrCampos as $campo => $valor){
                    $tbCliente->$campo = $valor;
                }
                
                //Define matriz
                $tbCliente->ID_MATRIZ = $idMatriz;
                
                //Consulta usuário
                return $tbCliente->save();
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Lista os usuários de uma determinada Matriz usando WHERE
         * 
         * @param int $idMatriz ID da Matriz logada no WS (Cliente)
         * @param string $where String com a cláusula WHERE. Ex: ID_CLIENTE = 9
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->usuarios  - Array com usuários encontrados                    <br />
         * </code>
         * @throws \api\classes\models\Exception
         */
        public function listarUsuarios($idMatriz, $where){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar clientes!";
                
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                
                //SQL
                $sql = "SELECT
                            C.ID_CLIENTE,
                            C.NOME_PRINCIPAL,
                            C.EMAIL,
                            C.LOGIN,
                            C.DATA_REGISTRO,
                            C.BLOQ,
                            C.DEL,
                            (SELECT SUM(DEBITO) FROM SPRO_HISTORICO_GERADOC WHERE ID_LOGIN = C.ID_CLIENTE) AS CONSUMO
                        FROM
                            SPRO_CLIENTE C
                        WHERE
                            C.ID_MATRIZ = {$idMatriz}
                        $where
                        ORDER BY
                            C.NOME_PRINCIPAL
                        ;";
                
                $rs = $tbCliente->query($sql);
                
                if(is_array($rs) && sizeof($rs) > 0){
                    $ret->status    = true;
                    $ret->msg       = "Usuários listados com sucesso!";
                    $ret->usuarios  = $rs;
                }else{
                    $ret->msg = "Nenhum usuário encontrado!";
                }
                
                //Retorno
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Calcula o saldo de um determinado usuário no banco de dados
         * - Se escola, é calculado o saldo baseado em seus dependentes
         * - Se professor é calculado baseado em suas operações no sistema
         * 
         * @param int $idCliente
         * @param int $idMatriz
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  itn     $ret->erro      - Código de erro                                    <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  int     $ret->saldo     - Cálculo final de saldo do cliente                <br />
         * </code>
         * @throws Exception
         */
        function calcularSaldo($idCliente, $idMatriz = 0){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao calcular o saldo do cliente!";
                $ret->erro      = 41;
                $ret->saldo     = 0;
                
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente($idCliente);
                
                if($tbCliente->ID_CLIENTE <= 0){
                    $ret->erro  = 41;
                    $ret->msg   = "Cliente não encontrado!";

                    return $ret;
                }

                //Valida o acesso aos dados do cliente
                $ver_cliente = false;

                //Se o cliente for o próprio usuário encontrado OK ou
                //Verifica se o cliente é da matriz que está sendo pesquisada
                if($tbCliente->ID_CLIENTE == $idCliente || $tbCliente->ID_MATRIZ == $idMatriz){
                    $ver_cliente = true;
                }

                //Caso não passe em nenhuma das validações é retornado um erro.
                if(!$ver_cliente){
                    $ret->erro  = 42;
                    $ret->msg   = "Você não possui permissão para acessar o saldo deste cliente!";

                    return $ret;
                }
                
                //Table SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                
                //Seleciona ultimo saldo da escola
                $tbCredito->setOrderBy("DATA_REGISTRO DESC"); //Defino Order by
                $tbCredito->setLimit(1); //Define LIMIT 1
                $rs = $tbCredito->findAll("ID_CLIENTE = {$idCliente} AND DATE(VENCIMENTO) >= DATE(NOW())");
                    
                //Se for escola
                if($tbCliente->ID_AUTH_PERFIL == 6){
                    //Caso não seja encotrado saldo é retornado um erro
                    if($rs->count() <= 0){
                        $ret->erro  = 43;
                        $ret->msg   = "Cliente não possui um saldo válido!";

                        return $ret;
                    }

                    //Transforma resultado em objeto
                    $saldo = $rs->getRs();
                    $saldo = $saldo[0];

                    /* O sistema busca todas as operações de crédito e débito
                     * feitas pelas escola após a aquisição do último crédito 
                     * da mesma.
                     */
                    $rs = $tbCredito->consultarCreditoCliente($idCliente, $saldo->DATA_REGISTRO);

                    //Caso não exista nenhuma opeção ela ainda possui o SALDO_FINAL intacto
                    if($rs->count <= 0){
                        $ret->status    = true;
                        $ret->erro      = 0;
                        $ret->msg       = "Saldo encontrado!";
                        $ret->saldo     = $saldo->SALDO_FINAL;

                        return $ret;
                    }

                    //Almarzena o saldo total da escola
                    $saldo_total = (int)$saldo->SALDO_FINAL;
                    
                    foreach($rs as $row){
                        if($row['OPERACAO'] == 'C'){
                            $saldo_total -= (int)$row['TOTAL'];
                        }else if($row['OPERACAO'] == 'D'){
                            $saldo_total += (int)$row['TOTAL'];
                        }
                    }

                    //Retorna saldo final
                    $ret->status    = true;
                    $ret->erro      = 0;
                    $ret->msg       = "Saldo encontrado!";
                    $ret->saldo     = $saldo_total;

                    return $ret;
                }else{
                    //Caso não seja encotrado saldo é retornado um erro
                    if($rs->count() <= 0){
                        $ret->erro  = 43;
                        $ret->msg   = "Cliente não possui um saldo válido!";

                        return $ret;
                    }

                    $saldoFinal = $rs->getRs();
                    $saldoFinal = $saldoFinal[0];

                    //Verifica se o cliente já utilizou algo do saldo dele
                    $tbHistorico = new TB\HistoricoGeradoc();
                    $rsHistorico = $tbHistorico->somarDebitosCliente($idCliente, $saldoFinal->DATA_REGISTRO);

                    //Se não utilizou nada é retornado saldo completo
                    if($rsHistorico->count <= 0){
                        $ret->status    = true;
                        $ret->erro      = 0;
                        $ret->msg       = "Saldo encontrado!";
                        $ret->saldo     = (int)$saldoFinal->SALDO_FINAL;

                        return $ret;
                    }else{
                        //Se já foi utilizado alguma coisa, é subtraido do total e retornado
                        $ret->status    = true;
                        $ret->erro      = 0;
                        $ret->msg       = "Saldo encontrado!";
                        $ret->saldo     = (int)$saldoFinal->SALDO_FINAL - (int)$rsHistorico['DEBITOS'];

                        return $ret;
                    }
                } //Verifica escola
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Consulta os dados de um usuário de matriz através do e-mail
         * 
         * @param string $email E-mail do usuário
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->usuario   - Objeto com o usuário encontrado                   <br />
         * </code>
         * 
         * @throws Exception
         */
        public function consultarUsuarioMatriz($email){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Usuário não encontrado";
                
                //SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                $tbCliente->setLimit(1); // Define limit 1
                $rs = $tbCliente->findAll("EMAIL = '{$email}' AND ID_MATRIZ > 0 ");
                     
                //Se o usuário não for encontrado
                if($rs->count() <= 0){
                    return $ret;
                }
                
                //Armazena Resultado
                $tmpRs = $rs->getRs();
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Usuário encontrado!";
                $ret->usuario   = $tmpRs[0];
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
