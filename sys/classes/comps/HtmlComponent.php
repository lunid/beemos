<?php
class HtmlComponent {
    static $html_path           = "/sys/classes/comps/html/";
    static $html_path_templates = "/comps/html/";
    static $default_html;
    
    private static $table_data;
    
    private static $sel_options;
    private static $select_option;
    
    private static $id;
    private static $first_option;
    private static $onchange;
    private static $class;
    private static $style;
    private static $disabled;
    
    public static function select($arr_options, $id, $first_option = null, $onchange = null, $select_option = 0, $class = null, $style = null, $disabled = FALSE, $html_template = null){
        try{
            //Setando propriedades
            self::$sel_options      = $arr_options;
            self::$id               = $id;
            self::$first_option     = $first_option;
            self::$onchange         = $onchange;
            self::$select_option    = $select_option;
            self::$class            = $class;
            self::$style            = $style;
            self::$disabled         = $disabled;
            
            //Setando template default
            self::$default_html = 'select';
            
            //Renderizando o HTML
            return self::renderHtml($html_template);
        }catch(Exception $e){
            echo ">>>>>>>>>>>>>>> Erro Fatal - HtmlComponent <<<<<<<<<<<<<<< <br />\n";
            echo "Erro: " . $e->getMessage() . "<br />\n";
            echo "Arquivo:  " . $e->getFile() . "<br />\n";
            echo "Linha:  " . $e->getLine() . "<br />\n";
            echo "<br />\n";
            die;
        }
    }
    
    public static function table($arr_data, $class = null, $html_template = null){
        try{
            //Setando propriedades
            self::$table_data   = $arr_data;
            self::$class        = $class;
            
            //Setando template default
            self::$default_html = 'table';
            
            //Renderizando o HTML
            return self::renderHtml($html_template);
        }catch(Exception $e){
            echo ">>>>>>>>>>>>>>> Erro Fatal - HtmlComponent <<<<<<<<<<<<<<< <br />\n";
            echo "Erro: " . $e->getMessage() . "<br />\n";
            echo "Arquivo:  " . $e->getFile() . "<br />\n";
            echo "Linha:  " . $e->getLine() . "<br />\n";
            echo "<br />\n";
            die;
        }
    }
    
    private static function renderHtml($html_template){
        try{
            if($html_template != null){
                $arq = "/" . __APP__ . self::$html_path_templates . "{$html_template}.phtml";
            }else{
                $arq = self::$html_path . self::$default_html . ".phtml";
            }
            
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
