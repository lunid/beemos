<?php
    namespace api\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \db_tables as TB;
    
    /**
     * Operações de Banco para o SoapServer
     */
    class ServerModel extends Model{
        /**
         * Valida o Login e Senha enviadosa via HTTP
         * 
         * @param string $usuario
         * @param string $senha
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         * </code>
         * 
         * @throws Exception
         */
        public function validarUsuario($usuario, $senha){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->erro      = 1;
                $ret->status    = false;
                $ret->msg       = "Falha ao cunsultar usuário de WS!";

                //Tabela WsUsuario
                $tbWsUsuario    = new TB\WsUsuario();
                $rs             = $tbWsUsuario->consultarWsUsuarioCliente($usuario, $senha); //Consulta usuário e senha

                //Valida retorno
                if(!is_array($rs) || sizeof($rs) <= 0){
                    $ret->erro  = 2;
                    $ret->msg   = "Falha na autenticacao HTTP - Usuário inválido!";
                    return $ret;
                }
                
                //Valida bloqueio do usuário
                if($rs[0]->BLOQ == 1){
                    $ret->erro  = 3;
                    $ret->msg   = "Falha na autenticacao HTTP - Usuário bloqueado!";
                    return $ret;
                }

                //Retorno OK
                $ret->status    = true;
                $ret->erro      = 0;
                $ret->msg       = "Usuário válido!";
                $ret->user      = $rs[0];
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Verifica a se o cliente possui um token válido no momento
         * 
         * @param int $idCliente ID do CLIENTE
         * 
         * @return boolean
         * @throws Exception
         */
        public function verificarTokenCliente($idCliente){
            try{
                //Datas para validação
                $dt_inicio      = date("Y-m-d H:i:s");
                $dt_validade    = date("Y-m-d H:i:s", mktime(date('H'), date('i'), (date('s')+10)));
                
                //Table TOKEN_ACESSO
                $tbTokenAcesso  = new TB\TokenAcesso();
                $tbTokenAcesso->setLimit(1); //Define LIMIT 1
                
                //Consulta Token do usuário
                $rs = $tbTokenAcesso->findAll("ID_CLIENTE = " . (int)$idCliente . " AND DATA_REGISTRO BETWEEN '{$dt_inicio}' AND '{$dt_validade}'"); 
                
                //Se já existem Token ativo retorna TRUE
                if($rs->count() > 0){
                    return true;
                }else{
                    return false;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Verifica se o token exista na base de dados
         * 
         * @param string $token Código do token
         * 
         * @return boolean
         * @throws Exception
         */
        public function validarToken($token){
            try{
                //Tabela TOKEN_ACESSO
                $tbTokenAcesso  = new TB\TokenAcesso();
                $tbTokenAcesso->setLimit(1); //Define LIMIT 1
                
                //Executa SQL
                $rs = $tbTokenAcesso->findAll("TOKEN = '" . $token . "'");
                
                //Se não houver retorno retorna TRUE
                if($rs->count() <= 0){
                    return true;
                }else{
                    return false;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Verifica se o token do cliente está valido ou não
         * 
         * @param int $idCliente ID do cliente
         * @param string $token Código token enviado
         * 
         * @return boolean
         * @throws Exception
         */
        public function validarTokenAtivoCliente($idCliente, $token){
            try{
                //Data atual para validação
                $dt_atual = date("Y-m-d H:i:s");
                
                //Tabela TOKEN_ACESSO
                $tbTokenAcesso  = new TB\TokenAcesso();
                $tbTokenAcesso->setLimit(1); //Define LIMIT 1
                
                //Executa SQL
                $rs = $tbTokenAcesso->findAll("ID_CLIENTE = " . (int)$idCliente . " AND DATA_REGISTRO >= '{$dt_atual}'");
                
                //Se não houver retorno valida token
                if($rs->count() > 0){
                    //Captura retorno 
                    $ret = $rs->getRs()[0];
                    
                    //Se token encontrado igual ao token enviado
                    if($ret->TOKEN == $token){
                        return true;
                    }
                }
                
                return false;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Salva um novo token para o clinete
         * 
         * @param int $idCliente ID do cliente
         * @param string $login Login de acesso
         * @param string $token Código do token
         * 
         * @return boolean
         * @throws Exception
         */
        public function salvarTokenCliente($idCliente, $login, $token){
            try{
                //Define data de expiração do Token
                $dt_validade = date("Y-m-d H:i:s", mktime(date('H'), date('i'), (date('s')+10)));
                
                //Tabela TOKEN_ACESSO
                $tbTokenAcesso = new TB\TokenAcesso();
                
                //Define valores do insert
                $tbTokenAcesso->ID_CLIENTE      = (int)$idCliente;
                $tbTokenAcesso->LOGIN           = $login;
                $tbTokenAcesso->TOKEN           = $token;
                $tbTokenAcesso->DATA_REGISTRO   = $dt_validade;
                
                //Executa INSERT
                $id = $tbTokenAcesso->save();
                
                //Se insert funcionar retorna TRUE
                if($id > 0){
                    return true;
                }else{
                    return false;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
