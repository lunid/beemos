<?php
    namespace common\db_tables;   
    
    /**
     * Classe fake para controles de dados do PERIODO
     */
    class Periodo {
        /**
         * Traduz a sigla do Período para o descritivo completo dele 
         * 
         * @param type $periodo
         * @return string Descrição do período
         * @throws Exception
         */
        static function traduzirPeriodo($periodo){
            try{
                switch($periodo){
                    case 'M': 
                        return 'Manhã';
                        break;
                    case 'T': 
                        return 'Tarde';
                        break;
                    case 'N': 
                        return 'Noite';
                        break;
                    default: 
                        return $periodo;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
