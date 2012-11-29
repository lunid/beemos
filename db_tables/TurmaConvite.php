<?php
    namespace db_tables;    
    
    /**
     * Representa uma entidade da tabela SPRO_TURMA_CONVITE
     * 
     * @property int ID_TURMA_CONVITE
     * @property int ID_TURMA
     * @property int ID_CLIENTE
     * @property int ID_HISTORICO_GERADOC
     * @property char COD_LISTA
     * @property char EMAIL_ENVIADO ENUM(S,N)
     * @property string DATA_ENVIO_EMAIL 
     * @property char ENVIAR_SMS ENUM(S,N)   
     * @property char SMS_ENVIADO ENUM(S,N)   
     * @property string DATA_ENVIO_SMS ENUM(S,N)   
     */
    class TurmaConvite extends \Table {
        
    }
?>
