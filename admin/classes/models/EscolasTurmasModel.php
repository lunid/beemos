<?php
    namespace admin\classes\models;
    use \sys\classes\mvc\Model;        
    use \common\db_tables as TB;
    
    class EscolasTurmasModel extends Model {
        /**
         * Listas as escolas cadastradas para um determinado cliente
         * 
         * @param int $ID_CLIENTE Código do cliente para filtro de escolas
         * @param string $where texto com o WHERE para Filtro de SQL
         * @param array $arrPg Array com informações de Ordenação e Paginação
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
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método   <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                    <br />
         *  array   $ret->escolas   - Armazena o array de escolas encontrados no Banco<br />
         * </code>
         * @throws Exception
         */
        public function listarEscolasCliente($ID_CLIENTE, $where, $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar Escolas do Cliente!";
                $ret->escolas   = array();
                
                //Valida se existe ID_CLIENTE
                if(!$ID_CLIENTE || $ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE não foi inicializado!";
                    return $ret;
                }
                
                //Objeto de controle da table SPRO_ESCOLAS
                $tbEscola = new TB\Escola();
                  
                $whereSql  = " ID_CLIENTE = {$ID_CLIENTE} ";
                $whereSql .= $where;
                
                //Verifica dados de paginação e ordenação
                if(is_array($arrPg)){
                    //Ordenação
                    if(isset($arrPg['campoOrdenacao'])){
                        $tbEscola->setOrderBy($arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao']);
                    }
                    
                    //Paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                        $tbEscola->setLimit((int)$arrPg['inicio'], (int)$arrPg['limite']);
                    }
                }
                
                //Busca escolas baseado no WHERE 
                $rs = $tbEscola->findAll($whereSql);
                
                //Verifica se houve retorno
                if($rs->count() <= 0){
                    $ret->msg = "Nenhuma escola encontrada!";
                    return $ret;
                }
                
                //Retorna escola(s) encontrada(s)
                $ret->status    = true;
                $ret->msg       = "Escolas encontradas!";
                $ret->escolas   = $rs->getRs();
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Lista as Turmas de um determinado cliente cpom filtros, ordenação e paginação
         * 
         * @param int $ID_CLIENTE
         * @param int $ID_ESCOLA
         * @param int $utilizadas Valida o filtro para busca de apenas Turmas utilizadas (0 ou 1)
         * @param int $ID_HISTORICO_GERADOC Código da lista para filtro de utilizadas
         * @param string $where Filtros de pesquisa SQL
         * @param string $arrPg Campos de paginação e ordenação do select
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
         *  array   $ret->turmas    - Armazena o array de turmas encontradas no Banco   <br />
         * </code>
         * @throws Exception
         */
        public function listarTurmasCliente($ID_CLIENTE, $ID_ESCOLA = 0, $utilizadas = 0, $ID_HISTORICO_GERADOC = 0, $where = "", $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar Turmas do Cliente!";
                $ret->escolas   = array();
                
                //Valida se existe ID_CLIENTE
                if(!$ID_CLIENTE || $ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE não foi inicializado!";
                    //return $ret;
                }
                
                //Objeto de controle da table SPRO_ESCOLAS
                $tbTurma            = new TB\Turma();
                $tbTurma->ID_ESCOLA = $ID_ESCOLA;
                //Busca as turmas de acordo com os parâmetros enviados
                $rs = $tbTurma->listaTurmasEscolas($ID_CLIENTE, $utilizadas, $ID_HISTORICO_GERADOC, $where, $arrPg);
                
                //Retorna resultado da busca
                return $rs;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Salva uma nova escola no banco de dados, validando se ela já existe para o cliente.
         * 
         * @param int $ID_CLIENTE
         * @param string $NOME
         *
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  int     $ret->id        - Armazena Códiga da nova escola                    <br />
         * </code>
         * 
         * @return stdClass
         * @throws Exception
         */
        public function salvarEscola($ID_CLIENTE, $NOME){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar Escola!";
                
                //Valida campos obrigatórios
                if((int)$ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE inválido ou nulo!";
                    return $ret;
                }
                
                if($NOME == ''){
                    $ret->msg = "Noma de Escola inválido ou nulo!";
                    return $ret;
                }
                
                //ORM de SPRO_ESCOLA
                $tbEscola                   = new TB\Escola();
                //Inicia campos para salvar o novo registro
                $tbEscola->ID_CLIENTE       = (int)$ID_CLIENTE;
                $tbEscola->NOME             = $NOME;
                $tbEscola->STATUS           = 1;
                $tbEscola->DATA_REGISTRO    = date('Y-m-d H:i:s');
                
                //Verifica se a Escola já existe
                $tbEscola->setLimit(1);
                $verEscola = $tbEscola->findAll("ID_CLIENTE = " . $ID_CLIENTE . " AND NOME = '" . $NOME . "'");
                
                if($verEscola->count() > 0){
                    $ret->msg = "Esse nome de Escola já existe!";
                    return $ret;
                }
                
                //Salva a escola no Banco
                $id = $tbEscola->save();
                
                //Se o ID retornado for inválido, não foi salva.
                if($id <= 0){
                    $ret->msg = "Falha ao INSERIR escola! Tente novamente mais tarde.";
                    return $ret;
                }
                
                //Retorna sucesso
                $ret->status    = true;
                $ret->msg       = "Escola salva com sucesso!";
                $ret->id        = $id;
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que atualiza o Status de ATIVO e INATIVO de uma escola
         * 
         * @param int $ID_ESCOLA
         * @param int $ID_CLIENTE
         * @param int $STATUS
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>  
         * 
         * @return stdClass
         * @throws admin\classes\models\Exception
         */
        public function alterarStatusEscola($ID_ESCOLA, $ID_CLIENTE, $STATUS){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao alterar status da Escola!";
                
                //Valida campos obrigatórios
                if((int)$ID_ESCOLA <= 0){
                    $ret->msg = "ID_ESCOLA inválido ou nulo!";
                    return $ret;
                }
                
                if((int)$ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE inválido ou nulo!";
                    return $ret;
                }
                
                if((int)$STATUS < 0){
                    $ret->msg = "Status inválido ou nulo!";
                    return $ret;
                }
                
                //ORM de SPRO_ESCOLA
                $tbEscola                   = new TB\Escola();
                //Inicia campos para salvar o novo registro
                $tbEscola->ID_ESCOLA        = (int)$ID_ESCOLA;
                $tbEscola->ID_CLIENTE       = (int)$ID_CLIENTE;
                $tbEscola->STATUS           = (int)$STATUS;
                //Where
                $arrWhere = array('ID_ESCOLA=%i AND ID_CLIENTE=%i', $ID_ESCOLA, $ID_CLIENTE);
                
                //Salva alterações da escola no Banco
                $tbEscola->update($arrWhere);
                
                //Retorna sucesso
                $ret->status    = true;
                $ret->msg       = "Escola atualizada com sucesso!";
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Salva os dados de uma Turma, seja ela existente ou não
         * 
         * @param int $ID_TURMA
         * @param int $ID_ESCOLA
         * @param string $CLASSE
         * @param char $ENSINO
         * @param int $ANO
         * @param char $PERIODO
         * 
         * @return stdClass
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  int     $ret->id        - ID da Turma Inserida ou número de linhas afetadas <br />
         * </code>
         * 
         * @throws Exception
         */
        public function salvarTurma($ID_TURMA, $ID_ESCOLA, $CLASSE, $ENSINO, $ANO, $PERIODO){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar Turma!";
                
                //Valida campo classe
                if($CLASSE == ''){
                    $ret->msg = "Nome da Classe inválido ou nulo!";
                    return $ret;
                }
                
                //Valida campo classe
                if((int)$ID_ESCOLA <= 0){
                    $ret->msg = "Escola inválida!";
                    return $ret;
                }
                
                //Valida ID_TURMA
                $where = "";
                if((int)$ID_TURMA > 0){
                    $where = " AND ID_TURMA != {$ID_TURMA} ";
                }
                
                //Instancia do ORM de SPRO_TURMA
                $tbTurma    = new TB\Turma();
                //VErifica se já existe o nome de classe para a escola
                $tbTurma->setLimit(1);
                $verClasse  = $tbTurma->findAll("CLASSE = '" . $CLASSE . "' AND ID_ESCOLA = " . $ID_ESCOLA . " {$where} ");
                
                if($verClasse->count() > 0){
                    $ret->msg = "Esse nome de Classe já existe!";
                    return $ret;
                }
                
                //Seta campos genérios para INSERT ou UPDATE
                $tbTurma->ID_ESCOLA = $ID_ESCOLA;
                $tbTurma->CLASSE    = $CLASSE;
                $tbTurma->ENSINO    = $ENSINO;
                $tbTurma->ANO       = $ANO;
                $tbTurma->PERIODO   = $PERIODO;
                
                //Se não foi enciado um ID_TURMA o sistema fará um INSERT
                if((int)$ID_TURMA <= 0){
                    //Inicia data de registro
                    $tbTurma->DATA_REGISTRO = date("Y-m-d H:i:s");
                    //Salva dados no Banco
                    $id = $tbTurma->save();
                }else{
                    //Senão o sistema atualiza os dados com um UPDATE
                    $arrWhere = array("ID_TURMA = %i", $ID_TURMA);
                    //Executando o UPDATE
                    $id = $tbTurma->update($arrWhere);
                }
                
                //Retorna sucesso
                $ret->status    = true;
                $ret->msg       = "Turma salva com sucesso!";
                $ret->id        = $id;
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que calcula o total de Alunos em uma Turma e o Total de Alunos que possuem celular
         * 
         * @param string $idsTurmas Códigos de Turmas concatenos por virgula, ex: 12,65,978
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  int     $ret->qtd       - Número total de alunos em uma turma               <br />
         *  int     $ret->qtdCel    - Número total de alunos com celular em uma turma               <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregarContatosTurma($idsTurmas){
            try{
                //Faz a consulta de informações do cliente e repassa resultado
                $tbCliente  = new TB\Cliente();
                $ret        = $tbCliente->carregaInfoAlunosTurma($idsTurmas);
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Traduz a sigla do Ensino para o decritivo completo dele
         * 
         * @param string $ensino
         * @return string Descrição do Ensino
         * @throws Exception
         */
        static function traduzirEnsino($ensino){
            try{
                return TB\Ensino::traduzirEnsino($ensino);
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Traduz a sigla do Período para o descritivo completo dele 
         * 
         * @param type $periodo
         * @return string Descrição do período
         * @throws Exception
         */
        static function traduzirPeriodo($periodo){
            try{
                return TB\Periodo::traduzirPeriodo($periodo);
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
