<?php
    namespace admin\classes\models;
    use \sys\classes\mvc\Model;        
    use \sys\classes\util\Date;
    use \admin\classes\models\tables\Escola;
    
    class EscolasTurmasModel extends Model {
        /**
         * Listas as escolas cadastradas para um determinado cliente
         * 
         * @param int $ID_CLIENTE Código do cliente para filtro de escolas
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  <b>bool</b>    $ret->status    - Retorna TRUE ou FALSE para o status do Método   <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                    <br />
         *  array   $ret->escolas   - Armazena o array de escolas encontrados no Banco<br />
         * </code>
         * @throws Exception
         */
        public function listaEscolasCliente($ID_CLIENTE, $where, $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar Escolas do Cliente!";
                $ret->escolas   = array();
                
                //Valida se existe ID_CLIENTE
                if(!$ID_CLIENTE || $ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE não foi inicializado!";
                    return $ret;
                }
                
                //Objeto de controle da table SPRO_ESCOLAS
                $tbEscola = new Escola();
                
                $whereSql  = " ID_CLIENTE = {$ID_CLIENTE} ";
                $whereSql .= $where;
                
                //Verifica dados de paginação e ordenação
                if(is_array($arrPg)){
                    //Ordenação
                    if(isset($arrPg['campoOrdenacao'])){
                        $tbEscola->setOrderBy($arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao']);
                    }
                    
                    //Paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                        $tbEscola->setLimit((int)$arrPg['inicio'], (int)$arrPg['limite']);
                    }
                }
                
                //Busca escolas baseado no WHERE 
                $rs = $tbEscola->findAll($whereSql);
                
                //Verifica se houve retorno
                if($rs->count() <= 0){
                    $ret->msg = "Nenhuma escola encontrada!";
                    return $ret;
                }
                
                //Retorna escola(s) encontrada(s)
                $ret->status    = true;
                $ret->msg       = "Escolas encontradas!";
                $ret->escolas   = $rs->getRs();
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
