<?php


class BmCartaoDeCredito  extends BmXml implements BmXmlInterface {
    
    private $bandeiraCc     = ''; //visa | mastercard | diners | discover | elo | amex
    private $arrBandeiraCc  = array('visa','mastercard','diners','discover','elo','amex');
    private $arrConveniosCc = array('CIELO','REDECARD','AMEX');
    private $captura        = 1;
    private $numParcelas    = 1;    
    
    private $convenio;//CIELO | REDECARD | AMEX
    private $cc;//16 d�gitos num�ricos
    private $codSeg;//3..4 d�gitos
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
     * Informa qual o conv�nio que ser� utilizado para efetuar a cobran�a do cart�o.
     * Se nenhum conv�nio for informado ser� usado o conv�nio padr�o definido no painel de controle.
     * 
     * @param string $convenio Valores poss�veis (case insensitive): CIELO | REDECARD | AMEX
     * @return void
     * 
     * @throws Exception Caso o conv�nio informado seja inv�lido.
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
                $msgErr = "Erro nos dados do cart�o de cr�dito: O conv�nio informado '".$convenio."' n�o � v�lido. ";
                $msgErr .= 'Os valores permitidos s�o '.$strConvenios.'.';
                throw new Exception($msgErr);                  
            }
        }
    }
    
    /**
     * Define os dados do cart�o de cr�dito;
     * 
     * 
     * @param string $bandeira
     * @param integer $cc
     * @param integer $codSeg
     * @param integer $validadeCc
     * 
     * @return void
     * @throws Exception Caso ocorra erros na valida��o dos dados informados
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
     * Define a bandeira do cart�o.
     * 
     * @param $bandeira O valor deve existir em $arrBandeiraCc.
     * @return string Retorna a bandeira em caixa baixa
     * @throws Exception Caso a bandeira informada n�o seja v�lida
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
            $msgErr = 'Erro nos dados do cart�o de cr�dito: A bandeira informada '.$bandeiraCc.' n�o � v�lida.';
            throw new Exception($msgErr);                
        } 
        return $bandeiraCc;
    }
    
    /**
     * Faz a valida��o do cart�o de cr�dito informado.
     * 0
     * @param string $cc
     * @return string | FALSE
     * 
     * @throws Exception Caso exista caracteres n�o num�ricos no n�mero informado.
     * @throws Exception Caso o n�mero informado tenha menos digitos que o esperado.
     */
    public function setNumCc($cc){    
        $msgErr = '';        
        if (strlen($cc) > 0 && ctype_digit($cc)) {
            $limCc      = 16;//N�mero de posi��es do cart�o            
            if (strlen($cc) !== $limCc) {
                $msgErr = 'Erro nos dados do cart�o de cr�dito: O n�mero do cart�o parece estar incorreto.';                 
            } else {
                //Cart�o Ok
                $msgErr = '';
                $this->cc = $cc;                
            }
        } else {
            $msgErr = 'Erro nos dados do cart�o de cr�dito: O n�mero do cart�o possui caracteres n�o num�ricos ou n�o foi informado.'; 
        }
        
        if (strlen($msgErr) > 0) {
            throw new Exception($msgErr); 
            return FALSE;
        }
        return $cc;
    }     
    
    /**
     * Define o c�digo de seguran�a do cart�o.
     * Valida o c�digo informado de acordo com a bandeira do cart�o.
     * 
     * @param integer $codSeg 3..4 d�gitos.
     * @return integer
     * 
     * @throws Exception Caso o n�mero de d�gitos do c�digo informado seja diferente do esperado.
     */
    public function setCodSeg($codSeg){
        $bandeira   = $this->getBandeira();
        $limCodSeg  = ($bandeira == 'amex') ? 4 : 3;            
        if (strlen($codSeg) !== $limCodSeg) {
            $msgErr = 'Erro nos dados do cart�o de cr�dito: O c�digo de seguran�a parece estar incorreto.';
            throw new Exception($msgErr);            
        } else {
            $this->codSeg = $codSeg;
        }         
        return $codSeg;
    }    
    
    /**
     * Define a validade do cart�o de cr�dito.
     * Verifica se o formato informado (yyyymm) est� correto e se a data n�o est� vencida.
     * 
     * @param integer $validadeCc Deve estar no formato yyyymm
     * @return integer
     * 
     * @throws Exception Caso a validade informada esteja no formato incorreto
     * @throws Exception Caso o cart�o esteja vencido
     */
    public function setValidadeCc($validadeCc){     
        $msgErr = '';
        if (strlen($validadeCc) == 6 && ctype_digit($validadeCc)) {
            //Validade est� no formato correto. Verifica se o cart�o j� est� vencido.
            $ano        = substr($validadeCc, 0, 4);
            $mes        = substr($validadeCc, 4, 2);
              
            $vencEn     =  mktime(0, 0, 0, $mes, 1, $ano); 
            $dateNow    =  mktime(0, 0, 0, date('m'), 1,date('Y')); 
            $diff       =($vencEn - $dateNow)/(3600*24); //Retorna a diferen�a em n�mero de dias  

            if ($diff >= 0) {
                //Cart�o dentro da validade.
                $this->validadeCc = $validadeCc;
            } else {
                $msgErr = 'Erro na validade do cart�o de cr�dito: Cart�o parece estar vencido.';
            }           
        } else {
            $msgErr = 'Erro na validade do cart�o de cr�dito: A validade do cart�o foi informada incorretamente.';
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
            $msgErr = "O n�mero de parcelas informado '{$numParcelas}' n�o � v�lido. Informe um valor num�rico >= 1 e <= 60 (o n�mero m�ximo de parcelas permitidas depende do contrato com seu conv�nio).";
            throw new Exception($msgErr); 
        }
    }  
    
    /**
     * Retorna o nome da institui��o financeira que far� a transa��o de pagamento.
     * Caso nenhum conv�nio seja informado a op��o usada ser� a institui��o padr�o 
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
