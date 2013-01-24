<?php

    namespace auth\classes\helpers;
    
    /**
     * Classe que encapsula os dados do usuário logado e implementa comportamentos 
     * que permitem extrair informações adicionais desse usuário (por exemplo, saldo e desconto na renovação)
     * 
     * IMPORTANTE: os campos recebidos no construtor devem estar baseados nos campos 
     * da tabela USER, pois alguns métodos utilizam um ou mais campos conhecidos dessa tabela.
     * 
     * @see auth/classes/models/AuthModel
     */
    class Usuario {
        
        var $objUser = NULL;//Guarda um array unidimensional com os campos do registro de um usuário.
        
        function __construct($objUser) {
            if (is_object($objUser)) $this->objUser = $objUser;//Campos da tabela USER de um usuário conhecido.
        }        
        
        /**
         * Retorna o saldo de créditos do usuário atual.
         * 
         * @return integer;
         */
        function getSaldoCreditos(){
            
        }
        
        
        /**
         * Verifica se o usuário atual possui desconto por indicação
         * 
         * @return integer O valor retornado refere-se ao percentual de desconto, caso exista.
         */
        function getDescontoIndicacao(){
            $percentDesc = 0;
            
        }
        
        function __get($var){
            $objUser    = $this->objUser;            
            $value      = '';            
            if (is_object($objUser)) $value = $objUser->$var;
            return $value;            
        }
    }
?>
