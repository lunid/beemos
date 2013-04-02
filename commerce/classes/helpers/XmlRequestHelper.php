<?php

namespace commerce\classes\helpers;
use \sys\classes\util\Xml;
use \commerce\classes\models\NumPedidoModel;

class XmlRequestHelper extends Xml {
    
    static  $arrParamsReq       = array('BANDEIRA','NUM_CARTAO','COD_SEGURANCA','VALOR_COMPRA','VLD_MES','VLD_ANO','PARCELAS','NUM_PEDIDO');//Parâmetros obrigatórios
    static  $arrParamsOpt       = array('CAPTURA_AUTO');//Parâmetros Opcionais
    private $arrParams          = array();
    private $arrMsgErr          = array();
    private $stringXmlRequest   = '';
    private $numPedido          = 0;
    private $objDadosPedido     = NULL;//Objeto stdClass();
    private $arrObjItensPedido  = NULL;//Array de objetos;
            
    function __construct($stringXmlRequest){
        $this->stringXmlRequest = $stringXmlRequest;
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
     *      
     * @return boolean
     * @throws \Exception Caso algum erro de validação seja encontrado.
     */
    function vldXmlNovoPedido(){
        $stringXmlRequest   = $this->stringXmlRequest;
        $msgErr             = array();
        $out                = FALSE;
        
        if (strlen($stringXmlRequest) > 0) {                  
            $objXml = simplexml_load_string($stringXmlRequest);  
            if (is_object($objXml)) {
                $nodePedido = $objXml->PEDIDO->PARAM;
                $nodeItens  = $objXml->PEDIDO->ITEM;//Pode ter um ou mais itens   
                
                $msgErr             = $this->vldPedidoNode($nodePedido,$msgErr);
                $msgErr             = $this->vldItemNode($nodeItens,$msgErr);
                
                $objDadosPedido     = $this->objDadosPedido;
                $arrObjItensPedido  = $this->arrObjItensPedido;
                
                if (is_object($objDadosPedido) && count($arrObjItensPedido) >= 1) {
                    //Todos os dados foram validados com sucesso.
                    //Guarda os valores no DB.
                    
                    $out = TRUE;
                }
                
            } else {                
                $msgErr[] = 'Impossível ler o arquivo '.$pathXml;                                            
            }            
        }
        
        if (count($msgErr) > 0) {
            $stringErr = join('; ',$msgErr);
            throw new \Exception($stringErr);
        }
        return $out;
    }
    
    /**
     * Recebe um nó XML <PEDIDO> e faz a validação das tags PARAM.
     * 
     * @param object $node
     * @return string[]
     */
    private function vldPedidoNode($node,$msgErr=array()){  
        //Formato de cada índice de arrParams:
        //PARAM:tipoValor:obrigatorio        
        $arrParams = array(
            'NUM_PEDIDO:integer:0',
            'NOME_SAC:string:1',
            'EMAIL_SAC:email:0',
            'ENDERECO_SAC:string:0',
            'CIDADE_SAC:string:0',
            'UF_SAC:string:0',
            'CPF_CNPJ_SAC:string:0',
            'VALOR_FRETE:float:0',
            'TOTAL_PEDIDO:float:0',
            'SAVE_SAC:integer:0'
        );        
        
        if (!is_null($node)) {  
            $objDadosPedido = new \stdClass();
            foreach($arrParams as $item) {                
                list($param,$type,$required) = explode(':',$item);
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
}

?>
