<?php

    namespace site\classes\helpers;
    
    class BoxPlanoHelper {
        
        private $codPlano = 'INDEF';
        
        function __construct($codPlano){
            if (strlen($codPlano) && ctype_alnum($codPlano))$this->codPlano = $codPlano;
        }
        
    }
?>
