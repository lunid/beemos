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
    
    public function carregaClassificacao(){
        try{
            $ret = array();
            
            $sql = "SELECT 
                        CQ.ID_CLASSIFICACAO,
                        M.ID_MATERIA,
                        M.MATERIA,
                        D.ID_DIVISAO,
                        D.DIVISAO,
                        T.ID_TOPICO,
                        T.TOPICO,
                        I.ID_ITEM,
                        I.NOME_ITEM,
                        SI.ID_SUBITEM,
                        SI.SUBITEM
                    FROM 
                        SPRO_CLASSIFICACAO_QUESTAO CQ
                    LEFT JOIN
                        SPRO_MATERIA_QUESTAO M ON M.ID_MATERIA = CQ.ID_MATERIA
                    LEFT JOIN
                        SPRO_DIVISAO_QUESTAO D ON D.ID_DIVISAO = CQ.ID_DIVISAO
                    LEFT JOIN
                        SPRO_TOPICO_QUESTAO T ON T.ID_TOPICO = CQ.ID_TOPICO
                    LEFT JOIN
                        SPRO_ITEM_QUESTAO I ON I.ID_ITEM = CQ.ID_ITEM
                    LEFT JOIN
                        SPRO_SUBITEM_QUESTAO SI ON SI.ID_SUBITEM = CQ.ID_SUBITEM
                    WHERE
                        CQ.ID_BCO_QUESTAO = {$this->ID_BCO_QUESTAO}
                    ;";
                        
            MySQL::connect();
            
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return $ret;
            }
            
            while($row = mysql_fetch_object($rs)){
                $ret[] = $row;
            }
            
            return $ret;
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
