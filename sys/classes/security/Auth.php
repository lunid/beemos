<?php

/**
 * Classe utilizada para fazer a autenticação do usuário em páginas protegidas.
 *
 * @author Supervip
 */
class Auth {

    const SESSION_USER_ID   = 'sessionUserId';
    const SESSION_ID        = 'sessionId';
    
        
    /**
    * Destrói a autenticação do usuário logado. 
    */
    private function logout(){
        unset($_SESSION[self::SESSION_USER_ID]);
        unset($_SESSION[self::SESSION_ID]);   
        session_destroy();
    }      
    
    /**
     * Verifica se há uma sessão ativa
     * 
     * @return boolean 
     */
    public static function checkAuth($redirect=''){
        $userId     = (int)(isset($_SESSION[self::SESSION_USER_ID]))?$_SESSION[self::SESSION_USER_ID]:0;
        $sessionId  = (isset($_SESSION[self::SESSION_ID]))?$_SESSION[self::SESSION_ID]:'';
        $out        = FALSE;
        
        if ($userId > 0 && strlen($sessionId) > 0) {
            $out = TRUE;//Session ativa
        } elseif(strlen($redirect) > 0) {
            header('Location:'.$redirect);
            die();
        }
        return $out;        
    }
}

?>
