<?php

    namespace admin\models\tables;
    use \sys\classes\db\ORM;

    class AdmUsuario extends ORM {
        public function getUsuariosQuestao($in_materias){
            try{
                $arr_usuarios = array();
                
                $sql = "SELECT
                            U.ID_USUARIO,
                            U.NOME
                        FROM
                            SPRO_ADM_USUARIO U
                        INNER JOIN
                            SPRO_ADM_USUARIO_MATERIA UM ON UM.ID_USUARIO = U.ID_USUARIO
                        WHERE
                            UM.ID_MATERIA IN ({$in_materias})
                        AND
                            ID_PERFIL = 2
                        ;";
                
                $rs_usuario = $this->query($sql);
                
                if(sizeof($rs_usuario) > 0){
                    foreach ($rs_usuario as $usuario) {
                        $arr_usuarios[] = $usuario;
                    }
                }
                            
                return $arr_usuarios;
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal - AdmUsuarios - Model <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
    }

?>

