<?php
/**
 * Classe para gerarmos elementos HTML que precisam interagir com PHP
 * Arquivos baseados em PHTML
 * 
 * @property string $html_path Caminho dos templates HTML padrões
 * @property string $html_path_templates Caminho dos templates HTML personalizados
 * @property string $default_html Arquivo HTML padrão a ser utilizado quando gerar um elemento
 * 
 * @property array $table_data Array de dados para criação de uma <table> HTML
 * @property array $sel_options Array de dados para criação das <options> de um elemento <select>
 * @property string $first_option Primeiro elemento de um <select>
 * @property string $select_option Valor do elemento que deve ser selecionado na criação do <select>

 * @property string $id Valor da propriedade ID do elemento HTML a ser criado
 * @property string $onchange Valor da propriedade ONCHANGE do elemento HTML a ser criado
 * @property string $class Valor propriedade CLASS (css) do elemento HTML a ser criado
 * @property string $style Valor da propriedade STYLE (css) do elemento HTML a ser criado
 * @property boolean $disabled Valor propriedade DISABLE do elemento HTML a ser criado
 * 
 * @author Interbis <interbist@interbits.com.br>
 */
class HtmlComponent {
    static $html_path           = "/sys/classes/comps/html/";
    static $html_path_templates = "/comps/html/";
    static $default_html;
    
    private static $arrMenuOpts;

    private static $table_data;
    
    private static $sel_options;
    private static $select_option;
    private static $first_option;
    
    private static $id;
    private static $onchange;
    private static $class;
    private static $style;
    private static $disabled = FALSE;
    
    /**
     * Cria um elemento HTML <select> e seus <option>
     * 
     * @param array $arr_options Dados a serem carregados nos <option>
     * <code>
     * $arr_options = array( 
     *                      array(
     *                              "ID" => 1, 
     *                              "TEXT" => "Biologia"
     *                      ), 
     *                      array(
     *                              "ID" => 2, 
     *                              "TEXT" => "Física"
     *                      ) 
     *                );
     * </code>
     * 
     * @param stdClass $opts Objeto utilizado para iniciar as propriedades que você deseja usar no elemento HTML
     * <code>
     * $opts = new stdClass();
     * 
     * $opts->id     = "id_do_elemento";
     * $opts->class  = "classe_1 classe_2";
     * </code>
     * 
     * @return string $html Retorna o HTML gerado para o elemento <select>
     */
    public static function select($arr_options, $opts){
        try{
            //Setando propriedades
            self::$sel_options      = $arr_options;
            self::$id               = @$opts->id;
            self::$first_option     = @$opts->first_option;
            self::$onchange         = @$opts->onchange;
            self::$select_option    = @$opts->select_option;
            self::$class            = @$opts->class;
            self::$style            = @$opts->style;
            self::$disabled         = @$opts->disabled;
            
            //Setando template default
            self::$default_html = 'select';
            
            //Renderizando o HTML
            return self::renderHtml(@$opts->html_template);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Cria um elemento HTML <table>
     * 
     * @param array $arr_data Dados para geração de linhas e colunas
     * @param stdClass $opts Objeto utilizado para iniciar as propriedades que você deseja usar no elemento HTML
     * <code>
     * $opts = new stdClass();
     * 
     * $opts->id     = "id_do_elemento";
     * $opts->class  = "classe_1 classe_2";
     * </code>
     * 
     * @return string Retorna o HTML gerado para o elemento <table>
     */
    public static function table($arr_data, $opts){
        try{
            //Setando propriedades
            self::$table_data   = $arr_data;
            self::$class        = @$opts->class;
            
            //Setando template default
            self::$default_html = 'table';
            
            //Renderizando o HTML
            return self::renderHtml(@$opts->html_template);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * 
     * @param string $html_template Nome do arquivo físico a ser processado (PHTML)
     * 
     * @return string HTML porcessado
     */
    private static function renderHtml($html_template = null){
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
            throw $e;
        }
    }
    
    public static function menuHorizontal($arrMenuOpts){
        try{
            //Setando propriedades
            self::$arrMenuOpts  = $arrMenuOpts;
            self::$default_html = 'menuHorizontal';
            
            //Renderizando o HTML
            return self::renderHtml();
        }catch(Exception $e){
            throw $e;
        }
    }
}

?>
