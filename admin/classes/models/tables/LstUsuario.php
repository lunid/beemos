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
        public function calculaRespostasLista($ID_HISTORICO_GERADOC){
            try{
                //Objeto de etorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao calcular repostas da lista!";
                
                //Sql da pesquisa de respostas
                $sql = "SELECT
                            IF(LR.RESPOSTA = LR.GABARITO, 1, 0) as STATUS
                        FROM
                            SPRO_LST_USUARIO LU
                        INNER JOIN
                            SPRO_LST_HIST_RESPOSTA LR ON LR.ID_LST_USUARIO = LU.ID_LST_USUARIO
                        WHERE
                            LU.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC}                        
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
        
        public function calculaAlunosRespostasLista($ID_HISTORICO_GERADOC){
            try{
                //Objeto de etorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao calcular alunos que reponderam a lista!";
                
                //Sql da pesquisa de respostas
                $sql = "SELECT
                            IF(LR.ID_LST_HIST_RESPOSTA IS NULL, 0, 1) as STATUS
                        FROM
                            SPRO_LST_USUARIO LU
                        LEFT JOIN
                            SPRO_LST_HIST_RESPOSTA LR ON LR.ID_LST_USUARIO = LU.ID_LST_USUARIO
                        WHERE
                            LU.ID_HISTORICO_GERADOC = {$ID_HISTORICO_GERADOC}                        
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
    }
?>
