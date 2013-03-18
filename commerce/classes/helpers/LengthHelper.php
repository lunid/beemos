<?php

namespace commerce\classes\helpers;

class LengthHelper {
    
    /**
     * Recebe um código/número e acrescenta, se necessário, zeros à esquerda
     * para compor um código com a quantidade de caracteres solicitada.
     * Cada convênio bancário exige uma quantidade de dígitos específica.
     * 
     * @param string $value Código alfanumérico
     * @param integer $numChars Tamanho em caracteres que deve ter o código solicitado.
     * @return string
     */
    public static function format($value,$numChars=0){        
        if ($numChars > 0) $value = str_pad($value, $numChars, '0', STR_PAD_LEFT);
        return $value;        
    }
}

?>
