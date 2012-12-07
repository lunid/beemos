<?php
    namespace db_tables;

    /**
     * Representa uma entidade da tabela SPRO_WS_USUARIO
     * 
     * @property int ID_WS_USUARIO  
     * @property int ID_CLIENTE
     * @property string LOGIN
     * @property string SENHA
     * @property datetime DT_REGISTRO
     * @property datetime DT_ULTIMO_ACESSO
     */
    class WsUsuario extends \Table {
        /**
         * Consulto a tabela de usuários de WebService relacionando o cliente com a tabela Cliente
         * 
         * @param string $usuario Login do usuário
         * @param type $senha Senha do usuário
         * 
         * @return array Resultado obtido
         * 
         * @throws Exception
         */
        public function consultarWsUsuarioCliente($usuario, $senha){
            try{
                //WS_USUARIO
                $tbWsUsuario                = $this;
                $tbWsUsuario->alias         = "WU";
                $tbWsUsuario->fieldsJoin    = "ID_WS_USUARIO,
                                                ID_CLIENTE";

                //CLIENTE
                $tbCliente                  = new Cliente();
                $tbCliente->alias           = "C";
                $tbCliente->fieldsJoin      = "BLOQ";

                //Monta Join e Limit
                $this->setLimit(1);
                $this->joinFrom($tbWsUsuario, $tbCliente, "ID_CLIENTE");

                //Executa Join
                return $this->setJoin("WU.LOGIN = '{$usuario}' AND WU.SENHA = '{$senha}'");
            }catch (Exception $e){
                throw $e;
            }
        }
    }
?>
