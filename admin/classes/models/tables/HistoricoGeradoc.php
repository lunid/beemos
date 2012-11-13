<?php
    namespace admin\classes\models\tables;
    use \sys\classes\db\ORM;
    
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
    class HistoricoGeradoc extends ORM {
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
    }

?>
