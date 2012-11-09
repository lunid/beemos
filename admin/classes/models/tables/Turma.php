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
        public function listaTurmasEscolas($ID_CLIENTE = 0, $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha no ORM ao listas turmas!";
                
                //Instância da table SPRO_TURMA
                $tbTurma                = $this;
                $tbTurma->alias         = "T";
                $tbTurma->fieldsJoin    = "ID_TURMA,
                                            ID_ESCOLA,
                                            CLASSE,
                                            ANO,
                                            PERIODO,
                                            ENSINO,
                                            STATUS,
                                            DATA_REGISTRO";
                
                //Instância da table SPRO_ESCOLA
                $tbEscola              = new Escola();
                $tbEscola->alias       = "E";
                $tbEscola->fieldsJoin  = "NOME";
                
                //Campo de união do JOIN
                $fieldMap = "ID_ESCOLA";
                //Montando SQL do Inner Join
                $this->innerJoinFrom($tbTurma, $tbEscola, $fieldMap);
                                
                //Valida ID_ESCOLA
                if((int)$this->ID_ESCOLA > 0){
                    //Montando where do SQL
                    $where = " T.ID_ESCOLA = " . $this->ID_ESCOLA;
                }
                
                //Valida ID_CLIENTE
                if((int)$ID_CLIENTE > 0){
                    if($where != ""){
                        $where .= " AND ";
                    }
                    
                    $where .= "E.ID_CLIENTE = " . $ID_CLIENTE;
                }
                
                //Insere Ordenação e Paginação caso exista
                if(is_array($arrPg)){
                    //Ordenação
                    if(isset($arrPg['campoOrdenacao'])){
                        $this->setOrderBy("T" . $arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao']);
                    }
                    
                    //Paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                        $this->setLimit((int)$arrPg['inicio'], (int)$arrPg['limite']);
                    }
                }
                
                $rs = $this->setJoin($where);
                
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
