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
        
        /**
         * Recebe um número de CNPJ e forma ela no padrão 99.999.999/9999-99
         * 
         * @param int $number Número do CNPJ sem formatação
         * @return type
         */
        public static function formatCNPJ($number){
            return substr($number, 0, 2) . "." . substr($number, 2, 3) . "." . substr($number, 5, 3) . "/" . substr($number, 8, 4) . "-" . substr($number, 12, 2);
        }
        
        public static function formatTel($number){
            if(strlen($number) > 10){
                return "(" . substr($number, 0, 2) . ") " . substr($number, 2, 5) . "-" . substr($number, 7);
            }else{
                return "(" . substr($number, 0, 2) . ") " . substr($number, 2, 4) . "-" . substr($number, 6);
            }
        }
    }

?>
