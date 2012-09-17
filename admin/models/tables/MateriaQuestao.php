<?php

    namespace app\models\tables;
    use \sys\classes\db\ORM;

    class MateriaQuestao extends ORM {

        function getTotalQuestoesEnem(){
            return $this->getTotalquestoes('TOTAL_QUESTOES_ENEM');            
        }   

        function getTotalQuestoesDb(){
            return $this->getTotalQuestoes();
        }

        private function getTotalQuestoes($cp='TOTAL_QUESTOES'){
            $table      = $this->getTable();
            $sql        = "SELECT SUM(".$cp.") FROM ".$table;
            $total      = (int)$this->queryOneCol($sql);
            return $total;            
        }
    }

?>

