<?php

    namespace admin\models\tables;
    use \sys\classes\db\ORM;

    class MateriaQuestao extends ORM {
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
                echo ">>>>>>>>>>>>>>> Erro Fatal - MateriaQuestao - Model <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
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
                echo ">>>>>>>>>>>>>>> Erro Fatal - MateriaQuestao - Model <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
    }

?>

