<?php

namespace commerce\classes\helpers;
use \sys\classes\util\Xml;

class XmlRequestHelper extends Xml {
    
    static  $arrParamsReq       = array('BANDEIRA','NUM_CARTAO','COD_SEGURANCA','VALOR_COMPRA','VLD_MES','VLD_ANO','PARCELAS','NUM_PEDIDO');//Parâmetros obrigatórios
    static  $arrParamsOpt       = array('CAPTURA_AUTO');//Parâmetros Opcionais
    private $arrParams          = array();
    private $arrMsgErr          = array();
    private $stringXmlRequest   = '';
    private $numPedido          = 0;
    
            
    function __construct($stringXmlRequest){
        $this->stringXmlRequest = $stringXmlRequest;
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
    
    function vldXmlNovoPedido(){
        $stringXmlRequest   = $this->stringXmlRequest;
        $msgErr             = array();
        
        if (strlen($stringXmlRequest) > 0) {                  
            $objXml = simplexml_load_string($stringXmlRequest);  
            if (is_object($objXml)) {
                $nodePedido = $objXml->PEDIDO->PARAM;
                $nodeItens  = $objXml->PEDIDO->ITEM;//Pode ter um ou mais itens   
                
                $msgErr     = $this->vldPedidoNode($nodePedido,$msgErr);
                $msgErr     = $this->vldItemNode($nodeItens,$msgErr);
            } else {                
                $msgErr[] = 'Impossível ler o arquivo '.$pathXml;                                            
            }            
        }
        
        if (count($msgErr) > 0) {
            $stringErr = join('; ',$msgErr);
            throw new \Exception($stringErr);
        }
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
            foreach($arrParams as $item) {
                list($param,$type,$required) = explode(':',$item);
                $value = self::valueForAttrib($node,'id',$param);
                if ($type == 'integer') {
                    $value = (int)$value;
                    if ($param == 'NUM_PEDIDO' && $value == 0) {
                        //Um valor de NUM_PEDIDO não foi informado, localiza o próximo NUM_PEDIDO no DB.                                
                        $this->getProxNumPedido();
                    } elseif ($required == 1 && $value <= 0) {
                        $msgErr[] = "O PARAM{{$param}} obrigatório não foi informado ou é menor igual a zero";
                    }
                } elseif ($type == 'float') {
                    $value = (float)$value;                                                    
                    if ($required == 1 && $value <= 0) $msgErr[] = "O PARAM{{$param}}= {$value} obrigatório não foi informado ou é menor igual a zero";
                } elseif ($type == 'email') {
                    //Validação de e-mail:     
                    if (strlen($value) > 0) {
                        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){ 
                            $msgErr[] = "O PARAM{{$param}} = '{$value}' não parece ser um e-mail válido";
                        }
                    } elseif ($required == 1) {
                        $msgErr[] = "O PARAM{{$param}} é obrigatório e não foi informado";
                    }
                } 
            }
        } 
        return $msgErr;
    }
    
    /**
     * Localiza o próximo NUM_PEDIDO do usuário atual.
     * 
     * @return integer
     */
    private function getProxNumPedido(){
        
    }
    
    private function vldItemNode($nodeItens,$msgErr=array()){
        //Formato de cada índice de arrParams:
        //PARAM:tipoValor:obrigatorio
        $arrParams = array(
            'DESCRICAO:string:1',
            'QUANTIDADE:string:0',
            'PRECO_UNIT:float:0',
            'CAMPANHA:string:0',
            'SUBTOTAL:float:0',            
            'SAVE_ITEM:integer:0'
        );         

        if (is_object($nodeItens)) {
            $i          = 1;              
            foreach($nodeItens as $nodeItem) {
                //Um ou mais nós ITEM:
                $node           = $nodeItem->PARAM;
                $subtotal       = 0;
                $quantidade     = 1;
                $precoUnit      = 0;

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
                    }                    
                }
                
                if ($subtotal == 0) {
                    //O valor de subtotal deve ser calculado:
                    $subtotal = $quantidade*$precoUnit;
                }
                $i++;
            }
        } else {
            $msgErr[] = "Pedido sem itens. Pelo menos um item deve ser informado.";
        }
        return $msgErr;
    }
}

?>
