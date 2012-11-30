<?php
    namespace api\classes\models;
    use \sys\classes\mvc\Model;    
    use \db_tables as TB;
    
    class UsuariosModel extends Model{
        /**
         * Função interna para atualização dos Status de um usuário
         * 
         * @param int $id_usuario
         * @param int $id_matriz 
         * @param string $status (BLOQ, DESBLOQ e EXCLUIR)
         * 
         * @return \stdClass
         */
        function atualizaStatusUsuario($id_usuario, $id_matriz, $status){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao atualizar usuário";

                switch(strtoupper($status)){
                    case 'BLOQ':
                        $sql = "UPDATE
                                    SPRO_CLIENTE
                                SET
                                    BLOQ = 1
                                WHERE
                                    ID_MATRIZ = {$id_matriz}
                                AND
                                    ID_CLIENTE = {$id_usuario}
                                ;";
                        break;
                    case 'DESBLOQ':
                        $sql = "UPDATE
                                    SPRO_CLIENTE
                                SET
                                    BLOQ = 0
                                WHERE
                                    ID_MATRIZ = {$id_matriz}
                                AND
                                    ID_CLIENTE = {$id_usuario}
                                ;";
                    case 'EXCLUIR':
                        $sql = "UPDATE
                                    SPRO_CLIENTE
                                SET
                                    DEL = 1
                                WHERE
                                    ID_MATRIZ = {$id_matriz}
                                AND
                                    ID_CLIENTE = {$id_usuario}
                                ;";
                        break;
                    default :
                        $ret->msg = "Status não identificado!";
                        return $ret;
                        break;
                }

                //Executa comando SQL definido
                $mysql = new Mysql();
                $mysql->query($sql);

                $ret->status    = true;
                $ret->msg       = "Usuário atualizado com sucesso!";

                return $ret;
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();

                return $ret;
            }
        }
    }
?>
