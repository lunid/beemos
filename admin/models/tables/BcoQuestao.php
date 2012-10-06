<?php
    namespace admin\models\tables;
    use \sys\classes\db\ORM;

    class BcoQuestao extends ORM {
        public function listaQuestoesTop10($id_materia = 1, $id_fonte_vestibular = 0){
            try{
                //$this->debugOn();
                
                $where = "";
                
                if($id_materia > 0){
                    $where = " CQ.ID_MATERIA = {$id_materia} ";
                }

                if($id_fonte_vestibular > 0){
                    if($where != ""){
                        $where .= " AND ";
                    }
                    $where .= " Q.ID_FONTE_VESTIBULAR = {$id_fonte_vestibular} ";
                }
                
                $objTableQuestoes                   = $this;
                $objTableQuestoes->alias            = 'Q';
                $objTableQuestoes->fieldsJoin       = 'ID_BCO_QUESTAO, TOTAL_USO, ID_FONTE_VESTIBULAR';
                
                $objClassficacaoQuestao             = new ClassificacaoQuestao();
                $objClassficacaoQuestao->alias      = 'CQ';
                $objClassficacaoQuestao->fieldsJoin = 'ID_CLASSIFICACAO';
                
                $objFonteVestibular                 = new FonteVestibular();
                $objFonteVestibular->alias          = 'FV';
                $objFonteVestibular->fieldsJoin     = 'FONTE_VESTIBULAR';
                
                $objAvaliacaoQuestao                = new AvaliacaoQuestao();
                $objAvaliacaoQuestao->alias         = 'AQ';
                $objAvaliacaoQuestao->fieldsJoin    = 'ID_AVALIACAO_QUESTAO';
                
                $objUsuarioAvaliaQuestao                = new UsuarioAvaliaQuestao();
                $objUsuarioAvaliaQuestao->alias         = 'UAQ';
                $objUsuarioAvaliaQuestao->fieldsJoin    = 'ID_USUARIO';
                
                $fieldMap = "ID_BCO_QUESTAO";
                $this->innerJoinFrom($objTableQuestoes, $objClassficacaoQuestao, $fieldMap);
                
                $fieldMap = "ID_FONTE_VESTIBULAR";
                $this->joinFromAdd($objFonteVestibular, $objTableQuestoes, $fieldMap);
                
                $fieldMap = "ID_BCO_QUESTAO";
                $this->joinFromAdd($objAvaliacaoQuestao, $objTableQuestoes, $fieldMap, "LEFT");
                
                $fieldMap = "ID_BCO_QUESTAO";
                $this->joinFromAdd($objUsuarioAvaliaQuestao, $objTableQuestoes, $fieldMap, "LEFT");
                
                $this->setOrderBy("TOTAL_USO DESC");
                $this->setLimit(10);
                
                return $this->setJoin($where);
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal - BcoQuestal - ORM <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
    }
?>

