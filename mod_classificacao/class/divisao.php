<?php

require_once 'mysql.php';

class Divisao{
    public function carregaCombo($classificacao, $id_divisao = 0, $id_materia = 0){
        try{
            $where = "";
            
            if($id_materia > 0){
                $where = " WHERE ID_MATERIA = {$id_materia} ";
            }
            
            $html = "<select id=\"sel_id_divisao_{$classificacao}\" onchange=\"montaCombo('id_topico', {$classificacao})\">";
            $html .= "<option value='0'>Selecione uma divisão</option>";
            
            $sql = "SELECT
                        ID_DIVISAO,
                        DIVISAO
                    FROM
                        SPRO_DIVISAO_QUESTAO
                    {$where}
                    ORDER BY
                        DIVISAO
                    ;";
           
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return 'Nenhuma divisão encontrada';
            }
            
            while($row = mysql_fetch_object($rs)){
                $selected = "";
                
                if($id_divisao == $row->ID_DIVISAO){
                    $selected = "selected";
                }
                
                $html .= "<option value='{$row->ID_DIVISAO}' {$selected}>".utf8_encode($row->DIVISAO)."</option>";
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
