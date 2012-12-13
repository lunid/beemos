<?php
    namespace common\db_tables;  

    class ClassificacaoQuestao extends \Table {
        public function getMateriasQuestao($id_bco_questao){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                
                $sql = "SELECT
                            DISTINCT
                            MQ.ID_MATERIA,
                            MQ.MATERIA
                        FROM
                            SPRO_MATERIA_QUESTAO MQ
                        INNER JOIN
                            SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_MATERIA = MQ.ID_MATERIA
                        WHERE
                            CQ.ID_BCO_QUESTAO = {$id_bco_questao}
                        ;";
                
                $rs_materia = $this->query($sql);
                
                if(sizeof($rs_materia) <= 0){
                    throw new Exception("Falha ao carregar matéria(s) da Questão");
                }else{
                    $txt_materias   = '';
                    $in_materias    = '';
                    
                    foreach ($rs_materia as $row_materia) {
                        if($txt_materias != ""){
                            $txt_materias .= ", ";
                        }

                        if($in_materias != ""){
                            $in_materias .= ",";
                        }

                        $txt_materias   .= $row_materia['MATERIA'];
                        $in_materias    .= $row_materia['ID_MATERIA'];
                    }
                    
                    $ret->status        = true;
                }
                
                $ret->txt_materias  = $txt_materias;
                $ret->in_materias   = $in_materias;
                            
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>
