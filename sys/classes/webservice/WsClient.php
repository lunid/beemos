<?php
/**
 * Classe de abstração das chamadas PHP ao WebService
 *
 * @property constant url Url de acesso ao serviço WSDL
 * @property constant httpUser Usuário de acesso HTTP -Basic
 * @property constant httpPass Senha de acesso HTTP - Basic
 * 
 * @property nusoap_client $client Objeto de Cliente SOAP 
 * 
 * @author Marcelo Pacheco - Interbits 2012 - Ultima Atualização em 24/10/2012
 */
class WsClient {
    const url           = "http://www.interbits.provisorio.ws/interbits/api/";
    const httpUser      = "marcelo";
    const httpPass      = "teste";
    
    private $client;
    
    public function __construct($interfaceWs) {
        try{
            //Opções de inicialização do WebService
            $options = array(
                'location'  => self::url . "{$interfaceWs}/",
                'uri'       => self::url,
                'encoding'  => "utf-8",
                'trace'     => 1,
                "stream_context" => stream_context_create(
                    array(
                        //Usuário para autenticação HTTP
                        "http" => array("header" => "Authorization: Basic " . base64_encode(self::httpUser .":". md5(self::httpPass)))
                    )
                )
            );

            //Inicia serviço SOAP
            $this->client = new \SoapClient(null, $options);
            
            //Verifica erros no serviço
            if(is_soap_fault($this->client)){
                throw new Exception($this->client);
            }
        }catch(Exception $e){
            $erro = (isset($e->faultcode))?$e->faultstring:$e->getMessage();

            $msg = "Erro - " . $erro . "<br /><br />";
            $msg .= "Resposta Servidor: <br />";
            throw(new \Exception($msg));
        }
    }       
    
    /**
     * Função que efetua chamada do método novoUsuario no Webservice
     * 
     * @param string $nome
     * @param string $email
     * @param string $login
     * @param string $senha
     * 
     * @return \stdClass
     * @throws Exception
     */
    public function callNovoUsuario($nome, $email, $login, $senha){
        try{
            //Obejto de retorno
            $ret            = new \stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao criar um novo usuário!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='nome'><![CDATA[".$nome."]]></param>";
            $xmlParams .= "<param id='email'><![CDATA[".$email."]]></param>";
            $xmlParams .= "<param id='login'><![CDATA[".$login."]]></param>";
            $xmlParams .= "<param id='passwd_md5'><![CDATA[".(trim($senha) != '' ? md5($senha) : '')."]]></param>";
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";

            //Chamada do método ao WS
            $rs = $this->client->novoUsuario($xmlParams);

            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função NovoUsuario - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status     = (int)$xml->status->erro;
                    $ret->erro  = $status;
                    $ret->msg   = (string)$xml->status->msg;
                    
                    /*
                     * Se o retorno for 0 - Consuta gerada com sucesso
                     * Será lida a TAG <dados> para recebimento de informações 
                     * conforme documentação do método NovoUsuario
                     */
                    if($status == 0){
                        $ret->status        = true;
                        $ret->erro          = $status;
                        $ret->id_usuario    = (string)$xml->dados->id_usuario;
                    }
                }else{
                    $ret->msg = "Função NovoUsuario - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    /**
     * Função que efetua a chamada do método atualizaUsuario no Webservice
     * 
     * @param int $id_usuario
     * @param string $nome
     * @param string $email
     * @param string $login
     * @param string $senha
     * 
     * @return \stdClass
     * @throws Exception
     */
    public function callAtualizaUsuario($id_usuario, $nome = null, $email = null, $login = null, $senha = null){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao atualiza usuário!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='id_usuario'>{$id_usuario}</param>";
            $nome != null && trim($nome) != '' ? $xmlParams .= "<param id='nome'><![CDATA[".$nome."]]></param>" : null;
            $email != null && trim($email) != '' ? $xmlParams .= "<param id='email'><![CDATA[".$email."]]></param>" : null;
            $login != null && trim($login) != '' ? $xmlParams .= "<param id='login'><![CDATA[".$login."]]></param>" : null;
            $senha != null && trim($senha) != '' ? $xmlParams .= "<param id='passwd_md5'><![CDATA[".(md5($senha))."]]></param>" : null;
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";

            //Chamada do método ao WS
            $rs = $this->client->atualizaUsuario($xmlParams);

            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função AtualizaUsuario - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status     = (int)$xml->status->erro;
                    $ret->erro  = $status;
                    $ret->msg   = (string)$xml->status->msg;
                    
                    /*
                     * Se o retorno for 0 - Consuta gerada com sucesso
                     * Será lida a TAG <dados> para recebimento de informações 
                     * conforme documentação do método AtualizaUsuario
                     */
                    if($status == 0){
                        $ret->status        = true;
                        $ret->erro          = $status;
                        $ret->id_usuario    = (string)$xml->dados->id_usuario;
                    }
                }else{
                    $ret->msg = "Função AtualizaUsuario - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    /**
     * Função que efetua a chamada do método listaUsuários no Webservice
     * 
     * @param string $filtro
     * @param string $dataIni YYYY-mm-dd
     * @param string $dataFim YYYY-mm-dd
     * 
     * @return \stdClass
     * @throws Exception
     */
    public function callListaUsuarios($filtro = null, $dataIni = null, $dataFim = null){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao listar usuários!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $filtro != null && trim($filtro) != '' ? $xmlParams .= "<param id='filtro'><![CDATA[" . $filtro . "]]></param>" : null;
            $dataIni != null && trim($dataIni) != '' ? $xmlParams .= "<param id='dataIni'>" . $dataIni . "</param>" : null;
            $dataFim != null && trim($dataFim) != '' ? $xmlParams .= "<param id='dataFim'>" . $dataFim . "</param>" : null;
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->listaUsuarios($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função ListaUsuarios - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status     = (int)$xml->status->erro;
                    $ret->erro  = $status;
                    $ret->msg   = (string)$xml->status->msg;
                    
                    /*
                     * Se o retorno for 0 - Consuta gerada com sucesso
                     * Será lida a TAG <dados> para recebimento de informações 
                     * conforme documentação do método ListaUsuario
                     */
                    if($status == 0){
                        $ret->status        = true;
                        $ret->erro          = $status;
                        $ret->usuarios      = $xml->dados->usuario; //Array com usuários encontrados
                    }
                }else{
                    $ret->msg = "Função ListaUsuarios - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    /**
     * Função que realiza chamada do método Pedidos no WS
     * 
     * @param string $dataIni Data de ínicio da consulta
     * @param string $dataFim Data final da consulta
     * 
     * @return \stdClass
     * @throws Exception
     */
    public function callPedidos($dataIni = null, $dataFim = null){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao consultar pedidos!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $dataIni != null && trim($dataIni) != '' ? $xmlParams .= "<param id='dataIni'>" . $dataIni . "</param>" : null;
            $dataFim != null && trim($dataFim) != '' ? $xmlParams .= "<param id='dataFim'>" . $dataFim . "</param>" : null;
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->pedidos($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função Pedidos - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status     = (int)$xml->status->erro;
                    $ret->erro  = $status;
                    $ret->msg   = (string)$xml->status->msg;
                    
                    /*
                     * Se o retorno for 0 - Consuta gerada com sucesso
                     * Será lida a TAG <dados> para recebimento de informações 
                     * conforme documentação do método Pedidos
                     */
                    if($status == 0){
                        $ret->status    = true;
                        $ret->erro      = $status;
                        $ret->pedidos   = $xml->dados->pedido; //Array com pedidos encontrados
                    }
                }else{
                    $ret->msg = "Função Pedidos - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    public function callDetalhaPedidos($dtInicio, $dtFim){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao detalhar pedidos!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='dtInicio'>".$dtInicio."</param>";
            $xmlParams .= "<param id='dtFim'>".$dtFim."</param>";
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->detalhaPedidos($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função detalhaPedidos - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status     = (int)$xml->status->erro;
                    $ret->erro  = $status;
                    $ret->msg   = (string)$xml->status->msg;
                    
                    /*
                     * Se o retorno for 0 - Consuta gerada com sucesso
                     * Será lida a TAG <dados> para recebimento de informações 
                     * conforme documentação do método Pedidos
                     */
                    if($status == 0){
                        $ret->status    = true;
                        $ret->erro      = $status;
                        $ret->pedidos   = $xml->dados->pedido; //Array com pedidos encontrados
                    }
                }else{
                    $ret->msg = "Função detalhaPedidos - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    /**
     * Função que efetua a chamada do método getSaldo no Webservice
     * 
     * @param int $id_usuario
     * @return \stdClass
     * @throws Exception
     */
    public function callGetSaldo($id_usuario){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao recuperar o saldo do usuário!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='id_usuario'>".((int)$id_usuario)."</param>";
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->getSaldo($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função getSaldo - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status     = (int)$xml->status->erro;
                    $ret->erro  = $status;
                    $ret->msg   = (string)$xml->status->msg;
                    
                    /*
                     * Se o retorno for 0 - Consuta gerada com sucesso
                     * Será lida a TAG <dados> para recebimento de informações 
                     * conforme documentação do método getSaldo
                     */
                    if($status == 0){
                        $ret->status        = true;
                        $ret->erro          = $status;
                        $ret->saldo         = $xml->dados->saldo;
                    }
                }else{
                    $ret->msg = "Função getSaldo - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    public function callOpCredito($id_usuario, $credito){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao inserir créditos ao usuário!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='id_usuario'>".((int)$id_usuario)."</param>";
            $xmlParams .= "<param id='credito'>".((int)$credito)."</param>";
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->opCredito($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função opCredito - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status         = (int)$xml->status->erro;
                    $ret->status    = $status == 0 ? true : false;
                    $ret->erro      = $status;
                    $ret->msg       = (string)$xml->status->msg;
                }else{
                    $ret->msg = "Função opCredito - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    /**
     * Função que efetua chamada do método opEstrono do Webservice
     * 
     * @param int $id_usuario Usuário que terá os créditos estornados
     * @param int $estorno Quantidade de créditoas a serem estornados
     * 
     * @return \stdClass
     * @throws Exception
     */
    public function callOpEstorno($id_usuario, $estorno){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao estornar créditos do usuário!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='id_usuario'>".((int)$id_usuario)."</param>";
            $xmlParams .= "<param id='estorno'>".((int)$estorno)."</param>";
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->opEstorno($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função opEstorno - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status         = (int)$xml->status->erro;
                    $ret->status    = $status == 0 ? true : false;
                    $ret->erro      = $status;
                    $ret->msg       = (string)$xml->status->msg;
                }else{
                    $ret->msg = "Função opEstorno - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    /**
     * Função que efetua a chamada do método bloqueiaUsuario no WS
     * 
     * @param int $id_usuario Código do usuário a ser bloqueado
     * 
     * @return \stdClass
     * @throws Exception
     */
    public function callBloqueiaUsuario($id_usuario){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao bloquear usuário!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='id_usuario'>".((int)$id_usuario)."</param>";
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->bloqueiaUsuario($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função bloqueiaUsuario - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status         = (int)$xml->status->erro;
                    $ret->status    = $status == 0 ? true : false;
                    $ret->erro      = $status;
                    $ret->msg       = (string)$xml->status->msg;
                }else{
                    $ret->msg = "Função bloqueiaUsuario - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    /**
     * Função que efetua a chamada do método desbloqueiaUsuario no WS
     * 
     * @param int $id_usuario Código do usuário a ser desbloqueado
     * 
     * @return \stdClass
     * @throws Exception
     */
    public function callDesbloqueiaUsuario($id_usuario){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao desbloquear usuário!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='id_usuario'>".((int)$id_usuario)."</param>";
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->desbloqueiaUsuario($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função desbloqueiaUsuario - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status         = (int)$xml->status->erro;
                    $ret->status    = $status == 0 ? true : false;
                    $ret->erro      = $status;
                    $ret->msg       = (string)$xml->status->msg;
                }else{
                    $ret->msg = "Função desbloqueiaUsuario - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>"; 
        }
    }
    
    public function callExcluiUsuario($id_usuario){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao excluir usuário!";
            
            //Solicita um novo TOKEN para acesso ao serviço
            $rs_token = $this->getTokenAcesso();
            
            //Caso tenha encontrado um erro na solicitação do Token, o erro é retornado.
            if(!$rs_token->status){
                return $rs_token;
            }
            
            //Parâmetros XML que serão enviados ao método
            $xmlParams .= "<root>";
            $xmlParams .= "<params>";
            $xmlParams .= "<param id='token'>".$rs_token->token."</param>";
            $xmlParams .= "<param id='id_usuario'>".((int)$id_usuario)."</param>";
            $xmlParams .= "</params>";
            $xmlParams .= "</root>";
            
            //Chamada do método ao WS
            $rs = $this->client->excluiUsuario($xmlParams);
            
            //Verifica retorno
            if($this->client->fault){
                $ret->msg = "Função excluiUsuario - " . $rs['faultstring'];
            }else{
                $xml = new SimpleXMLElement($rs);
                
                if($xml){
                    $status         = (int)$xml->status->erro;
                    $ret->status    = $status == 0 ? true : false;
                    $ret->erro      = $status;
                    $ret->msg       = (string)$xml->status->msg;
                }else{
                    $ret->msg = "Função excluiUsuario - O retorno não é um XML";
                }
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
    
    /**
     * Acesso o Webservice e solicita um novo TOKEN para acesso.
     * 
     * @return \stdClass
     * @throws Exception
     */
    private function getTokenAcesso(){
        try{
            //Obejto de retorno
            $ret            = new stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao gerar Token!";
            $ret->token     = null;
            
            //Chamada do método ao WS para captura de TOKEN
            $rs = $this->client->getToken();
            
            //Verifica retorno
            $xml = new SimpleXMLElement($rs);

            if($xml){
                $status     = (int)$xml->status->erro;
                $ret->erro  = $status;
                $ret->msg   = (string)$xml->status->msg;                        

                //Se o retorno for 0 - Consuta gerada com sucesso, e token armazenado
                if($status == 0){
                    //Retorno OK
                    $ret->status = true;
                    //Armazena um token de acesso
                    $ret->token = (string)$xml->dados->token;
                }
            }else{
                $ret->msg = "Função getTokenAcesso - O retorno não é um XML";
            }
            
            return $ret;
        }catch(Exception $e){
            if(isset($e->faultcode)){
                $erro = $e->faultstring;
            }else{
                $erro = $e->getMessage();
            }

            echo "Erro - " . $erro . "<br /><br />";
            echo "Resposta Servidor: <br />";
            echo "<pre>";
            print_r($this->client->__getLastResponse());
            echo "</pre>";
        }
    }
}

?>
