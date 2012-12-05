<?php
    use \sys\lib\soap\classes\Soap;
    
    class Usuarios extends Soap {
        public function __construct() {
            try{
                //Inicia o ServerSoap
                parent::__construct(__CLASS__);
                
                //Métodos a serem ignorados no wsdl
                $this->addIgnore("__construct");
            }catch(Exception $e){
                die(utf8_decode("<b>Erro Fatal:</b> " . $e->getMessage() . " - Entre em contato com suporte!"));
            }
        }
        
        public function showString($nome){
            try{
                $erro      = 0;
                $msg       = "Carregado!";
                
                $ret = $this->authenticate();
                
                if($ret->status){
                    $msg = "Teste " . $nome;
                }else{
                    $erro   = $ret->erro;
                    $msg    = $ret->msg;
                }
                
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>". $erro ."</erro>";
                $ret .= "<msg>" . $msg . "</msg>";
                $ret .= "</status>";
                $ret .= "</root>";

                return $ret;
            }catch(Exception $e){
                throw new SoapFault("Teste", 401);
            }
        }
    }
?>
