<?php
    namespace admin\classes\models;
    use \sys\classes\util\Date;
    use \sys\classes\mvc\Model;    
    use \db_tables as TB;
    
    class CaixaPostalModel extends Model {
        public function carregarAlunosPara($ID_CLIENTE, $where = '', $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar lista de Alunos!";
                
                //Valida ID_CLIENTE
                if((int)$ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE inválido ou nulo!";
                    return $ret;
                }
                
                //Instância da table SPRO_TURMA
                $tbTurma  = new TB\Turma;
                
                //Carrega alunos do cliente
                $ret      = $tbTurma->listarAlunosCliente($ID_CLIENTE, $where, $arrPg);
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
    
?>
