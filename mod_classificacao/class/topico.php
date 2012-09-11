<?php

require_once 'mysql.php';

class Topico{
    public function carregaCombo($classificacao, $id_topico = 0, $id_materia = 0, $id_divisao = 0){
        try{
            $where = "";
            
            if($id_materia > 0){
                $where = " WHERE ID_MATERIA = {$id_materia} ";
            }
            
            if($id_materia > 0){
                if($where != ""){
                    $where .= " AND ";
                }else{
                    $where .= " WHERE ";
                }
                $where .= " ID_DIVISAO = {$id_divisao} ";
            }
            
            $html = "<select id=\"sel_id_topico_{$classificacao}\" onchange=\"montaCombo('id_item', {$classificacao})\">";
            $html .= "<option value='0'>Selecione um tópico</option>";
            
            $sql = "SELECT
                        ID_TOPICO,
                        TOPICO
                    FROM
                        SPRO_TOPICO_QUESTAO
                    {$where}
                    ORDER BY
                        TOPICO
                    ;";
           
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return '';
            }
            
            while($row = mysql_fetch_object($rs)){
                $selected = "";
                
                if($id_topico == $row->ID_TOPICO){
                    $selected = "selected";
                }
                
                $html .= "<option value='{$row->ID_TOPICO}' {$selected}>".utf8_encode($row->TOPICO)."</option>";
            }
            
            $html .= "</select>";
            return $html;
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
