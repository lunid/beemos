<?php

require_once 'mysql.php';

class Materia{
    public function carregaCombo($classificacao, $id_materia = 0){
        try{
            $html = "<select id=\"sel_id_materia_{$classificacao}\" onchange=\"montaCombo('id_divisao', {$classificacao})\">";
            $html .= "<option value='0'>Selecione uma materia</option>";
            
            $sql = "SELECT
                        ID_MATERIA,
                        MATERIA
                    FROM
                        SPRO_MATERIA_QUESTAO
                    ORDER BY
                        MATERIA
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return '';
            }
            
            while($row = mysql_fetch_object($rs)){
                $selected = "";
                
                if($id_materia == $row->ID_MATERIA){
                    $selected = "selected";
                }
                
                $html .= "<option value='{$row->ID_MATERIA}' {$selected}>".utf8_encode($row->MATERIA)."</option>";
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
