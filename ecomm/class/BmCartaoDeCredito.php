<?php


class BmCartaoDeCredito  extends BmXml implements BmXmlInterface {
    
    private $bandeiraCc = ''; //visa | mastercard | diners | discover | elo | amex
    private $arrBandeiraCc = array('visa','mastercard','diners','discover','elo','amex');
    private $convenio;//CIELO | REDECARD | AMEX
    private $cc;//16 d�gitos num�ricos
    private $codSeg;//3..4 d�gitos
    private $validade; //yyyymm    
    private $captura = 1;
    
    function __construct() {
        
    }
    
    function capturaOn(){
        $this->captura = 1;
    }
    
    function capturaOff(){
        $this->captura = 0;
    }    
    
    function getCaptura(){
        
    }    
    
    /**
     * Informa qual o conv�nio que ser� utilizado para efetuar a cobran�a do cart�o.
     * Se nenhum conv�nio for informado ser� usado o conv�nio padr�o definido no painel de controle.
     * 
     * @param string $convenio Valores poss�veis: CIELO | REDECARD | AMEX
     * @return void
     * @throws Exception
     */
    function setConvenio($convenio){
        if (strlen($convenio) > 0) {
            $convenio = strtoupper($convenio);
            if ($convenio == 'CIELO' || $convenio == 'REDECARD' || $convenio == 'AMEX') {
                $this->convenio = $convenio;
            } else {
                $msgErr = 'Erro nos dados do cart�o de cr�dito: O conv�nio '.$convenio.' n�o � v�lido. ';
                $msgErr .= 'Os valores permitidos s�o CIELO, REDECARD e AMEX.';
                throw new Exception($msgErr);                  
            }
        }
    }
    
    function getConvenio(){
        
    }
    
    /**
     * Define os dados do cart�o de cr�dito;
     * 
     * 
     * @param string $bandeira
     * @param integer $cc
     * @param integer $codSeg
     * @param integer $validade
     * 
     * @return void
     * @throws Exception Caso ocorra erros na valida��o dos dados informados
     */
    function setCc($bandeira,$cc,$codSeg,$validade){
        $msgErr = '';
        try {
            $bandeira = $this->setBandeira($bandeira);
            if (strlen($cc) > 0 && ctype_digit($cc)) {
                $limCc      = 16;//N�mero de posi��es do cart�o
                $limCodSeg  = 3;
                
                if ($bandeira == 'amex') {
                    $limCodSeg  = 4;
                }
                
                if (strlen($cc) !== $limCc) {
                    $msgErr = 'Erro nos dados do cart�o de cr�dito: O n�mero do cart�o parece estar incorreto.';  
                } elseif (strlen($codSeg) !== $limCodSeg) {
                    $msgErr = 'Erro nos dados do cart�o de cr�dito: O c�digo de seguran�a parece estar incorreto.';
                } else {                
                    //Dados do cart�o validados com sucesso. Verifica a validade:
                    
                    if (strlen($validade) == 6 && ctype_digit($validade)) {
                        //Cc validado com sucesso.                        
                        $this->cc       = $cc;                       
                        $this->codSeg   = $codSeg;
                        $this->validade = $validade;
                    } else {
                        $msgErr = 'Erro nos dados do cart�o de cr�dito: A validade do cart�o foi informada incorretamente.';
                    }
                }
                
            } else {
                $msgErr = 'Erro nos dados do cart�o de cr�dito: O n�mero do cart�o possui caracteres n�o num�ricos ou n�o foi informado.';                              
            }
        } catch (Exception $e) {
            throw $e;
        }
        
        if (strlen($msgErr) > 0) {
            throw new Exception($msgErr);    
        }
    }
    
    function getCc(){
        
    }
    
    function getCodSeg(){
        
    }
    
    function getValidade(){
        
    }
    
    function setParcelas($numParcelas) {
        $numParcelas = (int)$numParcelas;
        if ($numParcelas >= 1) {
            $this->numParcelas = $numParcelas;
        } else {
            $msgErr = 'O n�mero de parcelas informado n�o � v�lido. Informe um valor num�rico >= 1.';
            throw new Exception($msgErr); 
        }
    }
    
    function getParcelas(){
        
    }
    
    

    /**
     * Define a bandeira do cart�o.
     * 
     * @param $bandeira O valor deve existir em $arrBandeiraCc.
     * @return string Retorna a bandeira em caixa baixa
     * @throws Exception Caso a bandeira informada n�o seja v�lida
     */
    private function setBandeira($bandeiraCc){
        $key = FALSE;
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
    
    function getBandeira(){
        
    }
    
    function getXml(){
     $xml = "
     <CARTAO>
        ".$this->setTagXml('CONVENIO', $this->getConvenio())."
        ".$this->setTagXml('CC', $this->getBandeira())." 
        ".$this->setTagXml('CC', $this->getCc())."       
        ".$this->setTagXml('COD_SEG', $this->getCodSeg())." 
        ".$this->setTagXml('VALIDADE', $this->getValidade())." 
        ".$this->setTagXml('PARCELAS', $this->getParcelas())." 
        ".$this->setTagXml('CAPTURA', $this->getCaptura())." 
     </CARTAO>";
     return $xml;
    }
    
    function printXml(){
        $xml = $this->getXml();
        $this->headerXml($xml);
    }    
}

?>
