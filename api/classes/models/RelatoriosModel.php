<?php
    namespace api\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \db_tables as TB;
    
    class RelatoriosModel extends Model{
        /**
         * Consulta pedidos de uam determinada Matriz (Cliente logado)
         * 
         * @param int $idMatriz Código da Matriz (Cliente)
         * @param string $arrWhere Array com parãmetros de filtro para o select
         * <br />
         * Exemplo $arrWhere
         * <br />
         * <code>
         * $arrWhere['CAMPO_DA_TABLE']  = 'CLAUSULA WHERE'  <br />
         * $arrWhere['ID_MATRIZ']       = ' = 153'          <br />
         * </code>
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  itn         $ret->erro      - Código de erro                                    <br />
         *  bool        $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  staClass[]  $ret->pedidos   - Array de objetos encontrados                      <br />
         * </code>
         * @throws Exception
         */
        public function consultarPedidosMatriz($idMatriz, $arrWhere = array()){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao consultar pedidos da Matriz!";
                
                //Where para SQL
                $where = "";
                
                //Monta where com campos enviados
                foreach($arrWhere as $campo => $clausula){
                    $where .= " AND " . $campo . " " . $clausula;
                }
                
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                $tbCredito->setOrderBy("DATA_REGISTRO"); //Define ordenação do SQL
                
                //Executa SQL
                $rs = $tbCredito->findAll("ID_CLIENTE = {$idMatriz} " . $where);
                
                //Se não for encontrado nenhum pedido
                if($rs->count() <= 0){
                    $ret->msg = "Nenhum pedido encontrado!";
                    return $ret;
                }
                
                //Senão, Retorno OK
                $ret->status    = true;
                $ret->msg       = "Pedidos encontrados";
                $ret->count     = $rs->count();
                $ret->pedidos   = $rs->getRs();
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function consultarPedidoSuperior($idCliente, $dtRegistro){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao consultar pedido superior!";
                
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                $tbCredito->setLimit(1); //Define LIMIT 1
                $tbCredito->setOrderBy("DATA_REGISTRO"); //Define ordenação do SQL
                
                //Executa SQL
                $rs = $tbCredito->findAll("DATA_REGISTRO > '{$dtRegistro}'
                                            AND
                                                ID_CLIENTE = {$idCliente}
                                            AND
                                                OPERACAO = 'C'
                                            AND
                                                (NUM_PEDIDO > 0 OR BONUS = 1)");
                                                
                //Se não for encontrado resultado
                if($rs->count() <= 0){
                    $ret->msg = "Nenhum pedido superior encontrado!";
                    return $ret;
                }               
                
                //Senão, Retorno OK
                $ret->status    = true;
                $ret->msg       = "Pedido superior encontrado";
                $ret->pedido    = $rs->getRs()[0];
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function consultarLancamentosMatriz($idMatriz, $arrWhere = array()){
            try{
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                //Efetua consulta e devolve retorno stdClass
                return $tbCredito->consultarLancamentosMatriz($idMatriz, $arrWhere);
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
