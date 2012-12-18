<?php
    namespace common\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \common\db_tables as TB;
    
    class AdmUsuariosModel extends Model{
        public function listarUsuariosMatriz($idMatriz, $where = '', $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao listar usuários!";
                
                //Table SPRO_CLIENTE
                $tbAdmUser = new TB\AdmUsuario();
                
                //Paginação e Ordenação
                $order = "";
                $limit = "";                
                if($arrPg != null){
                    //Monta ordenação
                    if(isset($arrPg['campoOrdenacao']) && isset($arrPg['tipoOrdenacao'])){
                        $order = $arrPg['campoOrdenacao'] . " " . $arrPg['tipoOrdenacao'];
                    }
                    
                    //Monta paginação
                    if(isset($arrPg['inicio']) && isset($arrPg['limite'])){
                        $tbAdmUser->setLimit($arrPg['inicio'], $arrPg['limite']);
                    }
                }else{
                    $order = " ORDER BY NOME ";
                }
                
                //Monta order no SQL               
                $tbAdmUser->setOrderBy($order);

                $rs = $tbAdmUser->findAll("ID_MATRIZ = {$idMatriz} {$where}");
                
                if($rs->count() > 0){
                    $ret->status    = true;
                    $ret->msg       = "Usuários listados com sucesso!";
                    $ret->usuarios  = $rs->getRs();
                }else{
                    $ret->msg = "Nenhum usuário encontrado!";
                }
                
                //Retorno
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
