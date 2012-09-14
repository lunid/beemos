<?php

if(isset($path)){
    require_once $path . "/class/mysql.php";
}else{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/interbits_dev/class/mysql.php";
}

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
    private $ID_FONTE_VESTIBULAR;
    private $FONTE_VESTIBULAR;
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
     * Função para iniciar o valor da propriedade ID_FONTE_VESTIBULAR
     * 
     * @param int $ID_FONTE_VESTIBULAR
     */
    public function setIdFonteVestibular($id){
        $this->ID_FONTE_VESTIBULAR = (int)$id;
    }
    
    /**
     * Função que retona o valor da propriedade ID_FONTE_VESTIBULAR
     * 
     * @return int $ID_FONTE_VESTIBULAR
     */
    public function getIdFonteVestibular(){
        return $this->ID_FONTE_VESTIBULAR;
    }
    
    /**
     * Função para iniciar o valor da propriedade FONTE_VESTIBULAR
     * 
     * @param string $FONTE_VESTIBULAR
     */
    public function setFonteVestibular($fonte){
        $this->FONTE_VESTIBULAR = $fonte;
    }
    
    /**
     * Função que retona o valor da propriedade FONTE_VESTIBULAR
     * 
     * @return string $FONTE_VESTIBULAR
     */
    public function getFonteVestibular(){
        return $this->FONTE_VESTIBULAR;
    }
    
    /**
     * Função que retona o valor da propriedade $materias
     * 
     * @return int $meterias
     */
    public function getMaterias(){
        return $this->materias;
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
                        AQ.ID_AVALIACAO_QUESTAO
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
     * Função que lista as 10 questões mais utilizadas por Matéria ou Fonte
     * 
     * @param type $id_materia Código da matéria para filtro
     * @param type $id_fonte Código da fonte para filtro
     * @return Questoes[] Array com os Objetos Questoes encontrados
     */
    public function listaQuestoesTop10Materia($id_materia = 0, $id_fonte = 0){
        try{
            $ret            = array(); //Variável de retorno
            $arr_usuarios   = array(); //Armazena usuários relacionados as questões.
            $id_materia     = (int)$id_materia;
            $id_fonte       = (int)$id_fonte;
            $where          = "";
            $count          = 0; //Contador o array $ret
            
            if($id_materia > 0){
                $where = " WHERE CQ.ID_MATERIA = {$id_materia} ";
            }
            
            if($id_fonte > 0){
                if($where != ""){
                    $where .= " AND ";
                }else{
                    $where .= " WHERE ";
                }
                $where .= " Q.ID_FONTE_VESTIBULAR = {$id_fonte} ";
            }
            
            $sql = "SELECT
                        DISTINCT
                        Q.ID_BCO_QUESTAO,
                        Q.TOTAL_USO,
                        Q.ID_FONTE_VESTIBULAR,
                        FV.FONTE_VESTIBULAR,
                        AQ.ID_AVALIACAO_QUESTAO
                    FROM 
                        SPRO_BCO_QUESTAO Q
                    INNER JOIN
                        SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    INNER JOIN
                        SPRO_FONTE_VESTIBULAR FV ON FV.ID_FONTE_VESTIBULAR = Q.ID_FONTE_VESTIBULAR
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
                $txt_materias   = "";
                $in_materias    = "";
                
                while($row = mysql_fetch_object($rs, 'Questoes')){
                    //Concatena as matérias que fazem relação com a questão carregada
                    if($id_materia <= 0){
                        $sql = "SELECT
                                    DISTINCT
                                    MQ.ID_MATERIA,
                                    MQ.MATERIA
                                FROM
                                    SPRO_MATERIA_QUESTAO MQ
                                INNER JOIN
                                    SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_MATERIA = MQ.ID_MATERIA
                                WHERE
                                    CQ.ID_BCO_QUESTAO = {$row->getIdBcoQuestao()}
                                ;";
                                    
                        MySQL::connect();
                        $rs_materia = MySQL::executeQuery($sql);
                        $total_rows = mysql_num_rows($rs_materia);
                        
                        if($total_rows <= 0){
                            throw new Exception("Falha ao carregar matéria(s) da Questão");
                        }else{
                            while ($row_materia = mysql_fetch_object($rs_materia)) {
                                if($txt_materias != ""){
                                    $txt_materias .= ", ";
                                }
                                
                                if($in_materias != ""){
                                    $in_materias .= ",";
                                }
                                
                                $txt_materias   .= $row_materia->MATERIA;
                                $in_materias    .= $row_materia->ID_MATERIA;
                            }
                            
                            //Se for uma questã orelacionada apenas com uma matéria, a propriedade ID_MATERIA é carregada.
                            if($total_rows == 1){
                                $row->ID_MATERIA = mysql_result($rs_materia, 0, 'ID_MATERIA');
                            }
                            $row->materias = $txt_materias;
                        }
                    }else{
                        $sql = "SELECT
                                    MQ.MATERIA
                                FROM
                                    SPRO_MATERIA_QUESTAO MQ
                                WHERE
                                    MQ.ID_MATERIA = {$id_materia}
                                LIMIT
                                    1
                                ;";
                                    
                        MySQL::connect();
                        $rs_materia = MySQL::executeQuery($sql);
                        
                        $row->materias  = mysql_result($rs_materia, 0, 'MATERIA');
                        $in_materias    = $id_materia;
                    }
                    
                    $sql = "SELECT
                                U.ID_USUARIO,
                                U.NOME
                            FROM
                                SPRO_ADM_USUARIO U
                            INNER JOIN
                                SPRO_ADM_USUARIO_MATERIA UM ON UM.ID_USUARIO = U.ID_USUARIO
                            WHERE
                                UM.ID_MATERIA IN ({$in_materias})
                            AND
                                ID_PERFIL = 2
                            ;";
                    
                    MySQL::connect();
                    $rs_usuario = MySQL::executeQuery($sql);
                    
                    if(mysql_num_rows($rs_usuario) > 0){
                        while ($row_usuario = mysql_fetch_object($rs_usuario)) {
                            $arr_usuarios[] = $row_usuario;
                        }
                    }
                    
                    $ret[$count]['questao']     = $row;
                    $ret[$count]['usuarios']    = $arr_usuarios;
                    
                    $arr_usuarios = array();
                    $txt_materias = "";
                    $in_materias  = "";
                    
                    $count++;
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
    
    /**
     * Lista as questões que o colocaborador terá de avaliar
     * 
     * @param int $id_usuario Código do usuário logado no sistema
     * @return Questoes[] Array com os Objetos Questoes encontrados
     */
    public function listaQuestoesTop10Colaborador($id_usuario){
        try{
            
            $ret            = array(); //Variável de retorno
            $id_usuario     = (int)$id_usuario;
            $txt_materias   = "";
            $in_materias    = "";
            $count          = 0;
            
            //Valida valor de id_materia
            if($id_usuario <= 0){
                throw new Exception("O campo ID_USUARIO é obrigatório para efetuar a busca de questões do colaborador");
            }
            
            $sql = "SELECT
                        DISTINCT
                        Q.ID_BCO_QUESTAO,
                        Q.TOTAL_USO,
                        FV.ID_FONTE_VESTIBULAR,
                        FV.FONTE_VESTIBULAR,
                        AQ.ID_AVALIACAO_QUESTAO
                    FROM 
                        SPRO_BCO_QUESTAO Q
                    INNER JOIN
                        SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    INNER JOIN
                        SPRO_FONTE_VESTIBULAR FV ON FV.ID_FONTE_VESTIBULAR = Q.ID_FONTE_VESTIBULAR
                    INNER JOIN
                        SPRO_USUARIO_AVALIA_QUESTAO UQ ON UQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    LEFT JOIN
                        SPRO_AVALIACAO_QUESTAO AQ ON AQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    WHERE
                        UQ.ID_USUARIO = {$id_usuario}
                    ORDER BY
                        Q.TOTAL_USO DESC
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                $txt_materias   = "";
                $in_materias    = "";
                
                while($row = mysql_fetch_object($rs, 'Questoes')){
                    $sql = "SELECT
                                DISTINCT
                                MQ.ID_MATERIA,
                                MQ.MATERIA
                            FROM
                                SPRO_MATERIA_QUESTAO MQ
                            INNER JOIN
                                SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_MATERIA = MQ.ID_MATERIA
                            WHERE
                                CQ.ID_BCO_QUESTAO = {$row->getIdBcoQuestao()}
                            ;";

                    MySQL::connect();
                    $rs_materia = MySQL::executeQuery($sql);
                    $total_rows = mysql_num_rows($rs_materia);

                    if($total_rows <= 0){
                        throw new Exception("Falha ao carregar matéria(s) da Questão");
                    }else{
                        while ($row_materia = mysql_fetch_object($rs_materia)) {
                            if($txt_materias != ""){
                                $txt_materias .= ", ";
                            }

                            if($in_materias != ""){
                                $in_materias .= ",";
                            }

                            $txt_materias   .= $row_materia->MATERIA;
                            $in_materias    .= $row_materia->ID_MATERIA;
                        }

                        //Se for uma questã orelacionada apenas com uma matéria, a propriedade ID_MATERIA é carregada.
                        if($total_rows == 1){
                            $row->ID_MATERIA = mysql_result($rs_materia, 0, 'ID_MATERIA');
                        }
                        $row->materias = $txt_materias;
                    }
                    
                    
                    $ret[$count]['questao']     = $row;
                    
                    $txt_materias = "";
                    $in_materias  = "";
                    
                    $count++;
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
    
    /**
     * Função que altera colaborador responsável em avaliar a questão
     * 
     * @param int $id_questao Código da questão
     * @param int $id_usuario Código do usuário 
     * @return boolean
     */
    public function alteraUsuarioQuestao($id_questao, $id_usuario){
        try{
            MySQL::connect();
            
            $sql = "DELETE FROM SPRO_USUARIO_AVALIA_QUESTAO WHERE ID_BCO_QUESTAO = {$id_questao};";
            MySQL::executeQuery($sql);
            
            $sql = "DELETE FROM SPRO_AVALIACAO_QUESTAO WHERE ID_BCO_QUESTAO = {$id_questao};";
            MySQL::executeQuery($sql);
            
            $sql = "INSERT INTO 
                        SPRO_USUARIO_AVALIA_QUESTAO
                        (
                            ID_BCO_QUESTAO,
                            ID_USUARIO,
                            DATA_INDICACAO
                        )
                        VALUES
                        (
                            {$id_questao},
                            {$id_usuario},
                            NOW()
                        );";
            
            MySQL::executeQuery($sql);
            
            return true;
        }catch(Exception $e){
            echo "Erro alterar usuário questão<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
