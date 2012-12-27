<?php
    namespace common\classes;

    /**
     * Classe para funções de utilidade pública
     */
    class Util {
        /**
         * Valida o REGEX de um E-mail - Estrutura
         * 
         * @param string $email
         * 
         * @return boolean
         * @throws Exception
         */
        static function validaEmail($email){
            try{
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    return true;
                }else{
                    return false;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Formata uma string de Data
         * 
         * @param string $data Data a ser formatada
         * @param string $type Formato de saída. Ex: DD/MM/AAAA
         * 
         * @return string
         */
        static function formataData($data, $type = "DD/MM/AAAA"){
            switch (strtoupper($type)){
                case 'DD/MM/AAAA':
                    $dataTmp = explode(' ', $data);
                    $dataTmp = explode('-', $dataTmp[0]);
                    return $dataTmp[2] . "/" . $dataTmp[1] . "/" . $dataTmp[0];
                    break;
                case 'DD/MM/AAAA HH:MM:SS':
                    $dataTimeTmp = explode(' ', $data);
                    $data = explode('-', $dataTimeTmp[0]);
                    return $data[2] . "/" . $data[1] . "/" . $data[0] . " " . $dataTimeTmp[1];
                    break;
                default:
                    return $data;
            }
        }
        
        static function limpaTel($tel){
            $tel = trim($tel);
            $tel = str_replace("(", "", $tel);
            $tel = str_replace(")", "", $tel);
            $tel = str_replace("-", "", $tel);
            
            return $tel;
        }
    }
?>