<?php
    use \sys\classes\webservice\WsServer;
    use \api\classes\models\CreditosModel;
    use \common\classes\models\UsuariosModel;
    use \app_escola\classes\models\EscolaModel;
    use \common\classes\helpers\Usuario;
    
    class Creditos extends WsServer {
        public function __construct() {
            try{      
                $this->setWsInterfaceClass(__CLASS__);   
            }catch(Exception $e){
                die(utf8_decode("<b>Erro Fatal:</b> " . $e->getMessage() . " - Entre em contato com suporte!"));
            }
        }
        
        /**
        * Método que calcula o Saldo atual de um usuário atravé do seu ID
        * 
        * @param string $xmlParams String XML com campos de entrada
        * @return string
        */
        function getSaldo($xmlParams){
            try{
                $erro   = 0;
                $msg    = "Consulta efetuada com sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token      = $this->getXmlField($params, 'token');
                        $idUsuario  = $this->getXmlField($params, 'id_usuario');

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
                                //Se não for enviado um id_usuario a consulta será feita para o usuário logado
                                $idUsuario = $idUsuario == null || $idUsuario == 0 ? $ret->ID_USER : $idUsuario;
                                
                                //Model de Usuários
                                $mdUsuarios = new UsuariosModel();
                                
                                //Calculo de saldo do usuário
                                $rs_saldo = $mdUsuarios->calcularSaldo($idUsuario, $ret->ID_USER);

                                //Verifica erros com saldo
                                if(!$rs_saldo->status){
                                    $erro   = 99;
                                    $msg    = $rs_saldo->msg;
                                }else{
                                    //XML de retorno do Saldo
                                    $dados = "<dados>";
                                        $dados .= "<saldo>";
                                            $dados .= $rs_saldo->saldo;
                                        $dados .= "</saldo>";
                                    $dados .= "</dados>";
                                }
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
            }
        }
        
        /**
         * Função que atribui créditos a um usuário dependente da escola logada no WS
         * 
         * @param string $xmlParams String XML com os campos de entrada
         * @return string
         */
        public function opCredito($xmlParams){
            try{
                $erro   = 0;
                $msg    = "Crédito inserido com sucesso!";
                $dados  = "";

                if($xmlParams){
                    $xml = new SimpleXMLElement($xmlParams);

                    if($xml){
                        //Array de parâmetros
                        $params = $xml->params->param;

                        //Campos utilizados
                        $token      = $this->getXmlField($params, 'token');
                        $idUsuario  = $this->getXmlField($params, 'id_usuario');
                        $credito    = (int)$this->getXmlField($params, 'credito');

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
                                //Valida envio do id_usuario
                                if($idUsuario <= 0 || $idUsuario == $ret->ID_USER){
                                    if($idUsuario <= 0){
                                        $erro   = 56;
                                        $msg    = "ID_USUARIO inválido ou nulo!";
                                    }else{
                                        $erro   = 57;
                                        $msg    = "Sem permissão para conceder créditos a si mesmo!";
                                    }
                                }else{
                                    //Model de usuários
                                    $mdUsuarios = new UsuariosModel();

                                    //Valida se o usuário é dependente do usuário logado
                                    if(!$mdUsuarios->validarUsuarioMatriz($idUsuario, $ret->ID_USER)){
                                        $erro   = 51;
                                        $msg    = "Usuário não é seu dependente ou não existe!";
                                    }else{
                                        //Verifica saldo da escola
                                        $userEscola = new Usuario();
                                        $userEscola->carregarUsuarioId($ret->ID_USER);
                                        $rs_saldo   = $userEscola->calcSaldo();
                                        
                                        //Verifica erros com saldo
                                        if(!$rs_saldo->status){
                                            $erro   = 52;
                                            $msg    = $rs_saldo->msg;
                                        }else if($rs_saldo->saldo <= 0){
                                            $erro   = 52;
                                            $msg    = "Você não possui saldo para distribuição!";
                                        }else if($credito <= 0){
                                            $erro   = 53;
                                            $msg    = "A quantidade de créditos deve ser maior que zero!";
                                        }else if($credito > $rs_saldo->saldo){
                                            $erro   = 54;
                                            $msg    = "A quantidade de créditos é maior do que seu saldo atual!";
                                        }else{
                                            //Model de Escolas
                                            $mdEscola   = new EscolaModel();
                                            $rsOperacao = $mdEscola->operacaoCredito($ret->ID_USER, $idUsuario, 1, $credito);
                                            
                                            if(!$rsOperacao->status){
                                                $erro   = 7;
                                                $msg    = $rsOperacao->msg;
                                            }
                                        } //Validações para inserção
                                    } //Verifica dependencia do usuário
                                } //Valida envio de id_usuario
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
         * Função que estorna créditos a um usuário dependente da escola logada no WS
         * 
         * @param string $xmlParams String XML com os campos de entrada
         * @return string
         */
        public function opEstorno($xmlParams){
           try{
               $erro   = 0;
               $msg    = "Crédito(s) estornado(s) com sucesso!";
               $dados  = "";

               if($xmlParams){
                   $xml = new SimpleXMLElement($xmlParams);

                   if($xml){
                       //Array de parâmetros
                       $params = $xml->params->param;

                       //Campos utilizados
                       $token      = $this->getXmlField($params, 'token');
                       $idUsuario  = $this->getXmlField($params, 'id_usuario');
                       $estorno    = (int)$this->getXmlField($params, 'estorno');

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
                               //Valida envio do id_usuario
                               if($idUsuario <= 0 || $idUsuario == $ret->ID_USER){
                                   if($idUsuario <= 0){
                                       $erro   = 61;
                                       $msg    = "ID_USUARIO inválido ou nulo!";
                                   }else{
                                       $erro   = 62;
                                       $msg    = "Sem permissão para estornar créditos de si mesmo!";
                                   }
                               }else{
                                   //Model de Usuários
                                   $mdUsuarios = new UsuariosModel();
                                   
                                   //Valida se o usuário é dependente do usuário logado
                                   if(!$mdUsuarios->validarUsuarioMatriz($idUsuario, $ret->ID_USER)){
                                        $erro   = 63;
                                        $msg    = "Usuário não é seu dependente ou não existe!";
                                   }else{
                                       //Verifica saldo da escola
                                       $userEscola = new Usuario();
                                       $userEscola->carregarUsuarioId($ret->ID_USER);
                                       $rs_saldo   = $userEscola->calcSaldo();
                                       
                                       //Verifica erros com saldo
                                       if(!$rs_saldo->status){
                                           $erro   = 64;
                                           $msg    = $rs_saldo->msg;
                                       }else if($rs_saldo->saldo <= 0){
                                           $erro   = 64;
                                           $msg    = "Você não possui saldo para estorno!";
                                       }else if($estorno <= 0){
                                           $erro   = 65;
                                           $msg    = "A quantidade de créditos para estorno deve ser maior que zero!";
                                       }else if($estorno > $rs_saldo->saldo){
                                           $erro   = 66;
                                           $msg    = "A quantidade de créditos para é maior do que o saldo atual do usuário!";
                                       }else{
                                           //Model de Escolas
                                           $mdEscola   = new EscolaModel();
                                           $rsOperacao = $mdEscola->operacaoCredito($ret->ID_USER, $idUsuario, 2, $estorno);
                                            
                                           if(!$rsOperacao->status){
                                               $erro   = 7;
                                               $msg    = $rsOperacao->msg;
                                           }
                                       } //Validações para inserção
                                   } //Verifica dependencia do usuário
                               } //Valida envio de id_usuario
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
               $ret .= "<msg>". $msg."</msg>";
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
