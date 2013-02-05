<?php
    namespace auth\classes\helpers;
    
    use \auth\classes\models\AuthModel;
    
    class AuthHelper {
        /**
         * Verifica se a senha enviada, é um senha de Admin e retorna TRUE ou FALSE
         * 
         * @param string $passwdMd5 Senha MD5 à ser verificada
         * 
         * @return boolean
         */
        public static function passAdmin($passwdMd5){
            $mdAuth = new AuthModel();
            $rs     = $mdAuth->authAcessoAdmin($passwdMd5);
            
            if($rs == false){
                return false;
            }else{
                return true;
            }
        }
    }
?>
