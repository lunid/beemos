<?php

namespace sys\classes\util;
class Cache {
    public static function init(){
        $m = new \Memcached();//Conectando...
        $m->addServer('localhost', 11211);
        return $m;
    }
    
    public static function getVarMemCache($varCache){
        $m = self::init();        
        return $m->get($varCache);
    }
    
    public static function setVarMemCache($varCache,$value){
        $m      = self::init();  
        $out    = FALSE;
        if (!$m->get($varCache)) {
            if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
                // dados não encontrados. Guarda no cache
                $m->set($varCache, $value);
                $out = TRUE;
            }
        }
        return $out;        
    }
}
?>
