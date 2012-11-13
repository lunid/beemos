<?php
    namespace admin\classes\models;
    use \sys\classes\mvc\Model;        
    use \admin\classes\models\tables\HistoricoGeradoc;
    use \admin\classes\models\tables\TurmaLista;
    
    class ListasModel extends Model {
        /**
         * Carrega as Listas de uma Turma para um jqgrid.
         * Podendo utilizar ordenação e paginação
         * 
         * @param int $ID_CLIENTE Código do cliente
         * @param string $where String enviada para filtro de resultados
         * <code>
         *  Ex: AND L.COD_LISTA LIKE '%Teste%'
         * </code>
         * @param int $utilizadas Filtrar apenas listas utilizadas ou não
         * @param int $ID_TURMA Filtrar apenas listas utilizadas pela Turma enviada
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
         *  array   $ret->listas    - Armazena o array de listas encontrados no Banco   <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregaListasCliente($ID_CLIENTE, $where = "", $utilizadas = 0, $ID_TURMA = 0, $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar listas de exercicios do cliente!";
                
                //Valida ID_CLIENTE
                if((int)$ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE inválido ou nulo!";
                    return $ret;
                }
                
                //Instância da table SPRO_HISTORICO_GERADOC
                $tbHistGeradoc  = new HistoricoGeradoc();
                //Carrega listas do cliente
                $ret            = $tbHistGeradoc->carregaListasCliente($ID_CLIENTE, $where, $utilizadas, $ID_TURMA, $arrPg);
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Função que salva a alteração de relacionamentos entre Turmas e Listas
         * 
         * @param string $idTurmas String com IDs de turmas separados por vírgula. Ex: 23,56,87
         * @param string $idsListas String com IDs de listas separados por vírgula. Ex: 17878,21233,98877
         * @param char $tipo Tipo de operação a ser realizada I - Inserção ou E - Exclusão
         * @return \stdClass
         * @throws Exception
         */
        public function salvaListasTurmas($idTurmas, $idsListas, $tipo){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar Lista(s) da Turma";
                
                //Array de Turmas
                $arrIdTurmas = explode(",", $idTurmas);
                
                //Percorre array limpando registros e adicionando relacionamentos
                foreach($arrIdTurmas as $idTurma){
                    //Limpa as listas do com IDs enviados
                    $tbTurmaLista   = new TurmaLista();
                    $sql            = "DELETE FROM SPRO_TURMA_LISTA WHERE ID_TURMA = {$idTurma} AND ID_HISTORICO_GERADOC IN({$idsListas});";
                    $tbTurmaLista->query($sql);

                    //Verifica qual operação executar
                    switch($tipo){
                        case "I":
                            //Tranforma IDs em Array
                            $arrId = explode(",", $idsListas);
                            //Verifica se forma encontrados IDs
                            if(is_array($arrId) && sizeof($arrId) > 0){
                                foreach($arrId as $idLista){
                                    //Popula informações para insert
                                    $tbTurmaLista->ID_TURMA             = (int)$idTurma;
                                    $tbTurmaLista->ID_HISTORICO_GERADOC = (int)$idLista;

                                    //Executa inserção
                                    $tbTurmaLista->save();
                                }

                                //Se não houver erro, é retornado status OK
                                $ret->status    = true;
                                $ret->msg       = "Relação salva com sucesso!";
                            }else{
                                //Caso não seja enviado nenhum ID
                                $ret->msg = "Nenhuma ID de lista enviado!";
                            }
                            break;
                        case "E":
                            //Caso seja apenas uma exclusão
                            $ret->status    = true;
                            $ret->msg       = "Relação desfeita com sucesso!";
                            break;
                        default:
                            $ret->msg = "Operação não identificada!";
                            return $ret;
                    }
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
