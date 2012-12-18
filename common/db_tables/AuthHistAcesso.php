<?php
    namespace common\db_tables;   
    
    /**
     * Representa uma entidade da tabela SPRO_AUTH_HIST_ACESSO
     * 
     * @property int ID_AUTH_HIST_ACESSO
     * @property int ID_LOGIN
     * @property string IP_ORIG
     * @property int ID_MATRIZ
     * @property int ID_FILIAL
     * @property string BROWSER
     * @property string BROWSER_VER
     * @property string PLATAFORMA
     * @property date DATA_REGISTRO
     */
    class AuthHistAcesso extends \Table {
        public function totalAcessos($idLogin){
            try{
                //conta o total de acessos de um Login
                $sql    = "SELECT COUNT(1) AS QTD FROM SPRO_AUTH_HIST_ACESSO WHERE ID_LOGIN = {$idLogin}";
                $rs     = $this->query($sql);
                
                //Se houver retorno...
                if(is_array($rs) && sizeof($rs) > 0){
                    return (int)$rs[0]['QTD'];
                }else{
                    return 0;
                }
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
