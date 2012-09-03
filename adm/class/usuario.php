<?php

require_once '../class/mysql.php';
require_once 'perfil.php';

/**
 * Classe para controle de dados de ADM_USUARIO
 */
class Usuario{
    private $id_usuario;
    private $nome;
    private $email;
    private $senha;
    private $id_perfil;
    private $status;
    private $descricao_status;
    private $data_registro;
    private $ultimo_acesso;
    
    private $obj_perfil;
    
    /**
     * Função para iniciar o valor da propriedade ID_USUARIO
     * 
     * @param int $id
     */
    public function setIdUsuario($id){
        $this->id_usuario = (int)$id;
    }
    
    /**
     * Função para iniciar o valor da propriedade ID_USUARIO
     * 
     * @return int
     */
    public function getIdUsuario(){
        return $this->id_usuario;
    }
    
    /**
     * Função para iniciar o valor da propriedade NOME
     * 
     * @param string $id
     */
    public function setNome($nome){
        $this->nome = mysql_escape_string($nome);
    }
    
    /**
     * Função que retorna o valor da propriedade NOME
     * 
     * @return string
     */
    public function getNome(){
        return $this->nome;
    }
    
    /**
     * Função para iniciar o valor da propriedade EMAIL
     * 
     * @param string $email
     */
    public function setEmail($email){
        $this->email = mysql_escape_string($email);
    }
    
    /**
     * Função que retorna o valor da propriedade EMAIL
     * 
     * @return string
     */
    public function getEmail(){
        return $this->email;
    }
    
    /**
     * Função para iniciar o valor da propriedade SENHA
     * 
     * @param string $senha
     */
    public function setSenha($senha){
        $this->senha = mysql_escape_string($senha);
    }
    
    /**
     * Função que retorna o valor da propriedade SENHA
     * 
     * @return string
     */
    public function getSenha(){
        return $this->senha;
    }
    
    /**
     * Função para iniciar o valor da propriedade ID_PERFIL
     * 
     * @param int $id_perfil
     */
    public function setIdPerfil($id_perfil){
        $this->id_perfil = (int)$id_perfil;
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
     * Função para iniciar o valor da propriedade STATUS
     * 
     * @param string $status
     */
    public function setStatus($status){
        switch ($status) {
            case 'A':
                $this->descricao_status = 'Ativo';
                break;
            case 'I':
                $this->descricao_status = 'Inativo';
                break;
            case 'B':
                $this->descricao_status = 'Bloqueado';
                break;
            default:
                $this->descricao_status = $status;
                break;
        }
        
        $this->status = $status;
    }
    
    /**
     * Função que retorna o valor da propriedade STATUS
     * 
     * @return string
     */
    public function getStatus(){
        return $this->status;
    }
    
    /**
     * Função que retorna o valor da propriedade DESCRICAO_STATUS
     * 
     * @return string
     */
    public function getDescricaoStatus(){
        return $this->descricao_status;
    }
    
    /**
     * Função para iniciar o valor da propriedade DATA_REGISTRO
     * 
     * @param string $data_registro
     */
    public function setDataRegistro($data_registro){
        $this->data_registro = $data_registro;
    }
    
    /**
     * Função que retorna o valor da propriedade DATA_REGISTRO
     * 
     * @return string
     */
    public function getDataRegistro(){
        return $this->data_registro;
    }
    
    /**
     * Função para iniciar o valor da propriedade ULTIMO_ACESSO
     * 
     * @param string $ultimo_acesso
     */
    public function setUltimoAcesso($ultimo_acesso){
        $this->ultimo_acesso = $ultimo_acesso;
    }
    
    /**
     * Função que retorna o valor da propriedade ULTIMO_ACESSO
     * 
     * @return string
     */
    public function getUltimoAcesso(){
        return $this->ultimo_acesso;
    }
    
    /**
     * Função que retorna o valor do Objeto Perfil
     * 
     * @return Perfil
     */
    public function getPerfil(){
        return $this->obj_perfil;
    }
    
    /**
     * Funcção que efetua INSERÇÃO ou ou UPDATE do dados do Usuário.
     * Se o ID_USUARIO for iniciado, a função fará o UPDATE de dados.
     * Caso contrário, será feita a INSERÇÃO dos dados com uma validação de E-MAIL existente.
     * 
     * @return \stdClass Objeto com o retorno final da operação. Propriedade status e msg.
     */
    public function save(){
        try{
            //Variável de retorno
            $ret = new stdClass();
            
            if($this->id_usuario == 0 || $this->id_usuario == null){
                if($this->validaEmailUsuario()){
                    $ret->status    = false;
                    $ret->msg       = "Esse e-mail ({$this->email}) já está cadastrado!";
                    return $ret;
                }
                
                $sql = "INSERT INTO
                            SPRO_ADM_USUARIO
                            (
                                NOME,
                                EMAIL,
                                ID_PERFIL,
                                SENHA,
                                DATA_REGISTRO
                            )
                        VALUES
                            (
                                '{$this->nome}',
                                '{$this->email}',
                                '{$this->id_perfil}',
                                '".md5($this->senha)."',
                                NOW()
                            )
                        ;";
                
                MySQL::connect();
                MySQL::executeQuery($sql);
                
                $ret->status    = true;
                $ret->msg       = "Usuário cadastrado com sucesso!";
                return $ret;
            }else{
                $sql = "UPDATE
                            SPRO_ADM_USUARIO
                        SET
                            nome = '{$this->nome}',
                            id_perfil = {$this->id_perfil}
                        ";
               
                if(trim($this->senha) != ''){
                    $sql .= " ,senha = '".md5(trim($this->senha))."' ";
                }
                
                $sql .= " WHERE 
                            ID_USUARIO = {$this->id_usuario}
                         AND
                            EMAIL = '{$this->email}'
                       ;";
                            
                MySQL::connect();
                MySQL::executeQuery($sql);
                
                $ret->status    = true;
                $ret->msg       = "Usuário alterado com sucesso!";
                return $ret;
            }
        }catch(Exception $e){
            echo "Erro salvar dados do Usuário<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função que valida o usuário e senha da tela de LOGIN do painel administrativo.
     * É necessário iniciar os valor de EMAIL e SENHA para que a função seja executada
     * 
     * @return boolean 
     */
    public function efetuaLogin(){
        try{
            //Valida valor de e-mail
            if($this->email == '' || $this->email == null){
                throw new Exception("O campo EMAIL é obrigatório para efetuar o login");
            }
            
            //Valida valor de senha
            if($this->senha == '' || $this->senha == null){
                throw new Exception("O campo SENHA é obrigatório para efetuar o login");
            }
            
            $sql = "SELECT 
                        U.ID_USUARIO,
                        U.NOME,
                        U.EMAIL,
                        U.SENHA,
                        U.ID_PERFIL,
                        U.STATUS,
                        U.DATA_REGISTRO,
                        U.ULTIMO_ACESSO,
                        P.STATUS AS 'PERFIL_STATUS',
                        P.DESCRICAO AS 'PERFIL_DESCRICAO'
                    FROM 
                        SPRO_ADM_USUARIO U
                    INNER JOIN
                        SPRO_ADM_PERFIL P ON P.ID_PERFIL = U.ID_PERFIL
                    WHERE
                        U.EMAIL = '{$this->email}'
                    AND
                        U.SENHA = '".md5($this->senha)."'
                    LIMIT 
                        1
                    ;";
                        
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            //Caso o usuario seja encontrado, todas as propriedades do objeto são preenchidas
            if(mysql_num_rows($rs) == 1){
                $this->id_usuario       = mysql_result($rs, 0, "ID_USUARIO");
                $this->nome             = mysql_result($rs, 0, "NOME");
                $this->email            = mysql_result($rs, 0, "EMAIL");
                $this->senha            = mysql_result($rs, 0, "SENHA");
                $this->id_perfil        = mysql_result($rs, 0, "ID_PERFIL");
                $this->data_registro    = mysql_result($rs, 0, "DATA_REGISTRO");
                $this->ultimo_acesso    = mysql_result($rs, 0, "ULTIMO_ACESSO");
                $this->setStatus(mysql_result($rs, 0, "STATUS"));
                
                //Inicia uma instância do Objeto Perfil
                $perfil = new Perfil();
                $perfil->setIdPerfil($this->id_perfil);
                $perfil->setDescricao(mysql_result($rs, 0, "PERFIL_DESCRICAO"));
                $perfil->setStatus(mysql_result($rs, 0, "PERFIL_STATUS"));
                
                $this->obj_perfil = $perfil;
                
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            echo "Erro efetuar Login<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função que atualiza a data de ULTIMO_ACESSO do Usuário.
     * 
     * @return boolean
     */
    public function atualizaUltimoAcesso(){
        try{
            //Valida valor de ID_USUARIO
            if($this->id_usuario == 0 || $this->id_usuario == null){
                throw new Exception("O campo ID_USUARIO é obrigatório para efetuar a atualização de acesso");
            }
            
            $sql = "UPDATE
                        SPRO_ADM_USUARIO 
                    SET
                        ULTIMO_ACESSO = NOW()
                    WHERE
                        ID_USUARIO = {$this->id_usuario}
                    ;";
                        
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if($rs){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            echo "Erro ao atualizar Último Acesso<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função que valida o objeto Usuario carregado para cesso ao sistema
     * 
     * @param Usuario $usuario
     * @return \stdClass
     */
    public static function validaAcesso(Usuario $usuario){
        try{
            //Variável de retorno
            $ret = new stdClass();
            
            if($usuario->getStatus() != 'A'){
                $ret->status    = false;
                $ret->msg       = "Usuário {$usuario->getDescricaoStatus()}! Entre em contato com o Suporte.";
            }else{
                if($usuario->getPerfil()->getStatus() != 1){
                    $ret->status    = false;
                    $ret->msg       = "Este Perfil de usuário esta inativado! Entre em contato com o Suporte.";                    
                }else{
                    $ret->status    = true;
                }
            }
            
            return $ret;
        }catch(Exception $e){
            echo "Erro ao validar acesso do Usuário<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função para validação de existência do E-MAIL de usuário.
     * 
     * @return boolean
     */
    public function validaEmailUsuario(){
        try{
            if($this->email == '' || $this->email == null){
                throw new Exception("O campo EMAIL é obrigatório para efetuar a validação do Usuário");
            }
            
            $sql = "SELECT ID_USUARIO FROM SPRO_ADM_USUARIO WHERE EMAIL = '{$this->email}' LIMIT 1 ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) == 1){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            echo "Erro ao validar Usuário<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função para validação de existência do E-MAIL de usuário.
     * 
     * @return boolean
     */
    public function carregaUsuario($id_usuario){
        try{
            if((int)$id_usuario == 0){
                throw new Exception("O campo ID_USUARIO é obrigatório para carregar os dados do Usuário");
            }
            
            $sql = "SELECT
                        *
                    FROM
                        SPRO_ADM_USUARIO
                    WHERE
                        ID_USUARIO = {$id_usuario}
                    LIMIT
                        1
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) == 1){
                $this->id_usuario       = mysql_result($rs, 0, 'ID_USUARIO');
                $this->nome             = mysql_result($rs, 0, 'NOME');
                $this->email            = mysql_result($rs, 0, 'EMAIL');
                $this->id_perfil        = mysql_result($rs, 0, 'ID_PERFIL');
                $this->status           = mysql_result($rs, 0, 'STATUS');
                $this->data_registro    = mysql_result($rs, 0, 'DATA_REGISTRO');
                $this->ultimo_acesso    = mysql_result($rs, 0, 'ULTIMO_ACESSO');
                
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            echo "Erro ao validar Usuário<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função que carrega as matérias que o usuário possui pendentes de avaliação no TOP 10
     * 
     * @return array Array com o resultado da busca SQL
     */
    public function carregaMateriasUsuarioAvaliacao(){
        try{
            //Variável de retorno
            $ret = array();
            
            if($this->id_usuario == 0){
                throw new Exception("O campo ID_USUARIO é obrigatório para carregar suas Matérias");
            }
            
            $sql = "SELECT
                        UM.ID_MATERIA,
                        MQ.MATERIA
                    FROM
                        SPRO_ADM_USUARIO_MATERIA UM
                    INNER JOIN
                        SPRO_MATERIA_QUESTAO MQ ON MQ.ID_MATERIA = UM.ID_MATERIA
                    INNER JOIN
                        SPRO_USUARIO_AVALIA_MATERIA AM ON AM.ID_MATERIA = UM.ID_MATERIA AND AM.ID_USUARIO = UM.ID_USUARIO
                    WHERE
                        UM.ID_USUARIO = {$this->id_usuario}
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) > 0){
                while ($row = mysql_fetch_object($rs)) {
                    $ret[] = $row;
                }
            }
            
            return $ret;
        }catch(Exception $e){
            echo "Erro ao carregar Matérias para avaliação do Usuário<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    /**
     * Função responsável em validar a permissão de avalição de questões ao usuário logado.
     * 
     * @param int $id_materia Código da matéria a ser validada
     * 
     * @return boolean
     */
    public function validaUsuarioAvaliacao($id_questao = 0){
        try{
            //Se ADM...
            if($this->id_perfil == 1){
                return true;
            }
            
            if($this->id_usuario == 0){
                throw new Exception("O campo ID_USUARIO é obrigatório para efertuar a validação de acesso");
            }
            
            if((int)$id_questao == 0){
                throw new Exception("O campo ID_BCO_QUESTAO é obrigatório para efertuar a validação de acesso");
            }
            
            $sql = "SELECT
                        UQ.ID_USUARIO,
                        UQ.ID_BCO_QUESTAO
                    FROM
                        SPRO_BCO_QUESTAO Q
                    INNER JOIN
                        SPRO_USUARIO_AVALIA_QUESTAO UQ ON UQ.ID_USUARIO = UQ.ID_USUARIO AND UQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                    WHERE
                        UQ.ID_USUARIO = {$this->id_usuario}
                    AND
                        UQ.ID_BCO_QUESTAO = {$id_questao}
                    LIMIT 
                        1
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) == 1){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            echo "Erro ao validar a permissão de avalições do Usuário<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
