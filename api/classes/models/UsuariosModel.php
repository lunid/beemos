<?php
    namespace api\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \db_tables as TB;
    
    class UsuariosModel extends Model{
        /**
         * Verifica se o usuário enviado pertence a matriz utilizada pelo WS
         * 
         * @param int $idUsuario ID do usuário
         * @param int $idCliente ID da matriz (Cliente) logado no WS
         * 
         * @return boolean
         * @throws Exception
         */
        public function validarUsuarioMatriz($idUsuario, $idCliente){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                $tbCliente->setLimit(1); //Define LIMIT 1
                
                //Consulta usuário
                $rs = $tbCliente->findAll("ID_MATRIZ = " . (int)$idCliente . " AND ID_CLIENTE = " . (int)$idUsuario);
                
                //Se houver retorno, retorno TRUE
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
         * Função interna para atualização dos Status de um usuário
         * 
         * @param int $idCliente
         * @param int $idMatriz 
         * @param string $status (BLOQ, DESBLOQ e EXCLUIR)
         * 
         * @return \stdClass
         */
        function atualizarStatusUsuario($idCliente, $idMatriz, $status){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao atualizar usuário";
                
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                
                //Define Campo do Update
                switch(strtoupper($status)){
                    case 'BLOQ':
                        $tbCliente->BLOQ = 1;
                        break;
                    case 'DESBLOQ':
                        $tbCliente->BLOQ = 0;
                        break;
                    case 'EXCLUIR':
                        $tbCliente->DEL  = 1;
                        break;
                    default :
                        $ret->msg = "Status não identificado!";
                        return $ret;
                        break;
                }

                //Executa UPDATE
                $tbCliente->update(array("ID_CLIENTE = %i AND ID_MATRIZ = %i", $idCliente, $idMatriz));
                
                //Retorno OK
                $ret->status    = true;
                $ret->msg       = "Usuário atualizado com sucesso!";

                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Valida a existência de um e-mail em outra Matriz
         * 
         * @param string $email E-mail a ser validado
         * @param int $idUsuario ID do usuário (para ser retirado do select)
         * 
         * @return boolean
         * @throws Exception
         */
        public function validarEmailUsuarioMatriz($email, $idUsuario){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                $tbCliente->setLimit(1); //Define LIMIT 1
                
                //Consulta usuário
                $rs = $tbCliente->findAll("EMAIL = '{$email}' AND ID_CLIENTE != {$idUsuario} AND ID_MATRIZ > 0");
                
                //Se não houver retorno, retorno TRUE
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
         * Valida a existência de um LOGIN na tabela de Clientes
         * 
         * @param type $login Login a ser verificado
         * @param type $idUsuario ID do usuário (para ser retirado do select)
         * 
         * @return boolean
         * @throws Exception
         */
        public function validarLoginUsuario($login, $idUsuario){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                $tbCliente->setLimit(1); //Define LIMIT 1
                
                //Consulta usuário
                $rs = $tbCliente->findAll("LOGIN = '{$login}' AND ID_CLIENTE != {$idUsuario}");
                
                //Se não houver retorno, retorno TRUE
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
         * Atualiza os dados de um usuário
         * 
         * @param int $idUsuario ID do usuário que será atualizado
         * @param int $idMatriz ID da Matriz logada no WS (Cliente)
         * @param array $arrCampos Array com os campos para atualização
         * <br />
         * <code>
         * Ex: $arrCampos['NOME_PRINCIPAL'] = 'Cláudio Rubens';
         * </code>
         * 
         * @return int
         * @throws Exception
         */
        public function atualizarUsuario($idUsuario, $idMatriz, $arrCampos = array()){
            try{
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                
                //Implementa campos a serem alterados
                foreach($arrCampos as $campo => $valor){
                    $tbCliente->$campo = $valor;
                }
                
                //Consulta usuário
                return $tbCliente->update(array("ID_CLIENTE = %i AND ID_MATRIZ = %i", $idUsuario, $idMatriz));
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function listarUsuarios($idMatriz, $where){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar clientes!";
                
                //Table SPRO_CLIENTE
                $tbCliente = new TB\Cliente();
                
                //SQL
                $sql = "SELECT
                            C.ID_CLIENTE,
                            C.NOME_PRINCIPAL,
                            C.EMAIL,
                            C.LOGIN,
                            C.DATA_REGISTRO,
                            C.BLOQ,
                            C.DEL,
                            (SELECT SUM(DEBITO) FROM SPRO_HISTORICO_GERADOC WHERE ID_LOGIN = C.ID_CLIENTE) AS CONSUMO
                        FROM
                            SPRO_CLIENTE C
                        WHERE
                            C.ID_MATRIZ = {$idMatriz}
                        $where
                        ORDER BY
                            C.NOME_PRINCIPAL
                        ;";
                
                $rs = $tbCliente->query($sql);
                
                if(is_array($rs) && sizeof($rs) > 0){
                    $ret->status    = true;
                    $ret->msg       = "Usuários listados com sucesso!";
                    $ret->usuarios  = $rs;
                }else{
                    $ret->msg = "Nenhum usuário encontrado!";
                }
                
                //Retorno
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
