<?php

    namespace db_tables;
   
    class MateriaQuestao extends \Table {
        public function getMateriasSelectBox(){
            try{
                $sql = "SELECT
                            ID_MATERIA AS ID,
                            MATERIA AS TEXT
                        FROM
                            SPRO_MATERIA_QUESTAO
                        ORDER BY
                            MATERIA
                        ;";
                
                return $this->query($sql);
            }catch(Exception $e){
                throw $e;               
            }
        }
        
        public function traduzMateria($id_materia){
            try{
                $sql = "SELECT
                            MQ.ID_MATERIA,
                            MQ.MATERIA
                        FROM
                            SPRO_MATERIA_QUESTAO MQ
                        WHERE
                            MQ.ID_MATERIA = {$id_materia}
                        LIMIT
                            1
                        ;";
                
                return $this->query($sql);
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>

