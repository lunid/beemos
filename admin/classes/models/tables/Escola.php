<?php
    namespace admin\classes\models\tables;
    use \sys\classes\db\ORM;
    
    /**
     * Representa uma entidade da tabela SPRO_ESCOLA
     * 
     * @property int ID_ESCOLA
     * @property int ID_CLIENTE
     * @property string NOME
     * @property bool STATUS
     * @property datetime DATA_REGISTRO
     */
    class Escola extends ORM {
        
    }
?>
