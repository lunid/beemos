<?php

    namespace admin\classes\models\tables;
    use \sys\classes\db\ORM;
    
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
        
        public function carregaInfoAlunosTurma($ID_TURMA){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar informações de Alunos!";
                
                $sql = "SELECT
                            count(1) as QTD
                        FROM
                            SPRO_CLIENTE C
                        INNER JOIN
                            SPRO_TURMA_ALUNO TA ON TA.ID_CLIENTE = C.ID_CLIENTE
                        WHERE
                            TA.ID_TURMA = {$ID_TURMA}
                        ";
                            
                $rs = $this->query($sql);
                
                echo "<pre style='color:#FF0000;'>";
                print_r($rs);
                echo "</pre>";
                die;
                
                //VErifica resultado retornado
                if($rs['QTD'] <= 0){
                    $ret->msg = "Nenhum Aluno cadastrado nesta Turma!";
                }else{
                    $ret->status    = true;
                    $ret->msg       = "dados carregado com sucesso!";
                    $ret->qtd       = $rs['QTD'];
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>

