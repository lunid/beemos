<?php
    namespace app\models\tables; 
    use \sys\classes\db\ORM;
    /**
     * Classe para manipulação de dados da table SPRO_ADM_USUARIO
     * 
     * @property int ID_USUARIO
     * @property string NOME
     * @property string EMAIL
     * @property string SENHA
     * @property int ID_PERFIL
     * @property char STATUS
     * @property datetime DATA_REGISTRO
     * @property datetime ULTIMO_ACESSO
     * 
     */
    class AdmUsuario extends ORM{
        public function verificaUserFacebook(){
            try{
                echo "<pre style='color:#FF0000;'> Teste ";
                print_r($this->FB_ID);
                echo "</pre>";
                //die;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
