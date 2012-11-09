<?php
    namespace admin\classes\models;
    use \sys\classes\mvc\Model;        
    use \admin\classes\models\tables\HistoricoGeradoc;
    
    class ListasModel extends Model {
        public function carregaListasCliente($ID_CLIENTE, $ID_ESCOLA = 0){
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
                $tbHistGeradoc = new HistoricoGeradoc();
                $tbHistGeradoc->carregaListasCliente($ID_CLIENTE, $ID_ESCOLA);
                
                $rs = $tbHistGeradoc->findAll(" ID_LOGIN = {$ID_CLIENTE} ");
                
                //Caso não seja encontrado resultado algum
                if($rs->count() <= 0){
                    $ret->msg = "Nenhuma lista encontrada!";
                    return $ret;
                }
                
                //Senão retorna TRUE e uma propriedade com o arraye de listas
                $ret->status    = true;
                $ret->msg       = "Listas carregadas";
                $ret->listas    = $rs->getRs();
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
