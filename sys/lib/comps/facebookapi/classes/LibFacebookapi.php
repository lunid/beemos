<?php
/**
 * COMPONENTE Facebook:
 * Cria o conjunto de serviços que podem ser utilizados na API do Facebook
 *
 * Exemplo de uso:
 * <code>
 * </code>
 */
use \sys\lib\classes\LibComponent;
use \sys\lib\classes\Url;

class Facebookapi extends LibComponent {
    //Define configurações da classe
    private $config;
    
    private $redirectUrl = "http://dev.superproweb.com.br";
    
    public function init(){	
        try{
            //Inclui arquivo da biblioteca
            $rootComps = Url::pathRootComps('facebookapi');
            require_once($rootComps.'src/facebook/src/facebook.php'); 
            
            //Ambiente de teste
            //$appId  = '514528098573528';
            //$secret = '42b8ed58d8523ac43a7c40a4c4b42a4d';
            
            //Ambiente de produção
            $appId  = '597519426928589';
            $secret = 'c97ab4835e7f7bcfc8e8825e91b79c94';
            
            $this->config = array(
                'appId'         => $appId,
                'secret'        => $secret,
                'fileUpload'    => false
            );
            
            $this->setReturn($this);
        }catch(Exception $e){
            throw $e;    
        }
    }
    
    public function getAppId(){
        return $this->config['appId'];
    }
    
    public function getSecretId(){
        return $this->config['secret'];
    }
    
    public function getRedirectUrl(){
        return $this->redirectUrl . '/auth/login/facebook';
    }
    
    public function getRedirectUrlCadastro(){
        return $this->redirectUrl . '/auth/cadastro/facebook';
    }
    
    public function getUser($access_token){
        //Cria instancia do Objeto com as configuirações
        $fb = new Facebook($this->config);
        $fb->setAccessToken($access_token);
        
        return $fb->api("/me");
    }
}
