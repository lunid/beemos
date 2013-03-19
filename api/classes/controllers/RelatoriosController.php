<?php
    use \sys\classes\webservice\WsServer;
    use \api\classes\Util;
    use \api\classes\models\RelatoriosModel;
    
    class Relatorios extends WsServer {
        public function __construct() {
            try{      
                $this->setWsInterfaceClass(__CLASS__);   
            }catch(Exception $e){
                die(utf8_decode("<b>Erro Fatal:</b> " . $e->getMessage() . " - Entre em contato com suporte!"));
            }
        }
        
        /**
         * Função que lista os pedidos do cliente logado dentro de um determinado período
         * 
         * @param string $xmlParams XML de parâmetros
         * @return string
         */
        function pedidos($xmlParams){
            try{
                $erro   = 0;
                $msg    = "Pedidos listados com sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token      = $this->getXmlField($params, 'token');
                        $dataIni    = $this->getXmlField($params, 'dataIni');
                        $dataFim    = $this->getXmlField($params, 'dataFim');

                        if(!isset($token) || $token == null || $token == ""){
                            $erro   = 4;
                            $msg    = "Token inválido!";
                        }else{
                            //Autentica usuário e token
                            $ret = $this->authenticate($token);

                            if(!$ret->status){
                                $erro   = $ret->erro;
                                $msg    = $ret->msg;
                            }else{
                                //Se a data de inicio não for definida. O sistema assume cinco dias atrás
                                $dataIni = $dataIni == null ? date("Y-m-d", mktime(date('H'), date('i'), date('s'), date('m'), (date('d')-5), date('Y'))) : mysql_escape_string($dataIni);

                                //Se a data de fim não for definida, o sistema assume a data atual
                                $dataFim = $dataFim == null ? date("Y-m-d") : mysql_escape_string($dataFim);
                                
                                //Model de relatórios
                                $mdRelatorios = new RelatoriosModel();
                                
                                //Array de Filtros
                                $arrWhere['NUM_PEDIDO']     = " > 0 ";
                                $arrWhere['OPERACAO']       = " = 'C' ";
                                $arrWhere['DATA_REGISTRO']  = " BETWEEN '$dataIni' AND '$dataFim' ";
                                
                                //Executa SQL
                                $rs = $mdRelatorios->consultarPedidosMatriz($ret->ID_USER, $arrWhere);

                                //Valida se houve retorno
                                if(!$rs->status){
                                    $erro   = 101;
                                    $msg    = "Nenhum pedido encontrado!";
                                }else{
                                    $dados = "<dados>";

                                    foreach($rs->pedidos as $row){
                                        $dados .= "<pedido>";
                                            $dados .= "<numPedido>";
                                                $dados .= $row->NUM_PEDIDO;
                                            $dados .= "</numPedido>";
                                            $dados .= "<credito>";
                                                $dados .= $row->CREDITO ;
                                            $dados .= "</credito>";
                                            $dados .= "<saldoFinal>";
                                                $dados .= $row->SALDO_FINAL ;
                                            $dados .= "</saldoFinal>";
                                            $dados .= "<dataVencimento>";
                                                $dados .= Util::formataData($row->VENCIMENTO);
                                            $dados .= "</dataVencimento>";
                                            $dados .= "<dataRegistro>";
                                                $dados .= Util::formataData($row->DATA_REGISTRO, 'DD/MM/AAAA HH:MM:SS');
                                            $dados .= "</dataRegistro>";
                                        $dados .= "</pedido>";
                                    }
                                    $dados .= "</dados>";
                                } //Valida se houve retorno
                            }
                        }
                    }else{
                        $erro   = 6;
                        $msg    = "XML de entrada fora do padrão!";
                    }
                }else{
                    $erro   = 5;
                    $msg    = "XML Params inválido ou nulo!";
                }

                //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>". $msg ."</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";

                return $ret;
            }catch(Exception $e){
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>1</erro>";
                $ret .= "<msg>". $e->getMessage() ."</msg>";
                $ret .= "</status>";
                $ret .= "</root>";

                return $ret;

                //return new soap_fault("Server", null, $e->getMessage());
            }
        }
        
        /**
         * Função que lista os pedidos do cliente logado dentro de um determinado período
         * 
         * @param string $xmlParams XML de parâmetros
         * @return string
         */
        function detalhaPedidos($xmlParams){
            try{
                $erro   = 0;
                $msg    = "Detalhe(s) do(s) pedido(s) listado(s) com  sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token      = $this->getXmlField($params, 'token');
                        $dtInicio   = $this->getXmlField($params, 'dtInicio');
                        $dtFim      = $this->getXmlField($params, 'dtFim');

                        if(!isset($token) || $token == null || $token == ""){
                            $erro   = 4;
                            $msg    = "Token inválido!";
                        }else{
                            //Autentica usuário e token
                            $ret = $this->authenticate($token);

                            if(!$ret->status){
                                $erro   = $ret->erro;
                                $msg    = $ret->msg;
                            }else{
                                $pedidos = explode(",", $pedidos);

                                //Verifica se foi encotrado algum pedido
                                if(is_array($pedidos) && sizeof($pedidos) > 0){
                                    $tmpPedidos = "";

                                    foreach($pedidos as $pedido){
                                        if($tmpPedidos != ""){
                                            $tmpPedidos .= ", ";
                                        }

                                        $tmpPedidos .= (int)$pedido;
                                    }
                                    
                                    //Model de relatórios
                                    $mdRelatorios = new RelatoriosModel();

                                    //Array de Filtros
                                    $arrWhere['']                       = " (NUM_PEDIDO > 0 OR BONUS = 1) ";
                                    $arrWhere['OPERACAO']               = " = 'C' ";
                                    $arrWhere['DATE(DATA_REGISTRO)']    = " BETWEEN '$dtInicio' AND '$dtFim' ";

                                    //Executa SQL
                                    $rs = $mdRelatorios->consultarPedidosMatriz($ret->ID_USER, $arrWhere);
                                    
                                    //Verifica retorno
                                    if($rs->status){
                                        $dados = "<dados>";
                                        
                                        for ($i = 0; $i < $rs->count; $i++) {
                                            //Captura resultado da Query
                                            $tmpNumPedido       = $rs->pedidos[$i]->NUM_PEDIDO;
                                            $tmpCredito         = $rs->pedidos[$i]->CREDITO;
                                            $tmpSaldoAnt        = $rs->pedidos[$i]->SALDO_ANT;
                                            $tmpSaldoFinal      = $rs->pedidos[$i]->SALDO_FINAL;
                                            $tmpVencimento      = $rs->pedidos[$i]->VENCIMENTO;
                                            $tmpDataRegistro    = $rs->pedidos[$i]->DATA_REGISTRO;


                                            //Variável que armazena Where da instrução
                                            $arrWhere = array();

                                            /*
                                             * Se existir casa acima de I, a mesma
                                             * é transformada em objeto para utilização
                                             */
                                            if($i < ($rs->count-1)){
                                                $nextDataRegistro = $rs->pedidos[$i+1]->DATA_REGISTRO;
                                                
                                                $arrWhere['CC.DATA_REGISTRO'] = " BETWEEN '{$tmpDataRegistro}' AND '{$nextDataRegistro}' ";
                                            }else{
                                                //Verifica se existe um pedido maior que o atual (sem estar no Array Result)
                                                $rsData = $mdRelatorios->consultarPedidoSuperior($ret->ID_USER, $tmpDataRegistro);
                                                
                                                //Caso encontre uma data maior, a mesma será a data limite
                                                if($rsData->status){
                                                    $arrWhere['CC.DATA_REGISTRO'] = " BETWEEN '{$tmpDataRegistro}' AND '{$rsData->pedido->DATA_REGISTRO}' ";
                                                }else{
                                                    //Caso contrário não existirá data limite
                                                    $arrWhere['CC.DATA_REGISTRO'] = " >= '{$tmpDataRegistro}' ";
                                                }
                                            }

                                            $dados .= "<pedido>";
                                                $dados .= "<numPedido>" . $tmpNumPedido . "</numPedido>";
                                                $dados .= "<credito>" . $tmpCredito . "</credito>";
                                                $dados .= "<saldoAnt>" . $tmpSaldoAnt . "</saldoAnt>";
                                                $dados .= "<saldoFinal>" . $tmpSaldoFinal . "</saldoFinal>";
                                                $dados .= "<dataVencimento>" . Util::formataData($tmpVencimento) . "</dataVencimento>";
                                                $dados .= "<dataRegistro>" . Util::formataData($tmpDataRegistro, 'DD/MM/AAAA HH:MM:SS') . "</dataRegistro>";
                                                
                                                $rsLancamentos = $mdRelatorios->consultarLancamentosMatriz($ret->ID_USER, $arrWhere);
                                                
                                                if($rsLancamentos->status){
                                                    $dados .= "<lancamentos>";
                                                    
                                                    foreach($rsLancamentos->lancamentos as $lancamento){
                                                        $dados .= "<lancamento>";
                                                            $dados .= "<idCliente>" . $lancamento['ID_CLIENTE'] . "</idCliente>";
                                                            $dados .= "<nome><![CDATA[" . utf8_encode($lancamento['NOME_PRINCIPAL']) . "]]></nome>";
                                                            $dados .= "<operacao>" . $lancamento['OPERACAO'] . "</operacao>";
                                                            $dados .= "<credito>" . $lancamento['CREDITO'] . "</credito>";
                                                            $dados .= "<dtRegistro>" . Util::formataData($lancamento['DATA_REGISTRO'], 'DD/MM/AAAA HH:MM:SS') . "</dtRegistro>";
                                                        $dados .= "</lancamento>";
                                                    }
                                                    
                                                    $dados .= "</lancamentos>";
                                                }

                                            $dados .= "</pedido>";
                                        } 

                                        $dados .= "</dados>";                                        
                                    }else{
                                        $erro   = 112;
                                        $msg    = "Nenhum pedido encontrado!";
                                    } //Verifica retorno
                                }else{
                                    $erro   = 111;
                                    $msg    = "Nenhum número de pedido encontrado!";
                                } //Verifica se existem números de pedidos
                            }
                        }
                    }else{
                        $erro   = 6;
                        $msg    = "XML de entrada fora do padrão!";
                    }
                }else{
                    $erro   = 5;
                    $msg    = "XML Params inválido ou nulo!";
                }

                //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>{$erro}</erro>";
                $ret .= "<msg>". $msg ."</msg>";
                $ret .= "</status>";
                $ret .= $dados;
                $ret .= "</root>";

                return $ret;
            }catch(Exception $e){
                $ret = "<root>";
                $ret .= "<status>";
                $ret .= "<erro>1</erro>";
                $ret .= "<msg>". $e->getMessage() ."</msg>";
                $ret .= "</status>";
                $ret .= "</root>";

                return $ret;

                //return new soap_fault("Server", null, $e->getMessage());
            }
        }
    }
?>
