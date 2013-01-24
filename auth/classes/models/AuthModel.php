<?php
    namespace auth\classes\models;
    
    use \sys\classes\security\Password;
    use \sys\classes\mvc\Model;  
    use \auth\classes\helpers\Usuario;    
    use \auth\classes\helpers\Error;    
    use \common\db_tables as TB;
    use \common\classes\models\UsuariosModel;    
    
    class AuthModel extends Model {        
        
        /**
         * Verifica se a senha atual é senha de administrador.
         *          
         * @param string $passwdMd5
         * @return boolean
         */
        function passwdAdmin($passwdMd5){
            $passwdAdmin = FALSE;
            
            return $passwdAdmin;
        }
        
        /**
         * Carrega um usuário através da HASH cadastrada no Banco
         * 
         * @param string $hash
         * @return boolean|stdClass
         */
        function carregarUsuarioHash($hash){
            $tbUser = new TB\User();
            $tbUser->setLimit(1);
            $rs = $tbUser->findAll("HASH = '{$hash}'");
            
            //Retorn FALSE se não encontrar HASH
            if($rs->count() <= 0) return false;
            
            //Senão retorna dados do usuário
            $arrFields  = $rs->getRs();
            $objUser    = new Usuario($arrFields[0]);            

            return $objUser;
        }
        
        /**
         * Valida o acesso a partir do login e senha informados.
         * 
         * @param string $user
         * @param string $passwdMd5 Senha criptografada com MD5 
         * @param boolena $admin Se TRUE significa que o acesso atual foi feito com senha administrativa
         * @return User
         */
        function authAcesso($user,$passwdMd5,$admin=FALSE){
                                        
            \Auth::logout();
            $objUsuario = FALSE;
            if (strlen($user) > 0 && strlen($passwdMd5) > 0) {
                //Verifica no DB se o acesso é válido.
                $tbUser = new TB\VwUser();
                $tbUser->setLimit(1);
                //$rs = $tbUser->findAll("(EMAIL = '{$user}' OR LOGIN = '{$user}') AND PASSWD = '{$passwdMd5}'");
                $rs         = $tbUser->findAll("(EMAIL = '{$user}' OR LOGIN = '{$user}')");
                $numItens   = $rs->count();
                if($numItens == 0)return FALSE;                
                
                $arrFields  = $rs->getRs();
               
                $objUsuario    = new Usuario($arrFields[0]);                  
            }
            return $objUsuario;
        } 
        
        function authAcessoAdmin($user,$passwd){
                       
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
