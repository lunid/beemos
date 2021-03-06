<?php

namespace common\classes\helpers\commerce;
use \sys\classes\util\Xml;
use \commerce\classes\models\NumPedidoModel;

class XmlRequestHelper extends Xml {
    
    static  $arrParamsReq       = array('NOME','BANDEIRA','NUM_CARTAO','COD_SEGURANCA','VALOR_COMPRA','VLD_MES','VLD_ANO','PARCELAS','NUM_PEDIDO');//Parâmetros obrigatórios
    static  $arrParamsOpt       = array('CAPTURA_AUTO');//Parâmetros Opcionais
    private $arrParams          = array();
    private $arrMsgErr          = array();
    private $strXmlRequest      = '';
    private $numPedido          = 0;
    private $objDadosPedido     = NULL;//Objeto stdClass();
    private $arrObjItensPedido  = NULL;//Array de objetos;
            
    function __construct($strXmlRequest){
        $this->strXmlRequest = trim($strXmlRequest);
        $this->vldXml();
    }
    
    private function vldXml(){
        $strXmlRequest = $this->strXmlRequest;
        if (strlen($strXmlRequest) > 0) { 
            try {
                $objXml = simplexml_load_string(utf8_encode($strXmlRequest));  
                if (is_object($objXml)) {
                    $nodeCfg            = $objXml->PEDIDO->CFG;
                    $nodeSacado         = $objXml->PEDIDO->SACADO->PARAM;
                    $nodeItens          = $objXml->PEDIDO->ITENS->ITEM;//Pode ter um ou mais itens   
                    $nodeCheckoutCc     = $objXml->PEDIDO->CHECKOUT->CARTAO->PARAM;
                    $nodeCheckoutBlt    = $objXml->PEDIDO->CHECKOUT->BOLETO->PARAM;

                    $objXmlSacado       = new XmlSacado($nodeSacado);
                    $objXmlCfg          = new XmlCfg($nodeCfg);
                    echo $objXmlCfg->NUM_PEDIDO;
                    die();
                    //$msgErr             = $this->vldPedidoNode($nodePedido,$msgErr);
                    $msgErr             = $this->vldSacadoNode($nodeSacado,$msgErr);
                    $msgErr             = $this->vldItemNode($nodeItens,$msgErr);
                    $msgErr             = $this->vldCheckoutCc($nodeCheckoutCc,$msgErr);
                    $msgErr             = $this->vldCheckoutBlt($nodeCheckoutBlt,$msgErr);                

                    $objDadosPedido     = $this->objDadosPedido;
                    $arrObjItensPedido  = $this->arrObjItensPedido;

                    if (is_object($objDadosPedido) && count($arrObjItensPedido) >= 1) {
                        //Todos os dados foram validados com sucesso.                    
                        $out = TRUE;
                    }

                } else {                
                    $msgErr[] = 'Erro ao carregar o XML '.$stringXmlRequest;                                            
                }  
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
    
    function vldSacadoNode($node){
        $value  = self::valueForAttrib($node,'id','NUM_PEDIDO');
    }
    
    function getObjDadosPedido(){
        return $this->objDadosPedido;
    }
    
    function getArrObjItensPedido(){
        return $this->arrObjItensPedido;
    }
    
    function addParam($name,$value){
        $name               = strtoupper($name);
        $arrParamsPermit    = array_merge(self::$arrParamsReq,self::$arrParamsOpt);
        $key                = array_search($name,$arrParamsPermit);
        if ($key !== false){
                $this->arrParams[$name] = $value;
        } else {
                $this->arrMsgErr[] = "O parâmetro informado (".$name.") é inválido.";	
        }	        
    }
    
    /**
     * Retorna uma string com os parâmetros obrigatórios não informados, se houver.
     * 
     * @param string[] $arrParams
     * @return string
     */
    public static function requiredParamsNotInfo($arrParams){            
        $arrParamsNotInfo    = array();
        $listParamsNotInfo   = '';
        
        if (is_array($arrParams)){
            foreach(self::$arrParamsReq as $param){
                $value = $arrParams[$param];
                if (strlen($value) == 0) $arrParamsNotInfo[] = $param;
            }
            $listParamsNotInfo = join(', ',$arrParamsNotInfo);			
        }
        return $listParamsNotInfo;
    }   
    
    /**
     * Verifica se todos os nós recebidos na string XML são válidos.
     * Faz a validação dos dados do pedido e também do(s) item(ns) do pedido.
     * Se o XML for validado com sucesso, gera o valor de duas propriedades, como segue:
     * 
     *  - $objDadosPedido, guarda dados do pedido atual em propriedades de mesmo nome.
     *  - $arrObjItensPedido, array onde cada índice contém um objeto referente a um item do pedido
     * 
     * @return boolean
     * @throws \Exception Caso algum erro de validação seja encontrado.
     */
    function vldXmlNovoPedido(){
        $stringXmlRequest   = $this->stringXmlRequest;
        $msgErr             = array();
        $out                = FALSE;
        
        if (strlen($stringXmlRequest) > 0) { 
            $objXml = simplexml_load_string(utf8_encode($stringXmlRequest));  
            if (is_object($objXml)) {
                $nodePedido         = $objXml->PEDIDO->PARAM;
                $nodeSacado         = $objXml->PEDIDO->SACADO->PARAM;
                $nodeItens          = $objXml->PEDIDO->ITENS->ITEM;//Pode ter um ou mais itens   
                $nodeCheckoutCc     = $objXml->PEDIDO->CHECKOUT->CARTAO->PARAM;
                $nodeCheckoutBlt    = $objXml->PEDIDO->CHECKOUT->BOLETO->PARAM;
                
                //$msgErr             = $this->vldPedidoNode($nodePedido,$msgErr);
                $msgErr             = $this->vldSacadoNode($nodeSacado,$msgErr);
                $msgErr             = $this->vldItemNode($nodeItens,$msgErr);
                $msgErr             = $this->vldCheckoutCc($nodeCheckoutCc,$msgErr);
                $msgErr             = $this->vldCheckoutBlt($nodeCheckoutBlt,$msgErr);                
                
                $objDadosPedido     = $this->objDadosPedido;
                $arrObjItensPedido  = $this->arrObjItensPedido;
                
                if (is_object($objDadosPedido) && count($arrObjItensPedido) >= 1) {
                    //Todos os dados foram validados com sucesso.                    
                    $out = TRUE;
                }
                
            } else {                
                $msgErr[] = 'Erro ao carregar o XML '.$stringXmlRequest;                                            
            }            
        }
        
        if (count($msgErr) > 0) {
            $stringErr = join('; ',$msgErr);
            throw new \Exception($stringErr);
        }
        return $out;
    }
    
    /**
     * Recebe um nó XML <PEDIDO> e faz a validação das tags PARAM para dados do Sacado.
     * 
     * @param object $node
     * @return string[]
     */
    private function vldSacadoNodeOld($node,$msgErr=array()){  
        //Formato de cada índice de arrParams:
        //PARAM:tipoValor:obrigatorio:length      
        $arrParams = array(
            'NUM_PEDIDO:integer:0:0',
            'NOME:string:1:100',
            'EMAIL:email:0:100',
            'ENDERECO:string:0:120',
            'CIDADE:string:0:100',
            'UF:string:0:2',
            'CPF_CNPJ:string:0:20',
        );        
        
        if (!is_null($node)) {  
            $objDadosPedido = new \stdClass();
            foreach($arrParams as $item) {                
                list($param,$type,$required,$length) = explode(':',$item);
                $err    = '';
                $value  = self::valueForAttrib($node,'id',$param);
                if ($type == 'integer') {                    
                    if ($param == 'NUM_PEDIDO' && strlen($value) > 0 && (int)$value == 0) {
                        //Valida o valor informado ou então localiza o próximo NUM_PEDIDO no DB.                                
                        $err = "O PARAM{{$param}} = {$value} informado não é válido. Informe um valor numérico para {$param}.";
                    } elseif ($required == 1 && (int)$value <= 0) {
                        $err = "O PARAM{{$param}} obrigatório não foi informado ou é menor igual a zero";
                    }
                    $value = (int)$value;                    
                } elseif ($type == 'float') {
                    $value = (float)$value;                                                    
                    if ($required == 1 && $value <= 0) $err = "O PARAM{{$param}}= {$value} obrigatório não foi informado ou é menor igual a zero";
                } elseif ($type == 'email') {
                    //Validação de e-mail:     
                    if (strlen($value) > 0) {
                        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){ 
                            $err = "O PARAM{{$param}} = '{$value}' não parece ser um e-mail válido";
                        }
                    } elseif ($required == 1) {
                        $err = "O PARAM{{$param}} é obrigatório e não foi informado";
                    }
                } 
                if (strlen($err) > 0) {
                    //Há um erro de validação.
                    $msgErr[] = $err;
                } else {
                    //Valor validado com sucesso.
                    $objDadosPedido->$param = ($type == 'email' || $type == 'string')?(string)$value:$value;
                }
            }
        } 
        if (count($msgErr) == 0) $this->objDadosPedido = $objDadosPedido;//Não houve erros. Guarda o objeto de dados do pedido.
        return $msgErr;
    }        
    
    private function vldItemNode($nodeItens,$msgErr=array()){
        //Formato de cada índice de arrParams:
        //PARAM:tipoValor:obrigatorio
        $arrParams = array(
            'CATEGORIA:string:0',
            'CODIGO:string:1',
            'UNIDADE:string:1',
            'DESCRICAO:string:1',
            'QUANTIDADE:string:0',
            'PRECO_UNIT:float:0',
            'CAMPANHA:string:0',
            'SUBTOTAL:float:0',            
            'SAVE:integer:0'
        );         

        $arrObjItens = array();
        
        if (is_object($nodeItens)) {
            $i = 1;              
            foreach($nodeItens as $nodeItem) {
                //Um ou mais nós ITEM:
                $node           = $nodeItem->PARAM;
                $subtotal       = 0;
                $quantidade     = 1;
                $precoUnit      = 0;
                $objItem        = new \stdClass();
                
                foreach($arrParams as $item) {
                    //Atributos PARAM do nó atual
                    list($param,$type,$required) = explode(':',$item);
                    $value = self::valueForAttrib($node,'id',$param);  
                    if ($required == 1 && strlen($value) == 0) {
                        $msgErr[] = "O PARAM{{$param}} do item {$i} é obrigatório e não foi informado";
                        continue 2;//Vai para o próximo nó ITEM.
                    }

                    if ($type == 'integer') {
                        $value = (int)$value;
                        if ($param == 'QUANTIDADE' && $value > 0) $quantidade = $value;
                    } elseif ($type == 'float') {
                        $value = (float)$value;
                        if ($param == 'SUBTOTAL' && $value > 0) {
                            //O Subtotal foi informado
                            $subtotal = $value;
                        } elseif ($param == 'PRECO_UNIT' && $value > 0) {
                            //O preço unitário foi informado.
                            $precoUnit = $value;
                        }
                    } else {
                        $value = (string)$value;
                    }
                                        
                    $objItem->$param = $value;
                }
                                    
                //Calcula o subtotal do item atual, caso não tenha sido informado.
                if ($subtotal == 0) {                        
                    $subtotal           = $quantidade*$precoUnit;
                    $objItem->SUBOTOTAL = $subtotal;
                }
                
                $arrObjItens[] = $objItem;
                
                $i++;
            }
        } else {
            $msgErr[] = "Pedido sem itens. Pelo menos um item deve ser informado.";
        }
        
        if (count($msgErr) == 0) $this->arrObjItensPedido = $arrObjItens;
        return $msgErr;
    }
    
    private function vldCheckoutBlt($nodes,$msgErr=array()){
        
        $msgErr     = array();
        $objDados   = new \stdClass();
        
        //Formato de cada índice de arrParams:
        //PARAM:tipoValor:obrigatorio
        $arrStrParams = array(
            'BANCO:string:0',
            'VENCIMENTO:date:0'
        );  
        
        $arrProcess = $this->vldNodeParams($arrStrParams, $nodes);
        $objParams  = $arrProcess['OBJ_PARAMS'];
        $arrErr     = $arrProcess['ARR_MSG_ERR'];
        return $msgErr;        
    }
    
    private function vldCheckoutCc($node,$msgErr=array()){
        //Formato de cada índice de arrParams:
        //PARAM:tipoValor:obrigatorio
        $arrParams = array(
            'CONVENIO:string:0',
            'BANDEIRA:string:1',
            'CC:string:1',
            'COD_SEG:integer:1',
            'VALIDADE:integer:1',
            'PARCELAS:integer:0',
            'CAPTURA:integer:0'
        );           
    }
    
    private function vldNodeParams($arrStrParams,$nodes){
        $objDados   = new \stdClass();
        $msgErr[]   = array();
        
        if (is_object($nodes)) {
            $i = 1;              
            foreach($nodes as $node) {
                //Um ou mais nós:
                $node  = $node->PARAM;
         
                foreach($arrStrParams as $strParams) {
                    //Atributos PARAM do nó atual
                    $err = '';
                    list($param,$type,$required) = explode(':',$strParams);
                    $value = self::valueForAttrib($node,'id',$param);  
                    if ($required == 1 && strlen($value) == 0) {
                        //Parâmetro obrigatório.
                        $msgErr[] = "O PARAM{{$param}} do item {$i} é obrigatório e não foi informado";
                        continue 2;//Vai para o próximo nó ITEM.
                    } elseif ($required == 0 && strlen($value) > 0) {
                        if ($type == 'date' && strlen($value) == 10) {
                            @list($y,$m,$d) = @explode('-',$value);
                            if (!@checkdate($m,$d,$y)){                        
                                $err = "O PARAM{{$param}} não parece ser uma data válida";
                            }                    
                        }                        
                    }                                          
                    
                    if (strlen($err) > 0) {
                        //Há um erro de validação.
                        $msgErr[] = $err;
                    } else {
                        $objDados->$param = $value;        
                    }
                }
            }
        }
        
        $arrOut['OBJ_PARAMS']   = $objDados;
        $arrOut['ARR_MSG_ERR']  = $msgErr;
        return $arrOut;
    }
}

?>
