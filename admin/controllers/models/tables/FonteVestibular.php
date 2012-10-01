<?php

    namespace admin\models\tables;
    use \sys\classes\db\ORM;

    class FonteVestibular extends ORM {
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
                echo ">>>>>>>>>>>>>>> Erro Fatal - FonteVestibular - Model <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
    }

?>

