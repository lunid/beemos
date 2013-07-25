<?php

    namespace commerce\classes\models;
    use \sys\classes\mvc\Model;  
    use \auth\classes\helpers\ErrorHelper;
    use \common\db_tables as TB;    
    
    class EcommModel extends Model {   

        function findUserForHash($hash){
            $row = array();
            if (strlen($hash) >= 32) {
                $tbEcommCfg = new TB\EcommCfgBradesco();     
                $row        = $tbEcommCfg->select('*')
                            ->where("HASH_USER = '{$hash}'")
                            ->execute();
            }
            return $row;
        }
    }

?>
