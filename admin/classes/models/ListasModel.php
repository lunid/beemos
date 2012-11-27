<?php
    namespace admin\classes\models;
    use \sys\classes\util\Date;
    use \sys\classes\mvc\Model;        
    use \admin\classes\models\tables\HistoricoGeradoc;
    use \admin\classes\models\tables\TurmaLista;
    use \admin\classes\models\tables\TurmaConvite;
    use \admin\classes\models\tables\LstUsuario;
    use \admin\classes\models\tables\Escola;
    
    class ListasModel extends Model {
        /**
         * Carrega as Listas de uma Turma para um jqgrid.
         * Podendo utilizar ordenação e paginação
         * 
         * @param int $ID_CLIENTE Código do cliente
         * @param string $where String enviada para filtro de resultados
         * <code>
         *  Ex: AND L.COD_LISTA LIKE '%Teste%'
         * </code>
         * @param int $utilizadas Filtrar apenas listas utilizadas ou não
         * @param int $ID_TURMA Filtrar apenas listas utilizadas pela Turma enviada
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
         *  array   $ret->listas    - Armazena o array de listas encontrados no Banco   <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregaListasCliente($ID_CLIENTE, $where = "", $utilizadas = 0, $ID_TURMA = 0, $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar listas de exercicios do cliente!";
                
                //Valida ID_CLIENTE
                if((int)$ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE inválido ou nulo!";
                    return $ret;
                }
                
                //Instância da table SPRO_HISTORICO_GERADOC
                $tbHistGeradoc  = new HistoricoGeradoc();
                //Carrega listas do cliente
                $ret            = $tbHistGeradoc->carregaListasCliente($ID_CLIENTE, $where, $utilizadas, $ID_TURMA, $arrPg);
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que salva a alteração de relacionamentos entre Turmas e Listas
         * 
         * @param string $idTurmas String com IDs de turmas separados por vírgula. Ex: 23,56,87
         * @param string $idsListas String com IDs de listas separados por vírgula. Ex: 17878,21233,98877
         * @param char $tipo Tipo de operação a ser realizada I - Inserção ou E - Exclusão
         * @return \stdClass
         * @throws Exception
         */
        public function salvaListasTurmas($idTurmas, $idsListas, $tipo){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar Lista(s) da Turma";
                
                //Array de Turmas
                $arrIdTurmas = explode(",", $idTurmas);
                
                //Percorre array limpando registros e adicionando relacionamentos
                foreach($arrIdTurmas as $idTurma){
                    //Limpa as listas do com IDs enviados
                    $tbTurmaLista   = new TurmaLista();
                    $sql            = "DELETE FROM SPRO_TURMA_LISTA WHERE ID_TURMA = {$idTurma} AND ID_HISTORICO_GERADOC IN({$idsListas});";
                    $tbTurmaLista->query($sql);

                    //Verifica qual operação executar
                    switch($tipo){
                        case "I":
                            //Tranforma IDs em Array
                            $arrId = explode(",", $idsListas);
                            //Verifica se forma encontrados IDs
                            if(is_array($arrId) && sizeof($arrId) > 0){
                                foreach($arrId as $idLista){
                                    //Popula informações para insert
                                    $tbTurmaLista->ID_TURMA             = (int)$idTurma;
                                    $tbTurmaLista->ID_HISTORICO_GERADOC = (int)$idLista;

                                    //Executa inserção
                                    $tbTurmaLista->save();
                                }

                                //Se não houver erro, é retornado status OK
                                $ret->status    = true;
                                $ret->msg       = "Relação salva com sucesso!";
                            }else{
                                //Caso não seja enviado nenhum ID
                                $ret->msg = "Nenhuma ID de lista enviado!";
                            }
                            break;
                        case "E":
                            //Caso seja apenas uma exclusão
                            $ret->status    = true;
                            $ret->msg       = "Relação desfeita com sucesso!";
                            break;
                        default:
                            $ret->msg = "Operação não identificada!";
                            return $ret;
                    }
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que salva as informações de Turma e Lista para que depois os convites sejam disparados via CRON
         * 
         * @param int $ID_CLIENTE
         * @param int $ID_TURMA
         * @param int $ID_HISTORICO_GERADOC ID da Lista
         * @param char $sms S ou N
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * 
         * @throws Exception
         */
        public function salvaConvites($ID_CLIENTE, $ID_TURMA, $ID_HISTORICO_GERADOC, $sms){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar disparo de convites!";
                
                //Instância da table SPRO_TURMA_CONVITE
                $tbTurmaConvite                         = new TurmaConvite();
                
                //Carrega Lista e seus dados
                $tbLista = new HistoricoGeradoc($ID_HISTORICO_GERADOC);
                
                //Verifica se foi encontrada a lista
                if($tbLista->ID_HISTORICO_GERADOC != $ID_HISTORICO_GERADOC){
                    $ret->msg = "Lista não encontrada!";
                    return $ret;
                }
                
                //Seta valores para INSERT
                $tbTurmaConvite->ID_TURMA_CONVITE       = 0;
                $tbTurmaConvite->ID_CLIENTE             = $ID_CLIENTE;
                $tbTurmaConvite->ID_TURMA               = $ID_TURMA;
                $tbTurmaConvite->ID_HISTORICO_GERADOC   = $tbLista->ID_HISTORICO_GERADOC;
                $tbTurmaConvite->ENVIAR_SMS             = $sms;
                $tbTurmaConvite->COD_LISTA              = $tbLista->COD_LISTA;
                
                //Executa INSERT
                $id = $tbTurmaConvite->save();
                
                if($id > 0){
                    $ret->status    = true;
                    $ret->msg       = "Disparo de convide gravado com sucesso!";
                }else{
                    $ret->msg       = "Falha ao gravar disparo de convide!";
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Carrega todos os dados de uma determinada listas
         * 
         * @param int $ID_HISTORICO_GERADOC ID da Lista
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool                $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string              $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  HistoricoGeradoc    $ret->lista     - Armazena objeto com dados da lista                <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregaDadosLista($ID_HISTORICO_GERADOC){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar dados da lista!";
                
                //Carrega Lista e seus dados
                $tbLista = new HistoricoGeradoc($ID_HISTORICO_GERADOC);
                
                //Verifica se foi encontrada a lista
                if($tbLista->ID_HISTORICO_GERADOC != $ID_HISTORICO_GERADOC){
                    $ret->msg = "Lista não encontrada!";
                    return $ret;
                }
                
                //Retorno
                $ret->status    = true;
                $ret->msg       = "Lista carregada com sucesso!";
                $ret->lista     = $tbLista;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Altera status da coluna ANTICOLA na tabela SPRO_HISTORICO_GERADOC
         * 
         * @param int $ID_HISTORICO_GERADOC
         * @param int $status 0 ou 1
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * 
         * @throws Exception
         */
        public function alteraAnticola($ID_HISTORICO_GERADOC, $status){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar status Anticola!";
                
                //Carrega table Listas
                $tbLista = new HistoricoGeradoc();
                
                //Seta valores para UPDATE
                $tbLista->ANTICOLA = $status;
                
                //Executa UPDATE
                $tbLista->update(array("ID_HISTORICO_GERADOC = %i", $ID_HISTORICO_GERADOC));
                
                //Retorno
                $ret->status    = true;
                $ret->msg       = "Anticola alterado com sucesso!";
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Altera a data de início ou final da validade de uma lista
         * 
         * @param int $ID_HISTORICO_GERADOC
         * @param string $data DD/MM/AAAA
         * @param char $tipo INI ou FIM
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * 
         * @throws Exception
         */
        public function alteraPeriodo($ID_HISTORICO_GERADOC, $data, $tipo){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar Período de Validade!";
                
                //Carrega table Listas
                $tbLista = new HistoricoGeradoc();
                
                switch ($tipo){
                    case 'INI':
                        $tbLista->LISTA_ATIVA_DT_HR_INI = Date::formatDate($data, 'AAAA-MM-DD') . " 00:00:00";
                        break;
                    case 'FIM':
                        $tbLista->LISTA_ATIVA_DT_HR_FIM = Date::formatDate($data, 'AAAA-MM-DD') . " 23:59:59";
                        break;
                    default:
                        $ret->msg = "Falha ao salvar Período de Validade! Tipo não é válido!";
                        return $ret;
                        break;
                }
                
                //Executa UPDATE
                $tbLista->update(array("ID_HISTORICO_GERADOC = %i", $ID_HISTORICO_GERADOC));
                
                //Retorno
                $ret->status    = true;
                $ret->msg       = "Período salvo com sucesso!";
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Altera a permissão de um aluno consultar (ou não) o resultado final de uma lista
         * 
         * @param int $ID_HISTORICO_GERADOC
         * @param int $status 0 ou 1
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * 
         * @throws Exception
         */
        public function alteraResultadoAluno($ID_HISTORICO_GERADOC, $status){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar status de Resultado do Aluno!";
                
                //Carrega table Listas
                $tbLista = new HistoricoGeradoc();
                
                //Seta valores para UPDATE
                $tbLista->ST_RESULTADO_ALUNO = $status;
                
                //Executa UPDATE
                $tbLista->update(array("ID_HISTORICO_GERADOC = %i", $ID_HISTORICO_GERADOC));
                
                //Retorno
                $ret->status    = true;
                $ret->msg       = "Status de Resultado do Aluno alterado com sucesso!";
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Altera a permissão de um aluno visualizar (ou não) o Gabarito de respostas de uma Lista
         * 
         * @param int $ID_HISTORICO_GERADOC
         * @param int $status 0 ou 1
         *
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * 
         * @throws Exception
         */
        public function alteraGabaritoAluno($ID_HISTORICO_GERADOC, $status){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar status de Gabarito do Aluno!";
                
                //Carrega table Listas
                $tbLista = new HistoricoGeradoc();
                
                //Seta valores para UPDATE
                $tbLista->ST_GABARITO_ALUNO = $status;
                
                //Executa UPDATE
                $tbLista->update(array("ID_HISTORICO_GERADOC = %i", $ID_HISTORICO_GERADOC));
                
                //Retorno
                $ret->status    = true;
                $ret->msg       = "Status de Gabarito do Aluno alterado com sucesso!";
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Altera o tempo de vida em que é permitido ao aluno iniciar e finalizar a lista de questões 
         * 
         * @param int $ID_HISTORICO_GERADOC
         * @param string $tempo Ex: 00:45
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * 
         * @throws Exception
         */
        public function alteraTempoVida($ID_HISTORICO_GERADOC, $tempo){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar Tempo de Vida da Lista!";
                
                //Carrega table Listas
                $tbLista = new HistoricoGeradoc();
                
                //Seta valores para UPDATE
                $tbLista->TEMPO_VIDA = $tempo;
                
                //Executa UPDATE
                $tbLista->update(array("ID_HISTORICO_GERADOC = %i", $ID_HISTORICO_GERADOC));
                
                //Retorno
                $ret->status    = true;
                $ret->msg       = "Tempo de Vida da Lista alterado com sucesso!";
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Efetua a chamado do método calculaRespostasLista no ORM LstUsuario e repassa o resultado
         * do calculo de questões obtido.
         * 
         * @param int $ID_HISTORICO_GERADOC ID da lista
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  int  $ret->correta      - Total de questões já respondidas e Corretas       <br />
         *  int  $ret->errada       - Total de questões já respondidas e Erradas        <br />
         * </code>
         * 
         * @throws Exception
         */
        public function calculaRespostasLista($ID_HISTORICO_GERADOC, $ID_ESCOLA = 0, $ENSINO = '', $PERIODO = '', $ANO = '', $TURMA = ''){
            try{
                //Instância o objeto da Tabela SPRO_LST_USUARIO e retorna o calculo efetuado no método calculaRespostasLista
                $tbLstUsuario = new LstUsuario();
                return $tbLstUsuario->calculaRespostasLista($ID_HISTORICO_GERADOC, $ID_ESCOLA, $ENSINO, $PERIODO, $ANO, $TURMA);
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que calcula o total de alunos que respoderam as questões da Lista
         * e o total de alunos que abriram a lista mas não terminaram (não respoderam)
         * 
         * @param int $ID_HISTORICO_GERADOC ID da Lista
         
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  int  $ret->respondeu    - Total de alunos que responderam                   <br />
         *  int  $ret->naoRespondeu - Total de alunos que não respoderam                <br />
         * </code>
         * 
         * @throws Exception
         */
        public function calculaAlunosRespostasLista($ID_HISTORICO_GERADOC, $ID_ESCOLA = 0, $ENSINO = '', $PERIODO = '', $ANO = '', $TURMA = ''){
            try{
                //Instância o objeto da Tabela SPRO_LST_USUARIO e retorna o calculo efetuado no método calculaAlunosRespostasLista
                $tbLstUsuario = new LstUsuario();
                return $tbLstUsuario->calculaAlunosRespostasLista($ID_HISTORICO_GERADOC, $ID_ESCOLA, $ENSINO, $PERIODO, $ANO, $TURMA);
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Calcula o aproveitamento total de uma lista, somando o toltal de respostas
         * corretas dos alunos que respoderam e dividinfo pela multiplicação da quantidade
         * total de alunos que repsondeu x quantidade questões
         * 
         * @param int $ID_HISTORICO_GERADOC ID da Lista
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status        - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg           - Armazena mensagem ao usuário                      <br />
         *  double  $ret->aproveitamento   - Percentual total de aproveitamento da lista    <br />
         * </code>
         * 
         * @throws Exception
         */
        public function calculaAproveitamentoLista($ID_HISTORICO_GERADOC, $ID_ESCOLA = 0, $ENSINO = '', $PERIODO = '', $ANO = '', $TURMA = ''){
            try{
                //Instância o objeto da Tabela SPRO_LST_USUARIO e retorna o calculo efetuado no método calculaAlunosRespostasLista
                $tbLstUsuario = new LstUsuario();
                return $tbLstUsuario->calculaAproveitamentoLista($ID_HISTORICO_GERADOC, $ID_ESCOLA, $ENSINO, $PERIODO, $ANO, $TURMA);
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que lista as Escolas e Turmas de uam determinada lista.
         * 
         * @param int $ID_CLIENTE ID do cliente 
         * @param int $ID_ESCOLA ID da Escola
         * @param string $ENSINO Ensinos para serem filtrados usando IN. Ex: 'M', 'F'
         * @param string $PERIODO Períodos para serem filtrados usando IN. Ex: 'M', 'F'
         * @param string $ANO Abos para serem filtrados usando IN. Ex: 1, 3
         *
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status        - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg           - Armazena mensagem ao usuário                      <br />
         *  array   $ret->arrEscolas    - Array com as escolas encontradas                  <br />
         *  array   $ret->arrTurmas     - Array com as turmas encontradas                   <br />
         *  array   $ret->arrEnsino     - Array com os ensinos encontrados                  <br />
         *  array   $ret->arrPeriodo    - Array com os períodos encontrados                 <br />
         *  array   $ret->arrAno        - Array com os anos encontrados                     <br />
         * </code>
         * 
         * @throws \admin\classes\models\tables\Exception
         */
        public function carregaEscolasTurmasLista($ID_HISTORICO_GERADOC, $ID_CLIENTE, $ID_ESCOLA = 0, $ENSINO = '', $PERIODO = '', $ANO = ''){
            try{
                //Instância o objeto da Tabela SPRO_HISORICO_GERADOC 
                $tbLista = new HistoricoGeradoc($ID_HISTORICO_GERADOC);
                return $tbLista->carregaEscolasTurmasLista($ID_CLIENTE, $ID_ESCOLA, $ENSINO, $PERIODO, $ANO);
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function calculaAproveitamentoQuestao($ID_HISTORICO_GERADOC, $ID_ESCOLA = 0, $ENSINO = '', $PERIODO = '', $ANO = '', $TURMA = ''){
            try{
                //Instância o objeto da Tabela SPRO_LST_USUARIO e retorna o calculo efetuado no método calculaAlunosRespostasLista
                $tbLstUsuario = new LstUsuario();
                return $tbLstUsuario->calculaAproveitamentoQuestao($ID_HISTORICO_GERADOC, $ID_ESCOLA, $ENSINO, $PERIODO, $ANO, $TURMA);
            }catch(Exception $e){
                throw $e;
            }
        }
    }
    
?>
