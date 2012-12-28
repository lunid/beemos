<?php

/**
 * Classe utilizada para fazer a autenticação do usuário em páginas protegidas.
 *
 * @author Supervip
 */
use \sys\classes\util\Request;

class Auth {

    const SESSION_USER_ID   = 'sessionUserId';
    const SESSION_ID        = 'sessionId';
    const SESSION_MESSAGE   = 'sessionAuthMessage';
        
    /**
    * Destrói a autenticação do usuário logado. 
    */
    public static function logout(){
        unset($_SESSION[self::SESSION_USER_ID]);
        unset($_SESSION[self::SESSION_ID]);   
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
    
    
    /*
     * Inicializa uma sessão para um usuário autenticado.
     * 
     * @param integer $userId ID autonumber do registro do usuário.
     */
    public static function initSession($userId){
        $_SESSION[self::SESSION_USER_ID]    = (int)$userId;
        $_SESSION[self::SESSION_ID]         = md5(uniqid(rand(),1));  
    }
    
    /**
     * Persiste uma mensagem capturada no processo de autenticação do usuário.
     * A persistência é feita em uma variável de sessão.
     * 
     * Exemplo:
     * Caso falhe a autenticação de usuário e senha, uma mensagem é capturada via classe sys\classes\util\Dic.
     * Essa mensagem, ao ser persistida por \Auth::setMessage(), pode ser mostrada no formulário de acesso.
     * 
     * @param string $message
     * @return void
     */
    public static function setMessage($message){
        $_SESSION[self::SESSION_MESSAGE] = $message;
    }
    
    public static function unsetMessage(){
        unset($_SESSION[self::SESSION_MESSAGE]);
    }
    
    /**
     * Retorna uma mensagem persisitida no processo de autenticação.
     * 
     * @return string
     */
    public static function getMessage(){
        $message = Request::session(self::SESSION_MESSAGE);       
        return $message;
    }
}

?>
