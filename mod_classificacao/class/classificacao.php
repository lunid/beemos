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
    
    public function excluir(){
        try{
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao excluir Classificação! Tente novamente mais tarde.";
            
            $sql = "SELECT 
                        COUNT(1) AS QTD 
                    FROM 
                        SPRO_CLASSIFICACAO_QUESTAO 
                    WHERE
                        ID_BCO_QUESTAO = ".  mysql_escape_string($this->ID_BCO_QUESTAO)."
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                if(mysql_result($rs, 0, 'QTD') == 1){
                    $ret->msg = "A questão deve possuir no mínimo uma classificação.";
                    
                    return $ret;
                }
                
                $sql = "DELETE FROM SPRO_CLASSIFICACAO_QUESTAO WHERE ID_BCO_QUESTAO = ".  mysql_escape_string($this->ID_BCO_QUESTAO)." AND ID_CLASSIFICACAO = ".  mysql_escape_string($this->ID_CLASSIFICACAO) . ";"; 
                MySQL::executeQuery($sql);
                
                $ret->status = true;
                
                return $ret;
            }else{
                return $ret;
            }
        }catch(Exception $e){
            echo "============= ERRO =============<br /><br />";
            echo $e->getMessage() . "<br />";
            echo $e->getFile() . "<br />";
            echo $e->getLine() . "<br />";
            echo "============= ERRO =============<br /><br />";
        }
    }
    
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
    
    public function adicionar(){
        try{
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao adicionar Classificação. Tente mais tarde.";
            
            if($this->verificaClassificacao()){
                $ret->msg = "Essa classificação já existe para esta Questão!";
                
                return $ret;
            }
            
            $sql = "INSERT INTO
                        SPRO_CLASSIFICACAO_QUESTAO
                        (
                            ID_BCO_QUESTAO,
                            ID_MATERIA,
                            ID_DIVISAO,
                            ID_TOPICO,
                            ID_ITEM,
                            ID_SUBITEM
                        )
                    VALUES
                        (
                            " . mysql_escape_string($this->ID_BCO_QUESTAO) . ",
                            " . mysql_escape_string($this->ID_MATERIA) . ",
                            " . mysql_escape_string($this->ID_DIVISAO) . ",
                            " . mysql_escape_string($this->ID_TOPICO) . ",
                            " . mysql_escape_string($this->ID_ITEM) . ",
                            " . mysql_escape_string($this->ID_SUBITEM) . "
                         )
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            $ret->status    = true;
            $ret->msg       = "Classificação adicionada com sucesso!";
            $ret->id        = mysql_insert_id();
            
            return $ret;
        }catch(Exception $e){
            echo "============= ERRO =============<br /><br />";
            echo $e->getMessage() . "<br />";
            echo $e->getFile() . "<br />";
            echo $e->getLine() . "<br />";
            echo "============= ERRO =============<br /><br />";
        }
    }
    
    public function verificaClassificacao(){
        try{
            $sql = "SELECT
                        ID_CLASSIFICACAO
                    FROM
                        SPRO_CLASSIFICACAO_QUESTAO
                    WHERE
                        ID_BCO_QUESTAO = ".  mysql_escape_string($this->ID_BCO_QUESTAO)."
                    AND
                        ID_MATERIA  = ".  mysql_escape_string($this->ID_MATERIA)."
                    AND
                        ID_DIVISAO  = ".  mysql_escape_string($this->ID_DIVISAO)."
                    AND
                        ID_TOPICO   = ".  mysql_escape_string($this->ID_TOPICO)."
                    AND
                        ID_ITEM     = ".  mysql_escape_string($this->ID_ITEM)."
                    AND
                        ID_SUBITEM  = ".  mysql_escape_string($this->ID_SUBITEM)."
                    LIMIT
                        1
                    ;";
              
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                if(mysql_result($rs, 0, 'ID_CLASSIFICACAO') > 0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
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
