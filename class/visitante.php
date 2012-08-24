<?php
require_once 'mysql.php';
require_once 'email.php';

/**
 * Classe para manipulação dos dados de Contato
 */
class Visitante{
    private $id;
    private $nome;
    private $email;
    private $ddd;
    private $telefone;
    private $fb_id;
    private $google_id;
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
     * Função para iniciar o valor da propriedade TELEFONE
     * 
     * @param string $telefone
     */
    public function setTelefone($telefone){
        $telefone = str_replace("(", "", $telefone);
        $telefone = str_replace(")", "", $telefone);
        $telefone = str_replace("-", "", $telefone);
        
        $ddd        = trim(substr($telefone, 0, 2));
        $telefone   = trim(substr($telefone, 2));
        
        $this->setDDD($ddd);
        
        $this->telefone = (int)$telefone;
    }
    
    /**
     * Função que retorna o valor da propriedade TELEFONE
     * 
     * @return string $telefone
     */
    public function getTelefone(){
        return $this->telefone;
    }
    
    /**
     * Função para iniciar o valor da propriedade DDD
     * 
     * @param string $ddd
     */
    public function setDDD($ddd){
        $this->ddd = (int)$ddd;
    }
    
    /**
     * Função que retorna o valor da propriedade DDD
     * 
     * @return string $ddd
     */
    public function getDDD(){
        return $this->ddd;
    }
    
    /**
     * Função para iniciar o valor de ID da Rede Social utilizada
     * 
     * @param int $id
     */
    public function setSocialId($id, $rede){
        switch ($rede) {
            case 'facebook':
                $this->fb_id = $id;
                break;
            case 'google':
                $this->google_id = $id;
                break;
        }
    }
    
    /**
     * Função para iniciar o valor da propriedade FB_ID
     * 
     * @return int $fb_id
     */
    public function getFacebookId(){
        return $this->fb_id;
    }
    
    /**
     * Função para iniciar o valor da propriedade GOOGLE_ID
     * 
     * @return int $google_id
     */
    public function getGoogleId(){
        return $this->google_id;
    }
    
    /**
     * Função que salva os dados do visitante
     * 
     * @return array $ret
     */
    public function save(){
        try{
            $ret = array();
            
            if($this->validaVisitante()){
                $ret['status']  = 0;
                $ret['msg']     = 'Usuário já cadastrado';
                return $ret;
            }
            
            $sql = "INSERT INTO
                        SPRO_VISITANTE
                        (
                            NOME,
                            EMAIL,
                            DDD,
                            TELEFONE,
                            FB_ID,
                            GOOGLE_ID,
                            SENHA,
                            DATA_REGISTRO
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
            
            //Gera senha para o usuário
            $this->senha = $this->geraSenha($this->nome);
            
            $sql .= " '{$this->ddd}', ";
            
            $sql .= " '{$this->telefone}', ";   
            
            $sql .= " '{$this->fb_id}', ";   
            
            $sql .= " '{$this->google_id}', ";
            
            $sql .= " '".md5($this->senha)."', ";
            
            $sql .= " NOW() ";
            
            $sql .= " ); ";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(!$rs){
                throw new Exception("Falha ao inserir o visitante na base de dados.");
            }else{
                $email = new Email();
                $email->enviaCadastroVisitante($this->nome, $this->email, $this->senha);
                
                $ret['status']  = 0;
                $ret['msg']     = "Usuário cadastrado com sucesso! Confirme seu cadastro por e-mail";
                return $ret;
            }
        }catch(Exception $e){
            echo "Erro ao salvar CONTATO<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Funcção que valida a existência do visitante através do E-MAIL
     * 
     * @return boolean
     */
    public function validaVisitante(){
        try{
            $sql = "SELECT ID_VISITANTE FROM SPRO_VISITANTE WHERE EMAIL = '{$this->email}';";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            echo "Erro ao validar VISITANTE<br />\n";
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
