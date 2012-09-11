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
    
    public function excluir($id_usuario){
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
            
            $sql = "SELECT 
                        COUNT(1) AS QTD 
                    FROM 
                        SPRO_AUTORIZA_CLASSIFICACAO 
                    WHERE
                        ID_BCO_QUESTAO = ".  mysql_escape_string($this->ID_BCO_QUESTAO)."
                    AND
                        TIPO_MUDANCA = 'E'
                    ;";
            
            $rs_autoriza = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs_autoriza) > 0){
                $qtd_ex = mysql_result($rs_autoriza, 0, 'QTD');
            }else{
                $qtd_ex = 0;
            }
            
            if(mysql_num_rows($rs) > 0){
                if($qtd_ex >= ((int)mysql_result($rs, 0, 'QTD')-1)){
                    $ret->msg = "A questão deve possuir no mínimo uma classificação.";
                    
                    return $ret;
                }
                
                $sql = "DELETE FROM 
                            SPRO_AUTORIZA_CLASSIFICACAO 
                        WHERE 
                            ID_BCO_QUESTAO = " . mysql_escape_string($this->ID_BCO_QUESTAO) . " 
                        AND 
                            ID_CLASSIFICACAO = " . mysql_escape_string($this->ID_CLASSIFICACAO) . "
                        ;";
                
                MySQL::executeQuery($sql);
                
                $sql = "INSERT INTO
                            SPRO_AUTORIZA_CLASSIFICACAO
                            (
                                ID_CLASSIFICACAO,
                                ID_BCO_QUESTAO,
                                TIPO_MUDANCA,
                                DATA_MUDANCA,
                                ID_USUARIO
                            )
                        VALUES
                            (
                                " . mysql_escape_string($this->ID_CLASSIFICACAO) . ",
                                " . mysql_escape_string($this->ID_BCO_QUESTAO) . ",
                                'E',
                                NOW(),
                                " . $id_usuario . "
                            )
                        ;";
                
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
    
    public function desfazer(){
        try{
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao desfazer Classificação! Tente novamente mais tarde.";

            $sql = "DELETE FROM 
                        SPRO_AUTORIZA_CLASSIFICACAO 
                    WHERE 
                        ID_BCO_QUESTAO = ".  mysql_escape_string($this->ID_BCO_QUESTAO)." 
                    AND 
                        ID_CLASSIFICACAO = ".  mysql_escape_string($this->ID_CLASSIFICACAO) . "
                    ;"; 
            
            MySQL::connect();
            MySQL::executeQuery($sql);
                
            $ret->status = true;
            $ret->msg    = "Ação desfeita com sucesso!";
                
            return $ret;
        }catch(Exception $e){
            echo "============= ERRO =============<br /><br />";
            echo $e->getMessage() . "<br />";
            echo $e->getFile() . "<br />";
            echo $e->getLine() . "<br />";
            echo "============= ERRO =============<br /><br />";
        }
    }
    
    public function desfazerInsercao($id_autoriza_classificacao){
        try{
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao desfazer Classificação! Tente novamente mais tarde.";

            $sql = "DELETE FROM 
                        SPRO_AUTORIZA_CLASSIFICACAO 
                    WHERE 
                        ID_AUTORIZA_CLASSIFICACAO = ".  mysql_escape_string($id_autoriza_classificacao)." 
                    AND
                        ID_BCO_QUESTAO = ".  mysql_escape_string($this->ID_BCO_QUESTAO)."
                    ;"; 
            
            MySQL::connect();
            MySQL::executeQuery($sql);
                
            $ret->status = true;
            $ret->msg    = "Ação desfeita com sucesso!";
                
            return $ret;
        }catch(Exception $e){
            echo "============= ERRO =============<br /><br />";
            echo $e->getMessage() . "<br />";
            echo $e->getFile() . "<br />";
            echo $e->getLine() . "<br />";
            echo "============= ERRO =============<br /><br />";
        }
    }
    
    public function alterar($id_usuario){
        try{
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao alterar Classificação. Tente mais tarde.";
            
            if($this->verificaClassificacao()){
                $ret->msg = "Essa classificação já existe para esta Questão!";
                
                return $ret;
            }
            
            $sql = "INSERT INTO
                        SPRO_AUTORIZA_CLASSIFICACAO
                        (
                            ID_CLASSIFICACAO,
                            ID_BCO_QUESTAO,
                            ID_MATERIA,
                            ID_DIVISAO,
                            ID_TOPICO,
                            ID_ITEM,
                            ID_SUBITEM,
                            TIPO_MUDANCA,
                            DATA_MUDANCA,
                            ID_USUARIO
                        )
                    VALUES
                        (
                            " . mysql_escape_string($this->ID_CLASSIFICACAO) . ",
                            " . mysql_escape_string($this->ID_BCO_QUESTAO) . ",
                            " . mysql_escape_string($this->ID_MATERIA) . ",
                            " . mysql_escape_string($this->ID_DIVISAO) . ",
                            " . mysql_escape_string($this->ID_TOPICO) . ",
                            " . mysql_escape_string($this->ID_ITEM) . ",
                            " . mysql_escape_string($this->ID_SUBITEM) . ",
                            'A',
                            NOW(),
                            " . $id_usuario . "
                        )
                    ;";
            
            MySQL::connect();
            MySQL::executeQuery($sql);
            
            $ret->status = true;
            $ret->msg    = "Classificação alterada!";
            
            return $ret;
        }catch(Exception $e){
            echo "============= ERRO =============<br /><br />";
            echo $e->getMessage() . "<br />";
            echo $e->getFile() . "<br />";
            echo $e->getLine() . "<br />";
            echo "============= ERRO =============<br /><br />";
        }
    }
    
    public function adicionar($id_usuario){
        try{
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao adicionar Classificação. Tente mais tarde.";
            
            if($this->verificaClassificacao()){
                $ret->msg = "Essa classificação já existe para esta Questão!";
                
                return $ret;
            }
            
            $sql = "INSERT INTO
                        SPRO_AUTORIZA_CLASSIFICACAO
                        (
                            ID_BCO_QUESTAO,
                            ID_MATERIA,
                            ID_DIVISAO,
                            ID_TOPICO,
                            ID_ITEM,
                            ID_SUBITEM,
                            TIPO_MUDANCA,
                            DATA_MUDANCA,
                            ID_USUARIO
                        )
                    VALUES
                        (
                            " . mysql_escape_string($this->ID_BCO_QUESTAO) . ",
                            " . mysql_escape_string($this->ID_MATERIA) . ",
                            " . mysql_escape_string($this->ID_DIVISAO) . ",
                            " . mysql_escape_string($this->ID_TOPICO) . ",
                            " . mysql_escape_string($this->ID_ITEM) . ",
                            " . mysql_escape_string($this->ID_SUBITEM) . ",
                            'I',
                            NOW(),
                            " . $id_usuario . "
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
                return true;
            }else{
                $sql = "SELECT
                            ID_CLASSIFICACAO
                        FROM
                            SPRO_AUTORIZA_CLASSIFICACAO
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
                
                $rs = MySQL::executeQuery($sql);
            
                if(mysql_num_rows($rs) > 0){
                    return true;
                }else{
                    return false;
                }
            }
        }catch(Exception $e){
            echo "============= ERRO =============<br /><br />";
            echo $e->getMessage() . "<br />";
            echo $e->getFile() . "<br />";
            echo $e->getLine() . "<br />";
            echo "============= ERRO =============<br /><br />";
        }
    }
    
    public function buscaMudancas(){
        try{
            $ret = array();
            
            $sql = "SELECT 
                        AC.ID_AUTORIZA_CLASSIFICACAO,
                        AC.ID_BCO_QUESTAO,
                        AC.ID_CLASSIFICACAO,
                        AC.TIPO_MUDANCA,
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
                        SPRO_AUTORIZA_CLASSIFICACAO AC
                    LEFT JOIN
                        SPRO_MATERIA_QUESTAO M ON M.ID_MATERIA = AC.ID_MATERIA
                    LEFT JOIN
                        SPRO_DIVISAO_QUESTAO D ON D.ID_DIVISAO = AC.ID_DIVISAO
                    LEFT JOIN
                        SPRO_TOPICO_QUESTAO T ON T.ID_TOPICO = AC.ID_TOPICO
                    LEFT JOIN
                        SPRO_ITEM_QUESTAO I ON I.ID_ITEM = AC.ID_ITEM
                    LEFT JOIN
                        SPRO_SUBITEM_QUESTAO SI ON SI.ID_SUBITEM = AC.ID_SUBITEM
                    WHERE
                        AC.ID_BCO_QUESTAO = {$this->ID_BCO_QUESTAO}
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
    
    public function salvarMudancas($id_usuario, $id_questao){
        try{
            $ret         = new stdClass();
            $ret->status = false;
            $ret->msg    = "Nenhhuma mudança para ser salva.";
            
            $where = "";
            
            if($id_questao > 0){
                $where .= " AND ID_BCO_QUESTAO = " . mysql_escape_string($id_questao) . " ";
            }
            
            $sql = "SELECT
                        ID_AUTORIZA_CLASSIFICACAO,
                        ID_CLASSIFICACAO,
                        ID_BCO_QUESTAO,
                        ID_MATERIA,
                        ID_DIVISAO,
                        ID_TOPICO,
                        ID_ITEM,
                        ID_SUBITEM,
                        TIPO_MUDANCA
                    FROM
                        SPRO_AUTORIZA_CLASSIFICACAO
                    WHERE
                        ID_USUARIO = " . mysql_escape_string($id_usuario) . "
                    {$where}        
                    ;";
                    
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return $ret;
            }
            
            while($row = mysql_fetch_object($rs)){
                if($row->TIPO_MUDANCA == 'E'){
                    $sql = "DELETE FROM 
                                SPRO_CLASSIFICACAO_QUESTAO
                            WHERE
                                ID_BCO_QUESTAO = {$row->ID_BCO_QUESTAO}
                            AND
                                ID_CLASSIFICACAO = {$row->ID_CLASSIFICACAO}
                            ;";
                                
                    MySQL::executeQuery($sql);
                    
                    $sql = "DELETE FROM 
                                SPRO_AUTORIZA_CLASSIFICACAO
                            WHERE
                                ID_AUTORIZA_CLASSIFICACAO = {$row->ID_AUTORIZA_CLASSIFICACAO}
                            ;";
                                
                    MySQL::executeQuery($sql);
                }
                
                if($row->TIPO_MUDANCA == 'I'){
                    $sql = "INSERT INTO
                                SPRO_CLASSIFICACAO_QUESTAO
                                (
                                    ID_BCO_QUESTAO,
                                    ID_MATERIA,
                                    ID_DIVISAO,
                                    ID_TOPICO,
                                    ID_ITEM,
                                    ID_SUBITEM,
                                    DATA_REGISTRO
                                )
                            VALUES
                                (
                                    {$row->ID_BCO_QUESTAO},
                                    {$row->ID_MATERIA},
                                    {$row->ID_DIVISAO},
                                    {$row->ID_TOPICO},
                                    {$row->ID_ITEM},
                                    {$row->ID_SUBITEM},
                                    NOW()
                                )
                            ;";
                                
                    MySQL::executeQuery($sql);
                    
                    $sql = "DELETE FROM 
                                SPRO_AUTORIZA_CLASSIFICACAO
                            WHERE
                                ID_AUTORIZA_CLASSIFICACAO = {$row->ID_AUTORIZA_CLASSIFICACAO}
                            ;";
                                
                    MySQL::executeQuery($sql);
                }
                
                if($row->TIPO_MUDANCA == 'A'){
                    $sql = "UPDATE 
                                SPRO_CLASSIFICACAO_QUESTAO
                            SET
                                ID_MATERIA = {$row->ID_MATERIA},
                                ID_DIVISAO = {$row->ID_DIVISAO},
                                ID_TOPICO = {$row->ID_TOPICO},
                                ID_ITEM = {$row->ID_ITEM},
                                ID_SUBITEM = {$row->ID_SUBITEM},
                                DATA_REGISTRO = NOW()
                            WHERE
                                ID_BCO_QUESTAO = {$row->ID_BCO_QUESTAO}
                            AND
                                ID_CLASSIFICACAO = {$row->ID_CLASSIFICACAO}
                            ;";
                    
                    MySQL::executeQuery($sql);

                    $sql = "DELETE FROM 
                                SPRO_AUTORIZA_CLASSIFICACAO
                            WHERE
                                ID_AUTORIZA_CLASSIFICACAO = {$row->ID_AUTORIZA_CLASSIFICACAO}
                            ;";

                    MySQL::executeQuery($sql);
                }
            }
            
            $ret->status = true;
            $ret->msg    = "Mudanças salvas com sucesso!";
            return $ret;
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
