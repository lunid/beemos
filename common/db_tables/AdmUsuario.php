<?php

    namespace common\db_tables;  
    
    class AdmUsuario extends \Table {
        public function getUsuariosQuestao($in_materias){
            try{
                $arr_usuarios = array();
                
                $sql = "SELECT
                            U.ID_USUARIO,
                            U.NOME
                        FROM
                            SPRO_ADM_USUARIO U
                        INNER JOIN
                            SPRO_ADM_USUARIO_MATERIA UM ON UM.ID_USUARIO = U.ID_USUARIO
                        WHERE
                            UM.ID_MATERIA IN ({$in_materias})
                        AND
                            ID_PERFIL = 2
                        ;";
                
                $rs_usuario = $this->query($sql);
                
                if(sizeof($rs_usuario) > 0){
                    foreach ($rs_usuario as $usuario) {
                        $arr_usuarios[] = $usuario;
                    }
                }
                            
                return $arr_usuarios;
            }catch(Exception $e){
                throw $e;                
            }
        }
        
        /**
         * Carrega os usuários de uma determinada escola 
         * 
         * @param int $idMatriz ID da Escola(Cliente)
         * @param string $where Cláusula WHERE do SQL. Ex: BLOQ = 1
         * @param array $arrPg Array com parâmetros para Ordenação e Paginação
         * <code>
         * array(
         *   "campoOrdenacao"    => 'DATA_REGISTRO', 
         *   "tipoOrdenacao"     => 'DESC', 
         *   "inicio"            => 1, 
         *   "limite"            => 10
         * )
         * </code>
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->usuarios  - Armazena o array de usuários encontrados no Banco <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregarUsuariosEscola($idMatriz, $where = "", $arrPg = array()){
            try {
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar usuário da escola!";
                
                //Paginação e Ordenação
                $order = "";
                if($arrPg != null){
                    //Monta ordenação
                    if(isset($arrPg['campoOrdenacao']) && isset($arrPg['tipoOrdenacao'])){
                        $order = $arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao'];
                    }
                    
                    //Monta paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                        $this->setLimit($arrPg['inicio'], $arrPg['limite']);
                    }
                }else{
                    $order = " ORDER BY U.NOME ";
                }
                
                //Monta order no SQL               
                $this->setOrderBy($order);
                
                //Tabela de usuarios
                $this->alias        = "U";
                $this->fieldsJoin   = "ID_USUARIO,
                                        NOME,
                                        EMAIL,
                                        LOGIN,
                                        TELEFONE,
                                        BLOQ,
                                        DATA_REGISTRO,
                                        ULTIMO_ACESSO,
                                        DEL";
                
                //Taqblea de Perfis
                $tbPerfil               = new AdmPerfil();
                $tbPerfil->alias        = "P";
                $tbPerfil->fieldsJoin   = "DESCRICAO";
                
                //Join
                $this->joinFrom($this, $tbPerfil, "ID_PERFIL");
                
                //Executa SELECT
                $rs = $this->setJoin("U.ID_MATRIZ = {$idMatriz} {$where}");
                
                if(sizeof($rs) > 0){
                    $ret->status    = true;
                    $ret->msg       = "Usuários listados com sucesso!";
                    $ret->usuarios  = $rs;
                }else{
                    $ret->msg = "Nenhum usuário encontrado!";
                }
                
                //Retorno
                return $ret;
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

?>

