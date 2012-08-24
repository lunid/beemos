<?php
require_once 'mysql.php';
require_once 'email.php';

/**
 * Classe para manipulação dos dados de Usuário
 */
class Usuario{
    private $id;
    private $nome;
    private $email;
    private $senha;
    
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
        $this->nome = mysql_real_escape_string($nome);
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
     * Função busca os dados do usuário e reenvia a senha dele
     * 
     * @return array $ret
     */
    public function reenviarSenha(){
        try{
            $ret = array();
            
            $sql = "SELECT VISITANTE_ID, NOME, EMAIL FROM SPRO_VISITANTE WHERE EMAIL = '{$this->email}';";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                $this->nome = mysql_result($rs, 0, 'NOME');
                
                $this->senha = $this->geraSenha($this->nome);
                
                //Atualiza senha do usuário
                $sql = "UPDATE SPRO_VISITANTE SET SENHA = '" . md5($this->senha) . "' WHERE VISITANTE_ID = " . mysql_result($rs, 0, 'VISITANTE_ID') . ";";
                MySQL::executeQuery($sql);
                
                $email = new Email();
                $email->reenviaSenha($this->nome, $this->email, $this->senha);
                
                $ret['status']  = 0;
                $ret['msg']     = 'Sua senha foi redefinida e enviada para seu e-mail: ' . $this->email;
            }else{
                $ret['status']  = 1;
                $ret['msg']     = 'Usuário não encontrado';
            }
            
            return $ret;
        }catch(Exception $e){
            echo "Erro ao reenviar senha do Usuário<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função que geradinamicamente a senha do visitante
     * 
     * @return string $senha
     */
    public function geraSenha($nome){
        try{
            $tam    = strlen($nome);
            $tam--;
            
            $nome   = str_replace(" ", "_", trim($nome));
            
            return $nome[rand(0, $tam)] . $nome[rand(0, $tam)] . $nome[rand(0, $tam)] . $nome[rand(0, $tam)] . '_2012';
        }catch(Exception $e){
            echo "Erro ao gerar Senha<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
