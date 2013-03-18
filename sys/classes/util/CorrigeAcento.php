<?php
    namespace sys\classes\util;

    class CorrigeAcento{
            
            private $latin1_to_utf8;
            private $utf8_to_latin1;          
            
            function __construct(){
                for($i=32; $i<=255; $i++) {
                    $this->latin1_to_utf8[chr($i)] = utf8_encode(chr($i));
                    $this->utf8_to_latin1[utf8_encode(chr($i))] = chr($i);
                }	                
            }

            public static function getEncode($str){
                return mb_detect_encoding($str);
            }
            
            function mixed_to_latin1($text) {
                foreach( $this->utf8_to_latin1 as $key => $val ) {
                    $text = str_replace($key, $val, $text);
                }
                return $text;
            }

            function mixed_to_utf8($text) {
                return utf8_encode($this->mixed_to_latin1($text));
            }
            
	}
?>