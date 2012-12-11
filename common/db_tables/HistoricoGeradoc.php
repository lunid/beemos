<?php
    namespace common\db_tables;  
    
    /**
     * Representa uma entidade da tabela SPRO_HISTORICO_GERADOC
     * 
     * @property int ID_HISTORICO_GERADOC
     * @property char COD_LISTA
     * @property int LISTA_ATIVA
     * @property datetime LISTA_ATIVA_DT_HR_INI
     * @property daetime LISTA_ATIVA_DT_HR_FIM
     * @property int ST_GABARITO_ALUNO
     * @property int VER_IMPRESSA
     * @property int ID_LOGIN
     * @property int ID_LST_GRUPO
     * @property char TIPO
     * @property char FORMATO
     * @property int GERA_DEBITO
     * @property char SUFIXO
     * @property string NOME_ARQ
     * @property string DESCR_ARQ
     * @property int DEBITO
     * @property string ITENS_SELECIONADOS
     * @property string ITENS_ORDENADOS
     * @property string ORDEM_ATUAL
     * @property int NUM_QUESTOES
     * @property int NUM_DOWNLOAD
     * @property datetime DATA_REGISTRO
     * @property double TEMPO_LEITURA_SEC
     * @property char TEMPO_LEITURA_STR
     * @property double TEMPO_RESPOSTA_SEC
     * @property char TEMPO_RESPOSTA_STR
     * @property datetime DT_HR_VALIDADE
     * @property int COUNTER
     * @property int FLAG
     * @property int ANTICOLA
     * @property int DEL
     * @property string CACHE_NAV_LST
     */
    class HistoricoGeradoc extends \Table {
        /**
         * Função que efetua o INNER JOIN de DOCs com Turmas e filtra resultados através dos parâmetros enviados
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
        public function carregarListasCliente($ID_CLIENTE, $where = "", $utilizadas = 0, $ID_TURMA = 0, $arrPg = null){
            try{
                //Obejto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar listas do cliente!";
                $ret->listas    = array();
                
                //Verifica Paginação e Ordenação
                $order = "";
                $limit = "";
                
                if($arrPg != null){
                    //Monta ordeção
                    if(isset($arrPg['campoOrdenacao']) && isset($arrPg['tipoOrdenacao'])){
                        $order = " ORDER BY L." . $arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao'];
                    }
                    
                    //Monta paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limit3'])){
                        $order = " LIMIT " . $arrPg['inicio'] . ", " . $arrPg['limit3'];
                    }
                }
                
                //Montra instrução SQL
                //TODO: Utilizar ORM
                $sql = "SELECT
                            DISTINCT
                            L.ID_HISTORICO_GERADOC,
                            L.COD_LISTA,
                            L.DATA_REGISTRO,
                            L.DESCR_ARQ,
                            L.NUM_QUESTOES,
                            L.LISTA_ATIVA_DT_HR_INI,
                            L.LISTA_ATIVA_DT_HR_FIM,
                            IF(NOW() >= L.LISTA_ATIVA_DT_HR_INI AND NOW() <= L.LISTA_ATIVA_DT_HR_FIM, 'Ativa', 'Inativa') as STATUS,
                            VER_IMPRESSA,
                            (SELECT T.ID_TURMA FROM SPRO_TURMA_LISTA T WHERE T.ID_TURMA = {$ID_TURMA} AND T.ID_HISTORICO_GERADOC = L.ID_HISTORICO_GERADOC ) AS ID_TURMA
                        FROM
                            SPRO_HISTORICO_GERADOC L
                        ".($utilizadas == 1 ? "INNER JOIN SPRO_TURMA_LISTA TL ON TL.ID_HISTORICO_GERADOC = L.ID_HISTORICO_GERADOC AND TL.ID_TURMA = {$ID_TURMA}" : "")."
                        WHERE
                            L.ID_LOGIN = " . $ID_CLIENTE . " 
                        AND 
                            L.FORMATO = 'LST'
                        {$where}
                        {$order}
                        {$limit}
                        ;";
               
                //Executa query
                $rs = $this->query($sql);
                
                //Caso não seja encontrada nenhuma lista
                if(!is_array($rs) || sizeof($rs) <= 0){
                    $ret->msg = "Nenhuma lista encontrada!";
                    return $ret;
                }
                
                //Retorna listas encotradas
                $ret->status    = true;
                $ret->msg       = "Listas carregadas!";
                $ret->listas    = $rs;
                
                return $ret;
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
        public function carregarEscolasTurmasLista($ID_CLIENTE, $ID_ESCOLA = 0, $ENSINO = '', $PERIODO = '', $ANO = ''){
            try{
                //Obejto de retorno
                $ret            = new \stdClass();
                $ret->status    = true;
                $ret->msg       = "Falha ao listar Escolas e Turmas da Lista";
                
                //Objeto da tabela SPRO_ESCOLA para join
                $tbEscola               = new Escola();
                $tbEscola->alias        = "E";
                $tbEscola->fieldsJoin   = "ID_ESCOLA,
                                           NOME";
                
                //Objeto da tabela SPRO_TURMA para join
                $tbTurma                = new Turma();
                $tbTurma->alias         = "T";
                $tbTurma->fieldsJoin    = "ID_TURMA,
                                           CLASSE,
                                           ANO,
                                           PERIODO,
                                           ENSINO";
                
                //Objeto da tabela SPRO_TURMA para join
                $tbTurmaLista               = new TurmaLista();
                $tbTurmaLista->alias        = "TL";
                
                //Objeto da tabela SPRO_HISTORICO_GERADOC (LISTAS) para join
                $tbLista        = $this;
                $tbLista->alias = "L";
                
                //Inicia Joins
                $this->joinFrom     ($tbEscola      , $tbTurma      , 'ID_ESCOLA');
                $this->joinFromAdd  ($tbTurmaLista  , $tbTurma      , 'ID_TURMA');
                $this->joinFromAdd  ($tbLista       , $tbTurmaLista , 'ID_HISTORICO_GERADOC');
                
                //Executa select com os Joins
                $where = ""; //WHERE da query
                
                //Filtro por Escola
                if($ID_ESCOLA > 0){
                    $where .= " AND E.ID_ESCOLA = {$ID_ESCOLA} ";
                }
                
                //Filtro de Ensinos
                if($ENSINO != "'0'" && $ENSINO != "''" && $ENSINO != "" && $ENSINO != null){
                    $where .= " AND T.ENSINO IN ({$ENSINO}) ";
                }
                
                //Filtro de Períodos
                if($PERIODO != "'0'" && $PERIODO != "''" && $PERIODO != "" && $PERIODO != null){
                    $where .= " AND T.PERIODO IN ({$PERIODO}) ";
                }
                
                //Filtro de Anos
                if($ANO != 0 && $ANO != "" && $ANO != null){
                    $where .= " AND T.ANO IN ({$ANO}) ";
                }

                //Executa Select com Joins e Where
                $rs = $this->setJoin("E.ID_CLIENTE = {$ID_CLIENTE} AND L.ID_HISTORICO_GERADOC = {$this->ID_HISTORICO_GERADOC} {$where}");
                
                //Verifica retorno
                if(sizeof($rs) <= 0){
                    $ret->msg = "Nenhuma Escola encotrada!";
                    return $ret;
                }
                
                //Array de retorno
                $arrEscolas = array();
                $arrEnsino  = array();
                $arrTurmas  = array();
                $arrAno     = array();
                $arrPeriodo = array();
                
                //Monta Array
                foreach($rs as $row){
                    //Array de escolas encontradas
                    if(!array_key_exists($row['ID_ESCOLA'], $arrEscolas)){
                        $arrEscolas[$row['ID_ESCOLA']] = array(
                            "ID_ESCOLA"     => $row['ID_ESCOLA'],
                            "ESCOLA"        => $row['NOME'],
                        );
                    }
                    
                    //Array de turmas encontradas
                    if(!array_key_exists($row['ID_TURMA'], $arrTurmas)){
                        $arrTurmas[$row['ID_TURMA']] = array(
                            "ID_TURMA"     => $row['ID_TURMA'],
                            "CLASSE"       => $row['CLASSE'],
                        );
                    }
                    
                    //Array de ensinos encontrados
                    if(!array_key_exists($row['ENSINO'], $arrEnsino)){
                        $arrEnsino[$row['ENSINO']] = array(
                            "ENSINO"    => $row['ENSINO'],
                            "DESC"      => Ensino::traduzirEnsino($row['ENSINO']),
                        );
                    }
                    
                    //Array de períodos encontrados
                    if(!array_key_exists($row['PERIODO'], $arrPeriodo)){
                        $arrPeriodo[$row['PERIODO']] = array(
                            "PERIODO"   => $row['PERIODO'],
                            "DESC"      => Periodo::traduzirPeriodo($row['PERIODO']),
                        );
                    }
                    
                    //Array de Anos encontrados
                    if(!in_array($row['ANO'], $arrAno)){
                        $arrAno[] = $row['ANO'];
                    }
                }
                
                //Ordenação de arrays
                ksort($arrEscolas);
                ksort($arrTurmas);
                ksort($arrEnsino);
                ksort($arrPeriodo);
                sort($arrAno);
                
                //Retorno OK
                $ret->status            = true;
                $ret->msg               = "Escolas e Turmas da Lista carregadas com sucesso!";
                $ret->arrEscolas        = $arrEscolas;
                $ret->arrTurmas         = $arrTurmas;
                $ret->arrEnsino         = $arrEnsino;
                $ret->arrPeriodo        = $arrPeriodo;
                $ret->arrAno            = $arrAno;
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que lista os Alunos de uma terminada Lista
         * 
         * @param string $where Where para comando SQL
         * @param array $arrPg Array com parâmetros para Ordenação e Paginação
         * @param boolean $responderam TRUE - Todos que responderam a lista | FALSE - Todos que abriram e/ou responderam a lista
         * 
         * <code>
         * array(
         *   "campoOrdenacao"    => 'ALUNO', 
         *   "tipoOrdenacao"     => 'ASC', 
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
         *  array   $ret->alunos    - Armazena o array de alunos encontrados no Banco   <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregarAlunosLista($where = '', $arrPg = null, $responderam = true){
            try{
                //Obejto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar Alunos da Lista";
                
                //Ordenação e Paginação
                $orderPg = "";
                if($arrPg != null){
                    //VErifica se o usuário deseja ordenar por conclusão
                    $arrPg['campoOrdenacao'] = $arrPg['campoOrdenacao'] == 'CONCLUIDA' ? 'HR.ID_LST_HIST_RESPOSTA' : $arrPg['campoOrdenacao'];
                    
                    $orderPg = "ORDER BY
                                    {$arrPg['campoOrdenacao']} {$arrPg['tipoOrdenacao']}
                                LIMIT
                                    {$arrPg['inicio']}, {$arrPg['limite']}
                    ;";
                }
                
                $sql = "SELECT
                            L.ID_HISTORICO_GERADOC,
                            LU.ID_CLIENTE,
                            C.NOME_PRINCIPAL AS ALUNO,
                            E.NOME AS ESCOLA,
                            T.CLASSE AS TURMA,
                            IF(HR.ID_LST_HIST_RESPOSTA IS NULL, 0, 1) CONCLUIDA
                        FROM
                            SPRO_HISTORICO_GERADOC L
                        INNER JOIN
                            SPRO_LST_USUARIO LU ON LU.ID_HISTORICO_GERADOC = L.ID_HISTORICO_GERADOC
                        " . ($responderam ? "INNER" : "LEFT") . " JOIN
                            SPRO_LST_HIST_RESPOSTA HR ON HR.ID_LST_USUARIO = LU.ID_LST_USUARIO
                        INNER JOIN
                            SPRO_CLIENTE C ON C.ID_CLIENTE = LU.ID_CLIENTE
                        LEFT JOIN
                            SPRO_TURMA_ALUNO TA ON TA.ID_CLIENTE = LU.ID_CLIENTE
                        LEFT JOIN
                            SPRO_TURMA T ON T.ID_TURMA = TA.ID_TURMA
                        LEFT JOIN
                            SPRO_ESCOLA E ON E.ID_ESCOLA = T.ID_ESCOLA
                        WHERE
                            L.ID_HISTORICO_GERADOC = {$this->ID_HISTORICO_GERADOC}
                        {$where}
                        GROUP BY
                            LU.ID_CLIENTE
                        {$orderPg}
                        ;";
                
                $rs = $this->query($sql);
                
                //Verifica retorno
                if(sizeof($rs) <= 0){
                    $ret->msg = "Nenhum Aluno encotrado!";
                    return $ret;
                }
                
                //Retorno OK
                $ret->status            = true;
                $ret->msg               = "Alunos carregados com sucesso!";
                $ret->alunos            = $rs;
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Soma os débitos de um cliente
         * 
         * @param int $idCliente ID do cliente
         * @param string $dtRegistro Período de início da consulta
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->count     - Total de registros encontrados                    <br />
         *  array   $ret->debitos   - Array com débitos encontrados                     <br />
         * </code>
         * @throws \db_tables\Exception
         */
        public function somarDebitosCliente($idCliente, $dtRegistro){
            try{
                $sql = "SELECT
                            SUM(DEBITO) AS DEBITOS
                        FROM
                            SPRO_HISTORICO_GERADOC
                        WHERE
                            ID_LOGIN = {$idCliente}
                        AND
                            DATA_REGISTRO >= '{$dtRegistro}' 
                        GROUP BY 
                            ID_LOGIN
                        ;";
                            
                //Executa Select
                $rs = $this->query($sql);
                
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = true;
                $ret->msg       = "Consulta efetuada com sucesso!";
                $ret->count     = sizeof($rs);
                $ret->debitos   = $rs[0];
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>
