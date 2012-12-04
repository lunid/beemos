<?php

/**
 * 04/12/2012
 * Classe utilizada para envio de email.
 * Utiliza o pacote PHPMailer, localizado em sys/components.
 * 
 *
 * @author Claudio Rubens Silva Filho
 */
require_once('../sys/components/PHPMailer_5.2.2/class.phpmailer.php');
class EmailComponent {
    
    private $mail = NULL;
    
    function __construct(){
        $this->mail = new PHPMailer();
    }
    
    function setFrom($email,$name=''){
        $this->addEmail('setFrom',$email,$name);        
    } 
    
    function addReplyTo($email,$name=''){
        $this->addEmail('addReplyTo',$email,$name);           
    }
    
    function addAddress($email,$name=''){
        $this->addEmail('addAddress',$email,$name);
    }
    
    private function addEmail($action,$email,$name=''){
        if (strlen($email) > 0) {
            switch($action){
                case 'setFrom':
                    $this->mail->SetFrom($email,$name);
                    break;
                case 'addReplyTo':
                    $this->mail->AddReplyTo($email,$name);
                    break;
                case 'addAddress':
                    $this->mail->AddAddress($email, $name);
            }
        } else {
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'EMAIL_NULL');  
            $msgErr = str_replace('{ACTION}',$action,$msgErr);
            throw new \Exception( $msgErr );                
        }                
    }
        
    function setSubject($subject=''){
        if (strlen($subject) > 0) $this->mail->Subject = $subject;
    }
    
    function addAttachment($pathFile){
        $this->mail->AddAttachment($pathFile);
    }
    
    function send($subject='Sem assunto'){
        if (strlen($subject) > 0) $this->setSubject($subject);        
        $mail             = $this->mail;        
        $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test        
    }  
}

?>
