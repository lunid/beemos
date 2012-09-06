<?php

require_once 'mysql.php';

class Classificacao{
    private $ID_CLASSIFICACAO;
    private $ID_BCO_QUESTAO;
    private $ID_MATERIA;
    private $ID_DIVISAO;
    private $ID_TOPICO;
    private $ID_ITEM;
    private $ID_SUBITEM;
    
    public function alterar(){
        try{
            $sql = "UPDATE
                        SPRO_CLASSIFICACAO_QUESTAO
                    SET
                        ID_MATERIA  = ".  mysql_escape_string($this->ID_MATERIA).",
                        ID_DIVISAO  = ".  mysql_escape_string($this->ID_DIVISAO).",
                        ID_TOPICO   = ".  mysql_escape_string($this->ID_TOPICO).",
                        ID_ITEM     = ".  mysql_escape_string($this->ID_ITEM).",
                        ID_SUBITEM  = ".  mysql_escape_string($this->ID_SUBITEM)."
                    WHERE
                        ID_BCO_QUESTAO = ".  mysql_escape_string($this->ID_BCO_QUESTAO)."
                    AND
                        ID_CLASSIFICACAO = ".  mysql_escape_string($this->ID_CLASSIFICACAO)."
                    ;";
            
            MySQL::connect();
            MySQL::executeQuery($sql);
            
            return true;
        }catch(Exception $e){
            echo "============= ERRO =============<br /><br />";
            echo $e->getMessage() . "<br />";
            echo $e->getFile() . "<br />";
            echo $e->getLine() . "<br />";
            echo "============= ERRO =============<br /><br />";
        }
    }
    
    public function __set($name, $value) {
        $this->$name = $value;
    }
    
    public function __get($name) {
        return $this->$name;
    }
}
?>
