<?php
    namespace admin\models\tables;
    use \sys\classes\db\ORM;

    class AvaliacaoQuestao extends ORM {
        /**
         * Exclui todas as avaliações de uma questão.
         * 
         * @param int $id_questao
         * @return boolean
         * @throws Exception
         */
        public function excluiQuestao($id_questao, $id_usuario){
            try{
                $arrWhere = array("ID_BCO_QUESTAO = %i AND ID_USUARIO = %i", $id_questao, $id_usuario);
                $this->delete($arrWhere);
                
                return TRUE;
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>
