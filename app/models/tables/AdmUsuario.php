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
         * Verifica se existe uma conta de cadastro com a conta do RedeSocial Ativa no momento.
         * Caso exista uma conta em nosso banco ele faz ligação de rede social
         * 
         * @return stdClass $ret
         * @throws Exception
         */
        public function verificaRedeSocialUsuario($rede){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $arrObj         = NULL;
                $rede           = ucfirst(trim($rede));                
                
                if($rede == 'Facebook'){
                    if($this->FB_ID <= 0 || $this->FB_ID == ""){
                        $ret->msg = "O Facebook ID não foi setado!";
                        return $ret;
                    }
                    
                    $rs = $this->findAll("FB_ID = '{$this->FB_ID}'");
                }else if($rede == 'Google'){
                    if($this->GOOGLE_ID <= 0 || $this->GOOGLE_ID == ""){
                        $ret->msg = "O Google ID não foi setado!";
                        return $ret;
                    }
                    
                    $rs = $this->findAll("FB_ID = '{$this->FB_ID}'");
                }
                
                $ret->msg = "Nenhum usuário encontrado para esta conta do {$rede}";
                
                if($rs->count() > 0){
                    $arrObj = $rs->getRs();
                    
                    switch($arrObj[0]->STATUS){
                        case 'I':
                            $ret->msg = "Usuário inativo! Ative sua conta agora mesmo através do e-mail que lhe enviamos.";
                            return $ret;
                            break;
                        case 'B':
                            $ret->msg = "Usuário bloqueado! Entre em contato com o Suporte.";
                            return $ret;
                            break;
                    }
                }else{
                    if($rede == 'Facebook'){
                        //Armazenando o FB_ID e EMAIL para Update
                        $REDE_ID = $this->FB_ID;
                    }else if($rede == 'Google'){
                        //Armazenando o GOOGLE_ID e EMAIL para Update
                        $REDE_ID = $this->GOOGLE_ID;
                    }
                    
                    $rs = $this->findAll("EMAIL = '{$this->EMAIL}'");
                    
                    if($rs->count() > 0){
                        $arrObj         = $rs->getRs();
                        if($rede == 'Facebook'){
                            $this->FB_ID = $REDE_ID;
                        }else if($rede == 'Google'){
                            $this->GOOGLE_ID = $REDE_ID;
                        }
                        $arrWhere       = array('ID_USUARIO = %s', $arrObj[0]->ID_USUARIO);
                        
                        $this->update($arrWhere);
                        
                        switch($arrObj[0]->STATUS){
                            case 'I':
                                $ret->msg = "Usuário inativo! Ative sua conta agora mesmo através do e-mail que lhe enviamos.";
                                return $ret;
                                break;
                            case 'B':
                                $ret->msg = "Usuário bloqueado! Entre em contato com o Suporte.";
                                return $ret;
                                break;
                        }
                    }
                }
                
                if(is_array($arrObj)){
                    $ret->status = true;
                    
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
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Valida o usuário e senha enviados, e caso existam na base ele carrega todos os dados do ADM_USUARIO
         * 
         * @return stdClass $ret
         * @throws Exception
         */
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
                            $ret->msg = "Usuário inativo! Ative sua conta agora mesmo através do e-mail que lhe enviamos.";
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
        
        /**
         * Busca o cadatsro do usuário na base de dados, e caso exista gera uma nova senha e dispara por e-mail para ele.
         * 
         * @return stdClass $ret
         * @throws Exception
         */
        public function esqueciSenha(){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "E-mail não encontrado!";
                
                $rs = $this->findAll("EMAIL = '{$this->EMAIL}'");
                
                if($rs->count() == 1){
                    $ret            = new \stdClass();
                    $ret->status    = true;
                    $ret->msg       = "Nova senha enviada para o seu e-mail. Acesse e siga as instruções.";
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Efetua o acdastro de um visitante no site www.site.com.br/cadastro
         * 
         * @param int $id_perfil Perfilno qual deve ser cadastrado o usuário
         * 
         * @return stdClass $ret
         * @throws Exception
         */
        public function cadastrarUsuarioSite($id_perfil = 2){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao efetuar o cadastro! Tente novamente";
                $ret->login     = false;
                $verRede        = false;
                $arrObj         = NULL;
                
                //Armazena dados para utilização de UPDATE
                $FB_ID      = $this->FB_ID;
                $GOOGLE_ID  = $this->GOOGLE_ID;
                
                //Valida e-mail
                $rs = $this->findAll("EMAIL = '{$this->EMAIL}'");
                
                if($rs->count() > 0){
                    $arrObj = $rs->getRs();
                }
                
                //Valida facebook
                if($FB_ID != ''){
                    $rs_fb = $this->findAll("FB_ID = '{$FB_ID}'");

                    if($rs_fb->count() > 0){
                        $ret->msg = "Essa conta de Facebook já possui cadastro!";
                        return $ret;
                    }else if(is_array($arrObj)){
                        $this->FB_ID    = $FB_ID;
                        $this->STATUS   = $arrObj[0]->STATUS;
                        $arrWhere       = array("ID_USUARIO = %s", $arrObj[0]->ID_USUARIO);
                        $this->update($arrWhere);
                        $verRede        = true;
                    }
                }
                //Valida google
                if($GOOGLE_ID != ''){
                    $rs_google = $this->findAll("GOOGLE_ID = '{$GOOGLE_ID}'");

                    if($rs_google->count() > 0){
                        $ret->msg = "Essa conta de Google já possui cadastro!";
                        return $ret;
                    }else if(is_array($arrObj)){
                        $this->GOOGLE_ID    = $GOOGLE_ID;
                        $this->STATUS       = $arrObj[0]->STATUS;
                        $arrWhere           = array("ID_USUARIO = %s", $arrObj[0]->ID_USUARIO);
                        $this->update($arrWhere);
                        $verRede            = true;
                    }
                }
                
                if($rs->count() > 0 && !$verRede){
                    $ret->msg       = "E-mail já cadastrado!";
                    return $ret;
                }else if($rs->count() > 0 && $verRede){
                    $arrObj = $rs->getRs();
                    
                    switch($arrObj[0]->STATUS){
                        case 'I':
                            $ret->msg = "Usuário inativo! Ative sua conta agora mesmo através do e-mail que lhe enviamos.";
                            return $ret;
                            break;
                        case 'B':
                            $ret->msg = "Usuário bloqueado! Entre em contato com o Suporte.";
                            return $ret;
                            break;
                    }
                    
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
                    
                    $ret->status = true;
                    $ret->login  = true;
                    $ret->msg    = "Você já possui uma conta no SuperPro!<br />Apenas relacionamos sua Rede Social à sua conta.<br /><br />Você será logado automaticamente em instantes. Aguarde...";
                    return $ret;
                }
                
                //Inicia perfil do cadatsro como Professor caso não seja definido!
                $this->ID_PERFIL = (int)$this->ID_PERFIL <= 0 ? $id_perfil : (int)$this->ID_PERFIL;
                $id = $this->save();
                
                if($id > 0){
                    $ret->status = true;
                    $ret->msg    = "Usuário cadastrado com sucesso!<br />Você receberá um e-mail em instantes, por favor ative seu cadastrado através do link que lhe enviamos.";                    
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
