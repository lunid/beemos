<?php

require_once 'mysql.php';

class Item{
    public function carregaCombo($classificacao, $id_item = 0, $id_materia = 0, $id_divisao = 0, $id_topico = 0){
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
            
            $html = "<select id=\"sel_id_item_{$classificacao}\" onchange=\"montaCombo('id_subitem', {$classificacao})\">";
            $html .= "<option value='0'>Selecione um item</option>";
            
            $sql = "SELECT
                        ID_ITEM,
                        NOME_ITEM
                    FROM
                        SPRO_ITEM_QUESTAO
                    {$where}
                    ORDER BY
                        NOME_ITEM
                    ;";
           
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return 'Nenhum item encontrado';
            }
            
            while($row = mysql_fetch_object($rs)){
                $selected = "";
                
                if($id_item == $row->ID_ITEM){
                    $selected = "selected";
                }
                
                $html .= "<option value='{$row->ID_ITEM}' {$selected}>".utf8_encode($row->NOME_ITEM)."</option>";
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
