<?php

class BmItemPedido extends BmXml {
    
    private $categoria      = '';
    private $codigo         = '';
    private $descricao      = '';
    private $quantidade     = 1;    
    private $precoUnit      = 0;
    private $unidade        = 'CX';
    private $campanha       = '';
    private $precoUnitSemFormat; //Pre�o unit�rio sem formata��o. Ex.: 123,40 ficar� 12340, 2345 ficar� 234500, 65,3 ficar� 6530    
    
    /**
     * Inicializa um objeto com descri��o, pre�o unit�rio e quantidade.
     * Ao informar o pre�o unit�rio, o padr�o usado ser� o ponto '.' como separador decimal, sem separador de milhar.
     * 
     * @param string $descricao
     * @param float $precoUnit
     * @param integer $qtde
     */
    function __construct($cat='',$codigo,$descricao,$precoUnit=0,$qtde=1,$unidade='CX',$campanha=''){
        $this->setCategoria($cat);
        $this->setCodigo($codigo);
        $this->setDescricao($descricao);
        $this->quantidade = (int)$qtde;
        $this->setUnidade($unidade);
        $this->setCampanha($campanha);
        if ($precoUnit > 0) {
            $this->precoUnitEn($precoUnit);
        }
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
    
    function setUnidade($unidade){
        if (strlen($unidade) <= 3 && ctype_alpha($unidade)) {
            $this->unidade = $unidade;            
            $this->addParamXml('UNIDADE',$unidade);
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
    
    /**
     * Informa a quantidade de um produto e qual a sua unidade de medida.
     * 
     * @param integer $qtde Valor inteiro que indica a quantidade do item.
     * @param string $unid Unidade de medida do produto. � permitido utilizar no m�ximo 3 letras para indicar a unidade.
     * Por exemplo, "CX" para caixa, "PC" para pacote, "UN"
     * 
     * @throws \Exception
     */
    function setQuantidade($qtde=1,$unidade='CX'){
        if (ctype_alpha($unid)) {
            $this->quantidade = (int)$qtde;
            $this->setUnidade($unidade);
            $this->addParamXml('QUANTIDADE',$this->quantidade);
        } else {
            throw new \Exception('A unidade '.$unid.' informada em ItemPedido->setQuantidade() n�o � v�lida. O par�metro unid deve conter apenas letras.');
        }
    }
    
    function getQuantidade(){
        $quantidade = (int)$this->quantidade;
        if ($quantidade == 0) $quantidade = 1;
        return $quantidade;
    }
    
    /**
     * Informa o pre�o unit�rio do produto no formato 9999.99 (nota��o inglesa).
     * 
     * @param float $precoUnit Valor decimal no formato americano (usa ponto como separador decimal)
     */
    function precoUnitEn($precoUnit){
        $precoUnit = str_replace(',','',$precoUnit);
        $this->setPrecoUnit($precoUnit,'.','');
    }
    
    /**
     * Informa o pre�o unit�rio do produto no formato 9999,99.
     * 
     * @param float $precoUnit Valor decimal no formato brasileiro (usa v�rgula como separador decimal)
     * @param string $thousandsSep Caractere separador de milhar.
     */    
    function precoUnitBr($precoUnit) {
        $precoUnit = str_replace('.','',$precoUnit);        
        $this->setPrecoUnit($precoUnit,',','');
    }
    
    /**
     * Informa o pre�o unit�rio do produto.
     *      
     * @param float $precoUnit Pre�o como valor decimal
     * @param string $decPoint Separador decimal usado no $precoUnit informado.
     * @param string $thousandsSep Separador de milhar usado no $precoUnit informado.
     * @return void
     */
    private function setPrecoUnit($precoUnit,$decPoint,$thousandsSep){
        if (is_numeric($precoUnit)) {
            $precoUnitSemFormat             = $this->convertNumberDec2NumberInt($precoUnit, $decPoint, $thousandsSep); 
            $this->precoUnitSemFormat       = $precoUnitSemFormat;
            $this->precoUnit                = number_format($precoUnit,2,'.','');
           
            $this->addParamXml('PRECO_UNIT',$this->precoUnit);
            
        } else {
            throw new \Exception('ItemPedido->setPrecoUnit(): O pre�o unit�rio informado n�o � um valor v�lido.');
        }
    }
    
    function getPrecoUnit(){
        return $this->precoUnit;
    }
    
    function getPrecoUnitSemFormat(){
        return $this->precoUnitSemFormat;
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
     * Calcula o subtotal do produto atual multiplicando a quantidade pelo valor unit�rio.
     * 
     * @return float Retorna o subtotal do produto atual.
     */
    function calcSubtotal(){
        $quantidade    = (int)$this->quantidade;
        $precoUnit     = $this->precoUnitSemFormat;
        $subtotal      = $precoUnit;
        
        if ($quantidade > 1) {
            $subtotal = $quantidade*$precoUnit;
        }
        
        //Formata a sa�da com duas casas decimais:
        $subtotal = number_format($subtotal,2,'.','');
        
        $this->addParamXml('SUBTOTAL',$subtotal);
        
        return $subtotal;
    }
    
    function setPrecoPromo($precoDe,$precoPor){
        
    }
    
    public function getXml(){             
        try {
            $xml            = '';
            $xmlParams      = ''; 
            $arrParamsXml   = $this->getParamsXml();
            if (is_array($arrParamsXml)) {
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
        } catch (Exception $e) {
            throw $e;
        }
        return $xml;
    }                    
}

?>
