<?php
    namespace auth\classes\models;
    
    use \sys\classes\mvc\Model;  
    use \auth\classes\helpers\Error;
    use \common\classes\helpers\Usuario;  
    use \common\db_tables as TB;
    use \common\classes\models\UsuariosModel;
    use \sys\classes\security\Password;   
    
    class AuthModel extends Model {                
        
        /**
         * Carrega um usuário através da HASH cadastrada no Banco
         * 
         * @param string $hash
         * @return boolean|stdClass
         */
        function carregarUsuarioHash($hash){
            $tbUser = new TB\VwUser();
            $tbUser->setLimit(1);
            $rs = $tbUser->findAll("HASH = '{$hash}'");
            $objUsuario = $this->getObjUsuario($rs);       
            return $objUsuario;
        }
        
        /**
         * Valida o acesso a partir do login e senha informados.
         * 
         * @param string $user
         * @param string $passwdMd5 Senha criptografada com MD5 
         * @return User
         */
        function authAcesso($user,$passwdMd5){
                                        
            \Auth::logout();
            $objUsuario = FALSE;
            if (strlen($user) > 0 && strlen($passwdMd5) > 0) {
                $where = "(EMAIL = '{$user}' OR LOGIN = '{$user}') AND PASSWD = '{$passwdMd5}'";
                
                //Verifica se a senha é administrativa
                $acessoAdmin    = $this->authAcessoAdmin($passwdMd5);
                if ($acessoAdmin) {
                    //O usuário entrou com senha administrativa. Localiza os dados da conta solicitada sem checar senha.
                    $where = "(EMAIL = '{$user}' OR LOGIN = '{$user}')";
                }
                                
                //Verifica no DB se o acesso é válido.    
                $tbUser = new TB\VwUser();                
                $tbUser->setLimit(1);
                $rs = $tbUser->findAll($where);
                $objUsuario = $this->getObjUsuario($rs);                 
            }
            return $objUsuario;
        } 
        
        /**
         * Verifica se o acesso atual é feito com senha administrativa, checando também se o horário de acesso é permitido.
         * 
         * @param type $passwdMd5
         * @return boolean
         */
        public function authAcessoAdmin($passwdMd5){
            $tbUser = new TB\UserAdmin();
            $tbUser->setLimit(1);
            $hrAtual    = date('H:i:s');
            $rs         = $tbUser->findAll("PASSWD = '{$passwdMd5}' AND '{$hrAtual}' >= EXPED_HR_INI AND '{$hrAtual}' <= EXPED_HR_FIM");    
            return $this->getObjUsuario($rs);                                 
        }        
        
        private function getObjUsuario($rs=array()){
            $objUsuario = FALSE;
            $numItens   = $rs->count();
            if($numItens > 0) {
                $arrFields      = $rs->getRs();               
                $objUsuario     = new Usuario($arrFields[0]);                                   
            }
            return $objUsuario;
        }    
               
        function authAcessoForHash(){
            
        }
        
        /**
         * Efetua a busca dos dados do Usuário através do Facebook
         * 
         * @param array $fbUser Dados carregado no Facebook
         * @return \stdClass
         */
        function buscarUserFacebook($fbUser){
            //Objeto de retorno
            $ret            = new \stdClass();
            $ret->status    = false;
            $ret->user      = false;
            $ret->msg       = Error::eLogin("FALHA_PADRAO");
            
            //Tabela SPRO_USER
            $tbUser = new TB\User();
            
            //Busca Usuário pelo ID do Facebook
            $tbUser->setLimit(1);
            $rsUser = $tbUser->findAll("FB_ID = '{$fbUser['id']}'");
            
            //Verifica retorno
            if($rsUser->count() > 0){
                //Aramazena dados encontrados
                $user = $rsUser->getRs();
                
                //Se encontrado, retorno OK
                $ret->status = true;
                $ret->msg    = "Usuário encontrado!";
                $ret->user   = $user[0];
                return $ret;
            }
            
            //Se não, busca por email
            $tbUser->setLimit(1);
            $rsUser = $tbUser->findAll("EMAIL = '{$fbUser['email']}'");
            
            //Verifica retorno
            if($rsUser->count() > 0){
                //Aramazena dados encontrados
                $user = $rsUser->getRs();
                
                //Se encontrado, grava Facebook ID e retorna OK
                $tbUser->FB_ID = $fbUser['id'];
                $tbUser->update(array("ID_USER = %i AND EMAIL = %s", $user[0]->ID_USER, $user[0]->EMAIL));
                
                $ret->status = true;
                $ret->msg    = "Usuário encontrado!";
                $ret->user   = $user[0];
                return $ret;
            }else{
                $ret->msg    = "Login/E-mail do Facebook não encontrado!";
                return $ret;
            }
        }
    }
?>
