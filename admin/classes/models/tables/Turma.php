<?php
    namespace admin\classes\models\tables;
    use \sys\classes\db\ORM;
    
    /**
     * Representa uma entidade da tabela SPRO_ESCOLA
     * 
     * @property int ID_TURMA
     * @property int ID_ESCOLA
     * @property string CLASSE
     * @property int ANO
     * @property char PERIODO
     * @property char ENSINO
     * @property int STATUS
     * @property datetime DATA_REGISTRO
     */
    class Turma extends ORM {
        /**
         * Efetua a relação TURMA > ESCOLA e listas as turmas de um Cliente
         * 
         * @param int $ID_CLIENTE
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->turmas    - Armazena os resultados (se encontrados)           <br />
         * </code>
         * 
         * @throws Exception
         */
        public function listaTurmasEscolas($ID_CLIENTE = 0, $utilizadas = 0, $ID_HISTORICO_GERADOC = 0, $where = "", $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha no ORM ao listas turmas!";
                
                
//                //Instância da table SPRO_TURMA
//                $tbTurma                = $this;
//                $tbTurma->alias         = "T";
//                $tbTurma->fieldsJoin    = "ID_TURMA,
//                                            ID_ESCOLA,
//                                            CLASSE,
//                                            ANO,
//                                            PERIODO,
//                                            ENSINO,
//                                            STATUS,
//                                            DATA_REGISTRO";
//                
//                //Instância da table SPRO_ESCOLA
//                $tbEscola              = new Escola();
//                $tbEscola->alias       = "E";
//                $tbEscola->fieldsJoin  = "NOME AS ESCOLA";
//                
//                //Campo de união do JOIN
//                $fieldMap = "ID_ESCOLA";
//                //Montando SQL do Inner Join
//                $this->innerJoinFrom($tbTurma, $tbEscola, $fieldMap);
//                
//                
//                //Instância do objeto Listas
//                $tbTurmaLista               = new TurmaLista();
//                $tbTurmaLista->alias        = "TL";
//                $tbTurmaLista->fieldsJoin   = "ID_HISTORICO_GERADOC";
//                
//                
//                ".($utilizadas == 1 ? "INNER JOIN SPRO_TURMA_LISTA TL ON TL.ID_HISTORICO_GERADOC = L.ID_HISTORICO_GERADOC AND TL.ID_TURMA = {$ID_TURMA}" : "")."
                
                
                //Valida ID_ESCOLA
                if((int)$this->ID_ESCOLA > 0){
                    //Montando where do SQL
                    $where = " AND T.ID_ESCOLA = " . $this->ID_ESCOLA;
                }
                
                //Insere Ordenação e Paginação caso exista
                $order = "";
                $limit = "";
                if(is_array($arrPg)){
                    //Ordenação
                    if(isset($arrPg['campoOrdenacao'])){
                        $order = " ORDER BY " . ($arrPg['campoOrdenacao'] != 'ESCOLA' ? "T" : "") . $arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao'];
                    }
                    
                    //Paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                        $limit = " LIMIT " . (int)$arrPg['inicio'] . ", " . (int)$arrPg['limite'];
                    }
                }
                
                $sql = "SELECT
                            DISTINCT
                            T.ID_TURMA,
                            T.ID_ESCOLA,
                            T.CLASSE,
                            T.ANO,
                            T.PERIODO,
                            T.ENSINO,
                            T.STATUS,
                            T.DATA_REGISTRO,
                            E.NOME AS ESCOLA,
                            (SELECT TL.ID_HISTORICO_GERADOC FROM SPRO_TURMA_LISTA TL WHERE TL.ID_TURMA = T.ID_TURMA AND TL.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC} ) AS ID_HISTORICO_GERADOC
                        FROM
                            SPRO_TURMA T
                        INNER JOIN
                            SPRO_ESCOLA E ON E.ID_ESCOLA = T.ID_ESCOLA
                        ".($utilizadas == 1 ? "INNER JOIN SPRO_TURMA_LISTA L ON L.ID_TURMA = T.ID_TURMA AND L.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC}" : "")."
                        WHERE
                            E.ID_CLIENTE = {$ID_CLIENTE}
                        {$where}
                        {$order}
                        {$limit}
                        ;";
                
                $rs = $this->query($sql);
                
                if(!is_array($rs) || sizeof($rs) <= 0){
                    $ret->msg = "Nenhuma Turma encontrada!";
                    return $ret;
                }
                
                //Retorna o status TRUE e po Array de turmas
                $ret->status    = true;
                $ret->msg       = "Turmas listadas com sucesso!";
                $ret->turmas    = $rs;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
