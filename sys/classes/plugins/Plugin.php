<?php

    namespace sys\classes\plugins;
    use \sys\classes\util\IncludeConfig;

    class Plugin extends IncludeConfig {
        static $root = 'sys/plugins/';

	public static function __callStatic($fn,$value){
            $folderPlugin = $fn;
            switch($fn){
                case 'menuHorizontal':
                    $folderPlugin = 'menu';
                    break;
                case 'menuIdiomas':
                    $folderPlugin = 'dropdown';
                    break;
            }                    
             
            $root = self::$root.$folderPlugin;
            return self::loadConfig($root);	
	}
    }    
?>
