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
    private $ID_MATERIA;
    private $TOTAL_USO;
    private $avaliacao;
    private $materias;
    
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
    
    /**
     * Função que retona o valor da propriedade avaliacao
     * 
     * @return AvaliacaoQuestao $avaliacao
     */
    public function getAvaliacaoQuestao(){
        return $this->avaliacao;
    }
    
    public function __construct($ID_BCO_QUESTAO = null) {
        try{
            if($ID_BCO_QUESTAO > 0){
                $this->ID_BCO_QUESTAO = (int)$ID_BCO_QUESTAO;
                $this->carregaQuestao();
            }
        }catch(Exception $e){
            echo "Erro inicializar Questoes<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Funcção que carrega o Objeto questão instanciado a partir de um ID_QUESTAO enviado.
     */
    public function carregaQuestao(){
        try{
            if($this->ID_BCO_QUESTAO <= 0){
                throw new Exception("O campo ID_BCO_QUESTAO é obrigatório para carregar a Questão");
            }
            
            $sql = "SELECT
                        Q.ID_BCO_QUESTAO,
                        Q.TOTAL_USO,
                        AQ.ID_AVALIACAO_QUESTAO,
                        AQ.NOTA_ENUNCIADO,
                        AQ.NOTA_ABRANGENCIA,
                        AQ.NOTA_ILUSTRACAO,
                        AQ.NOTA_INTERDISCIPLINARIDADE,
                        AQ.NOTA_HABILIDADE_COMPETENCIA,
                        AQ.NOTA_ORIGINALIDADE,
                        AQ.DATA_AVALIACAO
                    FROM 
                        SPRO_BCO_QUESTAO Q
                    LEFT JOIN
                        SPRO_AVALIACAO_QUESTAO AQ ON AQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    WHERE
                        Q.ID_BCO_QUESTAO = {$this->ID_BCO_QUESTAO}
                    LIMIT
                        1
                    ;";
                        
            MySQL::connect();
            $rs     = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) == 1){
                $ret                    = mysql_fetch_object($rs, 'Questoes');
                $this->ID_BCO_QUESTAO   = $ret->ID_BCO_QUESTAO;
                $this->TOTAL_USO        = $ret->TOTAL_USO;
                
                if($ret->ID_AVALIACAO_QUESTAO > 0){
                    $this->avaliacao = new AvaliacaoQuestao();
                    $this->avaliacao->setNotaEnunciado($ret->NOTA_ENUNCIADO);
                    $this->avaliacao->setNotaAbrangencia($ret->NOTA_ABRANGENCIA);
                    $this->avaliacao->setNotaIlustracao($ret->NOTA_ILUSTRACAO);
                    $this->avaliacao->setNotaInterdisciplinaridade($ret->NOTA_INTERDISCIPLINARIDADE);
                    $this->avaliacao->setNotaHabilidadeCompetencia($ret->NOTA_HABILIDADE_COMPETENCIA);
                    $this->avaliacao->setNotaOriginalidade($ret->NOTA_ORIGINALIDADE);
                    $this->avaliacao->setDataAvaliacao($ret->DATA_AVALIACAO);
                }
            }else{
                $this->ID_BCO_QUESTAO   = null;
                $this->TOTAL_USO        = null;
            }
        }catch(Exception $e){
            echo "Erro carregar Questão<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Verifica se a questão já foi avaliada anteriormente.
     * 
     * @param int $id_usuario
     * @return boolean
     */
    public function validaQuestaoAvaliacao(){
        try{
            //Valida se o ID_QUESTAO está iniciado
            if($this->ID_BCO_QUESTAO <= 0){
                throw new Exception("O campo ID_BCO_QUESTAO é obrigatório para validar a Questão");
            }
            
            $sql = "SELECT
                        AQ.ID_MATERIA
                    FROM
                        SPRO_AVALIACAO_QUESTAO AQ
                    WHERE
                        AQ.ID_BCO_QUESTAO = {$this->ID_BCO_QUESTAO}
                    LIMIT
                        1
                    ;";
                    
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) == 1){
                return false;
            }else{
                return true;
            }
        }catch(Exception $e){
            echo "Erro validar Questão<br />\n";
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
    public function listaQuestoesTop10Materia($id_materia = 0, $id_fonte = 0){
        try{
            $ret        = array(); //Variável de retorno
            $id_materia = (int)$id_materia;
            $id_fonte   = (int)$id_fonte;
            $where      = "";
            
            if($id_materia > 0){
                $where = " WHERE CQ.ID_MATERIA = {$id_materia} ";
            }
            
            if($id_materia > 0){
                if($where != ""){
                    $where .= " AND ";
                }else{
                    $where .= " WHERE ";
                }
                $where .= " CQ.ID_FONTE = {$id_fonte} ";
            }
            
            $sql = "SELECT
                        Q.ID_BCO_QUESTAO,
                        Q.TOTAL_USO,
                        AQ.ID_AVALIACAO_QUESTAO
                    FROM 
                        SPRO_BCO_QUESTAO Q
                    INNER JOIN
                        SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    LEFT JOIN
                        SPRO_AVALIACAO_QUESTAO AQ ON AQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    {$where}
                    ORDER BY
                        Q.TOTAL_USO DESC
                    LIMIT
                        10
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                while($row = mysql_fetch_object($rs, 'Questoes')){
                    //Concatena as matérias que fazem relação com a questão carregada
                    if($id_materia <= 0){
                        $sql = "SELECT
                                    MQ.ID_MATERIA,
                                    MQ.MATERIA
                                FROM
                                    SPRO_MATERIA_QUESTAO MQ
                                INNER JOIN
                                    SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_MATERIA = MQ.ID_MATERIA
                                WHERE
                                    CQ.ID_BCO_QUESTAO = {$row->getIdBcoQuestao()}
                                LIMIT 5
                                ;";

                        MySQL::connect();
                        $rs_materia = MySQL::executeQuery($sql);
                        
                        if(mysql_num_rows($rs_materia) <= 0){
                            throw new Exception("Falha ao carregar matéria(s) da Questão");
                        }else{
                            while ($row_materia = mysql_fetch_array($rs_materia, MYSQLI_ASSOC)) {
                                echo "<pre style='color:#FF0000;'>";
                                print_r($row_materia);
                                echo "</pre>";
                                die;
                                $row->materias = implode(", ", $row_materia);
                            }
                        }
                    }
                    
                    $sql = "SELECT
                                U.ID_USUARIO,
                                U.NOME
                            FROM
                                SPRO_USUARIO U
                            INNER JOIN
                                SPRO_ADM_USUARIO_MATERIA UM ON UM.ID_USUARIO = U.ID_USUARIO
                            WHERE
                                
                            ;";
                    echo "<pre style='color:#FF0000;'>";
                    print_r($row);
                    echo "</pre>";
                    die;
                    
                    $sql = "SELECT 
                                U.ID_USUARIO,
                                U.NOME
                            FROM
                                SPRO_ADM_USUARIO U
                            INNER JOIN
                                SPRO_ADM_USUARIO_MATERIA UA ON UA.ID_USUARIO = U.ID_USUARIO
                                ";
                    
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
    
    public function listaQuestoesTop10Colaborador($id_usuario){
        try{
            
            $ret        = array(); //Variável de retorno
            $id_usuario = (int)$id_usuario;
            
            //Valida valor de id_materia
            if($id_usuario <= 0){
                throw new Exception("O campo ID_USUARIO é obrigatório para efetuar a busca de questões do colaborador");
            }
            
            $sql = "SELECT
                        Q.ID_BCO_QUESTAO,
                        Q.TOTAL_USO,
                        AQ.ID_AVALIACAO_QUESTAO
                    FROM 
                        SPRO_BCO_QUESTAO Q
                    INNER JOIN
                        SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    INNER JOIN
                        SPRO_USUARIO_AVALIA_MATERIA AM ON AM.ID_MATERIA = CQ.ID_MATERIA
                    LEFT JOIN
                        SPRO_AVALIACAO_QUESTAO AQ ON AQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    WHERE
                        CQ.ID_MATERIA = {$id_materia}
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
