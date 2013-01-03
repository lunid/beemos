<?php
    namespace common\classes\models;
    
    use \sys\classes\mvc\Model;    
    use \common\db_tables as TB;
    
    class UfModel extends Model{
        public function carregarUfCombo(){
            //Objeto de retorno 
            $ret            = new \stdClass();
            $ret->status    = false;
            $ret->msg       = "Falha ao carregar Estados!";
            
            //Tablea SPRO_UF
            $tbUf = new TB\Uf();
            
            //Ordenação e consulta
            $tbUf->setOrderBy("SIGLA");
            $rs = $tbUf->findAll();
            
            //Valida retorno 
            if($rs->count() <= 0){
                $ret->msg = "Nenhum Estado encontrado!";
                return $ret;
            }
            
            //Monta array de opções para Combobox HTML
            $arrOptions = array();
            $estados    = $rs->getRs();
            
            foreach($estados as $estado){
                $arrOptions[] = array($estado->SIGLA,$estado->SIGLA);
            }
            
            //Retorno OK
            $ret->status    = true;
            $ret->msg       = "Estados carregados com sucesso!";
            $ret->estados   = $arrOptions;
            
            return $ret;
        }
    }
?>