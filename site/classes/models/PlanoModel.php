<?php

    namespace site\classes\models;
    use \sys\classes\mvc\Model;  
    use \common\db_tables as TB;
    
    class PlanoModel  extends Model {
        
        /**
         * Localiza/retorna as informações de um plano a partir de seu código.
         * 
         * @param string $codPlano Ex.: BASICO, PROFISSIONAL, CORPORATIVO.
         * @return mixed[] ou NULL caso nenhum registro tenha sido encontrado.
         */
        function getInfoPlano($codPlano){            
            $row            = NULL;
            $tbEcommPlano   = new TB\EcommPlano();
            $codPlano       = strtoupper($codPlano);
            $results        = $tbEcommPlano->select('*')->where("COD_PLANO = '{$codPlano}'")->execute();
            if (count($results) > 0) $row = $results[0];
            return $row;
        }
    }
?>
