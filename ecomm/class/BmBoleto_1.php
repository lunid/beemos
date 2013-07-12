<?php

class BmBoleto extends BmXml implements BmXmlInterface {
    
    private $banco;//Opcional
    private $dataVencimento;//Opcional
    private $arrBancos  = array('BRADESCO','BB','ITAU','SANTANDER');    
    private $meioPgto   = 'BOLETO';
    /**
     * Permite inicializar o objeto informando o banco emissor e o vencimento do boleto.
     * 
     * @param string $banco
     * @param string $vencDate Data no formato yyyy-mm-dd
     */
    function __construct($banco='',$vencDate=''){
        $banco      = trim($banco);
        $vencDate   = trim($vencDate);
        
        if (strlen($banco) > 0) $this->setBanco($banco);
        if (strlen($vencDate) > 0) $this->setDataVencimento($vencDate);
    }
    
    /**
     * Recupera a URL para emiss�o do boleto referente ao pedido informado.
     * 
     * @param integer $numPedido
     * @return string Xml de retorno do servidor
     * 
     * @throws Exception Caso o $numPedido informado seja zero
     */
    public static function getUrlBoleto($numPedido) {
        $pathEmissaoBoleto  = '';
        $numPedido          = (int)$numPedido;
        if ($numPedido > 0) {
            
        } else {
            $msgErr = 'Erro ao solicitar link para emiss�o do boleto: O n�mero do pedido informado � inv�lido.';
            throw new Exception($msgErr);               
        }
        return $pathEmissaoBoleto;
    }
    
    function Bradesco(){
        $this->setBanco('BRADESCO');
    }
    
    function Itau(){
        $this->setBanco('ITAU');
    }
    
    function Bb(){
        $this->setBanco('BB');
    }
    
    /**
     * Define qual o banco emissor do boleto.
     * Caso nenhum banco seja definido, o banco cadastrado como sendo o principal no painel
     * de controle ser� usado.
     * 
     * @param string $banco
     * @return string
     * 
     * @throws Exception Caso o banco informado n�o seja localizado na lista de bancos aceitos.
     */
    function setBanco($banco){        
        $key    = FALSE;
        $banco_ = '';
        if (strlen($banco) > 0) {
            $banco_ = strtoupper(trim($banco));
            $key = array_search($banco_, $this->arrBancos);
            if ($key !== FALSE) {
                $this->banco = $banco_;
            }            
        }

        if ($key === FALSE) {
            $msgErr = 'O banco '.$banco.', informado como emissor do boleto, n�o � v�lido.';
            throw new Exception($msgErr);                
        } 
        return $banco_;        
    }
    
    function getBanco(){
        return $this->banco;        
    }
    
    /**
     * Define a data de vencimento do boleto, a partir de uma data no formato yyyy-mm-dd.
     * Caso nenhum vencimento seja especificado, este ser� calculado a partir da 
     * quantidade de dias ap�s a emiss�o do boleto, definida no painel de controle.
     * 
     * @param $vencDate Data de vencimento no formato yyyy-mm-dd
     * @return void
     * 
     * @throws Exception Caso a data informada n�o possua a quantidade de caracteres esperada (10 caracteres)  
     * @throws Exception Caso a data informada n�o esteja no formato yyyy-mm-dd   
     * @throws Exception Caso a data informada seja menor que a data atual     
     */
    function setDataVencimento($vencDate){
        $msgErr     = '';
        $vencDate   = trim($vencDate);
        
        if (strlen($vencDate) == 10) {
            @list($y,$m,$d) = @explode('-',$vencDate);
            if (@checkdate($m,$d,$y)){
                //A data informada � v�lida.
                $h = $i = $s = 0;//Hora, minuto, segundo
                
                $dateVenc   =  mktime($h, $i, $s, $m,$d,$y); 
                $dateNow    =  mktime($h, $i, $s, date('m'),date('d'),date('Y')); 
                
                $diff =($dateVenc - $dateNow)/(3600*24); //Retorna a diferen�a em n�mero de dias
                
                if ($diff >= 0) {
                    $this->dataVencimento = $vencDate;
                } else {
                    $msgErr = 'Erro ao definir a data de vencimento do boleto: A data '.$vencDate.' � menor que a data atual.';  
                }
            } else {
                $msgErr = 'Erro ao definir a data de vencimento do boleto: A data '.$vencDate.' n�o est� no formato yyyy-mm-dd ou n�o � v�lida.';            
            }
        } else {
            $msgErr = 'Erro ao definir a data de vencimento do boleto: A data '.$vencDate.' n�o possui a quantidade de caracteres esperada.';  
        }
        
        if (strlen($msgErr) > 0) throw new Exception($msgErr);                   
    }
    
    /**
     * Define a data de vencimento do boleto, a partir do n�mero de dias informado.
     * O vencimento ser� calculado a partir da data de hoje + quantidade de dias ($dias).
     * 
     * @param $days N�mero de dias para calcular o vencimento.
     * @return void
     */
    function setDiasVencimento($days){
        $days = (int)$days;
        if ($days >= 0) {
            $vencDate = date('Y-m-d', strtotime("+{$days} days"));
            $this->setDataVencimento($vencDate);
        }
    }
    
    function getVencimento(){
        return $this->dataVencimento;       
    }
    
    function getXml(){
     $xml = "
     <BOLETO>
        ".$this->setTagXml('BANCO', $this->getBanco())."
        ".$this->setTagXml('VENCIMENTO', $this->getVencimento())."       
     </BOLETO>";
     return $xml;
    }
    
    function printXml(){
        $xml = $this->getXml();
        $this->headerXml($xml);
    }

}

?>
