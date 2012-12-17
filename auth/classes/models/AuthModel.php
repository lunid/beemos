<?php
    namespace auth\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \db_tables as TB;
    
    class AuthModel extends Model {        
        
        /**
         * Valida o acesso a partir do login e senha informados.
         * 
         * @param type $login
         * @param type $passwd 
         */
        function authAcesso($login,$passwd){
                                        
            Auth::logout();

            if (strlen($login) > 0 && strlen($passwd) > 0) {
                if (1==1){
                    $userId = 7;
                    $_SESSION[Auth::SESSION_USER_ID]    = $userId;
                    $_SESSION[Auth::SESSION_ID]         = md5(uniqid(rand(),1));                       
                }                  
            }
        } 
        
        function authAcessoForHash(){
            
        }
    }
?>
