<?php

    namespace commerce\classes\helpers;
    use \sys\classes\util\Request;
    use sys\classes\util\CorrigeAcento;
    
    /**
    * CONVÊNIO BRADESCO:
    *
    * AMBIENTE DE TESTES:
    *	- http://mupteste.comercioeletronico.com.br/sepsmanager/senha.asp?loja=xxxx
    *	- LOGIN: adm_wo751, SENHA: marcbits3001
    *
    * AMBIENTE DE PRODUÇÃO:
    *	- https://mup.comercioeletronico.com.br/sepsmanager/senha.asp?loja=004523016
    *	- LOGIN: adm_col3016, SENHA: ADvw031211Ug
    *
    * FONE/SUPORTE: (11) 3909-3482 OU (11) 3909-3637
    * OBS: A documentação só está acessível n ambiente de teste.
    */    
    class BradescoHelper {
        
        private $loja;
        private $agencia;
        private $cc;
        private $orderId; //até 27 caracteres alfanuméricos.
        protected $numPedido; //até 9 caracteres numéricos.
        protected $objCfg; //Objeto contendo as configurações contidas no DB.
        protected $ambiente;
        private $arrItensPedido = array();
        private $arrDadosSacado = array();        
        private $xmlItensPedido;
        private $xmlValorAdicional;
        private $objPedido = NULL;
        private $assinatura;
        private $valorFormatoBr;
        private $valorSemSeparadores;        
        
        function __construct($objCfg,$orderId){
            
            $this->ambiente     = $objCfg->AMBIENTE_ATIVO;//TEST ou PROD        
            $this->objCfg       = $objCfg;
            
            $agencia            = $objCfg->AGENCIA;
            $cc                 = $objCfg->CONTA_CORRENTE;            
            $loja               = $objCfg->LOJA_PROD;
            $this->orderId      = $orderId;//alfanumérico

            if ($this->ambiente == 'TEST') {
                $cc             = '1234567';//TESTE: Valor não se altera independentemente do cliente
                $agencia        = '0001';//TESTE: Valor não se altera independentemente do cliente
                $loja           = $objCfg->LOJA_TEST;                
            }                        
            
            $this->loja = $loja;
            
            $this->setCc($cc);
            $this->setAgencia($agencia);
            $this->setNumPedido();                
        }              
        
        /**
         * Define o campo numPedido. Recomendável usar sempre o mesmo valor de orderId;
         * 
         * @return void
         */
        private function setNumPedido(){
            $numPedido       = Request::all('numPedido','NUMBER');
            $numPedido       = 0;//Request desabilitado. Não aceitar valor diferente de orderId.
            $orderId         = $this->orderId;
            $this->numPedido = $this->setNumeric($orderId,$numPedido);
        }

        /**
         * Não pode conter duplicidade e deve conter no máximo 9 caracteres numéricos.
         * Usado para validar o campo numPedido e numDoc.
         * 
         * @param integer $valorPrincipal Valor que tem prioridade se informado corretamente.
         * @param integer $valorAlt Valor alternativo caso o valorPrincipal não seja informado corretamente.
         *          
         * @return integer
         */
        protected function setNumeric($valorAlt,$valorPrincipal=0){               
            $limChar            = 9;
            $value              = $valorAlt;
            $out                = $valorAlt;
                       
            //Verifica se numPedido possui apenas dígitos numéricos e é menor ou igual ao limite permitido.
            if ($valorPrincipal > 0 && strlen($valorPrincipal) <= $limChar && ctype_digit($valorPrincipal)) {
                $value = $valorPrincipal;
            }
  
            if (ctype_digit($value) && strlen($value) <= $limChar) {
                 //Tudo ok. Valor dentro das regras de 9 caracteres numéricos.
                $out = LengthHelper::format($value,$limChar);         
            } else {
                throw new \Exception('Número do pedido inválido. Informe um número de pedido que contenha apenas caracteres numéricos e com até 9 caracteres.');
            }
            return $out;
        }  
        
        private function setAgencia($agencia){
           $this->agencia = LengthHelper::format($agencia,4);             
        }
        
        private function setCc($cc){
            $this->cc = LengthHelper::format($cc,7);
        }

        
        function setAmbienteTest(){
            $this->setAmbiente('TEST');
        }
        
        function setAmbienteProd(){
            $this->setAmbiente('PROD');
        }
        
        private function setAmbiente($ambiente){
            $this->ambiente = $ambiente;
        }
        
        protected function setAssinatura($assinatura){
            $this->assinatura = $assinatura;
        }
        
        /**
         * Retorna o link para a geração do boleto ou transferência on-line.
         * 
         * @return string Url de pagamento configurada com os parâmetros de consulta.
         */
        function getUrlPagamento(){
            $numPedido = $this->numPedido;
            $urlPgto = $this->getUrlTest();

            if ($this->ambiente == 'PROD') {
                //Ambiente de produção
                $urlPgto = $this->getUrlProd();                
            }
            $urlPgto .= "{$this->tipoTrans}/{$this->loja}/prepara_pagto.asp?merchantid={$this->loja}&orderid={$numPedido}";
            return $urlPgto;
        }
        
        private function getUrlTest(){
            $urlPgto = "http://mupteste.comercioeletronico.com.br/";
            return $urlPgto;
        }
        
        private function getUrlProd(){
            $urlPgto = "https://mup.comercioeletronico.com.br";
            return $urlPgto;            
        }
        
        /**
         * Método chamado para gerar o XML de notificação para o MUP BRADESCO.
         * 
         * @param PedidoHelper $objPedido Objeto com os dados do pedido informado.
         * @return string Xml de retorno após processar os valores do pedido.
         */
        function setXmlNotif($objPedido){
            $xmlNotif = '';
            if (is_object($objPedido)) {
                $this->objPedido = $objPedido;
                $this->setXmlItensPedido($objPedido->getItens());
                $this->arrDadosSacado   = $objPedido->getDadosSacado();
                $objInfo                = $objPedido->getObjInfo();
                
                //Valor da compra:
                $valorTotal                 = $objInfo->VALOR_TOTAL;
                $arrValor                   = $this->setValor($valorTotal);
                $this->valorFormatoBr       = $arrValor['VALOR_BR'];
                $this->valorSemSeparadores  = $arrValor['VALOR_SEM_FORMAT'];

                $xmlNotif = $this->getXmlNotif();
            }
            return $xmlNotif;
        }
        
        /**
         * Recebe o valor total da compra e gera o mesmo valor com e sem formatação.
         * 
         * @param float $valor Valor decimal referente ao valor total da compra.
         * @return String[] Array com duas posições, sendo uma 'VALOR_BR' e outra 'VALOR_SEM_FORMAT'.
         */
        function setValor($valor){
            $valor              = str_replace(',','.',$valor);//Troca vírgula por ponto se necessário.
            $valorFormatoBr     = number_format($valor,'2',',','');//Formata o valor com duas casas decimais.
            
            $valorSemSeparadores = str_replace(',','',$valorFormatoBr);
            $valorSemSeparadores = str_replace('.','',$valorSemSeparadores);                         
            
            $arrValor['VALOR_BR']            = $valorFormatoBr;
            $arrValor['VALOR_SEM_FORMAT']    = $valorSemSeparadores;
            
            return $arrValor;
        }
        
        function setXmlItensPedido($arrItensPedido){
            $xmlItensPedido = '';
            $objParse       = new CorrigeAcento();  
            
            if (is_array($arrItensPedido)) {
                
                foreach($arrItensPedido as $row) {
                    $validadeCreditos   = (int)$row['VALIDADE_CREDITOS'];
                    $sufixoValidade     = ($validadeCreditos == 1)?'mês':'meses';
                    
                    $quantidade         = trim($row['QUANTIDADE']);
                    $valor              = trim($row['VALOR']);//Quantidade * valor unitário do produto. Não deve conter separadores.                    
                    $produto            = trim($row['DESCR_PRODUTO']);
                    $unidade            = trim($row['UNIDADE']);
                    
                    //Se nenhuma unidade for informada, utilizar o valor CDTS:
                    if (strlen($unidade) == 0) $unidade = 'CDTS';
                   
                    //Retira parênteses do descritivo do produto, se houver:
                    $produto            = str_replace('(',' ',$produto);
                    $produto            = str_replace(')',' ',$produto);                                                            
                    $descritivo         = "$produto VLD {$validadeCreditos} {$sufixoValidade}";
                    
                    $descritivo         = $objParse->mixed_to_utf8($descritivo);
                    
                    //Retira separadores do valor, se houver:
                    $arrValor   = $this->setValor($valor);

                    $xmlItensPedido .= "<descritivo>=({$descritivo})\n";
                    $xmlItensPedido .= "<quantidade>=({$quantidade})\n";
                    $xmlItensPedido .= "<unidade>=({$unidade})\n";
                    $xmlItensPedido .= "<valor>=({$arrValor['VALOR_SEM_FORMAT']})";                    
                }
            }            
            
            $this->xmlItensPedido = $xmlItensPedido;
        }
        
        private function getXmlNotif(){
            $objCfg         = $this->objCfg;
            $arrDadosSacado = $this->arrDadosSacado;
            $objParse       = new CorrigeAcento();     
            $objModule      = new \Module();
            $tplXml         = $objModule->viewPartsLangFile('bradescoNotificacao.txt');
            $pathXml        = \Url::physicalPath($tplXml);
            $agora          = date('d/m/Y');
            $xmlInstrucao   = '';
            
            $nomeSacado     = '';
            $enderecoSacado = '';
            $cidadeSacado   = '';
            $ufSacado       = '';
            $cepSacado      = '';
            $cpfCnpjSacado  = '';
            
            //Encontra a data de vencimento do boleto a partir da quantidade de dias configurada no DB:
            $diasVenc   = (int)$objCfg->DIAS_VENC_BOLETO;
            $dataVencBr = date('d/m/Y', strtotime("+{$diasVenc} days"));

            //Monta as tags de instrução. Preenchimento opcional.
            //Se uma ou mais instrução for informada, sobrepõe as mensagens previamente cadastradas no MUP Bradesco.
            for($i=1,$c=1;$i<=12;$i++){
                $var = 'INSTRUCAO_'.$i;
                if (isset($objCfg->$var)) {
                    if (strlen($objCfg->$var) > 0) {
                        $instrucao      = $objCfg->$var;
                        $instrucao      = $this->vldXmlValue($instrucao); 
                        $instrucao      = $objParse->mixed_to_utf8($instrucao);                        
                        $xmlInstrucao   .= "<INSTRUCAO{$c}>=({$instrucao})\n";   
                        $c++;
                    }
                }
            }                        
            
            if (is_array($arrDadosSacado)) {
                $nomeSacado     = $this->vldXmlValue($arrDadosSacado['NOME']);
                $logradouro     = $this->vldXmlValue($arrDadosSacado['LOGRADOURO']);
                $numero         = $this->vldXmlValue($arrDadosSacado['NUMERO']);
                $bairro         = $this->vldXmlValue($arrDadosSacado['BAIRRO']);
                $complemento    = $this->vldXmlValue($arrDadosSacado['COMPLEMENTO']);
                $cidadeSacado   = $this->vldXmlValue($arrDadosSacado['CIDADE']);
                $ufSacado       = $this->vldXmlValue($arrDadosSacado['UF']);                 
                $cepSacado      = $this->vldNumOnly($arrDadosSacado['COD_POSTAL']);                                 
                $cpfCnpjSacado  = $this->vldNumOnly($arrDadosSacado['CPF_CNPJ']);//Apenas números.            
                                
                if (strlen($logradouro) > 0) {
                    $enderecoSacado = "$logradouro $numero $complemento $bairro";
                    $enderecoSacado = trim($enderecoSacado);
                    
                    $enderecoSacado = $objParse->mixed_to_utf8($enderecoSacado);
                }
                
                $cidadeSacado = $objParse->mixed_to_utf8($cidadeSacado);
            }            
            
            //Validação dos dados do sacado:
            //========================================================================
            $msgErr = array();
            if (strlen($nomeSacado) == 0) {
                $msgErr[] = "O nome do sacado não foi informado.";                
            }
            
            if (strlen($enderecoSacado) == 0) {
                $msgErr[] = "O endereco do sacado não foi informado.";                
            }
            
            if (strlen($cidadeSacado) == 0) {
                $msgErr[] = "A cidade do sacado não foi informada.";                
            }       
            
            if (strlen($ufSacado) != 2) {
                $msgErr[] = "O estado (UF) do sacado não foi informado.";                
            }
            
            if (strlen($cepSacado) == 0) {
                $msgErr[] = "O CEP do sacado não foi informado.";                
            }                   

            if (strlen($cpfCnpjSacado) != 11 && strlen($cpfCnpjSacado) != 14) {
                $msgErr[] = "O CPF/CNPJ do sacado não foi informado.";                
            }                     
            //========================================================================
            
            if (count($msgErr) > 0) {
                $strErr = join('<br>',$msgErr);
                die($strErr);
            } 
            
            if (file_exists($pathXml)) {
                $stringXml = file_get_contents($pathXml);
                $arrParams = array(
                    'ORDER_ID'                  => $this->orderId,
                    'ITENS_PEDIDO'              => $this->xmlItensPedido,
                    'VALOR_ADICIONAL'           => $this->xmlValorAdicional,
                    'CEDENTE'                   => $objParse->mixed_to_utf8($objCfg->CEDENTE),
                    'NUMEROAGENCIA'             => $this->agencia,
                    'NUMEROCONTA'               => $this->cc,
                    'ASSINATURA'                => $this->assinatura,
                    'DATAEMISSAO'               => $agora,//dd/mm/aaaa
                    'DATAPROCESSAMENTO'         => $agora,
                    'DATAVENCIMENTO'            => $dataVencBr,
                    'NOMESACADO'                => $nomeSacado,
                    'ENDERECOSACADO'            => $enderecoSacado,
                    'CIDADESACADO'              => $cidadeSacado,
                    'UFSACADO'                  => $ufSacado,
                    'CEPSACADO'                 => $cepSacado,
                    'CPFSACADO'                 => $cpfCnpjSacado,
                    'NUMEROPEDIDO'              => $this->numPedido,
                    'VALORDOCUMENTOFORMATADO'   => 'R$'.$this->valorFormatoBr,//Ex.: R$ 30,40.
                    'SHOPPING_ID'               => (int)$objCfg->SHOPPING_ID,// Pode ser 1 se a loja for participante do portal de compras ShopFácil, ou 0 caso contrário.
                    'NUMDOC'                    => $this->numDoc,//Campo opcional. Máximo 9 caracteres numéricos.
                    'CARTEIRA'                  => (int)$objCfg->CARTEIRA,//Default é 25.
                    'ANONOSSONUMERO'            => (int)$objCfg->ANO_NOSSO_NUMERO,//Default é 97.
                    'CIP'                       => (int)$objCfg->CIP,//Opcional. Valor default é 865.
                    'INSTRUCAO'                 => $xmlInstrucao                    
                );
                
                foreach($arrParams as $key=>$value){
                    $var        = "{{$key}}";
                    $stringXml  = str_replace($var, $value, $stringXml);
                }
                
                $arrCamposOpcionais = array('ANONOSSONUMERO:','CARTEIRA','NUMDOC','CIP');
                
                return $stringXml;
            } else {
                throw new \Exception('Erro ao criar notificação XML solicitada pelo MUP/Bradesco.');
            }
        }        
        
        private function vldXmlValue($value) {
            $value      = trim($value);
            $value      = str_replace('(',' ',$value);
            $value      = str_replace(')',' ',$value); 
            return $value;
        }
        
        /**
         * Recebe uma string e retira qualquer caractere que não seja número.
         * Exemplo: Recebe CPF com separadores e retorna apenas números.
         * 
         * @param String $string
         * @return String
         */
        private function vldNumOnly($string){
            $arrString  = str_split($string);
            $arrChar    = array();
            
            foreach($arrString as $char){
                if (ctype_digit($char)) $arrChar[] = $char;
            }
            $stringOut = join('',$arrChar);
            return $stringOut;
        }
    }

?>
