<?php

    namespace common\classes;
    
    class Menu {
        
        public static function main($activeController='index'){
            $arrNav     = array();
            $arrNav[]   = array('controller'=>'Index','link'=>'index','text'=>'HOME');
            $arrNav[]   = array('controller'=>'Recursos','link'=>'recursos','text'=>'RECURSOS');
            $arrNav[]   = array('controller'=>'sobre','link'=>'sobre','text'=>'SOBRE NÓS');
            $arrNav[]   = array('controller'=>'PlanosEprecos','link'=>'planosEprecos','text'=>'PLANOS & PREÇOS');
            $arrNav[]   = array('controller'=>'Ajuda','link'=>'ajuda','text'=>'CENTRAL DE AJUDA');
            
            $nav = "<ul>";
            foreach($arrNav as $arrItem){
                $controller = $arrItem['controller'];
                $link       = $arrItem['link'];
                $text       = $arrItem['text'];
                $class      = (strtoupper($controller) == strtoupper($activeController))?"class='active'":'';
                $nav        .= "<li><a href='".APPLICATION_PATH.$link."' {$class}>{$text}</a></li>";
            }
            $nav .= "</ul>";
            return $nav;
        }
    }
?>
