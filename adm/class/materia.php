<?php

require_once '../class/mysql.php';

/**
 * Classe para controle de dados de SPRO_MATERIA_QUESTAO
 */
class Materia{
    private $ID_MATERIA;
    private $MATERIA;
    
    /**
     * Função para iniciar o valor da propriedade ID_MATERIA
     * 
     * @param int $ID_MATERIA
     */
    public function setIdMateria($id){
        $this->ID_MATERIA = (int)$id;
    }
    
    /**
     * Função que retorna o valor da propriedade ID_MATERIA
     * 
     * @return int $ID_MATERIA
     */
    public function getIdMateria(){
        return $this->ID_MATERIA;
    }
    
    /**
     * Função para iniciar o valor da propriedade MATERIA
     * 
     * @param string MATERIA
     */
    public function setMateria($materia){
        $this->MATERIA = $materia;
    }
    
    /**
     * Função que retorna o valor da propriedade MATERIA
     * 
     * @return string MATERIA
     */
    public function getMateria(){
        return $this->MATERIA;
    }
    
    /**
     * Função que lista todas as matérias cadastradas no banco
     * 
     * @return Materia[] Array de matérias listadas
     */
    public function listaMaterias(){
        try{
            $ret = array(); //Variável de retorno
            
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
            
            if(mysql_num_rows($rs) > 0){
                while ($row = mysql_fetch_object($rs, 'Materia')) {
                    $ret[] = $row;
                }
            }
            
            return $ret;
        }catch(Exception $e){
            echo "Erro listar Matéria do Banco<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
