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
         * @throws \api\classes\models\Exception
         */
        public function listarUsuarios($idMatriz, $where = '', $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar clientes!";
                
                //Paginação e Ordenação
                $order = "";
                $limit = "";                
                if($arrPg != null){
                    //Monta ordenação
                    if(isset($arrPg['campoOrdenacao']) && isset($arrPg['tipoOrdenacao'])){
                        $order = " ORDER BY " . $arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao'];
                    }
                    
                    //Monta paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                        $limit = " LIMIT " . $arrPg['inicio'] . ", " . $arrPg['limite'];
                    }
                }else{
                    $order = " ORDER BY NOME_PRINCIPAL ";
                }
                
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
                            F.FUNCAO,
                            (SELECT SUM(DEBITO) FROM SPRO_HISTORICO_GERADOC WHERE ID_LOGIN = C.ID_CLIENTE) AS CONSUMO
                        FROM
                            SPRO_CLIENTE C
                        INNER JOIN
                            SPRO_AUTH_FUNCAO F ON F.ID_AUTH_FUNCAO = C.ID_AUTH_FUNCAO
                        WHERE
                            C.ID_MATRIZ = {$idMatriz}
                        $where
                        $order
                        $limit
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
                    
                    foreach($rs->creditos as $row){
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
        
        /**
         * Consulta os dados da última operação efetuada pelo cliente
         * 
         * @param int $idCliente Código do cliente a ser consultado
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool        $ret->status        - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string      $ret->msg           - Armazena mensagem ao usuário                      <br />
         *  stdClass    $ret->operacao      - Dados da operação                                 <br />
         * </code>
         * @throws Exception
         */
        public function consultarUltimaOperacaoCliente($idCliente){
            try{
                //Objeto de Retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao consultar última operação!";
                
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                $tbCredito->setLimit(1); //Define LIMIT 1
                $tbCredito->setOrderBy("DATA_REGISTRO DESC");
                
                //Executa o Select
                $rs = $tbCredito->findAll("ID_CLIENTE = {$idCliente}");
                
                //Se não encontrar retorno 
                if($rs->count() <= 0){
                    $ret->msg = "Operação não encontrada!";
                    return $ret;
                }
                
                //Armazena retorno 
                $rsOperacao = $rs->getRs();
                $rsOperacao = $rsOperacao[0];
                
                //Retorno OK
                $ret->status        = true;
                $ret->msg           = "Operação encontrada!";
                $ret->operacao      = $rsOperacao;  
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Insere créditos  para um determinado usuário da matriz logada (cliente)
         * 
         * @param int $idUsuario Código do cliente que receberá créditos
         * @param int $idRegAnterior Código do registro anterior aos créditos
         * @param int $saldoAnt Saldo anterior do cliente
         * @param int $credito Quantidade de créditos que será inserida
         * @param int $saldoFinal Saldo final do cliente
         * @param datetime $vencimento
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  itn     $ret->erro      - Código de erro                                    <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  int     $ret->id        - ID do novo registro inserido                      <br />
         * </code>
         * @throws Exception
         */
        public function inserirCredito($idMatriz, $idUsuario, $credito){
            try{
                //Objeto de Retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao inserir crédito!";
                
                //Última opeção do Cliente e Matriz
                $ultimaOperacaoCliente  = $this->consultarUltimaOperacaoCliente($idUsuario);
                $ultimaOperacaoMatriz   = $this->consultarUltimaOperacaoCliente($idMatriz);
                
                //Se houver falha nos dados da Matriz
                if(!$ultimaOperacaoMatriz->status){
                    return $ultimaOperacaoMatriz;
                }
                
                if($ultimaOperacaoCliente->status){
                    //Dados do INSERT
                    $idRegAnterior  = $ultimaOperacaoCliente->operacao->ID_CREDITO_CONSOLIDADO;
                    $saldoAnt       = $ultimaOperacaoCliente->operacao->SALDO_FINAL;
                    $saldoFinal     = $ultimaOperacaoCliente->operacao->SALDO_FINAL + $credito;
                    $vencimento     = $ultimaOperacaoMatriz->operacao->VENCIMENTO;
                }else{
                    //Dados do INSERT
                    $idRegAnterior  = 0;
                    $saldoAnt       = 0;
                    $saldoFinal     = $credito;
                    $vencimento     = $ultimaOperacaoMatriz->operacao->VENCIMENTO;
                }
                
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                $tbCredito->OPERACAO            = "C";
                $tbCredito->NUM_PEDIDO          = 0;
                $tbCredito->DATA_REGISTRO       = date("Y-m-d H:i:s");
                $tbCredito->ID_MATRIZ           = (int)$idMatriz;
                $tbCredito->ID_CLIENTE          = (int)$idUsuario;
                $tbCredito->ID_REG_SALDO_ANT    = (int)$idRegAnterior;
                $tbCredito->SALDO_ANT           = $saldoAnt;
                $tbCredito->CREDITO             = $credito;
                $tbCredito->SALDO_FINAL         = $saldoFinal;
                $tbCredito->VENCIMENTO          = $vencimento;
                        
                //Executa insert
                $id = $tbCredito->save();
                
                if($id > 0){
                    //Retorno OK
                    $ret->status    = true;
                    $ret->msg       = "Crédito lançado com sucesso!";
                    $ret->id        = $id;
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Estorna créditos de um determinado usuário para matriz logada (cliente)
         * 
         * @param int $idUsuario Código do cliente que receberá créditos
         * @param int $idRegAnterior Código do registro anterior aos créditos
         * @param int $saldoAnt Saldo anterior do cliente
         * @param int $estorno Quantidade de créditos a serem estornados
         * @param int $saldoFinal Saldo final do cliente
         * @param datetime $vencimento
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  itn     $ret->erro      - Código de erro                                    <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  int     $ret->id        - ID do novo registro inserido                      <br />
         * </code>
         * @throws Exception
         */
        public function estornarCredito($idMatriz, $idUsuario, $debito){
            try{
                //Objeto de Retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao lançar débito!";
                
                //Última opeção do Cliente e Matriz
                $ultimaOperacaoCliente  = $this->consultarUltimaOperacaoCliente($idUsuario);
                $ultimaOperacaoMatriz   = $this->consultarUltimaOperacaoCliente($idMatriz);
                
                //Se houver falha nos dados da Matriz
                if(!$ultimaOperacaoMatriz->status){
                    return $ultimaOperacaoMatriz;
                }
                
                if($ultimaOperacaoCliente->status){
                    //Dados do INSERT
                    $idRegAnterior  = $ultimaOperacaoCliente->operacao->ID_CREDITO_CONSOLIDADO;
                    $saldoAnt       = $ultimaOperacaoCliente->operacao->SALDO_FINAL;
                    $saldoFinal     = $ultimaOperacaoCliente->operacao->SALDO_FINAL - $debito;
                    $vencimento     = $ultimaOperacaoMatriz->operacao->VENCIMENTO;
                }else{
                    //Dados do INSERT
                    $idRegAnterior  = 0;
                    $saldoAnt       = 0;
                    $saldoFinal     = $debito;
                    $vencimento     = $ultimaOperacaoMatriz->operacao->VENCIMENTO;
                }
                
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                $tbCredito->OPERACAO            = "C";
                $tbCredito->NUM_PEDIDO          = 0;
                $tbCredito->DATA_REGISTRO       = date("Y-m-d H:i:s");
                $tbCredito->ID_MATRIZ           = (int)$idMatriz;
                $tbCredito->ID_CLIENTE          = (int)$idUsuario;
                $tbCredito->ID_REG_SALDO_ANT    = (int)$idRegAnterior;
                $tbCredito->SALDO_ANT           = $saldoAnt;
                $tbCredito->CREDITO             = $debito;
                $tbCredito->SALDO_FINAL         = $saldoFinal;
                $tbCredito->VENCIMENTO          = $vencimento;
                        
                //Executa insert
                $id = $tbCredito->save();
                
                if($id > 0){
                    //Retorno OK
                    $ret->status    = true;
                    $ret->msg       = "Débito lançado com sucesso!";
                    $ret->id        = $id;
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Salva um novo visitante no banco de dados realizando suas validações
         * 
         * @param array $arrDados
         * <code>
         *  <br />
         *  $arrDados = array(                          <br />
         *      "NOME"  => "Claudio Rubens",            <br />
         *      "EMAIL" => "interbits@interbits.com.br  <br />
         *  )
         * </code>
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  int     $ret->erro      - Código de erro                                    <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         * </code>
         */
        public function salvarUsuarioVistante($arrDados){
            //Objeto de retorno
            $ret            = new \stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao salvar usuário! Tente mais tarde.";
            
            //Tabela de Visitantes
            $tbVisitante = new TB\User();
            
            //Valida e-mail
            if(!isset($arrDados['EMAIL'])){
                $ret->msg = "Campo de e-mail inválido ou nulo!";
                return $ret;
            }
            
            //Valida existência do e-mail
            $tbVisitante->setLimit(1);
            $rs = $tbVisitante->findAll("EMAIL = '{$arrDados['EMAIL']}'");
            
            if($rs->count() > 0){
                $ret->msg = "Esse e-mail já possui cadastro!";
                return $ret;
            }
            
            //Atribui dados a serem salvos
            $tbVisitante->ID_USER_PERFIL        = isset($arrDados['ID_USER_PERFIL']) ? $arrDados['ID_USER_PERFIL'] : 1;
            $tbVisitante->ID_CAMPANHA_ORIG_CAD  = isset($arrDados['ID_CAMPANHA_ORIG_CAD']) ? $arrDados['ID_CAMPANHA_ORIG_CAD'] : 0;
            $tbVisitante->ID_MATRIZ             = isset($arrDados['ID_MATRIZ']) ? $arrDados['ID_MATRIZ'] : 0;
            $tbVisitante->NOME                  = isset($arrDados['NOME']) ? $arrDados['NOME'] : '';
            $tbVisitante->APELIDO               = isset($arrDados['APELIDO']) ? $arrDados['APELIDO'] : '';
            $tbVisitante->EMAIL                 = isset($arrDados['EMAIL'])? $arrDados['EMAIL'] : '';
            $tbVisitante->DDD_CELULAR           = isset($arrDados['CELULAR']) ? substr($arrDados['CELULAR'], 0, 2) : '';
            $tbVisitante->CELULAR               = isset($arrDados['CELULAR']) ? substr($arrDados['CELULAR'], 2) : '';
            $tbVisitante->LOGIN                 = isset($arrDados['LOGIN']) ? $arrDados['LOGIN'] : '';
            $tbVisitante->PASSWD                = isset($arrDados['PASSWD']) ? $arrDados['PASSWD'] : '';
            $tbVisitante->PASSWD_TEMPORARY      = isset($arrDados['PASSWD_TEMPORARY']) ? $arrDados['PASSWD_TEMPORARY'] : '';
//            $tbVisitante->FB_ID         = isset($arrDados['FB_ID']) ? $arrDados['FB_ID'] : '';
//            $tbVisitante->GOOGLE_ID     = isset($arrDados['GOOGLE_ID']) ? $arrDados['GOOGLE_ID'] : '';
            $tbVisitante->DATA_REGISTRO         = date("Y-m-d H:i:s");
            
            $tbVisitante->save();
            
            //Retorno OK
            $ret->status    = true;
            $ret->msg       = "Usuário cadastrado com sucesso!";
            return $ret;
        }
        
        /**
         * Salva um novo usuário no banco de dados realizando suas validações
         * 
         * @param array $arrDados
         * <code>
         *  <br />
         *  $arrDados = array(                          <br />
         *      "NOME"  => "Claudio Rubens",            <br />
         *      "EMAIL" => "interbits@interbits.com.br  <br />
         *  )
         * </code>
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  int     $ret->erro      - Código de erro                                    <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         * </code>
         */
        public function salvarUsuario($arrDados){
            //Objeto de retorno
            $ret            = new \stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao salvar usuário! Tente mais tarde.";
            
            //Tabela de Visitantes
            $tbUser = new TB\User();
            
            //Valida e-mail
            if(!isset($arrDados['EMAIL'])){
                $ret->msg = "Campo de e-mail inválido ou nulo!";
                return $ret;
            }
            
            //Valida existência do e-mail
            $tbUser->setLimit(1);
            $rs = $tbUser->findAll("EMAIL = '{$arrDados['EMAIL']}'");
            
            if($rs->count() > 0){
                $ret->msg = "Esse e-mail já possui cadastro!";
                return $ret;
            }
            
            //Atribui dados a serem salvos
            $tbUser->ID_USER_PERFIL        = isset($arrDados['ID_USER_PERFIL']) ? $arrDados['ID_USER_PERFIL'] : 1;
            $tbUser->ID_CAMPANHA_ORIG_CAD  = isset($arrDados['ID_CAMPANHA_ORIG_CAD']) ? $arrDados['ID_CAMPANHA_ORIG_CAD'] : 0;
            $tbUser->ID_MATRIZ             = isset($arrDados['ID_MATRIZ']) ? $arrDados['ID_MATRIZ'] : 0;
            $tbUser->NOME                  = isset($arrDados['NOME']) ? $arrDados['NOME'] : '';
            $tbUser->APELIDO               = isset($arrDados['APELIDO']) ? $arrDados['APELIDO'] : '';
            $tbUser->EMAIL                 = isset($arrDados['EMAIL'])? $arrDados['EMAIL'] : '';
            $tbUser->DDD_CELULAR           = isset($arrDados['CELULAR']) ? substr($arrDados['CELULAR'], 0, 2) : '';
            $tbUser->CELULAR               = isset($arrDados['CELULAR']) ? substr($arrDados['CELULAR'], 2) : '';
            $tbUser->LOGIN                 = isset($arrDados['LOGIN']) ? $arrDados['LOGIN'] : '';
            $tbUser->DT_NASCIMENTO         = isset($arrDados['DT_NASCIMENTO']) ? $arrDados['DT_NASCIMENTO'] : '';
            $tbUser->DDD_TEL_RES           = isset($arrDados['DDD_TEL_RES']) ? $arrDados['DDD_TEL_RES'] : '';
            $tbUser->TEL_RES               = isset($arrDados['TEL_RES']) ? $arrDados['TEL_RES'] : '';
            $tbUser->DDD_TEL_COM           = isset($arrDados['DDD_TEL_COM']) ? $arrDados['DDD_TEL_COM'] : '';
            $tbUser->TEL_COM               = isset($arrDados['TEL_COM']) ? $arrDados['TEL_COM'] : '';
            $tbUser->RAMAL_TEL_COM         = isset($arrDados['RAMAL_TEL_COM']) ? $arrDados['RAMAL_TEL_COM'] : '';
            $tbUser->OBS                   = isset($arrDados['OBS']) ? $arrDados['OBS'] : '';
            $tbUser->PASSWD                = isset($arrDados['PASSWD']) ? $arrDados['PASSWD'] : '';
            $tbUser->PASSWD_TEMPORARY      = isset($arrDados['PASSWD_TEMPORARY']) ? $arrDados['PASSWD_TEMPORARY'] : '';
            $tbUser->FB_ID                 = isset($arrDados['FB_ID']) ? $arrDados['FB_ID'] : '';
            $tbUser->GOOGLE_ID             = isset($arrDados['GOOGLE_ID']) ? $arrDados['GOOGLE_ID'] : '';
            $tbUser->DATA_REGISTRO         = date("Y-m-d H:i:s");
            
            //Executa insert e aramazena ID do novo usuário
            $id = $tbUser->save();
            
            //Retorno OK
            $ret->status    = true;
            $ret->msg       = "Usuário cadastrado com sucesso!";
            $ret->id        = $id;
            return $ret;
        }
        
        /**
         * Salva um novo usuário de cadastro no banco de dados realizando suas validações
         * 
         * @param array $arrDados
         * <code>
         *  <br />
         *  $arrDados = array(                          <br />
         *      "NOME"  => "Claudio Rubens",            <br />
         *      "EMAIL" => "interbits@interbits.com.br  <br />
         *  )
         * </code>
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  int     $ret->erro      - Código de erro                                    <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  int     $ret->id        - ID do novo Usuário de Cadastro                    <br />
         * </code>
         */
        public function salvarUsuarioCadastro($arrDados){
            //Objeto de retorno
            $ret            = new \stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao salvar usuário! Tente mais tarde.";
            
            //Tabela de Visitantes
            $tbUserCadastro = new TB\UserCadastro();
            
            //Atribui dados a serem salvos
            $tbUserCadastro->ID_USER_CADASTRO       = isset($arrDados['ID_USER_CADASTRO']) ? $arrDados['ID_USER_CADASTRO'] : 0;
            $tbUserCadastro->ID_USER                = isset($arrDados['ID_USER']) ? $arrDados['ID_USER'] : 0;
            $tbUserCadastro->HASH                   = isset($arrDados['HASH']) ? $arrDados['HASH'] : '';
            $tbUserCadastro->PF_PJ                  = isset($arrDados['PF_PJ']) ? $arrDados['PF_PJ'] : 'PF';
            $tbUserCadastro->CPF_CNPJ               = isset($arrDados['CPF_CNPJ'])? $arrDados['CPF_CNPJ'] : '';
            $tbUserCadastro->DATA_CONTRATO_RECEB    = isset($arrDados['DATA_CONTRATO_RECEB']) ? $arrDados['DATA_CONTRATO_RECEB'] : '';
            $tbUserCadastro->RAZAO_SOCIAL           = isset($arrDados['RAZAO_SOCIAL']) ? $arrDados['RAZAO_SOCIAL'] : '';
            $tbUserCadastro->SEXO                   = isset($arrDados['SEXO']) ? $arrDados['SEXO'] : '';
            $tbUserCadastro->COD_POSTAL             = isset($arrDados['COD_POSTAL']) ? $arrDados['COD_POSTAL'] : '';
            $tbUserCadastro->LOGRADOURO             = isset($arrDados['LOGRADOURO']) ? $arrDados['LOGRADOURO'] : '';
            $tbUserCadastro->NUMERO                 = isset($arrDados['NUMERO']) ? $arrDados['NUMERO'] : 0;
            $tbUserCadastro->COMPLEMENTO            = isset($arrDados['COMPLEMENTO']) ? $arrDados['COMPLEMENTO'] : '';
            $tbUserCadastro->BAIRRO                 = isset($arrDados['BAIRRO']) ? $arrDados['BAIRRO'] : '';
            $tbUserCadastro->CIDADE                 = isset($arrDados['CIDADE']) ? $arrDados['CIDADE'] : '';
            $tbUserCadastro->UF                     = isset($arrDados['UF']) ? $arrDados['UF'] : '';
            $tbUserCadastro->WEBSITE                = isset($arrDados['WEBSITE']) ? $arrDados['WEBSITE'] : '';
            $tbUserCadastro->NOME_CONTATO           = isset($arrDados['NOME_CONTATO']) ? $arrDados['NOME_CONTATO'] : '';
            $tbUserCadastro->RAZAO_SOCIAL           = isset($arrDados['RAZAO_SOCIAL']) ? $arrDados['RAZAO_SOCIAL'] : '';
            $tbUserCadastro->INSC_ESTADUAL          = isset($arrDados['INSC_ESTADUAL']) ? $arrDados['INSC_ESTADUAL'] : '';
            $tbUserCadastro->INSC_MUNICIPAL         = isset($arrDados['INSC_MUNICIPAL']) ? $arrDados['INSC_MUNICIPAL'] : '';
            $tbUserCadastro->DDD_TEL_2              = isset($arrDados['DDD_TEL_2']) ? $arrDados['DDD_TEL_2'] : 0;
            $tbUserCadastro->TEL_2                  = isset($arrDados['TEL_2']) ? $arrDados['TEL_2'] : 0;
            $tbUserCadastro->RAMAL_TEL_2            = isset($arrDados['RAMAL_TEL_2']) ? $arrDados['RAMAL_TEL_2'] : 0;
            $tbUserCadastro->DDD_FAX                = isset($arrDados['DDD_FAX']) ? $arrDados['DDD_FAX'] : 0;
            $tbUserCadastro->FAX                    = isset($arrDados['FAX']) ? $arrDados['FAX'] : 0;
            $tbUserCadastro->RAMAL_FAX              = isset($arrDados['RAMAL_FAX']) ? $arrDados['RAMAL_FAX'] : 0;
            $tbUserCadastro->DATA_REGISTRO          = date("Y-m-d H:i:s");
            
            //Executa insert e aramazena ID do novo usuário
            $id = $tbUserCadastro->save();
            
            //Retorno OK
            $ret->status    = true;
            $ret->msg       = "Usuário cadastrado com sucesso!";
            $ret->id        = $id;
            return $ret;
        }
    }
?>
