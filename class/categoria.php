<?php
require_once 'mysql.php';

/**
 * Classe para manipulação dos dados de CATEGORIA
 */
class Categoria{
    private $id;
    private $descricao;
    private $email_contato;
    
    /**
     * Função para iniciar o valor da propriedade ID
     * 
     * @param int $id
     */
    public function setId($id){
        $this->id = (int)$id;
    }
    
    /**
     * Função para iniciar o valor da propriedade ID
     * 
     * @return int $id
     */
    public function getId(){
        return $this->id;
    }
    
    /**
     * Função para iniciar o valor da propriedade DESCRICAO
     * 
     * @param string $descricao
     */
    public function setDescricao($descricao){
        $this->descricao = mysql_real_escape_string($descricao);
    }
    
    /**
     * Função que retorna o valor da propriedade DESCRICAO
     * 
     * @return string $descricao
     */
    public function getDescricao(){
        return $this->descricao;
    }
    
    /**
     * Função para iniciar o valor da propriedade E-MAIL CONTATO
     * 
     * @param string $email_contato
     */
    public function setEmailContato($email_contato){
        $this->email_contato = mysql_real_escape_string($email_contato);
    }
    
    /**
     * Função que retorna o valor da propriedade E-MAIL CONTATO
     * 
     * @return string $email_contato
     */
    public function getEmailContato(){
        return $this->email_contato;
    }
    
    /**
     * Função que lista todas as categorias cadastradas. 
     * Caso seja enviado o parâmetro CATEGORIA_ID, será listada apenas a categoria desejada
     * 
     * @return boolean
     */
    public function listaCategorias($categoria_id = null){
        try{
            $sql = "SELECT 
                        CATEGORIA_ID,
                        DESCRICAO,
                        EMAIL_CONTATO
                    FROM 
                        SPRO_CATEGORIA
                    ";
            
            if((int)$categoria_id > 0){
                $sql .= " WHERE CATEGORIA_ID = {$categoria_id} ;";
            }
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return null;
            }else{
                return $rs;
            }
        }catch(Exception $e){
            echo "Erro ao carregar CATEGORIA(S)<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
