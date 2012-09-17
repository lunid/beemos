<?php
class HtmlComponent {
    static $html_path = "/sys/classes/comps/html/";
    
    public static function select($arr_options){
        try{
            ob_start();
            include(self::$html_path . "select.phtml");
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }catch(Exception $e){
            echo ">>>>>>>>>>>>>>> Erro Fatal - HtmlComponent <<<<<<<<<<<<<<< <br />\n";
            echo "Erro: " . $e->getMessage() . "<br />\n";
            echo "Arquivo:  " . $e->getFile() . "<br />\n";
            echo "Linha:  " . $e->getLine() . "<br />\n";
            echo "<br />\n";
            die;
        }
    }
}

?>
