<?php
    namespace sys\classes\util;
    
    /**
     * Classse para gerenciamento de cookies da aplicação
     */
    class Cookie {
        /**
         * Recebe a View que deseja controlar o Cookie de memorização de Login.
         * Caso o cookie interbits_login esteja setado, a view receberá dois parâmetros:
         * <br />
         * LOGIN - O login gravado no cookie para exibição no <input>
         * CHECK_MEMORIA - O controle do <checkbox> para memorização
         * 
         * @param View $view
         */
        public static function verMemorizar($view){
            $view->LOGIN         = "";
            $view->CHECK_MEMORIA = "";

            if(isset($_COOKIE['interbits_login'])){
                $view->LOGIN         = $_COOKIE['interbits_login'];
                $view->CHECK_MEMORIA = "checked='checked'";
            }
        }
    }
?>
