<?php
    namespace api\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \common\db_tables as TB;
    use \common\classes\models as MD;
    
    class CreditosModel extends Model{
        /**
         * Insere créditos  para um determinado usuário da matriz logada (cliente)
         * 
         * @param int $idUsuario Código do cliente que receberá créditos
         * @param int $idRegAnterior Código do registro anterior aos créditos
         * @param int $saldoAnt Saldo anterior do cliente
         * @param int $credito Quantidade de créditos que será inserida
         * @param int $saldoFinal Saldo final do cliente
         * @param datetime $vencimento
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  itn     $ret->erro      - Código de erro                                    <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  int     $ret->id        - ID do novo registro inserido                      <br />
         * </code>
         * @throws Exception
         */
        public function inserirCredito($idUsuario, $idRegAnterior, $saldoAnt, $credito, $saldoFinal, $vencimento){
            try{
                //Objeto de Retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao inserir crédito!";
                
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                $tbCredito->OPERACAO            = "C";
                $tbCredito->NUM_PEDIDO          = 0;
                $tbCredito->DATA_REGISTRO       = date("Y-m-d H:i:s");
                $tbCredito->ID_CLIENTE          = (int)$idUsuario;
                $tbCredito->ID_REG_SALDO_ANT    = (int)$idRegAnterior;
                $tbCredito->SALDO_ANT           = $saldoAnt;
                $tbCredito->CREDITO             = $credito;
                $tbCredito->SALDO_FINAL         = $saldoFinal;
                $tbCredito->VENCIMENTO          = $vencimento;
                        
                //Executa insert
                $id = $tbCredito->save();
                
                if($id > 0){
                    //Retorno OK
                    $ret->status    = true;
                    $ret->msg       = "Crédito lançado com suceeo";
                    $ret->id        = $id;
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Estorna créditos de um determinado usuário para matriz logada (cliente)
         * 
         * @param int $idUsuario Código do cliente que receberá créditos
         * @param int $idRegAnterior Código do registro anterior aos créditos
         * @param int $saldoAnt Saldo anterior do cliente
         * @param int $estorno Quantidade de créditos a serem estornados
         * @param int $saldoFinal Saldo final do cliente
         * @param datetime $vencimento
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  itn     $ret->erro      - Código de erro                                    <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  int     $ret->id        - ID do novo registro inserido                      <br />
         * </code>
         * @throws Exception
         */
        public function inserirEstorno($idUsuario, $idRegAnterior, $saldoAnt, $estorno, $saldoFinal, $vencimento){
            try{
                //Objeto de Retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao inserir estorno!";
                
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = new TB\CreditoConsolidado();
                $tbCredito->OPERACAO            = "D";
                $tbCredito->NUM_PEDIDO          = 0;
                $tbCredito->DATA_REGISTRO       = date("Y-m-d H:i:s");
                $tbCredito->ID_CLIENTE          = (int)$idUsuario;
                $tbCredito->ID_REG_SALDO_ANT    = (int)$idRegAnterior;
                $tbCredito->SALDO_ANT           = $saldoAnt;
                $tbCredito->CREDITO             = $estorno;
                $tbCredito->SALDO_FINAL         = $saldoFinal;
                $tbCredito->VENCIMENTO          = $vencimento;
                        
                //Executa insert
                $id = $tbCredito->save();
                
                if($id > 0){
                    //Retorno OK
                    $ret->status    = true;
                    $ret->msg       = "Crédito lançado com suceeo";
                    $ret->id        = $id;
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Consulta os dados da última operação efetuada pelo cliente
         * 
         * @param int $idCliente Código do cliente a ser consultado
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool        $ret->status        - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string      $ret->msg           - Armazena mensagem ao usuário                      <br />
         *  stdClass    $ret->operacao      - Dados da operação                                 <br />
         * </code>
         * @throws Exception
         */
        public function consultarUltimaOperacao($idCliente){
            try{
                //Model de usuários
                $mdUsuarios = new MD\UsuariosModel();
                return $mdUsuarios->consultarUltimaOperacaoCliente($idCliente);
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
