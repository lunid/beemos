<?php

require_once '../class/mysql.php';

/**
 * Classe para controle de dados de BCO_QUESTAO
 * 
 * @property int $id_questao Código da Questão - Número único
 * @property int $total_uso Número que soma a quantidade de vezes que a questão foi utilizada
 */
class Questoes{
    private $ID_BCO_QUESTAO;
    private $TOTAL_USO;
    
    /**
     * Função para iniciar o valor da propriedade ID_BCO_QUESTAO
     * 
     * @param int $ID_BCO_QUESTAO
     */
    public function setIdBcoQuestao($id){
        $this->ID_BCO_QUESTAO = (int)$id;
    }
    
    /**
     * Função que retorna o valor da propriedade ID_BCO_QUESTAO
     * 
     * @return int $ID_BCO_QUESTAO
     */
    public function getIdBcoQuestao(){
        return $this->ID_BCO_QUESTAO;
    }
    
    /**
     * Função para iniciar o valor da propriedade TOTAL_USO
     * 
     * @param int $TOTAL_USO
     */
    public function setTotalUso($total){
        $this->TOTAL_USO = (int)$total;
    }
    
    /**
     * Função que retona o valor da propriedade TOTAL_USO
     * 
     * @return int $TOTAL_USO
     */
    public function getTotalUso(){
        return $this->TOTAL_USO;
    }
    
    public function __construct($ID_BCO_QUESTAO = null) {
        try{
            $this->ID_BCO_QUESTAO = (int)$ID_BCO_QUESTAO;
            
            if($this->ID_BCO_QUESTAO > 0){
                $this->carregaQuestao();
            }
        }catch(Exception $e){
            echo "Erro inicializar Questoes<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    public function carregaQuestao(){
        try{
            if($this->ID_BCO_QUESTAO <= 0){
                throw new Exception("O campo ID_BCO_QUESTAO é obrigatório para carregar a Questão");
            }
            
            $sql = "SELECT
                        Q.ID_BCO_QUESTAO,
                        Q.TOTAL_USO
                    FROM 
                        SPRO_BCO_QUESTAO Q
                    WHERE
                        Q.ID_BCO_QUESTAO = {$this->ID_BCO_QUESTAO}
                    LIMIT
                        1
                    ;";
                        
            MySQL::connect();
            $rs     = MySQL::executeQuery($sql);
            $ret   = mysql_fetch_object($rs, 'Questoes');
            
            echo "<pre style='color:#FF0000;'>";
            print_r($ret);
            echo "</pre>";
            die;
        }catch(Exception $e){
            echo "Erro carregar Questão<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função que lista as 10 questões mais utilizadas de uma determinada matéria
     * 
     * @param type $id_materia
     * @return Questoes[] Array com os Objetos Questoes encontrados
     */
    public function listaQuestoesTop10Materia($id_materia){
        try{
            
            $ret        = array(); //Variável de retorno
            $id_materia = (int)$id_materia;
            
            //Valida valor de id_materia
            if($id_materia <= 0){
                throw new Exception("O campo ID_MATERIA é obrigatório para efetuar a busca de questões TOP 10");
            }
            
            $sql = "SELECT
                        Q.ID_BCO_QUESTAO,
                        Q.TOTAL_USO
                    FROM 
                        SPRO_BCO_QUESTAO Q
                    INNER JOIN
                        SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    INNER JOIN
                        SPRO_USUARIO_AVALIA_MATERIA AM ON AM.ID_MATERIA = CQ.ID_MATERIA
                    WHERE
                        CQ.ID_MATERIA = {$id_materia}
                    AND
                        AM.DATA_AVALIACAO IS NULL
                    ORDER BY
                        Q.TOTAL_USO DESC
                    LIMIT
                        10
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                while($row = mysql_fetch_object($rs, 'Questoes')){
                    $ret[] = $row;
                }
            }
            
            return $ret;
        }catch(Exception $e){
            echo "Erro listar questão TOP 10 de Matéria<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
