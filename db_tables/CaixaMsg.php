<?php
    namespace db_tables;    
   
    class CaixaMsg extends \Table {
        /**
         * Carrega informações e dados das mensagens recebidas por um determinado cliente.
         * 
         * @param int $idCliente ID do Cliente
         * @param string $where Cláusula de filtro
         * @param array $arrPg Array com dados de Ordenação e Paginação Ex:
         * <code>
         * array(
         *   "campoOrdenacao"    => 'NOME_PRINCIPAL', 
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
         *  array   $ret->recebidas - Armazena os resultados (se encontrados)           <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregarMensagensRecebidas($idCliente, $where = '', $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar recebidas!";
                
                //Monta ORDER BY (se houver)
                $order = "";
                if($arrPg['campoOrdenacao'] != '' && $arrPg['tipoOrdenacao'] != ''){
                    $order = " ORDER BY {$arrPg['campoOrdenacao']} {$arrPg['tipoOrdenacao']} ";
                }
                
                //Monta LIMIT (se houver)
                $limit = "";
                if((int)$arrPg['inicio'] > 0 && $arrPg['limite'] > 0){
                    $limit = " LIMIT {$arrPg['inicio']}, {$arrPg['limite']} ";
                }
                
                $sql = "SELECT
                            CX.ID_CAIXA_MSG,
                            CX.ASSUNTO,
                            CX.DT_ENVIO,
                            CX.STATUS,
                            CX_PAI.ID_CLIENTE,
                            (SELECT C.NOME_PRINCIPAL FROM SPRO_CLIENTE C WHERE C.ID_CLIENTE = CX_PAI.ID_CLIENTE) as NOME_PRINCIPAL
                        FROM
                            SPRO_CAIXA_MSG CX
                        INNER JOIN
                            SPRO_CAIXA_MSG CX_PAI ON CX_PAI.ID_CAIXA_MSG = CX.SPRO_MSG_ID
                        WHERE
                            CX.ID_CLIENTE = {$idCliente}
                        {$order}
                        {$limit}
                        ;";
                     
                //Executa SQL
                $rs = $this->query($sql);
                
                if(sizeof($rs) <= 0){
                    $ret->msg = "Nenhuma mensagem recebida!";
                    return $ret;
                }
                
                $ret->status    = true;
                $ret->msg       = "Mensagens listadas com sucesso!";
                $ret->recebidas = $rs;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
