<?php
namespace commerce\classes\helpers;
use \sys\classes\util\Xml;
/**
 * Classe utilizada para validar os campos recebidos do XML.
 *
 * @author Claudio
 */
abstract class XmlValidation extends Xml {
    protected $nodeName     = 'Undefined';
    private $nodeXml        = NULL;
    private $objDados       = NULL; //Guarda um objeto stdClass contendo dados de um nó XML lido.
    protected $arrVldParams = array();
    
    function __construct($nodeXml){
        $this->nodeXml = $nodeXml;
        try {
           $this->getObjDadosXml();            
        } catch(\Exception $e) {
           throw $e;
        }        
    }

    function getNodeXml(){
        return $this->nodeXml;
    }
    
 /**
     * Valida um conjunto de valores contidos no nó XML ($nodeXml) a partir 
     * de uma lista de critérios definidos em $arrVldParams na classe filha.
     * 
     * @param string[] $arrParams Array unidimensional contendo string de parâmetros.
     * @return \stdClass Objeto cujas propriedades são os parâmetros validados.
     */
    protected function getObjDadosXml(){
        $arrVldParams   = $this->arrVldParams;
        $objDados       = new \stdClass();//Os parâmetros validados com sucesso são guardados em propriedades desse objeto.
        $nodeXml        = $this->getNodeXml();
        
        if (is_array($arrVldParams)) {
            $numItensNode = count(self::convertNode2Array($nodeXml));            
            if ($numItensNode > 0) {
                foreach($arrVldParams as $strItem) {
                    list($param,$type,$required,$length) = explode(':',$strItem);
                    $paramValue         = self::valueForAttrib($nodeXml,'id',$param);
                    $value              = $this->vldField($param, $paramValue, $required, $length, $type);
                    $objDados->$param   = $paramValue;
                }
            } else {
                throw new \Exception('O nó '.$this->nodeName.' está vazio.');
            }
        } else {
            throw new \Exception('Uma lista de parâmetros de validação não foi informada.');
        }
        $this->objDados = $objDados;
        return $objDados;
    }    
        
    /**
     * Verifica se o valor informado é válido.
     * 
     * @param string $param Nome ref. ao nome do nó recebido via XML.
     * @param integer $required Pode ser 0 ou 1 (valor obrigatório).
     * @param integer $length Se string, indica o limite de caracteres permitidos.
     * @param string $type Pode ser string, integer, float, email
     * @return mixed Valor informado caso nenhuma exceção seja disparada.
     * @throws Exception Caso o valor informado seja diferente de NULL e vazio, espera-se um integer, mas a coersão retorna zero.
     * @throws Exception Caso o valor informado seja obrigatório, espera-se um integer, mas a coersão retorna zero.
     * @throws Exception Caso o valor informado seja obrigatório, espera-se um float, mas a coersão retorna zero.
     * @throws Exception Caso o valor informado seja diferente de NULL e vazio, espera-se um formato de e-mail, mas a validação retorna FALSE.
     * @throws Exception Caso o valor informado seja obrigatório, espera-se um formato de e-mail, mas o valor recebido é vazio.
     */
    protected function vldField($param, $paramValue, $required, $length, $type='string'){
        $msgErr = '';
        if ($type == 'integer') {                    
            if (strlen($paramValue) > 0 && (int)$paramValue == 0) {
                //Valida o valor informado ou então localiza o próximo NUM_PEDIDO no DB.                                
                $msgErr = "O PARAM{{$param}} = {$paramValue} informado não é válido. Informe um valor numérico para {$param}.";
            } elseif ($required == 1 && (int)$paramValue <= 0) {
                $msgErr = "O PARAM{{$param}} obrigatório não foi informado ou é menor igual a zero.";
            }
            $paramValue = (int)$paramValue;                    
        } elseif ($type == 'float') {
            $paramValue = (float)$paramValue;                                                    
            if ($required == 1 && $paramValue <= 0) $msgErr = "O PARAM{{$param}}= {$paramValue} obrigatório não foi informado ou é menor igual a zero";
        } elseif ($type == 'email') {
            //Validação de e-mail:     
            if (strlen($paramValue) > 0) {
                if(!filter_var($paramValue, FILTER_VALIDATE_EMAIL)){ 
                    $msgErr = "O PARAM{{$param}} = '{$paramValue}' não parece ser um e-mail válido";
                }
            } elseif ($required == 1) {
                $msgErr = "O PARAM{{$param}} é obrigatório e não foi informado";
            }
        } else {
            if ($type == 'date' && strlen($paramValue) == 10) {
                @list($y,$m,$d) = @explode('-',$paramValue);
                if (!@checkdate($m,$d,$y)){                        
                    $msgErr = "O PARAM{{$param}} não parece ser uma data válida";
                }                    
            } elseif ($required == 1 && strlen($paramValue) == 0) {
                $msgErr = "O PARAM{{$param}} é obrigatório e não foi informado";
            }            
        }
        
        if (strlen($msgErr) > 0) throw new \Exception($msgErr);
        return $paramValue;      
    }
       
    function __get($var){
        $value      = '';
        $objDados   = $this->objDados;
        if (is_object($objDados)) {            
            if (isset($objDados->$var)) $value = $objDados->$var;
        }
        return $value;
    }
}

?>
