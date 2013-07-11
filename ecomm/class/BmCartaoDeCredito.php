<?php


class BmCartaoDeCredito  extends BmXml implements BmXmlInterface {
    
    private $bandeiraCc = ''; //visa | mastercard | diners | discover | elo | amex
    private $arrBandeiraCc = array('visa','mastercard','diners','discover','elo','amex');
    private $convenio;//CIELO | REDECARD | AMEX
    private $cc;//16 dígitos numéricos
    private $codSeg;//3..4 dígitos
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
     * Informa qual o convênio que será utilizado para efetuar a cobrança do cartão.
     * Se nenhum convênio for informado será usado o convênio padrão definido no painel de controle.
     * 
     * @param string $convenio Valores possíveis: CIELO | REDECARD | AMEX
     * @return void
     * @throws Exception
     */
    function setConvenio($convenio){
        if (strlen($convenio) > 0) {
            $convenio = strtoupper($convenio);
            if ($convenio == 'CIELO' || $convenio == 'REDECARD' || $convenio == 'AMEX') {
                $this->convenio = $convenio;
            } else {
                $msgErr = 'Erro nos dados do cartão de crédito: O convênio '.$convenio.' não é válido. ';
                $msgErr .= 'Os valores permitidos são CIELO, REDECARD e AMEX.';
                throw new Exception($msgErr);                  
            }
        }
    }
    
    function getConvenio(){
        
    }
    
    /**
     * Define os dados do cartão de crédito;
     * 
     * 
     * @param string $bandeira
     * @param integer $cc
     * @param integer $codSeg
     * @param integer $validade
     * 
     * @return void
     * @throws Exception Caso ocorra erros na validação dos dados informados
     */
    function setCc($bandeira,$cc,$codSeg,$validade){
        $msgErr = '';
        try {
            $bandeira = $this->setBandeira($bandeira);
            if (strlen($cc) > 0 && ctype_digit($cc)) {
                $limCc      = 16;//Número de posições do cartão
                $limCodSeg  = 3;
                
                if ($bandeira == 'amex') {
                    $limCodSeg  = 4;
                }
                
                if (strlen($cc) !== $limCc) {
                    $msgErr = 'Erro nos dados do cartão de crédito: O número do cartão parece estar incorreto.';  
                } elseif (strlen($codSeg) !== $limCodSeg) {
                    $msgErr = 'Erro nos dados do cartão de crédito: O código de segurança parece estar incorreto.';
                } else {                
                    //Dados do cartão validados com sucesso. Verifica a validade:
                    
                    if (strlen($validade) == 6 && ctype_digit($validade)) {
                        //Cc validado com sucesso.                        
                        $this->cc       = $cc;                       
                        $this->codSeg   = $codSeg;
                        $this->validade = $validade;
                    } else {
                        $msgErr = 'Erro nos dados do cartão de crédito: A validade do cartão foi informada incorretamente.';
                    }
                }
                
            } else {
                $msgErr = 'Erro nos dados do cartão de crédito: O número do cartão possui caracteres não numéricos ou não foi informado.';                              
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
            $msgErr = 'O número de parcelas informado não é válido. Informe um valor numérico >= 1.';
            throw new Exception($msgErr); 
        }
    }
    
    function getParcelas(){
        
    }
    
    

    /**
     * Define a bandeira do cartão.
     * 
     * @param $bandeira O valor deve existir em $arrBandeiraCc.
     * @return string Retorna a bandeira em caixa baixa
     * @throws Exception Caso a bandeira informada não seja válida
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
            $msgErr = 'Erro nos dados do cartão de crédito: A bandeira informada '.$bandeiraCc.' não é válida.';
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
