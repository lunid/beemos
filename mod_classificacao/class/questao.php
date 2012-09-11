<?php

require_once 'mysql.php';

class Questao{
    private $ID_BCO_QUESTAO;
    
    public function __construct($ID_BCO_QUESTAO = 0) {
        try{
            if($ID_BCO_QUESTAO > 0){
                MySQL::connect();
                
                $sql = "SELECT 
                            ID_BCO_QUESTAO 
                        FROM 
                            SPRO_BCO_QUESTAO
                        WHERE
                            ID_BCO_QUESTAO = {$ID_BCO_QUESTAO}
                        LIMIT 1
                        ;";
                            
                $rs = MySQL::executeQuery($sql);
                
                if(mysql_num_rows($rs) <= 0){
                    throw new Exception("Questão não encontrada!");
                }
                
                $this->ID_BCO_QUESTAO = mysql_result($rs, 0, 'ID_BCO_QUESTAO');
            }
        }catch(Exception $e){
            echo "============= ERRO =============<br /><br />";
            echo $e->getMessage() . "<br />";
            echo $e->getFile() . "<br />";
            echo $e->getLine() . "<br />";
            echo "============= ERRO =============<br /><br />";
        }
    }
}
?>
