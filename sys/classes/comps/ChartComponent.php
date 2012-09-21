<?php
class ChartComponent {
    static $chartData       = null;
    static $html_path       = "/sys/classes/comps/chart/";
    static $default_html;
    
    public static function gerGraficoTop10($data){
        self::$default_html = "top10";
        self::$chartData = $data;
        
        return self::renderHtml();
    }
    
    private static function renderHtml(){
        try{
            $arq = self::$html_path . self::$default_html . ".phtml";
            
            ob_start();
            include($arq);
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
