<?php
    namespace auth\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \db_tables as TB;
    
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
         * Valida o acesso a partir do login e senha informados.
         * 
         * @param string $user
         * @param string $passwdMd5 Senha criptografada com MD5 
         * @param boolena $admin Se TRUE significa que o acesso atual foi feito com senha administrativa
         * @return User
         */
        function authAcesso($user,$passwdMd5,$admin=FALSE){
                                        
            \Auth::logout();
            $objUser = FALSE;
            if (strlen($user) > 0 && strlen($passwdMd5) > 0) {
                //Verifica no DB se o acesso é válido.
                //@TODO Implementar verificação de acesso no DB
                $ret = TRUE;
                if ($ret !== FALSE) {
                    $objUser            = new \stdClass();
                    $objUser->id        = 7;
                    $objUser->nome      = 'Claudio Rubens';
                    $objUser->login     = 'claudio';
                    $objUser->email     = 'claudio@supervip.com.br';
                    $objUser->perfil    = 'PRO'; //Valores possíveis: PRO, ESC, ALN
                }
            }
            return $objUser;
        } 
        
        function authAcessoAdmin($user,$passwd){
                       
        }
               
        function authAcessoForHash(){
            
        }
    }
?>
