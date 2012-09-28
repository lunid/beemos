<?php

    namespace app\models\tables;
    use \sys\classes\db\ORM;
    
    /**
     * Representa uma entidade da tabela SPRO_CATEGORIA
     * 
     * @property int ID_CATEGORIA
     * @property string DESCRICAO
     * @property string EMAIL_CONTATO
     */
    class Categoria extends ORM {
        /**
         * Seleciona todas as categorias da Base de dados
         * 
         * @return RecordSet
         * @throws Exception
         */
        public function getCategoriasSelectBox(){
            try{
                $sql = "SELECT
                            ID_CATEGORIA AS ID,
                            DESCRICAO AS TEXT
                        FROM
                            SPRO_CATEGORIA
                        ORDER BY
                            DESCRICAO
                        ;";
                
                return $this->query($sql);
            }catch(Exception $e){
                throw $e;
            }
        }
        
        
        public function carregaEmailCategoria($ID_CATEGORIA = 0){
            try{
                $ret            = new \stdClass();
                $ret->status    = true;
                
                if((int)$ID_CATEGORIA <= 0){
                    $ret->status    = false;
                    $ret->msg       = "Código da categoria inválido!";
                    return $ret;
                }
                
                $sql = "SELECT
                            EMAIL_CONTATO
                        FROM
                            SPRO_CATEGORIA
                        WHERE
                            ID_CATEGORIA = " . $ID_CATEGORIA . "
                        ORDER BY
                            DESCRICAO
                        LIMIT
                            1
                        ;";
                
                $rs = $this->query($sql);
                
                if(sizeof($rs) > 0){
                    $ret->email = $rs[0]['EMAIL_CONTATO'];
                }else{
                    $ret->msg = "Nenhum e-mail encontrado!";
                    return $ret;
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>

