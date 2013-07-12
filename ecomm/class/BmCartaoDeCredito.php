<?php


class BmCartaoDeCredito  extends BmXml implements BmXmlInterface {
    
    private $bandeiraCc     = ''; //visa | mastercard | diners | discover | elo | amex
    private $arrBandeiraCc  = array('visa','mastercard','diners','discover','elo','amex');
    private $arrConveniosCc = array('CIELO','REDECARD','AMEX');
    private $captura        = 1;
    private $numParcelas    = 1;    
    
    private $convenio;//CIELO | REDECARD | AMEX
    private $cc;//16 dígitos numéricos
    private $codSeg;//3..4 dígitos
    private $validadeCc; //yyyymm    
    
    public function __construct() {
        
    }
    
    public function capturaOn(){
        $this->captura = 1;
    }
    
    public function capturaOff(){
        $this->captura = 0;
    }      
    
    /**
     * Informa qual o convênio que será utilizado para efetuar a cobrança do cartão.
     * Se nenhum convênio for informado será usado o convênio padrão definido no painel de controle.
     * 
     * @param string $convenio Valores possíveis (case insensitive): CIELO | REDECARD | AMEX
     * @return void
     * 
     * @throws Exception Caso o convênio informado seja inválido.
     */
    public function setConvenio($convenio){
        $key = FALSE;
        if (strlen($convenio) > 0) {
            $convenio_  = strtoupper($convenio);    
            $key        = array_search($convenio_, $this->arrConveniosCc);
            if ($key !== FALSE) {
                $this->convenio = $convenio_;
            } else {
                $strConvenios = join(', ', $this->arrConveniosCc);
                $msgErr = "Erro nos dados do cartão de crédito: O convênio informado '".$convenio."' não é válido. ";
                $msgErr .= 'Os valores permitidos são '.$strConvenios.'.';
                throw new Exception($msgErr);                  
            }
        }
    }
    
    /**
     * Define os dados do cartão de crédito;
     * 
     * 
     * @param string $bandeira
     * @param integer $cc
     * @param integer $codSeg
     * @param integer $validadeCc
     * 
     * @return void
     * @throws Exception Caso ocorra erros na validação dos dados informados
     */
    public function setCc($bandeira,$cc,$codSeg,$validadeCc){
        $msgErr = '';
        try {
            $bandeira = $this->setBandeira($bandeira);
            $this->setNumCc($cc);
            $this->setCodSeg($codSeg);
            $this->setValidadeCc($validadeCc);
        } catch (Exception $e) {
            throw $e;
        }
        
        if (strlen($msgErr) > 0) {
            throw new Exception($msgErr);    
        }
    }    
    
    /**
     * Define a bandeira do cartão.
     * 
     * @param $bandeira O valor deve existir em $arrBandeiraCc.
     * @return string Retorna a bandeira em caixa baixa
     * @throws Exception Caso a bandeira informada não seja válida
     */
    public function setBandeira($bandeiraCc){
        $msgErr = '';
        $key    = FALSE;
        if (strlen($bandeiraCc) > 0) {
            $bandeiraCc = strtolower($bandeiraCc);
            $key = array_search($bandeiraCc, $this->arrBandeiraCc);
            if ($key !== FALSE) {
                $this->bandeiraCc = $bandeiraCc;
            }            
        }

        if ($key === FALSE) {
            $msgErr = 'Erro nos dados do cartão de crédito: A bandeira informada '.$bandeiraCc.' não é válida.';
            throw new Exception($msgErr);                
        } 
        return $bandeiraCc;
    }
    
    /**
     * Faz a validação do cartão de crédito informado.
     * 0
     * @param string $cc
     * @return string | FALSE
     * 
     * @throws Exception Caso exista caracteres não numéricos no número informado.
     * @throws Exception Caso o número informado tenha menos digitos que o esperado.
     */
    public function setNumCc($cc){    
        $msgErr = '';        
        if (strlen($cc) > 0 && ctype_digit($cc)) {
            $limCc      = 16;//Número de posições do cartão            
            if (strlen($cc) !== $limCc) {
                $msgErr = 'Erro nos dados do cartão de crédito: O número do cartão parece estar incorreto.';                 
            } else {
                //Cartão Ok
                $msgErr = '';
                $this->cc = $cc;                
            }
        } else {
            $msgErr = 'Erro nos dados do cartão de crédito: O número do cartão possui caracteres não numéricos ou não foi informado.'; 
        }
        
        if (strlen($msgErr) > 0) {
            throw new Exception($msgErr); 
            return FALSE;
        }
        return $cc;
    }     
    
    /**
     * Define o código de segurança do cartão.
     * Valida o código informado de acordo com a bandeira do cartão.
     * 
     * @param integer $codSeg 3..4 dígitos.
     * @return integer
     * 
     * @throws Exception Caso o número de dígitos do código informado seja diferente do esperado.
     */
    public function setCodSeg($codSeg){
        $bandeira   = $this->getBandeira();
        $limCodSeg  = ($bandeira == 'amex') ? 4 : 3;            
        if (strlen($codSeg) !== $limCodSeg) {
            $msgErr = 'Erro nos dados do cartão de crédito: O código de segurança parece estar incorreto.';
            throw new Exception($msgErr);            
        } else {
            $this->codSeg = $codSeg;
        }         
        return $codSeg;
    }    
    
    /**
     * Define a validade do cartão de crédito.
     * Verifica se o formato informado (yyyymm) está correto e se a data não está vencida.
     * 
     * @param integer $validadeCc Deve estar no formato yyyymm
     * @return integer
     * 
     * @throws Exception Caso a validade informada esteja no formato incorreto
     * @throws Exception Caso o cartão esteja vencido
     */
    public function setValidadeCc($validadeCc){     
        $msgErr = '';
        if (strlen($validadeCc) == 6 && ctype_digit($validadeCc)) {
            //Validade está no formato correto. Verifica se o cartão já está vencido.
            $ano        = substr($validadeCc, 0, 4);
            $mes        = substr($validadeCc, 4, 2);
              
            $vencEn     =  mktime(0, 0, 0, $mes, 1, $ano); 
            $dateNow    =  mktime(0, 0, 0, date('m'), 1,date('Y')); 
            $diff       =($vencEn - $dateNow)/(3600*24); //Retorna a diferença em número de dias  

            if ($diff >= 0) {
                //Cartão dentro da validade.
                $this->validadeCc = $validadeCc;
            } else {
                $msgErr = 'Erro na validade do cartão de crédito: Cartão parece estar vencido.';
            }           
        } else {
            $msgErr = 'Erro na validade do cartão de crédito: A validade do cartão foi informada incorretamente.';
        }
        
        if (strlen($msgErr) > 0) {
            throw new Exception($msgErr);    
        }  
        return $validadeCc;
    }

        
    public function setParcelas($numParcelas) {
        $numParcelas_ = (int)$numParcelas;
        if ($numParcelas_ >= 1 && $numParcelas_ <= 60) {
            $this->numParcelas = $numParcelas_;
        } else {
            $msgErr = "O número de parcelas informado '{$numParcelas}' não é válido. Informe um valor numérico >= 1 e <= 60 (o número máximo de parcelas permitidas depende do contrato com seu convênio).";
            throw new Exception($msgErr); 
        }
    }  
    
    /**
     * Retorna o nome da instituição financeira que fará a transação de pagamento.
     * Caso nenhum convênio seja informado a opção usada será a instituição padrão 
     * definida no painel de controle.
     */
    public function getConvenio(){
        return $this->convenio;
    }          
    
    public function getNumCc(){
       return $this->setNumCc($this->cc);               
    }    
    
    public function getCodSeg(){        
        return $this->setCodSeg($this->codSeg);         
    }
    
    public function getValidade(){
        return $this->setValidadeCc($this->validadeCc); 
    }
    
    public function getParcelas(){
        $numParcelas = (int)$this->numParcelas;
        if ($numParcelas == 0) $numParcelas = 1;
        return $numParcelas;
    }
        
    public function getCaptura(){
        $captura = (int)$this->captura;
        return $captura;
    }      
    
    public function getBandeira(){        
        $bandeiraCc = $this->setBandeira($this->bandeiraCc);
        return $bandeiraCc;
    }
    
    public function getXml(){     
        try {
            $xml = "
            <CARTAO>
               ".$this->setTagXml('CONVENIO', $this->getConvenio())."
               ".$this->setTagXml('CC', $this->getBandeira())." 
               ".$this->setTagXml('CC', $this->getNumCc())."       
               ".$this->setTagXml('COD_SEG', $this->getCodSeg())." 
               ".$this->setTagXml('VALIDADE', $this->getValidade())." 
               ".$this->setTagXml('PARCELAS', $this->getParcelas())." 
               ".$this->setTagXml('CAPTURA', $this->getCaptura())." 
            </CARTAO>";
        } catch (Exception $e) {
            throw $e;
        }
        return $xml;
    }
    
    public function printXml(){
        $xml = $this->getXml();
        $this->headerXml($xml);
    }    
}

?>
