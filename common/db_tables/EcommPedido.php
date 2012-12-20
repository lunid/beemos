<?php
    namespace common\db_tables;   
    
    /**
     * Representa uma entidade da tabela SPRO_ECOMM_PEDIDO
     * 
     * @property int ID_PEDIDO
     * @property int ID_MATRIZ
     * @property int ID_FILIAL
     * @property int ID_PLANO
     * @property int ID_CLIENTE
     * @property int NUM_PEDIDO_PAI
     * @property int NUM_PEDIDO
     * @property string STR_NUM_PGTO
     * @property string NOME_CLIENTE
     * @property string DESCR_PRODUTO
     * @property int NUM_PEDIDO_VER_WEB
     * @property string EMAIL_CLIENTE
     * @property string FORMA_PGTO
     * @property int ID_MEIO_PGTO
     * @property int PARCELAS
     * @property float VALOR_TOTAL
     * @property float VALOR_PARCELA
     * @property float VALOR_ADM
     * @property int CREDITOS
     * @property int VALIDADE_CREDITOS
     * @property int ID_PEDIDO_LOJA
     * @property string SESSION_ID
     * @property int ID_STATUS_LOJA
     * @property string MSG_BANCO
     * @property string VISA_TID
     * @property int AMEX_NUM_CAPTURA
     * @property int COD_BANCO
     * @property string COD_AUTH
     * @property string COD_RET
     * @property string NR_CARTAO
     * @property int EMAIL_ENV_CLI
     * @property string OBS
     * @property int NAO_QUERO_PROMO
     * @property string CUPOM
     * @property string XML_RETORNO
     * @property date DATA_PGTO
     * @property date DATA_VENC_BOLETO
     * @property date DATA_FAT_CALC
     * @property date DATA_REGISTRO
     */
    class EcommPedido extends \Table {
         /**
         * Lista os Pedidos Financeiros de uma Escola
         * 
         * @param int $idCliente ID da Escola (Cliente)
         * @param string $status Filtro de Status dos Pedidos - PAGO / AVENCER / PENDENTE
         * @param string $where String com a cláusula WHERE. Ex: PARCELAS = 2
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
         *  array   $ret->pedidos   - Array com pedidos encontrados                    <br />
         * </code>
         * @throws \api\classes\models\Exception
         */
        public function carregarPedidosEscola($idCliente, $status = '', $where = '', $arrPg = null){
            //Objeto de retorno
            $ret            = new \stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao consultar dados financeiros da escola!";

            //Paginação e Ordenação
            $limit  = "";
            $order  = " ORDER BY ";
            $having = $status != "" ? " HAVING STATUS = '{$status}' " : "";
            
            if($arrPg != null){
                //Monta ordenação
                if(isset($arrPg['campoOrdenacao']) && isset($arrPg['tipoOrdenacao'])){
                    $order .= "P." . $arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao'];
                }

                //Monta paginação
                if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                    $limit = "LIMIT " . $arrPg['inicio'] . ", " . $arrPg['limite'];
                }
            }else{
                $order .= "P.DATA_REGISTRO DESC";
            }
                        
            //SQL
            $sql = "SELECT 
                        P.ID_PEDIDO,
                        P.NUM_PEDIDO,
                        P.DATA_REGISTRO,
                        P.PARCELAS,
                        P.VALOR_PARCELA,
                        P.DATA_VENC_BOLETO,
                        P.DATA_PGTO,
                        PP.NUM_PEDIDO AS NUM_PEDIDO_PAI,
                        IF(P.DATA_PGTO IS NOT NULL AND P.DATA_PGTO != '0000-00-00', 'PAGO', 
                            IF(P.DATA_VENC_BOLETO >= NOW(), 'AVENCER','PENDENTE')) AS STATUS
                    FROM 
                        SPRO_ECOMM_PEDIDO AS P 
                    INNER JOIN 
                        SPRO_ECOMM_PEDIDO AS PP ON P.NUM_PEDIDO_PAI = PP.NUM_PEDIDO
                    WHERE 
                        P.ID_CLIENTE = {$idCliente}  
                    {$where}
                    {$having}
                    {$order}
                    {$limit}
                    ;";
            
            //Executa Select
            $rs = $this->query($sql);
            
            if(is_array($rs) && sizeof($rs) > 0){
                $ret->status    = true;
                $ret->msg       = "Pedidos listados com sucesso!";
                $ret->pedidos   = $rs;
            }else{
                $ret->msg = "Nenhum pedido encontrado!";
            }

            return $ret;
        }
    }
?>
