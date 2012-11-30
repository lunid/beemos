<?php
    namespace db_tables;    
    
    /**
     * Classe fake para controles de dados do ENSINO
     */
    class Ensino {
        /**
         * Traduz a sigla do Ensino para o decritivo completo dele
         * 
         * @param string $ensino
         * @return string Descrição do Ensino
         * @throws Exception
         */
        static function traduzirEnsino($ensino){
            try{
                switch($ensino){
                    case 'M': 
                        return 'Médio';
                        break;
                    case 'F': 
                        return 'Fundamental';
                        break;
                    default: 
                        return $ensino;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
