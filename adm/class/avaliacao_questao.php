<?php

require_once '../class/mysql.php';

/**
 * Classe para controle de dados de AVALIACAO_QUESTAO
 */
class AvaliacaoQuestao{
    private $ID_AVALIACAO_QUESTAO;
    private $ID_USUARIO;
    private $ID_BCO_QUESTAO;
    private $NOTA_ENUNCIADO;
    private $NOTA_ABRANGENCIA;
    private $NOTA_ILUSTRACAO;
    private $NOTA_INTERDISCIPLINARIDADE;
    private $NOTA_HABILIDADE_COMPETENCIA;
    private $NOTA_ORIGINALIDADE;
    private $DATA_AVALIACAO;
    
    
    /**
     * Função para iniciar o valor da propriedade $ID_AVALIACAO_QUESTAO
     * 
     * @param int $ID_AVALIACAO_QUESTAO
     */
    public function setIdAvaliacaoQuestao($id){
        $this->ID_AVALIACAO_QUESTAO = (int)$id;
    }
    
    /**
     * Função que retorna o valor da propriedade $ID_AVALIACAO_QUESTAO
     * 
     * @return int $ID_AVALIACAO_QUESTAO
     */
    public function getIdAvaliacaoQuestao(){
        return $this->ID_AVALIACAO_QUESTAO;
    }
    
    /**
     * Função para iniciar o valor da propriedade ID_USUARIO
     * 
     * @param int ID_USUARIO
     */
    public function setIdUsuario($id){
        $this->ID_USUARIO = (int)$id;
    }
    
    /**
     * Função que retorna o valor da propriedade ID_USUARIO
     * 
     * @return int ID_USUARIO
     */
    public function getIdUsuario(){
        return $this->ID_USUARIO;
    }
    
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
     * Função para iniciar o valor da propriedade NOTA_ENUNCIADO
     * 
     * @param int NOTA_ENUNCIADO
     */
    public function setNotaEnunciado($nota){
        $this->NOTA_ENUNCIADO = (int)$nota;
    }
    
    /**
     * Função que retona o valor da propriedade NOTA_ENUNCIADO
     * 
     * @return int NOTA_ENUNCIADO
     */
    public function getNotaEnunciado(){
        return $this->NOTA_ENUNCIADO;
    }
    
    /**
     * Função para iniciar o valor da propriedade NOTA_ABRANGENCIA
     * 
     * @param int NOTA_ABRANGENCIA
     */
    public function setNotaAbrangencia($nota){
        $this->NOTA_ABRANGENCIA = (int)$nota;
    }
    
    /**
     * Função que retona o valor da propriedade NOTA_ENUNCIADO
     * 
     * @return int NOTA_ENUNCIADO
     */
    public function getNotaAbrangencia(){
        return $this->NOTA_ABRANGENCIA;
    }
    
    /**
     * Função para iniciar o valor da propriedade NOTA_ILUSTRACAO
     * 
     * @param int NOTA_ILUSTRACAO
     */
    public function setNotaIlustracao($nota){
        $this->NOTA_ILUSTRACAO = (int)$nota;
    }
    
    /**
     * Função que retona o valor da propriedade NOTA_ILUSTRACAO
     * 
     * @return int NOTA_ILUSTRACAO
     */
    public function getNotaIlustracao(){
        return $this->NOTA_ILUSTRACAO;
    }
    
    /**
     * Função para iniciar o valor da propriedade NOTA_INTERDISCIPLINARIDADE
     * 
     * @param int NOTA_INTERDISCIPLINARIDADE
     */
    public function setNotaInterdisciplinaridade($nota){
        $this->NOTA_INTERDISCIPLINARIDADE = (int)$nota;
    }
    
    /**
     * Função que retona o valor da propriedade NOTA_INTERDISCIPLINARIDADE
     * 
     * @return int NOTA_INTERDISCIPLINARIDADE
     */
    public function getNotaInterdisciplinaridade(){
        return $this->NOTA_INTERDISCIPLINARIDADE;
    }
    
    /**
     * Função para iniciar o valor da propriedade NOTA_HABILIDADE_COMPETENCIA
     * 
     * @param int NOTA_HABILIDADE_COMPETENCIA
     */
    public function setNotaHabilidadeCompetencia($nota){
        $this->NOTA_HABILIDADE_COMPETENCIA = (int)$nota;
    }
    
    /**
     * Função que retona o valor da propriedade NOTA_HABILIDADE_COMPETENCIA
     * 
     * @return int NOTA_HABILIDADE_COMPETENCIA
     */
    public function getNotaHabilidadeCompetencia(){
        return $this->NOTA_HABILIDADE_COMPETENCIA;
    }
    
    /**
     * Função para iniciar o valor da propriedade NOTA_ORIGINALIDADE
     * 
     * @param int NOTA_ORIGINALIDADE
     */
    public function setNotaOriginalidade($nota){
        $this->NOTA_ORIGINALIDADE = (int)$nota;
    }
    
    /**
     * Função que retona o valor da propriedade NOTA_ORIGINALIDADE
     * 
     * @return int NOTA_ORIGINALIDADE
     */
    public function getNotaOriginalidade(){
        return $this->NOTA_ORIGINALIDADE;
    }
    
    /**
     * Função para iniciar o valor da propriedade DATA_AVALIACAO
     * 
     * @param string DATA_AVALIACAO
     */
    public function setDataAvaliacao($data){
        $this->DATA_AVALIACAO = $data;
    }
    
    /**
     * Função que retona o valor da propriedade DATA_AVALIACAO
     * 
     * @return string DATA_AVALIACAO
     */
    public function getDataAvaliacao(){
        return $this->DATA_AVALIACAO;
    }
    
    public function salvaAvaliacaoQuestao(){
        try{
            //Valida valor ID_USUARIO
            if($this->ID_USUARIO <= 0 || $this->ID_USUARIO == null){
                throw new Exception("O campo ID_USUARIO é obrigatório para salvar a Avaliação");
            }
            
            //Valida valor ID_BCO_QUESTAO
            if($this->ID_BCO_QUESTAO <= 0 || $this->ID_BCO_QUESTAO == null){
                throw new Exception("O campo ID_BCO_QUESTAO é obrigatório para salvar a Avaliação");
            }
            
            //Validação de NOTAS_
            if($this->NOTA_ENUNCIADO <= 0 || $this->NOTA_ENUNCIADO == null){
                throw new Exception("O campo NOTA_ENUNCIADO é obrigatório para salvar a Avaliação");
            }
            
            if($this->NOTA_ABRANGENCIA <= 0 || $this->NOTA_ABRANGENCIA == null){
                throw new Exception("O campo NOTA_ABRANGENCIA é obrigatório para salvar a Avaliação");
            }
            
            if($this->NOTA_ILUSTRACAO <= 0 || $this->NOTA_ILUSTRACAO == null){
                throw new Exception("O campo NOTA_ILUSTRACAO é obrigatório para salvar a Avaliação");
            }
            
            if($this->NOTA_INTERDISCIPLINARIDADE <= 0 || $this->NOTA_INTERDISCIPLINARIDADE == null){
                throw new Exception("O campo NOTA_INTERDISCIPLINARIDADE é obrigatório para salvar a Avaliação");
            }
            
            if($this->NOTA_HABILIDADE_COMPETENCIA <= 0 || $this->NOTA_HABILIDADE_COMPETENCIA == null){
                throw new Exception("O campo NOTA_HABILIDADE_COMPETENCIA é obrigatório para salvar a Avaliação");
            }
            
            $sql = "INSERT INTO
                        SPRO_AVALIACAO_QUESTAO
                        (
                            ID_USUARIO,
                            ID_BCO_QUESTAO,
                            NOTA_ENUNCIADO,
                            NOTA_ABRANGENCIA,
                            NOTA_ILUSTRACAO,
                            NOTA_INTERDISCIPLINARIDADE,
                            NOTA_HABILIDADE_COMPETENCIA,
                            NOTA_ORIGINALIDADE,
                            DATA_AVALIACAO
                        )
                        VALUES
                        (
                            '{$this->ID_USUARIO}',
                            '{$this->ID_BCO_QUESTAO}',
                            '{$this->NOTA_ENUNCIADO}',
                            '{$this->NOTA_ABRANGENCIA}',
                            '{$this->NOTA_ILUSTRACAO}',
                            '{$this->NOTA_INTERDISCIPLINARIDADE}',
                            '{$this->NOTA_HABILIDADE_COMPETENCIA}',
                            '{$this->NOTA_ORIGINALIDADE}',
                            NOW()
                        )";
                            
            MySQL::connect();
            MySQL::executeQuery($sql);
            
            return true;
        }catch(Exception $e){
            echo "Erro salvar Avalição de Questão<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
