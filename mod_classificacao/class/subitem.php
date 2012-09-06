<?php

require_once 'mysql.php';

class Subitem{
    public function carregaCombo($classificacao, $id_subitem = 0, $id_materia = 0, $id_divisao = 0, $id_topico = 0, $id_item = 0){
        try{
            $where = "";
            
            if($id_materia > 0){
                $where = " WHERE ID_MATERIA = {$id_materia} ";
            }
            
            if($id_divisao > 0){
                if($where != ""){
                    $where .= " AND ";
                }else{
                    $where .= " WHERE ";
                }
                $where .= " ID_DIVISAO = {$id_divisao} ";
            }
            
            if($id_topico > 0){
                if($where != ""){
                    $where .= " AND ";
                }else{
                    $where .= " WHERE ";
                }
                $where .= " ID_TOPICO = {$id_topico} ";
            }
            
            if($id_item > 0){
                if($where != ""){
                    $where .= " AND ";
                }else{
                    $where .= " WHERE ";
                }
                $where .= " ID_ITEM = {$id_item} ";
            }
            
            $html = "<select id=\"sel_id_subitem_{$classificacao}\">";
            $html .= "<option value='0'>Selecione um subitem</option>";
            
            $sql = "SELECT
                        ID_SUBITEM,
                        SUBITEM
                    FROM
                        SPRO_SUBITEM_QUESTAO
                    {$where}
                    ORDER BY
                        SUBITEM
                    ;";
           
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return 'Nenhum subitem encontrado';
            }
            
            while($row = mysql_fetch_object($rs)){
                $selected = "";
                
                if($id_subitem == $row->ID_SUBITEM){
                    $selected = "selected";
                }
                
                $html .= "<option value='{$row->ID_SUBITEM}' {$selected}>".utf8_encode($row->SUBITEM)."</option>";
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
