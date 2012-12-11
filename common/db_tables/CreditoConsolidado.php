<?php
    namespace common\db_tables;  

    /**
     * Representa uma entidade da tabela SPRO_CREDITO_CONSOLIDADO
     * 
     * @property int ID_CREDITO_CONSOLIDADO  
     * @property string MOTIVO
     * @property int ID_MATRIZ
     * @property int ID_FILIAL
     * @property char OPERACAO
     * @property int ID_CLIENTE
     * @property int ID_REG_SALDO_ANT
     * @property int SALDO_ANT
     * @property int CREDITO
     * @property int SALDO_FINAL
     * @property int NUM_PEDIDO
     * @property date VENCIMENTO
     * @property string OBS
     * @property int ID_CAMPANHA
     * @property int BONUS
     * @property string CUPOM
     * @property datetime DATA_REGISTRO
     * @property datetime HISTORICO_APARTIR_DE
     */
    class CreditoConsolidado extends \Table {
        /**
         * Consulta o total de céditos consolidados de um cliente
         * 
         * @param int $idCliente ID do Cliente
         * @param string $dtRegistro Período de início para cálculo
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->count     - Total de registros encontrados                    <br />
         *  array   $ret->creditos  - Array com créditos encontrados                    <br />
         * </code>
         * @throws \db_tables\Exception
         */
        public function consultarCreditoCliente($idCliente, $dtRegistro){
            try{
                $sql = "SELECT
                            CC.OPERACAO,
                            SUM(CC.CREDITO) AS TOTAL
                        FROM
                            SPRO_CREDITO_CONSOLIDADO CC
                        INNER JOIN
                            SPRO_CLIENTE C ON C.ID_CLIENTE = CC.ID_CLIENTE
                        WHERE
                            C.ID_MATRIZ = {$idCliente}
                        AND
                            CC.DATA_REGISTRO >= '{$dtRegistro}'
                        GROUP BY
                            CC.OPERACAO
                        ;";
                
                //Executa Select
                $rs = $this->query($sql);
                
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = true;
                $ret->msg       = "Consulta efetuada com sucesso!";
                $ret->count     = sizeof($rs);
                $ret->creditos  = $rs;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function consultarLancamentosMatriz($idMatriz, $arrWhere = array()){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao consultar lançamentos da Matriz!";
                
                //Tabela SPRO_CREDITO_CONSOLIDADO
                $tbCredito = $this;
                $tbCredito->alias = "CC";
                $tbCredito->fieldsJoin = "OPERACAO,
                                          CREDITO,
                                          DATA_REGISTRO";
                
                //Tabela SPRO_CLIENTE
                $tbCliente = new Cliente();
                $tbCliente->alias = "C";
                $tbCliente->fieldsJoin = "ID_CLIENTE,
                                          NOME_PRINCIPAL";
                
                //Adiciona ORDER
                $this->setOrderBy("CC.DATA_REGISTRO");
                
                //Monda Join
                $this->joinFrom($tbCredito, $tbCliente, "ID_CLIENTE");
                
                //Where para SQL
                $where = "";
                
                //Monta where com campos enviados
                foreach($arrWhere as $campo => $clausula){
                    $where .= " AND " . $campo . " " . $clausula;
                }
                
                //Executa JOIN
                $rs = $this->setJoin("C.ID_MATRIZ = {$idMatriz} AND CC.NUM_PEDIDO <= 0 " . $where);
                
                if(!is_array($rs) || sizeof($rs) <= 0){
                    $ret->msg = "Nenhum lançamento encontrado!";
                    return $ret;
                }
                
                //Objeto de retorno OK
                $ret->status        = true;
                $ret->msg           = "Lançamentos listados com sucesso!";
                $ret->count         = sizeof($rs);
                $ret->lancamentos   = $rs;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
