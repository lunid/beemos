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
    
    private static $dadosEmail;
    
    private static $table_data;
    
    private static $sel_options;
    private static $select_option;
    private static $first_option;
    
    private static $id;
    private static $onchange;
    private static $class;
    private static $style;
    private static $disabled = FALSE;
    private static $field_name;
    
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
            self::$field_name       = @$opts->field_name;
            
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
            $defaultTemplate = 'table';
            
            $htmlTemplate   = (isset($opts->html_template))?$opts->html_template:$defaultTemplate;
            
            //Renderizando o HTML            
            return self::renderHtml($htmlTemplate);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Função que efetua captura do HTML Template e executa o PHP inserido nele
     * 
     * @param string $html_template Nome do arquivo físico a ser processado (PHTML)
     * 
     * @return string HTML porcessado
     */
    protected static function renderHtml($htmlTemplate){
        try{
           $phtmlFile = self::$html_path . self::$default_html . ".phtml";
           $pathPhtml  = \Url::absolutePath($phtmlFile);
              
            $phtmlFile   = \Application::getModule().'/comps/html/'. "{$htmlTemplate}.phtml";
            $pathPhtml   = \Url::physicalPath($phtmlFile);            

            ob_start();
            if (!@include($pathPhtml)) {                    
                throw new Exception (__METHOD__."(): Arquivo {$pathPhtml} não existe.");                    
            }
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Função que monta o HTML do menu Horinzotal do APP > Site
     * 
     * @param type $arrMenuOpts
     * <code>
     * array(
     *      "menu_home" => array( //menu_home será o ID do elemento HTML
     *          "href"      => "/",
     *          "titulo"    => "Home",
     *          "subTitulo" => "Bem vindo",
     *          "ativo"     => false
     *      )
     * );
     * </code>
     * @return string HTML processado para exibição
     * @throws Exception
     */
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
    
    public static function menuVertical($arrMenuOpts){
        try{
            //Setando propriedades
            self::$arrMenuOpts  = $arrMenuOpts;
            self::$default_html = 'menuVertical';
            
            //Renderizando o HTML
            return self::renderHtml();
        }catch(Exception $e){
            throw $e;
        }
    }    
    
    /**
     * Cria o HTML de E-mail contato automático que o usuário recebe.
     * 
     * @param array $dadosEmail
     * <code>
     * array(
     *      "nome"  => "Marcelo",
     *      "msg"   => "Mensagem",
     *      "email" => "marcelo@teste.com"
     * )
     * </code>
     * @return string Html do e-mail processado.
     * @throws Exception
     */
    public static function emailContatoUser($dadosEmail){
        try{
            //Setando propriedades
            self::$dadosEmail   = $dadosEmail;
            self::$default_html = 'email_contato';
            
            //Renderizando o HTML
            return self::renderHtml();
        }catch(Exception $e){
            throw $e;
        }   
    }
    
    /**
     * Cria o HTML de E-mail Suporte automático que o usuário recebe.
     * 
     * @param array $dadosEmail
     * <code>
     * array(
     *      "nome"  => "Marcelo",
     *      "msg"   => "Mensagem",
     *      "email" => "marcelo@teste.com"
     * )
     * </code>
     * @return string Html do e-mail processado.
     * @throws Exception
     */
    public static function emailSuporteUser($dadosEmail){
        try{
            //Setando propriedades
            self::$dadosEmail   = $dadosEmail;
            self::$default_html = 'email_suporte';
            
            //Renderizando o HTML
            return self::renderHtml();
        }catch(Exception $e){
            throw $e;
        }   
    }
    
    /**
     * Cria o HTML de E-mail Contato/Suporte automático enviado via Site pelo usuário.
     * 
     * @param array $dadosEmail
     * <code>
     * array(
     *      "nome"  => "Marcelo",
     *      "msg"   => "Mensagem",
     *      "email" => "marcelo@teste.com"
     * )
     * </code>
     * @return string Html do e-mail processado.
     * @throws Exception
     */
    public static function emailContatoSite($dadosEmail){
        try{
            //Setando propriedades
            self::$dadosEmail   = $dadosEmail;
            self::$default_html = 'email_contato_site';
            
            //Renderizando o HTML
            return self::renderHtml();
        }catch(Exception $e){
            throw $e;
        }   
    }
    
    public static function barraTopo(){
        try{
            //Setando propriedades
            self::$default_html = 'barraTopo';
            
            //Renderizando o HTML
            return self::renderHtml();
        }catch(Exception $e){
            throw $e;
        }   
    }
}

?>
