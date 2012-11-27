<?php
    namespace admin\classes\models\tables;
    use \sys\classes\db\ORM;
    
    /**
     * Representa uma entidade da tabela SPRO_LST_USUARIO
     * 
     * @property int ID_LST_USUARIO
     * @property int ID_HISTORICO_GERADOC
     * @property int ID_CLIENTE
     * @property int ID_LST_TIPO_USER
     * @property int NUM_QUESTOES
     * @property string ORDEM_QUESTOES_DINAMICAS
     * @property int NUM_QUESTOES_ESTATICAS
     * @property int NUM_QUESTOES_DINAMICAS
     * @property string LST_QUESTOES_ESTATICAS
     * @property string LST_QUESTOES_DINAMICAS
     * @property string RESPOSTAS_TEMP
     * @property string MSG_EMAIL_ENV
     * @property string DT_HR_EMAIL_ENV
     * @property string DATA_REGISTRO
     */
    class LstUsuario extends ORM {
        /**
         * Calcula total de questões respondidas em uma lista, assim como o 
         * total de respostas corretas e erradas
         * 
         * @param int $ID_HISTORICO_GERADOC ID da lista
         * @param int $ID_ESCOLA ID da Escola
         * @param string $ENSINO String com um ou mais ensinos a serem filtrados. Ex: 'M','F'
         * @param string $PERIODO String com um ou mais períodos a serem filtrados. Ex: 'N','M'
         * @param string $ANO String com um ou mais Anos a serem filtrados. Ex: 1,3
         * @param string $TURMA String com uma ou mais Turmas a serem filtradas. Ex: 12,55,74
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
                //Objeto de etorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao calcular repostas da lista!";
                
                //WHERE
                $where = "";
                
                //Filtro de escola
                if($ID_ESCOLA > 0){
                    $where .= " AND T.ID_ESCOLA = {$ID_ESCOLA} ";
                }
                
                //Filtro de ensino
                if($ENSINO != "'0'" && $ENSINO != "''" && $ENSINO != "" && $ENSINO != null){
                    $where .= " AND T.ENSINO IN ({$ENSINO}) ";
                }
                
                //Filtro de período
                if($PERIODO != "'0'" && $PERIODO != "''" && $PERIODO != "" && $PERIODO != null){
                    $where .= " AND T.PERIODO IN ({$PERIODO}) ";
                }
                
                //Filtro de amo
                if($ANO != "0" && $ANO != "" && $ANO != null){
                    $where .= " AND T.ANO IN ({$ANO}) ";
                }
                
                //Filtro de turmas
                if($TURMA != "0" && $TURMA != "" && $TURMA != null){
                    $where .= " AND T.ID_TURMA IN ({$TURMA}) ";
                }
                
                $INNER_JOIN = "";
                if($where != ""){
                    $INNER_JOIN = " 
                        INNER JOIN
                            SPRO_TURMA_ALUNO TA ON TA.ID_CLIENTE = LU.ID_CLIENTE
                        INNER JOIN
                            SPRO_TURMA T ON T.ID_TURMA = TA.ID_TURMA
                        INNER JOIN
                            SPRO_TURMA_LISTA TL ON TL.ID_TURMA = T.ID_TURMA
                        ";
                }
                
                //Sql da pesquisa de respostas
                $sql = "SELECT
                            IF(LR.RESPOSTA = LR.GABARITO, 1, 0) as STATUS
                        FROM
                            SPRO_LST_USUARIO LU
                        INNER JOIN
                            SPRO_LST_HIST_RESPOSTA LR ON LR.ID_LST_USUARIO = LU.ID_LST_USUARIO
                        {$INNER_JOIN}
                        WHERE
                            LU.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC}
                        {$where}
                        ;";
                
                //Executa SQL
                $rs = $this->query($sql);
                
                //Se não houver nenhuma resposta
                if(sizeof($rs) <= 0){
                    $ret->msg = "Nenhuma resposta encontrada!";
                    return $ret;
                }
                
                //Se forem encontradas as respostas
                $ret->total     = sizeof($rs); //total de questões respondidas
                $ret->correta   = 0; //contador de corretas
                $ret->errada    = 0; //contador de incorretas
                
                foreach($rs as $resposta){
                    if((int)$resposta['STATUS'] == 1){
                        //Se for correta soma contador
                        $ret->correta++;
                    }else if((int)$resposta['STATUS'] == 0){
                        //Se for incorreta soma contador
                        $ret->errada++;
                    }
                }
                
                //Retorno final
                $ret->status    = true;
                $ret->msg       = "Respostas encontradas!";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que calcula o total de alunos que respoderam as questões da Lista
         * e o total de alunos que abriram a lista mas não terminaram (não respoderam)
         * 
         * @param int $ID_HISTORICO_GERADOC ID da Lista
         * @param int $ID_ESCOLA ID da Escola
         * @param string $ENSINO String com um ou mais ensinos a serem filtrados. Ex: 'M','F'
         * @param string $PERIODO String com um ou mais períodos a serem filtrados. Ex: 'N','M'
         * @param string $ANO String com um ou mais Anos a serem filtrados. Ex: 1,3
         * @param string $TURMA String com uma ou mais Turmas a serem filtradas. Ex: 12,55,74
         * 
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
                //Objeto de etorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao calcular alunos que reponderam a lista!";
                
                //WHERE
                $where = "";
                
                //Filtro de escola
                if($ID_ESCOLA > 0){
                    $where .= " AND T.ID_ESCOLA = {$ID_ESCOLA} ";
                }
                
                //Filtro de ensino
                if($ENSINO != "'0'" && $ENSINO != "''" && $ENSINO != "" && $ENSINO != null){
                    $where .= " AND T.ENSINO IN ({$ENSINO}) ";
                }
                
                //Filtro de período
                if($PERIODO != "'0'" && $PERIODO != "''" && $PERIODO != "" && $PERIODO != null){
                    $where .= " AND T.PERIODO IN ({$PERIODO}) ";
                }
                
                //Filtro de amo
                if($ANO != "0" && $ANO != "" && $ANO != null){
                    $where .= " AND T.ANO IN ({$ANO}) ";
                }
                
                //Filtro de turmas
                if($TURMA != "0" && $TURMA != "" && $TURMA != null){
                    $where .= " AND T.ID_TURMA IN ({$TURMA}) ";
                }
                
                $INNER_JOIN = "";
                if($where != ""){
                    $INNER_JOIN = " 
                        INNER JOIN
                            SPRO_TURMA_ALUNO TA ON TA.ID_CLIENTE = LU.ID_CLIENTE
                        INNER JOIN
                            SPRO_TURMA T ON T.ID_TURMA = TA.ID_TURMA
                        INNER JOIN
                            SPRO_TURMA_LISTA TL ON TL.ID_TURMA = T.ID_TURMA
                        ";
                }
                
                //Sql da pesquisa de alunos
                $sql = "SELECT
                            IF(LR.ID_LST_HIST_RESPOSTA IS NULL, 0, 1) as STATUS
                        FROM
                            SPRO_LST_USUARIO LU
                        LEFT JOIN
                            SPRO_LST_HIST_RESPOSTA LR ON LR.ID_LST_USUARIO = LU.ID_LST_USUARIO
                        {$INNER_JOIN}
                        WHERE
                            LU.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC}      
                        {$where}
                        GROUP BY
                            LU.ID_LST_USUARIO
                        ;";
                
                //Executa SQL
                $rs = $this->query($sql);
                
                //Se não houver nenhuma resposta
                if(sizeof($rs) <= 0){
                    $ret->msg = "Nenhum aluno encontrado!";
                    return $ret;
                }
                
                //Se forem encontradas as respostas
                $ret->total         = sizeof($rs); //total de alunos 
                $ret->respondeu     = 0; //contador de alunos que responderam
                $ret->naoRespondeu  = 0; //contador de alunos que abriram e não finalizaram ainda
                
                foreach($rs as $aluno){
                    if((int)$aluno['STATUS'] == 1){
                        //Se for correta soma contador
                        $ret->respondeu++;
                    }else if((int)$aluno['STATUS'] == 0){
                        //Se for incorreta soma contador
                        $ret->naoRespondeu++;
                    }
                }
                
                //Retorno final
                $ret->status    = true;
                $ret->msg       = "Alunos encontradas!";
                
                return $ret;
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
         * @param int $ID_ESCOLA ID da Escola
         * @param string $ENSINO String com um ou mais ensinos a serem filtrados. Ex: 'M','F'
         * @param string $PERIODO String com um ou mais períodos a serem filtrados. Ex: 'N','M'
         * @param string $ANO String com um ou mais Anos a serem filtrados. Ex: 1,3
         * @param string $TURMA String com uma ou mais Turmas a serem filtradas. Ex: 12,55,74
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
                //Objeto de etorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao calcular repostas da lista!";
                
                //Carrega Lista e sesus dados
                $tbListas = new HistoricoGeradoc($ID_HISTORICO_GERADOC);
                
                //Caso a Lista não seja encontrada, é retornado um erro
                if($tbListas->ID_HISTORICO_GERADOC <= 0){
                    $ret->msg = "Lista não encontrada!";
                    return $ret;
                }
                
                //WHERE
                $where = "";
                
                //Filtro de escola
                if($ID_ESCOLA > 0){
                    $where .= " AND T.ID_ESCOLA = {$ID_ESCOLA} ";
                }
                
                //Filtro de ensino
                if($ENSINO != "'0'" && $ENSINO != "''" && $ENSINO != "" && $ENSINO != null){
                    $where .= " AND T.ENSINO IN ({$ENSINO}) ";
                }
                
                //Filtro de período
                if($PERIODO != "'0'" && $PERIODO != "''" && $PERIODO != "" && $PERIODO != null){
                    $where .= " AND T.PERIODO IN ({$PERIODO}) ";
                }
                
                //Filtro de amo
                if($ANO != "0" && $ANO != "" && $ANO != null){
                    $where .= " AND T.ANO IN ({$ANO}) ";
                }
                
                //Filtro de turmas
                if($TURMA != "0" && $TURMA != "" && $TURMA != null){
                    $where .= " AND T.ID_TURMA IN ({$TURMA}) ";
                }
                
                $INNER_JOIN = "";
                if($where != ""){
                    $INNER_JOIN = " 
                        INNER JOIN
                            SPRO_TURMA_ALUNO TA ON TA.ID_CLIENTE = LU.ID_CLIENTE
                        INNER JOIN
                            SPRO_TURMA T ON T.ID_TURMA = TA.ID_TURMA
                        INNER JOIN
                            SPRO_TURMA_LISTA TL ON TL.ID_TURMA = T.ID_TURMA
                        ";
                }
                
                //Sql da pesquisa de respostas e alunos
                $sql = "SELECT
                            IF(LR.RESPOSTA = LR.GABARITO, 1, 0) as STATUS,
                            LU.ID_LST_USUARIO
                        FROM
                            SPRO_LST_USUARIO LU
                        INNER JOIN
                            SPRO_LST_HIST_RESPOSTA LR ON LR.ID_LST_USUARIO = LU.ID_LST_USUARIO
                        {$INNER_JOIN}
                        WHERE
                            LU.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC}                        
                        {$where}
                        ;";
                
                //Executa SQL
                $rs = $this->query($sql);
                
                //Se não houver nenhuma resposta
                if(sizeof($rs) <= 0){
                    $ret->msg = "Nenhuma resposta encontrada!";
                    return $ret;
                }
                
                //Contador de respostas corretas
                $corretas   = 0;
                //Aray de alunos que responderam
                $alunos     = array(); 
                
                foreach($rs as $resposta){
                    if(!in_array($resposta['ID_LST_USUARIO'], $alunos)){
                        $alunos[] = $resposta['ID_LST_USUARIO'];
                    }
                    
                    if((int)$resposta['STATUS'] == 1){
                        //Se for correta soma contador
                        $corretas++;
                    }
                }
                
                //Total de questões * alunos
                $totalQuestoes          = $tbListas->NUM_QUESTOES * sizeof($alunos);
                $ret->aproveitamento    = round(($corretas / $totalQuestoes) * 100, 0);
                
                //Retorno final
                $ret->status    = true;
                $ret->msg       = "Respostas encontradas!";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Calcula o aproveitamento de cadas questão da lista
         * 
         * @param int $ID_HISTORICO_GERADOC ID da Lista
         * @param int $ID_ESCOLA ID da Escola
         * @param string $ENSINO String com um ou mais ensinos a serem filtrados. Ex: 'M','F'
         * @param string $PERIODO String com um ou mais períodos a serem filtrados. Ex: 'N','M'
         * @param string $ANO String com um ou mais Anos a serem filtrados. Ex: 1,3
         * @param string $TURMA String com uma ou mais Turmas a serem filtradas. Ex: 12,55,74
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status            - Retorna TRUE ou FALSE para o status do Método                                         <br />
         *  string  $ret->msg               - Armazena mensagem ao usuário                                                          <br />
         *  array   $ret->questoes          - Array com todas as questões da lista e cada uma com sua quantidade de acertos e erros <br />
         * </code>
         * 
         * @throws Exception
         */
        public function calculaAproveitamentoQuestao($ID_HISTORICO_GERADOC, $ID_ESCOLA = 0, $ENSINO = '', $PERIODO = '', $ANO = '', $TURMA = ''){
            try{
                //Objeto de etorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao calcular repostas da lista!";
                
                //Carrega Lista e sesus dados
                $tbListas = new HistoricoGeradoc($ID_HISTORICO_GERADOC);
                
                //Caso a Lista não seja encontrada, é retornado um erro
                if($tbListas->ID_HISTORICO_GERADOC <= 0){
                    $ret->msg = "Lista não encontrada!";
                    return $ret;
                }
                
                //WHERE
                $where = "";
                
                //Filtro de escola
                if($ID_ESCOLA > 0){
                    $where .= " AND T.ID_ESCOLA = {$ID_ESCOLA} ";
                }
                
                //Filtro de ensino
                if($ENSINO != "'0'" && $ENSINO != "''" && $ENSINO != "" && $ENSINO != null){
                    $where .= " AND T.ENSINO IN ({$ENSINO}) ";
                }
                
                //Filtro de período
                if($PERIODO != "'0'" && $PERIODO != "''" && $PERIODO != "" && $PERIODO != null){
                    $where .= " AND T.PERIODO IN ({$PERIODO}) ";
                }
                
                //Filtro de amo
                if($ANO != "0" && $ANO != "" && $ANO != null){
                    $where .= " AND T.ANO IN ({$ANO}) ";
                }
                
                //Filtro de turmas
                if($TURMA != "0" && $TURMA != "" && $TURMA != null){
                    $where .= " AND T.ID_TURMA IN ({$TURMA}) ";
                }
                
                $INNER_JOIN = "";
                if($where != ""){
                    $INNER_JOIN = " 
                        INNER JOIN
                            SPRO_TURMA_ALUNO TA ON TA.ID_CLIENTE = LU.ID_CLIENTE
                        INNER JOIN
                            SPRO_TURMA T ON T.ID_TURMA = TA.ID_TURMA
                        INNER JOIN
                            SPRO_TURMA_LISTA TL ON TL.ID_TURMA = T.ID_TURMA
                        ";
                }
                
                //Sql da pesquisa de respostas e alunos
                $sql = "SELECT
                            IF(LR.RESPOSTA = LR.GABARITO, 1, 0) as STATUS,
                            LR.ID_BCO_QUESTAO
                        FROM
                            SPRO_LST_USUARIO LU
                        INNER JOIN
                            SPRO_LST_HIST_RESPOSTA LR ON LR.ID_LST_USUARIO = LU.ID_LST_USUARIO
                        {$INNER_JOIN}
                        WHERE
                            LU.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC}                        
                        {$where}
                        ORDER BY
                            LR.ID_BCO_QUESTAO
                        ;";
                
                //Executa SQL
                $rs = $this->query($sql);
                
                //Se não houver nenhuma resposta
                if(sizeof($rs) <= 0){
                    $ret->msg = "Nenhuma resposta encontrada!";
                    return $ret;
                }
                
                //Array de alunos que responderam
                $questoes   = array(); 
                $tmp        = 0;
                $cont       = 0;
                
                foreach($rs as $questao){
                    if($tmp != $questao['ID_BCO_QUESTAO']){
                        if($tmp != 0){
                            $cont++;
                        }
                        
                        $questoes[$cont] = array(
                            'ID_BCO_QUESTAO'    => $questao['ID_BCO_QUESTAO'],
                            'corretas'          => 0,
                            'erradas'           => 0
                        );
                        
                        $tmp = $questao['ID_BCO_QUESTAO'];
                    }
                    
                    if((int)$questao['STATUS'] == 1){
                        //Se for correta soma contador de corretas da questão
                        $questoes[$cont]['corretas']++;
                    }else{
                        //Se for correta soma contador de erradas da questão
                        $questoes[$cont]['erradas']++;
                    }                  
                }
                
                for($i=0; $i < sizeof($questoes); $i++){
                    $questoes[$i]['aproveitamento'] = round(($questoes[$i]['corretas'] / ($questoes[$i]['corretas'] + $questoes[$i]['erradas'])) * 100, 2);
                }
                
                //Retorno final
                $ret->status    = true;
                $ret->msg       = "Questões contradas com sucesso!";
                $ret->questoes  = $questoes;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Calcula o aproveitamento de um determinado aluno em uma lista
         * 
         * @param int $ID_HISTORICO_GERADOC ID da Lista
         * @param int $ID_CLIENTE ID do Aluno
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status            - Retorna TRUE ou FALSE para o status do Método                                         <br />
         *  string  $ret->msg               - Armazena mensagem ao usuário                                                          <br />
         *  double  $ret->aproveitamento    - Aproveitamento do Aluno na Lista                                                      <br />
         * </code>
         * 
         * @throws Exception
         */
        public function calculaAproveitamentoAluno($ID_HISTORICO_GERADOC, $ID_CLIENTE){
            try{
                //Objeto de etorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao calcular aproveitamento do Aluno!";
                
                //Sql da pesquisa de respostas e alunos
                $sql = "SELECT
                            IF(LR.RESPOSTA = LR.GABARITO, 1, 0) as STATUS
                        FROM
                            SPRO_LST_USUARIO LU
                        INNER JOIN
                            SPRO_LST_HIST_RESPOSTA LR ON LR.ID_LST_USUARIO = LU.ID_LST_USUARIO
                        WHERE
                            LU.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC}                        
                        AND
                            LU.ID_CLIENTE = {$ID_CLIENTE}
                        ORDER BY
                            LR.ID_BCO_QUESTAO
                        ;";
                            
                $rs = $this->query($sql);
                
                //Verifica se houve retorno
                if(sizeof($rs) <= 0){
                    $ret->msg = "Nenhuma resposta encontrada!";
                    return $ret;
                }
                
                //Contadore de corretas
                $corretas = 0;
                $questoes = sizeof($rs);
                
                foreach($rs as $questao){
                    if((int)$questao['STATUS'] == 1){
                        //Se for correta soma contador de corretas da questão
                        $corretas++;
                    }                  
                }
                
                //Nome do Aluno
                $tbCliente = new Cliente($ID_CLIENTE);
                
                //Retorno OK
                $ret->status            = true;
                $ret->msg               = "Respostas encontradas!";
                $ret->aluno             = $tbCliente->NOME_PRINCIPAL;
                $ret->aproveitamento    = round(($corretas / $questoes) * 100, 2);
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
