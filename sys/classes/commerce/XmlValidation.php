<?php
namespace sys\classes\commerce;
use \sys\classes\util\Xml;
/**
 * Classe utilizada para validar os campos recebidos do XML.
 * 
 * @param string $nodeName 
 * Nome do nó que deve ser lido na classe-filha.
 * A classe-filha, ao estender a classe atual, deve obrigatóriamente definir a variável
 * $nodeName para informar explicitamente qual o nó que irá tratar.
 * 
 * @param SimpleXMLElement $nodeXml
 * Objeto recebido pelo construtor, que deve possuir todos os nós filhos esperados para
 * o nó tratado pela classe-filha (vide parâmetro $nodeName).
 * Os nós-filhos são definidos no atributo $arrVldParams.
 * 
 * @param stdClass $objDados
 * Objeto de dados que guarda um nó-filho com seu respectivo valor, após a validação 
 * dos dados recebidos pelo parâmetro $nodeXml, feita a partir dos dados de validação
 * da classe-filha contidos em $arrVldParams.
 * 
 * @param string[] $arrVldParams
 * Array de string onde cada índice refere-se a um nó-filho esperado em $nodeXml.
 * A classe-filha da classe atual deve definir explicitamente os seus parâmetros de
 * validação em $arrVldParams, como o exemplo a seguir:
 * 
 * protected $arrVldParams = array(            
 *      'NOME:string:1:100',
 *      'EMAIL:email:0:100',
 *      'ENDERECO:string:0:120',
 *      'NUMERO':integer:0:0,
 *      'CIDADE:string:0:100',
 *      'UF:string:0:2',
 *      'CPF_CNPJ:string:0:20',
 * ); 
 * 
 * Sendo que, para cada índice o formato da string deve ser:
 * NomeDoNóEsperado:formato:requerido:tamanho(length)
 * 
 * NomeDoNóEsperado: nome do nó propriamente dito (geralmente em caixa alta).
 * formato: pode ser string, integer, float, email ou date.
 * requerido: pode ser 0 (não obrigatório) ou 1 (obrigatório).
 * tamanho (length): se string ou email, valida o tamanho dos dados recebidos. Para
 * os outros tipos esse atributo pode ser zero, pois será ignorado.
 * 
 * @param boolean $required
 * Indica se o nó ($nodeName) tratado na classe-filha da classe atual precisa, 
 * obrigatóriamente, possuir dados. Por exemplo, uma opção de checkout em um
 * gateway de pagamento pode ser BOLETO ou CARTÃO. Portanto, ao checar o XML 
 * da requisição ambos deverão ser verificados e um deles estará vazio.
 * Este parâmetro é utilizado no método getObjDadosXml();
 * 
 * @author Claudio
 */
abstract class XmlValidation extends Xml {
    protected $nodeName     = 'Undefined';
    private $nodeXml        = NULL;
    private $objDados       = NULL; //Guarda um objeto stdClass contendo dados de um nó XML lido.
    protected $arrVldParams = array();
    protected $required     = TRUE;
    
    function __construct($nodeXml){
        $this->init();
        $this->nodeXml = $nodeXml;
        try {
           $this->getObjDadosXml();            
        } catch(\Exception $e) {
           throw $e;
        }        
    }
    
    protected function init(){
        /*
         * Este método deve ser implementado na classe-filha caso seja necessário
         * executar alguma ação adicional no construtor do objeto.
         */
        
    }


    /**
     * Define o nó da classe-filha como obrigatório.
     * Esta opção já está ativa por padrão.
     * 
     * @return void
     */
    protected function requiredOn(){        
        $this->required = TRUE;
    }
      
    /**
     * Define o nó da classe-filha como não requerido (opcional).
     * Significa que o nó ($nodeName) tratado pela classe-filha pode estar vazio.     
     * 
     * @return void
     */    
    protected function requiredOff(){
        $this->required = FALSE;
    }
    

    function getNodeXml(){
        return $this->nodeXml;
    }
    
 /**
     * Valida um conjunto de valores contidos no nó XML ($nodeXml) a partir 
     * de uma lista de critérios definidos em $arrVldParams na classe filha da classe atual.
     * 
     * @param string[] $arrParams Array unidimensional contendo string de parâmetros.
     * @return \stdClass Objeto cujas propriedades são os parâmetros validados.
     */
    protected function getObjDadosXml($nodeXml=NULL){
        $arrVldParams   = $this->arrVldParams;
        $objDados       = new \stdClass();//Os parâmetros validados com sucesso são guardados em propriedades desse objeto.
        $nodeXml        = (is_object($nodeXml)) ? $nodeXml : $this->getNodeXml();
        
        if (is_array($arrVldParams)) {
            $numItensNode = count(self::convertNode2Array($nodeXml));            
            if ($numItensNode > 0) {
                foreach($arrVldParams as $strItem) {
                    try {
                        list($param,$type,$required,$length) = explode(':',$strItem);
                        $paramValue         = self::valueForAttrib($nodeXml,'id',$param);
                        $value              = $this->vldField($param, $paramValue, $required, $length, $type);
                        $objDados->$param   = $paramValue;
                    } catch(\Exception $e) {
                        $msgErr = $e->getMessage();
                        $msgErr .= print_r($nodeXml,true);
                        throw new \Exception($msgErr);
                    }
                }
            } elseif ($this->required) {   
                //O nó é obrigatório, mas está vazio.
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
     * 
     * @throws Exception Caso o valor informado seja diferente de NULL e vazio, espera-se um integer, mas a coersão retorna zero.
     * @throws Exception Caso o valor informado seja obrigatório, espera-se um integer, mas a coersão retorna zero.
     * @throws Exception Caso o valor informado seja obrigatório, espera-se um float, mas a coersão retorna zero.
     * @throws Exception Caso o valor informado seja diferente de NULL e vazio, espera-se um formato de e-mail, mas a validação retorna FALSE.
     * @throws Exception Caso o valor informado seja obrigatório, espera-se um formato de e-mail, mas o valor recebido é vazio.
     */
    protected function vldField($param, $paramValue, $required, $length, $type='string'){
        $errDefault = "{$this->nodeName}: ";
        $msgErr     = $errDefault;        
        
        if ($type == 'integer') {                    
            if (strlen($paramValue) > 0 && (int)$paramValue == 0) {
                //Valida o valor informado ou então localiza o próximo NUM_PEDIDO no DB.                                
                $msgErr .= "O PARAM{{$param}} = {$paramValue} informado não é válido. Informe um valor numérico para {$param}.";
            } elseif ($required == 1 && (int)$paramValue <= 0) {
                $msgErr .= "O PARAM{{$param}} obrigatório não foi informado ou é menor igual a zero.";
            }
            $paramValue = (int)$paramValue;                    
        } elseif ($type == 'float') {
            $paramValue = (float)$paramValue;                                                    
            if ($required == 1 && $paramValue <= 0) $msgErr .= "O PARAM{{$param}}= {$paramValue} obrigatório não foi informado ou é menor igual a zero.";
        } elseif ($type == 'email') {
            //Validação de e-mail:     
            if (strlen($paramValue) > 0) {
                if(!filter_var($paramValue, FILTER_VALIDATE_EMAIL)){ 
                    $msgErr .= "O PARAM{{$param}} = '{$paramValue}' não parece ser um e-mail válido.";
                }
            } elseif ($required == 1) {
                $msgErr .= "O PARAM{{$param}} é obrigatório e não foi informado.";
            }
        } else {
            if ($type == 'date' && strlen($paramValue) == 10) {                
                @list($y,$m,$d) = @explode('-',$paramValue);
                if (!@checkdate($m,$d,$y)){                        
                    $msgErr .= "O PARAM{{$param}} não parece conter uma data válida => {$paramValue}.";
                }                    
            } elseif ($required == 1 && strlen($paramValue) == 0) {
                $msgErr .= "O PARAM{{$param}} é obrigatório e não foi informado. ";
            }            
        }
        
        if (strlen($msgErr) > 0 && $msgErr != $errDefault) throw new \Exception($msgErr);
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
