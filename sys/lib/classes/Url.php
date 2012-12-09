<?php

        namespace sys\lib\classes;
        
        class Url {
            
            public static function __callStatic($func,$args){                
                $path = '';                           
                
                if ($func == 'exceptionClassXml'){
                    $class  = (isset($args[0]))?$args[0]:'exception';
                    $path   = str_replace('\\','/',$class);
                    $path   = str_replace('classes/','/dic/e',$path);
                    $path   .= '.xml';
                } else {
                    $folderSys = \LoadConfig::folderSys();    
                    $folder    = (isset($args[0]))?$args[0].'/':'';
                    $rootComp  = $folderSys.'/lib/'.$folder;
                    if ($func == 'exceptionXml') {
                        $path  = $rootComp.'dic/exception.xml';
                    } elseif ($func == 'installXml') {
                        $path     = $rootComp.'/install.xml';                        
                    }                     
                }
                return $path;
            }
        }
        
?>
