<?php

class BmItemPedido extends BmXml {
    
    private $categoria      = ''; //[Optional]
    private $codigo         = ''; 
    private $descricao      = '';
    private $quantidade     = 1;    
    private $preco          = 0;
    private $precoPromo     = 0; //[Optional]
    private $unidade        = 'CX';//[Optional]
    private $campanha       = '';//[Optional]
    
    private $precoSemFormat; //Pre�o normal do item sem formata��o. Ex.: 123,40 ficar� 12340, 2345 ficar� 234500, 65,3 ficar� 6530    
    private $precoPromoSemFormat; //Pre�o promocional do item sem formata��o.  
    
    /**
     * Inicializa um objeto com descri��o, pre�o unit�rio e quantidade.
     * Ao informar o pre�o unit�rio, o padr�o usado ser� o ponto '.' como separador decimal, sem separador de milhar.
     * 
     * @param string $cat Categoria
     * @param string $codigo C�digo do produto
     * @param string $descricao
     * @param float $preco Pre�o normal do produto/servi�o
     * @param float $precoPromo Pre�o promocional do produto/servi�o
     * @param integer $qtde Quantidade
     * @param string $unidade Unidade de medida do produto
     * @param string $campanha Nome/c�digo da campanha vigente, se houver
     */
    function __construct($cat='',$codigo,$descricao,$preco=0,$precoPromo=0,$qtde=1,$unidade='CX',$campanha=''){
        $this->setCategoria($cat);
        $this->setCodigo($codigo);
        $this->setDescricao($descricao);
        $this->setQuantidade($qtde);
        $this->setUnidade($unidade);
        $this->setCampanha($campanha);
        if ($preco > 0) $this->setPreco($preco);
        if ($precoPromo > 0) $this->setPrecoPromo($precoPromo);
    }
    
    function setQuantidade($quantidade){
        $quantidade = (int)$quantidade;
        if ($quantidade == 0) $quantidade = 1;
        $this->quantidade = $quantidade;
        $this->addParamXml('QUANTIDADE',$this->quantidade);
    }
    
    function setCategoria($value){
        if (strlen($value) > 0) $this->categoria = $value;
        $this->addParamXml('CATEGORIA',$this->categoria);        
    }
        
    function getCategoria(){
        return $this->categoria;
    }    
    
    /**
     * Guarda um c�digo alfanum�rico de at� 20 caracteres.
     * 
     * @param string $codigo
     * @throws \Exception Caso o c�digo esteja vazio ou n�o seja alfanum�rico.
     */
    function setCodigo($codigo){
        if (strlen($codigo) > 0 && ctype_alnum($codigo)) {
            $this->codigo = $codigo;
            $this->addParamXml('CODIGO',$this->codigo);
        } else {
            throw new \Exception('ItemPedido->setCodigo(): o c�digo informado "'.$codigo.'" n�o � v�lido ou est� vazio. O c�digo � obrigat�rio e deve conter at� 20 caracteres alfanum�ricos.');
        }        
    }
    
    function getCodigo() {
        $codigo = $this->codigo;
        if (strlen($codigo) == 0 || !ctype_alnum($codigo)) {           
            throw new \Exception('ItemPedido->getCodigo(): O c�digo do item atual n�o foi informado.');
        }
        return $codigo;
    }
    
    /**
     * Define a unidade de medida para o atual.
     * Por exemplo, 'CX' para caixa, 'ASS' para assinatura etc.
     * 
     * @param string $unidade C�digo com at� 3 letras 
     * @throws \Exception Caso o c�digo informado seja inv�lido
     */
    function setUnidade($codUnidade){
        if (strlen($codUnidade) >= 1 && strlen($codUnidade) <= 3 && ctype_alpha($codUnidade)) {
            $this->unidade = $codUnidade;            
            $this->addParamXml('UNIDADE',$codUnidade);
        } else {
            throw new \Exception('ItemPedido->setUnidade(): a unidade informada '.$unidade.' n�o � v�lida. A unidade deve conter apenas letras e no m�ximo 3 catacteres.');
        }
    }
    
    function getUnidade(){
        $unidade = $this->unidade;
        if (strlen($unidade) == 0) $unidade = 'CX';        
        return $unidade;
    }

    /**
     * Informa o nome da campanha relacionada � compra do produto/servi�o atual (opcional).     
     * 
     * @param string $campanha String alfanum�rica de at� 20 caracteres.
     * @return void
     */
    function setCampanha($campanha){
        if (strlen($campanha) > 0) {
             $this->campanha = $campanha;                
        }
        $this->addParamXml('CAMPANHA',$this->campanha);
    }
    
    function getCampanha() {
        return $this->campanha;
    }
    
    /**
     * Informa a descri��o do produto.     
     * @param type $descricao
     */
    function setDescricao($descricao){
        if (strlen($descricao) > 0) {            
            $this->descricao = $descricao;
        }
        $this->addParamXml('DESCRICAO',$this->descricao);
    }
    
    function getDescricao(){
        $descricao = $this->descricao;        
        if (strlen($descricao) == 0) {           
            throw new \Exception('ItemPedido->getDescricao(): A descri��o do item atual n�o foi informado.');
        }
        return $descricao;        
    }
    
    function getQuantidade(){
        $quantidade = (int)$this->quantidade;
        if ($quantidade == 0) $quantidade = 1;
        return $quantidade;
    }

    /**
     * Informa o pre�o normal do item atual.
     *      
     * @param float $preco Pre�o normal como valor decimal
     * @return void
     */
    function setPreco($preco){
        $this->setPrecoUnit($preco,'PRECO');
    }      
        
    /**
     * Informa o pre�o promocional do item atual.
     *      
     * @param float $preco Pre�o promocional como valor decimal
     * @return void
     */
    function setPrecoPromo($preco){
        $this->setPrecoUnit($preco,'PRECO_PROMO');
    }    
    
    /**
     * Informa o pre�o unit�rio do produto.
     *      
     * @param float $preco Pre�o como valor decimal
     * @param string $tagName Tag Xml que ser� enviada ao servidor remoto.
     * @return void
     */
    private function setPrecoUnit($preco,$tagName){
        if (is_numeric($preco)) {
            $varPreco               = 'preco';
            $varPrecoSemFormat      = 'precoSemFormat';
            if ($tagName == 'PRECO_PROMO') {
                $varPreco            = 'precoPromo';
                $varPrecoSemFormat   = 'precoPromoSemFormat';                
            }
            
            $precoSemFormat             = $this->convertNumberDec2NumberInt($preco, '.', ''); 
            $this->$varPrecoSemFormat   = $precoSemFormat;
            $this->$varPreco            = number_format($preco,2,'.','');
           
            $this->addParamXml($tagName,$this->$varPreco);            
        } else {
            throw new \Exception('ItemPedido->setPrecoUnit(): O pre�o informado n�o � um valor v�lido.');
        }
    }
    
    function getPreco(){
        return $this->preco;
    }
    
    function getPrecoPromo(){
        return $this->precoPromo;
    }    
    
    function getPrecoSemFormat(){
        return $this->precoSemFormat;
    }
    
    function getPrecoPromoSemFormat(){
        return $this->precoPromoSemFormat;
    }    
    /**
     * Recebe um valor no formato 9.999,99, ou 9999.99, ou ainda 9999,99, e converte 
     * para um valor inteiro, sem separadores, onde os dois �ltimos caracteres representam 
     * a parte decimal.
     * 
     * Exemplos:
     * 123,543 ficar� 12354 (equivale a 123,54)
     * 1254    ficar� 125400 (equivale a 1254,00)
     * 
     * @param float $valueDec Valor decimal a ser convertido para inteiro
     * @param string $decPoint Separador decimal usado no $valueDec informado.
     * @param string $thousandsSep Separador de milhar usado no $valueDec informado.
     * @return integer
     */
    function convertNumberDec2NumberInt($valueDec,$decPoint,$thousandsSep){
        $numberInt  = number_format($valueDec, 2, $decPoint, $thousandsSep);
        $numberInt  = str_replace($decPoint,'',$valueDec);
        $numberInt  = str_replace($thousandsSep,'',$valueDec); 
        return $numberInt;
    }
    
    /**
     * Calcula o subtotal do item atual multiplicando a quantidade pelo valor unit�rio.
     * Caso o item esteja com pre�o promocional este ser� usado como valor unit�rio.
     * 
     * @return float Retorna o subtotal do produto atual.
     */
    function calcSubtotal(){
        $quantidade    = (int)$this->quantidade;        
        $preco         = $this->getPrecoSemFormat();
        $precoPromo    = $this->getPrecoPromoSemFormat();
        $precoUnit     = ($precoPromo > 0) ? $precoPromo : $preco;
        $subtotal      = $precoUnit;
        
        if ($quantidade > 1) {
            $subtotal = $quantidade*$precoUnit;
        }
        
        //Formata a sa�da com duas casas decimais:
        $subtotal = number_format($subtotal,2,'.','');
        
        $this->addParamXml('SUBTOTAL',$subtotal);
        
        return $subtotal;
    }
    
    public function getXml(){             
        try {
            $xml            = '';
            $xmlParams      = ''; 
            $arrParamsXml   = $this->getParamsXml();
            if (is_array($arrParamsXml)) {
                $this->calcSubtotal();
                foreach($arrParamsXml as $param => $value) {
                    if (strlen($param) > 0) {
                        $xmlParams .= $this->getTagXml($param, $value);
                    }
                }
            }            
            
            $save = ($this->save) ? 1 : 0;
            $xml = "
            <ITEM save='{$save}'>
                {$xmlParams}
            </ITEM>";
        } catch (\Exception $e) {
            throw $e;
        }
        return $xml;
    }                    
}

?>
