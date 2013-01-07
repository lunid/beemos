<?php
    namespace sys\classes\util;
    
    class Number {
        /**
         * Retira caracteres de um número de telefone, cpf, cnpj, rg e retorna apenas números
         * 
         * @param string $number
         * @return type
         */
        public static function clearNumber($number){
            $number = trim($number);
            $number = str_replace(" ", "", $number);
            $number = str_replace("(", "", $number);
            $number = str_replace(")", "", $number);
            $number = str_replace("-", "", $number);
            $number = str_replace("+", "", $number);
            $number = str_replace(".", "", $number);
            $number = str_replace(",", "", $number);
            $number = str_replace("/", "", $number);
            
            return $number;
        }
    }

?>
