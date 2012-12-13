<?php

    namespace common\db_tables;  

    class FonteVestibular extends \Table {
        public function getFontesSelectBox(){
            try{
                $sql = "SELECT
                            ID_FONTE_VESTIBULAR AS ID,
                            FONTE_VESTIBULAR AS TEXT
                        FROM
                            SPRO_FONTE_VESTIBULAR
                        ORDER BY
                            FONTE_VESTIBULAR
                        ;";
                
                return $this->query($sql);
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>

