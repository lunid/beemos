<?php

/**
 * Classe responsável por guardar/iniciar as configurações de conexão com mySql. 
 * @package api\db
 */
class Conn {

    private $user   = 'superpro';
    private $server = 'localhost';
    private $passwd = '';
   
    /**
     * Inicia a conexão com o DB (utiliza o pattern Singleton).
     * Exemplo:
     * <code>
     *  Conn::init();
     * </code>
     */
    public static function init(){
        $host       = '186.202.152.30';
        $dbName     = 'interbits1';
        $user       = 'interbits1'; 
        $passwd     = 'my230812';
        $local      = TRUE;

        if ($local){
            $host   = 'localhost';
            $dbName = 'interbits1';
            $user   = 'interbits1';
            $passwd = 'my230812';
        }

        DB::$host       = $host;
        DB::$dbName     = $dbName;        
        DB::$user       = $user;
        DB::$password   = $passwd;   
        DB::$encoding   = 'utf8';        
    }
}
?>
