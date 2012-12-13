<?php
/**
 * COMPONENTE Mail:
 * Envia mensagens eletrônicas por e-mail ou SMS.
 * Utiliza o PHPMailer para envio de mensagens por e-mail.
 *
 */
use \sys\lib\classes\LibComponent;
use \sys\lib\classes\Url;

class Mail extends LibComponent {
    
    private $objMailer          = NULL;
    private $arrAddress         = array();
    private $arrCco             = array();
    private $arrAnexo           = array();
    private $message            = '';
    private $emailFrom          = '';
    private $nameFrom           = '';
    private $emailReplyTo       = '';
    private $nameReplyTo        = '';
    private $confirmReadingTo   = '';
    /**
     * Faz a compactação de uma string gravando o resultado em um arquivo externo.
     * Os formatos permitidos são js, para conteúdo javascript, e css para conteúdo de folhas de estilo em cascata.
     *      
     * @return void
     * 
     * @throws \Exception Se uma extensão válida não for informada (valores permitidos: css, js).
     * @throws \Exception Se após a compactação de uma string válida de javascript o resultado for vazio.
     * @throws \Exception Se a tentativa de criar o arquivo de saída falhar.
     * @throws \Exception Se após a sua criação, o arquivo de saída possuir tamanho 0kb.
     */
    function init(){	        
        $rootComps = Url::pathRootComps('mail');
        require_once($this->rootComps.'src/PHPMailer_5.2.2/class.phpmailer.php'); 
        
        $objMailer = new PHPMailer(true);
        if (!is_object($objMailer)){
            
        }
        
        $this->objMailer = $objMailer;
        $this->setReturn($this);
    }
    
    function addAddress($email,$name=''){
       if ($this->vldEmail($email) !== FALSE) {           
           $name = (strlen($name) > 0)?$name:$email;
           $this->objMailer->AddAddress($email, $name);
           $this->arrAddress[]['email']    = $email;
           $this->arrAddress[]['name']     = (strlen($name) > 0)?$name:$email;
       } else {
           
       }       
    }
    
    private function vldEmail($email){
        list($prefixo,$sufixo)	= explode('@',$email);
        if (strlen($sufixo) > 0){
                $sufixo	= str_replace('.om','.com',$sufixo);
                $email	= $prefixo.'@'.$sufixo;
        }
        
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return $email;
	} else {
            return false;
	}                	
    }
    
    
    function getAddress(){
        return $this->arrAddress;
    }
    
    function setCco($email,$name=''){
       if ($this->vldEmail($email) !== FALSE) {
           $name = (strlen($name) > 0)?$name:$email;
           $this->objMailer->AddBCC($email, $name);
           $this->arrCco[] = $email;          
       } else {
           
       }        
    }
    
    function addAnexo($pathFile){
        if (file_exists($pathFile)) {
            $this->arrAnexo[] = $pathFile;
        } else {
            
        }
    }
    
    function setEmailFrom($email,$name=''){
        if ($this->vldEmail($email) !== FALSE) {
            $this->emailFrom  = $email;
            $this->nameFrom   = (strlen($name) > 0)?$name:$email;
        } else {

        }          
    }        
    
    function setReplyTo($email,$name=''){
        if ($this->vldEmail($email) !== FALSE) {
            $this->emailReplyTo = $email;
            $this->nameReplyTo  = (strlen($name) > 0)?$name:$email;
        } else {

        }          
    }            
    
    function confirmReadingTo($email){
        if ($this->vldEmail($email) !== FALSE) {            
            $this->objMailer->confirmReadingTo = $email;           
        } else {

        }         
    }
    
    function addMessage($message){
        $this->message = $message;
    }
    
    function printMsg(){
        
    }
       
    function send(){
        $this->cfgAddress();
        $this->cfgReplyTo();
        
        $objMailer = $this->objMailer;
        $confirmReadingTo   = $this->confirmReadingTo;
        
        
	$mail->IsSendmail();        
        
        try {
            if (strlen($confirmReadingTo) > 0) $mail->ConfirmReadingTo = $confirmReadingTo; 
            $mail = $this->cfgReplyTo($mail);
            $mail = $this->cfgAddress($mail);                        
            $mail->SetFrom($from, 'Interbits - Superpro Web');            
        
        } catch(\Exception $e) {
            
        }
    }
    
    private function cfgReplyTo(){
        $objMailer = $this->objMailer;
        if (is_object($objMailer)) {
            $emailReplyTo = $this->emailReplyTo;
            if (strlen($emailReplyTo) > 0) {
                $objMailer->AddReplyTo($emailReplyTo, $this->nameReplyTo);
            }
        }
        $this->objMailer = $objMailer;
    }    
    
    private function cfgAddress(){
        $objMailer = $this->objMailer;
        if (is_object($objMailer)) {
            $arrAddress = $this->arrAddress;
            if (is_array($arrAddress)) {
                foreach($arrAddress as $address){
                    $email  = $address['email']; 
                    $name   = $address['name'];
                    $objMailer->AddAddress($email, $name);
                }
            }
        }
        $this->objMailer = $objMailer;
    }     
 }