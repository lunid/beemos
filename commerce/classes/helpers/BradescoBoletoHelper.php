<?php

    namespace commerce\classes\helpers;
    use \sys\classes\util\Request;
    
    class BradescoBoletoHelper extends BradescoHelper {
        protected $tipoTrans      = 'sepsBoleto';
        protected $tpPagamento    = 3;
        protected $numDoc         = 0;
        
        function __construct($objCfg,$orderId){
            parent::__construct($objCfg,$orderId);
            
            if ((int)$objCfg->BOLETO_COM_RET == 1) {
                //Gera o boleto e armazena o retorno da operação.
                $this->tipoTrans = 'sepsBoletoRet';
            }
            
            $this->setNumDocBoleto();  
            
            $assinatura = $objCfg->ASS_BOLETO_PROD;

            if ($this->ambiente == 'TEST') {
                $assinatura = $objCfg->ASS_BOLETO_TEST;
            }           
            
            $this->setAssinatura($assinatura);
        }

        
        /**
         * Define o campo número do documento ao gerar um boleto.
         * Este campo deve ter até 9 caracteres numéricos.
         * 
         * @return void
         */        
        private function setNumDocBoleto(){
            $numPedido      = $this->numPedido;
            $numDocBoleto   = Request::all('numDoc','NUMBER');
            $this->numDoc   = $this->setNumeric($numPedido,$numDocBoleto);
        }
 
    }

?>
