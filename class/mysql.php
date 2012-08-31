<?php
/**
 * Classe (temporária) para conexão ao banco de dados.
 */
class MySQL{
    public static $conn;
    
    public function __construct() {
        MySQL::connect();
    }
    
    public static function connect(){
        if(MySQL::$conn == null){
            MySQL::$conn = mysql_connect("186.202.152.30", "interbits1", "my230812")or die("Falha ao conectar no Banco de Dados MySQL > " . mysql_error());
            mysql_select_db("interbits1")or die("Falha ao selecionar Banco de Dados MySQL > " . mysql_error());
        }
    }
    
    public static function executeQuery($query){
        if(MySQL::$conn == null){
            MySQL::connect();
        }
        
        $rs = mysql_query($query)or die(">>> Falha ao executar query MySQL <br />>>> {$query}<br />>>> " . mysql_error());
        return $rs;
    }
}
?>
