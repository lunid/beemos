<?php
    namespace app\models\tables; 
    use \sys\classes\db\ORM;
    /**
     * Classe para manipulação de dados da table SPRO_ADM_USUARIO
     * 
     * @property int ID_USUARIO
     * @property string NOME
     * @property string EMAIL
     * @property string SENHA
     * @property int ID_PERFIL
     * @property char STATUS
     * @property datetime DATA_REGISTRO
     * @property datetime ULTIMO_ACESSO
     * 
     */
    class AdmUsuario extends ORM{
        /**
         * Verifica se existe uma conta de cadastro com a conta do Facebook Ativa no momento.
         * 
         * @return stdClass $ret
         * @throws Exception
         */
        public function verificaUserFacebook(){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Nenhum usuário encontrado para esta conta de Facebook";
                
                if($this->FB_ID <= 0 || $this->FB_ID == ""){
                    $ret->msg = "O Facebook ID não foi setado!";
                    return $ret;
                }
                
                $rs = $this->findAll("FB_ID = {$this->FB_ID} AND STATUS = 'A'");
                
                if($rs->count() > 0){
                    $arrObj         = $rs->getRs();
                    $ret->status    = true;
                    
                    //Iniciando dados do objeto
                    $this->ID_USUARIO       = $arrObj[0]->ID_USUARIO;
                    $this->NOME             = $arrObj[0]->NOME;
                    $this->EMAIL            = $arrObj[0]->EMAIL;
                    $this->SENHA            = $arrObj[0]->SENHA;
                    $this->ID_PERFIL        = $arrObj[0]->ID_PERFIL;
                    $this->STATUS           = $arrObj[0]->STATUS;
                    $this->FB_ID            = $arrObj[0]->FB_ID;
                    $this->DATA_REGISTRO    = $arrObj[0]->DATA_REGISTRO;
                    $this->ULTIMO_ACESSO    = $arrObj[0]->ULTIMO_ACESSO;
                    
                    $ret->msg = "Usuário encontrado";
                    return $ret;
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Verifica se existe uma conta de cadastro com a conta do Google Ativa no momento.
         * 
         * @return stdClass $ret
         * @throws Exception
         */
        public function verificaUserGoogle(){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Nenhum usuário encontrado para esta conta de Google";
                
                if($this->GOOGLE_ID <= 0 || $this->GOOGLE_ID == ""){
                    $ret->msg = "O Google ID não foi setado!";
                    return $ret;
                }
                
                $rs = $this->findAll("GOOGLE_ID = {$this->GOOGLE_ID} AND STATUS = 'A'");
                
                if($rs->count() > 0){
                    $arrObj         = $rs->getRs();
                    $ret->status    = true;
                    
                    //Iniciando dados do objeto
                    $this->ID_USUARIO       = $arrObj[0]->ID_USUARIO;
                    $this->NOME             = $arrObj[0]->NOME;
                    $this->EMAIL            = $arrObj[0]->EMAIL;
                    $this->SENHA            = $arrObj[0]->SENHA;
                    $this->ID_PERFIL        = $arrObj[0]->ID_PERFIL;
                    $this->STATUS           = $arrObj[0]->STATUS;
                    $this->GOOGLE_ID        = $arrObj[0]->GOOGLE_ID;
                    $this->DATA_REGISTRO    = $arrObj[0]->DATA_REGISTRO;
                    $this->ULTIMO_ACESSO    = $arrObj[0]->ULTIMO_ACESSO;
                    
                    $ret->msg = "Usuário encontrado";
                    return $ret;
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function validaUsuarioSenha(){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Login e/ou Senha inválido(s)!";
                
                $rs = $this->findAll("EMAIL = '{$this->EMAIL}' AND SENHA = '".md5($this->SENHA)."'");
                
                if($rs->count() == 1){
                    $arrObj = $rs->getRs();
                    
                    //Iniciando dados do objeto
                    $this->ID_USUARIO       = $arrObj[0]->ID_USUARIO;
                    $this->NOME             = $arrObj[0]->NOME;
                    $this->EMAIL            = $arrObj[0]->EMAIL;
                    $this->SENHA            = $arrObj[0]->SENHA;
                    $this->ID_PERFIL        = $arrObj[0]->ID_PERFIL;
                    $this->STATUS           = $arrObj[0]->STATUS;
                    $this->FB_ID            = $arrObj[0]->FB_ID;
                    $this->GOOGLE_ID        = $arrObj[0]->GOOGLE_ID;
                    $this->DATA_REGISTRO    = $arrObj[0]->DATA_REGISTRO;
                    $this->ULTIMO_ACESSO    = $arrObj[0]->ULTIMO_ACESSO;
                    
                    switch ($this->STATUS) {
                        case 'B':
                            $ret->msg = "Usuário Bloqueado no momento! Entre em contato com nosso suporte.";
                            return $ret;
                            break;
                        case 'I':
                            $ret->msg = "Usuário Inativo no momento! Entre em contato com nosso suporte.";
                            return $ret;
                            break;
                        case 'A':
                            $ret->status    = true;
                            $ret->msg       = "Usuário Ativo";
                            return $ret;
                            break;
                        default:
                            $ret->msg = "Status não reconhecido! ({$this->STATUS})";
                            return $ret;
                            break;
                    }
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
