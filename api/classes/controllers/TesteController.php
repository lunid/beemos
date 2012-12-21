<?php

class Teste extends WsServer {
    
    public function __construct() {
        try{      
            //$this->setWsInterfaceClass(__CLASS__);                
        }catch(Exception $e){
            die(utf8_decode("<b>Erro Fatal:</b> " . $e->getMessage() . " - Entre em contato com suporte!"));
        }
    } 
    
    function teste(){
        
    }
   
}

?>
