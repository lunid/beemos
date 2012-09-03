<?php

require_once '../class/mysql.php';

/**
 * Classe para controle de dados de SPRO_FONTE_VESTIBULAR
 */
class Fonte{
    private $ID_FONTE_VESTIBULAR;
    private $FONTE_VESTIBULAR;
    
    /**
     * Função para iniciar o valor da propriedade ID_FONTE_VETIBULAR
     * 
     * @param int ID_FONTE_VETIBULAR
     */
    public function setIdFonteVestibular($id){
        $this->ID_FONTE_VESTIBULAR = (int)$id;
    }
    
    /**
     * Função que retorna o valor da propriedade ID_FONTE_VETIBULAR
     * 
     * @return int ID_FONTE_VETIBULAR
     */
    public function getIdFonteVestibular(){
        return $this->ID_FONTE_VESTIBULAR;
    }
    
    /**
     * Função para iniciar o valor da propriedade FONTE_VESTIBULAR
     * 
     * @param string FONTE_VESTIBULAR
     */
    public function setFonteVestibular($fonte){
        $this->FONTE_VESTIBULAR = $fonte;
    }
    
    /**
     * Função que retorna o valor da propriedade FONTE_VESTIBULAR
     * 
     * @return string FONTE_VESTIBULAR
     */
    public function getFonteVestibular(){
        return $this->FONTE_VESTIBULAR;
    }
    
    /**
     * Função que lista todas as fontes cadastradas no banco
     * 
     * @return Fonte[] Array de fontes listadas
     */
    public function listaFontes(){
        try{
            $ret = array(); //Variável de retorno
            
            $sql = "SELECT
                        ID_FONTE_VESTIBULAR,
                        FONTE_VESTIBULAR
                    FROM 
                        SPRO_FONTE_VESTIBULAR
                    ORDER BY
                        FONTE_VESTIBULAR
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                while ($row = mysql_fetch_object($rs, 'Fonte')) {
                    $ret[] = $row;
                }
            }
            
            return $ret;
        }catch(Exception $e){
            echo "Erro listar Fontes do Banco<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
