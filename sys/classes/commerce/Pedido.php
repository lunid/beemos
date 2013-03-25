<?php


namespace sys\classes\commerce;
use \sys\classes\util\Curl;

class Pedido {
    
    //Lista de parâmetros permitidos ao criar um novo pedido
    private $arrLibParams = array(
        'NUM_PEDIDO:setNumPedido',
        'VALOR_COMPRA:setValorCompra',                
        'VALOR_TOTAL:setValorTotal',
        'NOME_SAC:setNomeSac',
        'EMAIL_SAC:setEmailSac',
        'ENDERECO_SAC:setEnderecoSac',
        'CIDADE_SAC:setCidadeSac',
        'UF_SAC:setUfSac',
        'CPF_CNPJ_SAC:setCpfCnpjSac',
        'OBS:setObs'
    );
    
    private $arrParams = array();
    private $arrItemPedido = array(); //Array de objetos do tipo ItemPedido.
    private $nomeSac;
    private $emailSac;
    private $enderecoSac;
    private $cidadeSac;
    private $ufSac;
    private $cpfCnpjSac;
    private $_urlSend = 'http://dev.superproweb.com.br/commerce/pedido/request/';
    
    function __construct($arrDadosSac=array()){
        $this->loadArrDadosSac($arrDadosSac);
    }
    
    /**
     * Recebe um array associativo de dados onde cada índice deve coincidir com um índice em $arrLibParams.
     * 
     * @param string[] $arrDadosSac
     * @return void
     * 
     * @throws \Exception Caso um ou mais parâmetros informados não possuam correspondência em $arrLibParams.
     */
    function loadArrDadosSac($arrDadosSac){
        $arrMsgErr = NULL;
        if (is_array($arrDadosSac) && count($arrDadosSac) > 0) {
            $arrLibParams    = $this->arrLibParams;//Parâmetros permitidos
            $arrAction       = array();
            $arrTag          = array();
            
            //Separa um array com os parâmetros autorizados e outro com os seus respectivos métodos.
            foreach($arrLibParams as $label){
                list($indice,$action) = explode(':',$label);
                if (strlen($action) > 0) $arrAction[]   = $action;
                if (strlen($indice) > 0) $arrTag[]      = $indice;
            }          
            
            //Valida o array de dados do sacado:
            foreach($arrDadosSac as $name=>$value) {
                $key = array_search($name,$arrTag);
                if ($key !== FALSE) {
                    $action = $arrAction[$key];
                    if (method_exists($this, $action)) {
                        //Existe um método para definir o valor do parâmetro atual:
                        $this->$action($value);
                    }
                } else {
                    $arrMsgErr[] = "Parâmetro {$name} não permitido";
                }               
            }
        }
        
        if (is_array($arrMsgErr)) {
            $msgErr = join(', ',$arrMsgErr);
            throw new \Exception($msgErr);                
        }
    }
    /**
     * Adiciona um item (produto) ao pedido atual.
     * 
     * @param ItemPedido $objItemPedido
     * @return void
     */
    function addItem($objItemPedido){
        if (is_object($objItemPedido)) $this->arrItemPedido[] = $objItemPedido;
    }
    
    /**
     * Define o parâmetro numPedido.
     * O valor deve ser numérico.
     * 
     * @param integer $numPedido
     */
    function setNumPedido($numPedido){
        if (ctype_digit($numPedido)) {
            $this->addParam('NUM_PEDIDO',$numPedido);
        } else {
            throw new \Exception('O número do pedido deve ser um valor numérico.');
        }
    }
    
    /**
     * Informa um valor numérico que representa o valor total do frete do pedido.     
     * Se informado, o valor NÃO deve conter separadores.
     * A informação de frete é opcional. Se não for informado o valor zero será enviado.
     * 
     * Exemplo:
     *  10,00 deve ser informado como 1000.
     *  23,5  deve ser informado como 2350.
     * 
     * @return void
     */    
    function setTotalFrete($valorFrete=0){
        $this->addParam('VALOR_FRETE',(int)$valorFrete);
    }
    
    /**
     * Informa um valor numérico que representa um desconto no valor final do pedido. 
     */
    function setTotalDesc($value){
        
    }
    
    function setTotalPedido($value){
                
    }
    
    function setNomeSac($value){
        $value = trim($value);
        if (strlen($value) > 0){
            $this->nomeSac = $value;
            $this->addParam('NOME_SAC',$value);
        }          
    }
    
    function setEmailSac($value){
        $value = trim($value);
        if (strlen($value) > 0){
            $this->emailSac = $value;
            $this->addParam('EMAIL_SAC',$value);
        }          
    }
    
    function setEnderecoSac($value){
        $value = trim($value);
        if (strlen($value) > 0){
            $this->enderecoSac = $value;
            $this->addParam('ENDERECO_SAC',$value);
        }        
    }
    
    function setCidadeSac($value){
        $value = trim($value);
        if (strlen($value) > 0){
            $this->cidadeSac = $value;
            $this->addParam('CIDADE_SAC',$value);
        }
    }
    
    function setUfSac($value){
        $value = trim($value);
        if (strlen($value) == 2 && ctype_alpha($value)) {
            $value          = strtoupper($value);
            $this->ufSac    = $value;
            $this->addParam('UF_SAC',$value);
        }
    }
    
   /**
    * Recebe e guarda um valor referente a um CPF (11 dígitos) ou a um CNPJ (14 dígitos).
    * 
    * @param string $value
    */
    function setCpfCnpjSac($value){
        $value = trim($value);
        if (strlen($value) > 0) {
            $arrValue   = str_split($value);
            $arrChar    = array();
            
            //Retira caracteres que não sejam numéricos:
            foreach ($arrValue as $char){
                if (ctype_digit($char)) $arrChar[] = $char;
            }
            
            $valueChar = join('',$arrChar);
            
            if (strlen($valueChar) >=11 && strlen($valueChar) <= 14 && ctype_alnum($valueChar)) {
                $this->cpfCnpjSac = $valueChar;
                $this->addParam('CPF_CNPJ_SAC',$valueChar);
            } else {
                throw new \Exception('Pedido->setCpfCnpjSac() O CPF/CNPJ informado ('.$value.') parece ser inválido.');
            }       
        }
    }       
    
    function addParam($name,$value){
       $name            = strtoupper($name);
       $arrLibParams    = $this->arrLibParams;//Parâmetros permitidos
       $arrTag          = array();
       
       foreach($arrLibParams as $label){
           list($indice,$action) = explode(':',$label);
           if (strlen($indice) > 0) $arrTag[] = $indice;
       }
       
       $key = array_search($name,$arrTag);
       if ($key !== FALSE) {
           $this->arrParams[$name] = $value;
       } else {
           throw new \Exception('O parâmetro informado não é válido.');
       }
    }
    
    /**
     * Gera a string XML que será enviada ao gateway de pagamento.
     * 
     * @throws \Exception Caso nenhum parâmetro tenha sido informado ou então não exista produto(s).
     */
    function getXml(){
        $xml            = '<ROOT>';
        $arrParams      = $this->arrParams;
        $arrItemPedido  = $this->arrItemPedido;
        
        if (is_array($arrParams) && count($arrParams) > 0) {
            $xml .= "<PEDIDO>";
            foreach ($arrParams as $key=>$value){
                $xml .= "<PARAM id='{$key}'>{$value}</PARAM>";
            }
            
            if (is_array($arrItemPedido) && count($arrItemPedido) > 0) {
                foreach ($arrItemPedido as $objItemPedido){
                    $descricao           = $objItemPedido->getDescricao();
                    $quantidade          = $objItemPedido->getQuantidade();
                    $precoUnit           = $objItemPedido->getPrecoUnit();
                    $subtotal            = $objItemPedido->calcSubtotal();
                    
                    $xml .= "
                    <ITEM>
                        <PARAM id='DESCRICAO'>{$descricao}</PARAM>
                        <PARAM id='QUANTIDADE'>{$quantidade}</PARAM>                        
                        <PARAM id='PRECO_UNIT'>{$precoUnit}</PARAM>
                        <PARAM id='SUBTOTAL'>{$subtotal}</PARAM>
                    </ITEM>";
                }                
            } else {
                throw new \Exception('Pedido->getXml() Nenhum produto foi adicionado ao pedido.');
            }
            
            $xml .= "</PEDIDO>";
        } else {
            throw new \Exception('Pedido->getXml() Nenhum parâmetro foi informado.');
        }
        $xml .= "</ROOT>";
        
        return $xml;
    }
    
    function send(){
        $xmlNovoPedido  = $this->getXml(); 
        $uid            = 'b98af3c46666cb58b73677859074e116';
        $request	= "xmlNovoPedido=".$xmlNovoPedido."&uid=".$uid;
        $objCurl 	= new Curl($this->_urlSend);
        
        $objCurl->setPost($request);
        $objCurl->createCurl();
        $errNo = $objCurl->getErro();
        if ($errNo == 0){
            $response = $objCurl->getResponse();
            return $response;
        } else {
            $err    = $objCurl->getOutput();
            $msgErr = "Pedido->send() Erro ao se comunicar com o gateway: {$err}";
            throw new \Exception($msgErr);                
        }
    }
    
}

?>
