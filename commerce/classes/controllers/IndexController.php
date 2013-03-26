<?php
    
    namespace commerce\classes\controllers;
    
    use \commerce\classes\models\EcommModel;
    use \commerce\classes\helpers\XmlResponseHelper;
    use \commerce\classes\helpers\LoadXmlStatus;
    use \sys\classes\util\Request;   
    use \sys\classes\mvc as mvc; 
    use \commerce\classes\helpers as helpers;
    
    class IndexController extends mvc\ExceptionController {
        
        private $format         = 'XML';//Pode ser XML, JSON ou TEXT.
        
        private $codStatus      = 0;
        private $msgStatus      = 'Operação executada com sucesso.';
        private $objCfg         = NULL;    
        private $response       = NULL;
        protected $objPedido    = NULL;

        /**
         * Localiza via HASH os dados com as configurações de e-commerce do usuário informado.
         * 
         * Pode receber ainda, via get ou post, as seguintes variáveis:
         * 
         *  - uid = contém o hash que identifica o cliente, cujas configurações devem ser usadas.
         *  - format = contém o formato de retorno. Pode ser XML, JSON, TEXT.
         *  - ambiente = contém explicitamente o ambiente a ser usado na requisição. Pode ser TEST ou PROD.
         * 
         * @return \stdClass
         */
        protected function getDadosCfg(){
            $hash       = Request::post('uid');//HASH que identifica o usuário que solicitou a requisição.
            $hash       = 'b98af3c46666cb58b73677859074e116';
            
            $format     = strtoupper(Request::all('format','STRING'));
            $ambiente   = strtoupper(Request::all('ambiente','STRING'));
            
            if (strlen($format) > 0) $this->format = $format;   
            
            $objModel           = new EcommModel();
            $dadosCfg           = $objModel->findUserForHash($hash);             
            $objCfg             = NULL;
            
            if (is_array($dadosCfg) && count($dadosCfg) > 0) {     
                //Cria um objeto de dados com os valores de configuração:
                $objCfg = new \stdClass();                
                foreach($dadosCfg[0] as $var=>$value) {                    
                    $objCfg->$var = $value;
                }
                
                if (strlen($ambiente) > 0) {
                    $objCfg->AMBIENTE = $ambiente;
                }
                $objCfg->CEDENTE    = 'Colibri Informática Ltda';
                $this->objCfg       = $objCfg;
            } else {
                $this->setStatus('ERR_HASH');                             
            } 
            
            return $objCfg;
        }
        
        protected function setStatus($id){
            $nodeStatus = LoadXmlStatus::getId($id);
            $this->codStatus  = $nodeStatus->codigo;
            $this->msgStatus  = $nodeStatus->msg;                 
        }
        
        
        protected function addResponse($name,$value){
            if (strlen($name) > 0) $this->response[$name] = $value;
        }
        
        function getResponse(){
            return $this->response;
        }        

        /**
         * Imprime a resposta do método executado.
         * O formato de saída depende da variável $format enviada na requisição.
         * 
         * @return string O retorno pode ser XML, TEXT, JSON
         */
        protected function response(){
            $format     = $this->format;            
            $response   = 'INDEF';
            if ($format == 'JSON') {
                $response = $this->getJson();
            } elseif ($format == 'XML') {
                $response = $this->getXml();
            } elseif ($format == 'TEXT') {
                $response = $this->getText();
            }
            die($response);
        }
                
        private function getJson(){
            //Retorno em JSON
            $response               = $this->response;
            $out[0]['success']      = false;
            $out[0]['codStatus']    = $this->codStatus;
            $out[0]['msgStatus']    = $this->msgStatus;            
            $out[0]['data']         = '';
            
            if (is_array($response) && count($response) > 0) {
                $out[0]['success']  = true;
                $out[0]['data']     = $response;  
            }
            $outJson = json_encode($out);
            return $outJson;            
        }
        
        private function getXml(){
            $response       = $this->response;        
            $xml            = "<?xml version='1.0' encoding='UTF-8'?><ROOT>";
            $nodeParams     = '';                       
            if (is_array($response)){
                foreach($response as $key=>$value) {                    
                    $nodeParams .= "<PARAM><NAME>{$key}</NAME><VALUE>{$value}</VALUE></PARAM>";
                }                                                                               
            }            
            $xml .= "<STATUS><CODIGO>{$this->codStatus}</CODIGO><MSG>{$this->msgStatus}</MSG></STATUS>";
            $xml .= "<PARAMS>{$nodeParams}</PARAMS>";
            $xml .= "</ROOT>";                                        
            return $xml;
        }
        
        /**
         * Retorna a resposta no formato texto. 
         * As variáveis de retorno são separadas por ponto-e-vírgula e concatenadas no seguinte formato:
         * var1=value1;var2=value2;...
         * 
         * @return string
         */
        private function getText(){
            $response    = $this->response; 
            $codStatus   = $this->codStatus;
            $msgStatus   = $this->msgStatus;
            $string      = "codStatus={$codStatus};msgStatus={$msgStatus}";
            
            if (is_array($response)){
                foreach($response as $key=>$value) {   
                    $value  = str_replace(';',',',$value);
                    $string .= ";{$key}={$value}";
                }                                                                               
            }              
            return $string;
        }        
        
        protected function loadPedido($numPedido){                                    
            $objInfoPedido  = NULL;
            $objPedido      = new helpers\PedidoHelper($numPedido);   
            if (is_object($objPedido)) {
                if (is_object($objPedido->getObjInfo())) {
                    $this->objPedido    = $objPedido;
                } else {
                    
                }                
            }
            
            if (!is_object($this->objPedido)) {
                $this->setStatus('PEDIDO_NOT_FOUND');
                $this->response();
            }
            
            return $objPedido;
        }                
    }
?>
