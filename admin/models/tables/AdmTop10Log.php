<?php

    namespace admin\models\tables;
    use \sys\classes\db\ORM;

    class AdmTop10Log extends ORM {
        public function getTop10Periodo($data_inicio, $data_final, $id_materia = 0, $id_fonte_vestibular = 0){
            try{
                //$this->debugOn();
                
                $where = "DATE(DATA_LOG) BETWEEN '{$data_inicio}' AND '{$data_final}'
                            AND
                                ID_MATERIA = {$id_materia}
                            AND
                                ID_FONTE_VESTIBULAR = {$id_fonte_vestibular}";
                
                $this->setOrderBy("DATA_LOG");
                return $this->findAll($where);
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>

