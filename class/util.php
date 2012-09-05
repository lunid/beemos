<?php
class Util{
    public static function formataData($date, $type = 'DD/MM/AAAA'){
        switch($type){
            case 'DD/MM/AAAA':
                $date_time  = explode(" ", $date);
                $date_f     = explode("-", $date_time[0]);
                return $date_f[2] . "/" . $date_f[1] . "/" . $date_f[0];
                break;
            case 'AAAA-MM-DD':
                $date_time  = explode(" ", $date);
                $date_f     = explode("/", $date_time[0]);
                return $date_f[2] . "-" . $date_f[1] . "-" . $date_f[0];
                break;
            default:
                return $date;
        }
    }
}
?>
