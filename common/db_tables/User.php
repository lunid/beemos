<?php
    namespace common\db_tables;   
    
    /**
     * Representa uma entidade da tabela SPRO_USER
     * 
     * @property int ID_USER
     * @property int ID_USER_PERFIL
     * @property int ID_CAMPANHA_ORIG_CAD
     * @property int ID_MATRIZ
     * @property string NOME
     * @property string APELIDO
     * @property string EMAIL
     * @property int CELULAR
     * @property int DDD_CELULAR
     * @property string LOGIN
     * @property string PASSWD
     * @property string PASSWD_TEMPORARY
     * @property date BLOQUEADO_EM
     * @property date EXCLUIDO_EM
     * @property date DT_HR_UPD_PASSWD
     * @property date DT_HR_ULTIMO_ACESSO
     * @property date DATA_REGISTRO
     */
    class User extends \Table {
        
    }
?>
