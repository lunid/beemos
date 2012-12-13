<?php
    namespace common\db_tables;  
 
    class UsuarioAvaliaQuestao extends \Table {
        /**
         * Exclui todos os registros de um determinado usuário da tabela USUARIO_AVALIA_QUESTAO
         * 
         * @param int $id_usuario
         * @return boolean
         * @throws Exception
         */
        public function excluiUsuario($id_questao, $id_usuario){
            try{
                $arrWhere = array("ID_USUARIO = %i AND ID_BCO_QUESTAO = %i", $id_usuario, $id_questao);
                $this->delete($arrWhere);
                
                return TRUE;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Insere um novo registro para o Usuário que vai avaliar uma questão
         * 
         * @param int $id_usuario Código do Usuário
         * @param int $id_questao Código da Questão
         * 
         * @return stdClass $ret <br />
         * $ret->status (boolean) Status final da operação <br />
         * $ret->msg (string) Mensagem de retorno <br />
         * 
         * @throws Exception
         */
        public function insereUsuarioAvaliaQuestao($id_usuario, $id_questao){
            try{
                $ret            = new \stdClass();
                $ret->status    = FALSE;
                $ret->msg       = "Falha ao inserir usuário de avaliação!";
                
                $this->ID_USUARIO       = $id_usuario;
                $this->ID_BCO_QUESTAO   = $id_questao;
                $this->DATA_INDICACAO   = date("Y-m-d H:i:s");
                
                $this->save();
                
                $ret->status    = TRUE;
                $ret->msg       = "Usuário de avaliação inserido com sucesso";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
