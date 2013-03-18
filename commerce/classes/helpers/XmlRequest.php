<?php

namespace \commerce\classes\helpers;

class XmlRequest {
    
    static  $arrParamsReq    = array('BANDEIRA','NUM_CARTAO','COD_SEGURANCA','VALOR_COMPRA','VLD_MES','VLD_ANO','PARCELAS','NUM_PEDIDO');//PARÂMETROS OBRIGATÓRIOS
    static  $arrParamsOpt    = array('CAPTURA_AUTO');//PARÂMETROS OPCIONAIS    
    private $arrParams       = array();
    private $arrMsgErr       = array();
    
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
}

?>
