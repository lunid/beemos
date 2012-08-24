<?php
require_once 'mysql.php';
require_once 'email.php';

/**
 * Classe para manipulação dos dados de Contato
 */
class Contato{
    private $id;
    private $nome;
    private $email;
    private $assunto;
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
     * Função para iniciar o valor da propriedade NOME
     * 
     * @param string $nome
     */
    public function setNome($nome){
        $this->nome = mysql_escape_string($nome);
    }
    
    /**
     * Função que retorna o valor da propriedade NOME
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
        $this->email = mysql_escape_string($email);
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
     * Função para iniciar o valor da propriedade ASSUNTO
     * 
     * @param string $assunto
     */
    public function setAssunto($assunto){
        $this->assunto = mysql_escape_string($assunto);
    }
    
    /**
     * Função que retorna o valor da propriedade ASSUNTO
     * 
     * @return string $assunto
     */
    public function getAssunto(){
        return $this->assunto;
    }
    
    /**
     * Função para iniciar o valor da propriedade MENSAGEM
     * 
     * @param string $msg
     */
    public function setMensagem($msg){
        $this->msg = mysql_escape_string($msg);
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
     * Função que salva os dados do formulário de contato e dispara o e-mail com informações ao setor responsável
     * 
     */
    public function save(){
        try{
            $sql = "INSERT INTO
                        SPRO_CONTATO
                        (
                            NOME,
                            EMAIL,
                            ASSUNTO,
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
            
            if($this->assunto == '' || $this->assunto == null){
                throw new Exception("O campo ASSUNTO é obrigatório");
            }else{
                $sql .= " '{$this->assunto}', ";
            }
            
            if($this->msg == '' || $this->msg == null){
                throw new Exception("O campo MENSAGEM é obrigatório");
            }else{
                $sql .= " '{$this->msg}', ";
            }
            
            $sql .= " NOW(), ";
            $sql .= " 'prg.pacheco@interbits.com.br' ";
            
            $sql .= " ); ";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(!$rs){
                throw new Exception("Falha ao inserir o contato na base de dados.");
            }else{
                $email = new Email();
                
                if(!$email->enviaContato($this->nome, $this->email, $this->assunto, $this->msg)){
                    throw new Exception("Falha ao enviar e-mail de contato");
                }else{
                    return true;
                }
            }
        }catch(Exception $e){
            echo "Erro ao salvar CONTATO<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
