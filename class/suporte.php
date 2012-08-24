<?php
require_once 'mysql.php';
require_once 'email.php';
require_once 'categoria.php';

/**
 * Classe para manipulação dos dados de SUPORTE
 */
class Suporte{
    private $id;
    private $nome;
    private $email;
    private $categoria_id;
    private $msg;
    
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
     * Função para iniciar o valor da propriedade ID
     * 
     * @param string $nome
     */
    public function setNome($nome){
        $this->nome = mysql_real_escape_string($nome);
    }
    
    /**
     * Função que retorna o valor da propriedade N)OME
     * 
     * @return string $nome
     */
    public function getNome(){
        return $this->nome;
    }
    
    /**
     * Função para iniciar o valor da propriedade E-MAIL
     * 
     * @param string $email
     */
    public function setEmail($email){
        $this->email = mysql_real_escape_string($email);
    }
    
    /**
     * Função que retorna o valor da propriedade E_MAIL
     * 
     * @return string $email
     */
    public function getEmail(){
        return $this->email;
    }
    
    /**
     * Função para iniciar o valor da propriedade CATEGORIA_ID
     * 
     * @param int $id
     */
    public function setCategoriaId($id){
        $this->categoria_id = (int)$id;
    }
    
    /**
     * Função que retorna o valor da propriedade CATEGORIA_ID
     * 
     * @return int $categoria_id
     */
    public function getAssunto(){
        return $this->categoria_id;
    }
    
    /**
     * Função para iniciar o valor da propriedade MENSAGEM
     * 
     * @param string $msg
     */
    public function setMensagem($msg){
        $this->msg = mysql_real_escape_string($msg);
    }
    
    /**
     * Função que retorna o valor da propriedade MENSAGEM
     * 
     * @return string $msg
     */
    public function getMensagem(){
        return $this->msg;
    }
    
    /**
     * Função para salvar os dados do formulário de SUPORTE e disparar o e-mail ao setor resposável
     * 
     * @return boolean
     */
    public function save(){
        try{
            $sql = "INSERT INTO
                        SPRO_SUPORTE
                        (
                            NOME,
                            EMAIL,
                            CATEGORIA_ID,
                            MENSAGEM,
                            DATA_ENVIO,
                            EMAIL_PARA
                        )
                    VALUES
                        (
                    ";
            
            if($this->nome == '' || $this->nome == null){
                throw new Exception("O campo NOME é obrigatório");
            }else{
                $sql .= " '{$this->nome}', ";
            }
            
            if($this->email == '' || $this->email == null){
                throw new Exception("O campo E-MAIL é obrigatório");
            }else{
                $sql .= " '{$this->email}', ";
            }
            
            if($this->categoria_id == '' || $this->categoria_id == null){
                throw new Exception("O campo CATEGORIA é obrigatório");
            }else{
                $sql .= " '{$this->categoria_id}', ";
                
                $cat    = new Categoria();
                $rs_cat = $cat->listaCategorias($this->categoria_id);
                if($rs_cat != null){
                    $email_suporte = mysql_result($rs_cat, 0, 'EMAIL_CONTATO');
                    if($email_suporte == ''){
                        throw new Exception("Erro ao carregar e-mail do Suporte");
                    }
                }else{
                    throw new Exception("Erro ao listar CATEGORIA");
                }
            }
            
            if($this->msg == '' || $this->msg == null){
                throw new Exception("O campo MENSAGEM é obrigatório");
            }else{
                $sql .= " '{$this->msg}', ";
            }
            
            $sql .= " NOW(), ";
            $sql .= " '{$email_suporte}' ";
            
            $sql .= " ); ";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(!$rs){
                throw new Exception("Falha ao inserir o contato de suporte na base de dados.");
            }else{
                $email = new Email();
                
                if(!$email->enviaSuporte($email_suporte, $this->nome, $this->email, $this->msg)){
                    throw new Exception("Falha ao enviar e-mail ao Suporte");
                }else{
                    return true;
                }
            }
        }catch(Exception $e){
            echo "Erro ao salvar SUPORTE<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
