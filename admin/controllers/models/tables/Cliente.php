<?php

    namespace app\models\tables;
    use \sys\db\ORM;
    
    /**
     * Representa uma entidade da tabela SPRO_CLIENTE
     * @method void setNomePrincipal(string $nome)
     * 
     * @property int ID_CLIENTE
     * @property string HASH
     * @property string NOME_PRINCIPAL
     * @property string EMAIL
     * @property string LOGIN
     * @property string SENHA
     * @property string PF_PJ
     * @property datetime DATA_REGISTRO
     */

    class Cliente extends ORM {
        function joinUf(){

            self::debugOn();

            //Objeto atual
            $objA               = $this;
            $objA->alias        = 'a';    
            //$objA->fieldsJoin   = 'NOME_PRINCIPAL,LOGIN,EMAIL';

            //UF
            $objB              = new Uf();
            //$objB->fieldsJoin  = 'UF';
            $objB->alias       = 'b';                

            //$arrFields[] = 'ID_UF=ID_UF';
            //$arrFields[] = 'ID_CLIENTE=ID_LOGIN';
            $arrFields      = 'ID_UF';
            $a = $this->joinFrom($objA,$objB,$arrFields);                
            //$b = $this->joinFrom($objUf,'b');
            //$this->setOrderBy('UF ASC');
            //$this->setLimit(10);
            $this->setJoin("b.UF='SP'");                
        }
    }

?>

