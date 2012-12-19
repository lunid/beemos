<?php

    namespace app\classes\superpro;
    use \sys\classes\webservice\WsClient;
    use \sys\classes\webservice\IWsClient;
    use \sys\classes\security\Token;
    
    class WsUsuarioClient extends WsClient implements IWsClient {
        
        function config(){
            $this->webserviceAlias  = 'superpro'; 
            $this->wsInterface      = 'usuarios';
        }
        
        /**
        * Efetua o cadastro de um novo usuário.
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
                $ret->status    = FALSE;
                $ret->erro      = '';
                $ret->idUsuario = 0;                
                $ret->msg       = "Falha ao criar um novo usuário!";

                //Solicita um novo TOKEN para acesso ao serviço
                //$objToken   = new Token();
                //$token      = $objToken->tokenGen();                
         
                //Parâmetros XML que serão enviados ao método
                $xmlParams = "<root>";
                $xmlParams .= "<params>";
                $xmlParams .= "<param id='token'>".$token."</param>";
                $xmlParams .= "<param id='nome'><![CDATA[".$nome."]]></param>";
                $xmlParams .= "<param id='email'><![CDATA[".$email."]]></param>";
                $xmlParams .= "<param id='login'><![CDATA[".$login."]]></param>";
                $xmlParams .= "<param id='passwd_md5'><![CDATA[".(trim($senha) != '' ? md5($senha) : '')."]]></param>";
                $xmlParams .= "</params>";
                $xmlParams .= "</root>";

                //Chamada do método ao WS
                $rs = $this->client->novoUsuario($xmlParams);

                //Verifica retorno
                if(is_soap_fault($this->client)){
                    $ret->msg = "Função NovoUsuario - " . $rs['faultstring'];
                }else{
                    $xml = new \SimpleXMLElement($rs);

                    if($xml){
                        $status     = (int)$xml->status->erro;
                        $ret->erro  = $status;
                        $ret->msg   = (string)$xml->status->msg;

                        /*
                        * Status = 0 -> Consuta gerada com sucesso.
                        * Carrega <dados> para recebimento de informações 
                        * conforme documentação do método NovoUsuario.
                        */
                        if($status == 0){
                            $ret->status        = TRUE;
                            $ret->erro          = $status;
                            $ret->idUsuario    = (string)$xml->dados->id_usuario;
                        }
                    }else{
                        $ret->msg = "Função NovoUsuario - O retorno não é um XML";
                    }
                }
                var_dump($ret->status).'<br>';
                echo $ret->erro.'<br>';
                echo $ret->msg.'<br>';
                die();
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
