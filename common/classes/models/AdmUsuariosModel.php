<?php
    namespace common\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \common\db_tables as TB;
    
    class AdmUsuariosModel extends Model{
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
        public function carregarUsuariosEscola($idMatriz, $where = '', $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar usuários!";
                
                //Table SPRO_CLIENTE
                $tbAdmUser = new TB\AdmUsuario();
                return $tbAdmUser->carregarUsuariosEscola($idMatriz, $where, $arrPg);
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
