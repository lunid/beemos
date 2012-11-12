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
        public function carregaListasCliente($ID_CLIENTE, $ID_ESCOLA){
            try{
                //Objeto da tabela SPRO_HISTORICO_GERADOC
                $tbListas               = $this;
                $tbListas->alias        = "L";
                $tbListas->fieldsJoin   = "COD_LISTA,
                                            LISTA_ATIVA_DT_HR_INI";
                
                //Objeto da tabela SPRO_HISTORICO_ESCOLA
                $tbEscolas              = new Escola();
                $tbEscolas->alias       = "E";
                
                $fieldMap = array("ID_LOGIN = ID_CLIENTE");
                
                $this->innerJoinFrom($tbListas, $tbEscolas, $fieldMap);
                $rs = $this->setJoin();
                
                echo "<pre style='color:#FF0000;'>";
                print_r($rs);
                echo "</pre>";
                die;
            }catch(Exception $e){
                throw $e;
            }      
        }
    }

?>
