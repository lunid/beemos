<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/interbits_dev/class/mysql.php";

/**
 * Classe para controle de dados de ADM_PERFIL
 */
class Perfil{
    private $id_perfil;
    private $descricao;
    private $status;
    
    /**
     * Função para iniciar o valor da propriedade ID_PERFIL
     * 
     * @param int $id_perfil
     */
    public function setIdPerfil($id_perfil){
        $this->id_perfil = $id_perfil;
    }
    
    /**
     * Função que retorna o valor da propriedade ID_PERFIL
     * 
     * @return int
     */
    public function getIdPerfil(){
        return $this->id_perfil;
    }
    
    /**
     * Função para iniciar o valor da propriedade DESCRICAO
     * 
     * @param string $descricao
     */
    public function setDescricao($descricao){
        $this->descricao = $descricao;
    }
    
    /**
     * Função que retorna o valor da propriedade DESCRICAO
     * 
     * @return string
     */
    public function getDescricao(){
        return $this->descricao;
    }
    
    /**
     * Função para iniciar o valor da propriedade STATUS
     * 
     * @param boolean $status
     */
    public function setStatus($status){
        $this->status = $status;
    }
    
    /**
     * Função que retorna o valor da propriedade STATUS
     * 
     * @return boolean
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * Carrega o objeto PERFIL a partir de ID_PERFIL enviado
     * 
     * @return boolean
     */
    public function carregaPerfil(){
        try{
            //Valida valor de ID_PERFIL
            if($this->id_perfil == 0 || $this->email == null){
                throw new Exception("O campo ID_PERFIL é obrigatório para carregar os dados de Perfil");
            }
            
            $sql = "SELECT 
                        ID_PERFIL,
                        DESCRICAO,
                        STATUS
                    FROM 
                        SPRO_ADM_PERFIL
                    WHERE
                        ID_PERFIL = '{$this->id_perfil}'
                    LIMIT 
                        1
                    ;";
            
            
                        
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            //Caso o perfil seja encontrado, todas as propriedades do objeto são preenchidas
            if(mysql_num_rows($rs) == 1){
                $this->id_perfil    = mysql_result($rs, 0, "ID_PERFIL");
                $this->descricao    = mysql_result($rs, 0, "DESCRICAO");
                $this->status       = mysql_result($rs, 0, "STATUS");
                
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            echo "Erro carregar Perfil<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função que lista todos os Perfis cadastrados no sistema
     * 
     * @return array Array de Objetos stdClass
     */
    public function carregaPerfis(){
        try{
            //Variável de retorno
            $ret = array();
            
            $sql = "SELECT 
                        ID_PERFIL,
                        DESCRICAO,
                        STATUS
                    FROM 
                        SPRO_ADM_PERFIL
                    ORDER BY
                        ID_PERFIL
                    ;";
                        
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                while($row = mysql_fetch_object($rs)){
                    $ret[] = $row;
                }
            }
            
            return $ret;
        }catch(Exception $e){
            echo "Erro carregar Perfil<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
