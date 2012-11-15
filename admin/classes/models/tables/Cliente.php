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
        
        /**
         * Função que calcula o Total de Alunos e Total de Alunos com Celular em uma Turma
         * 
         * @param int $ID_TURMA Código da Turma
         * 
         * @return \stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  int     $ret->qtd       - Número total de alunos em uma turma               <br />
         *  int     $ret->qtdCel    - Número total de alunos com celular em uma turma               <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregaInfoAlunosTurma($ID_TURMA){
            try{
                //Objeto de retorno 
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar informações de Alunos!";
                
                $sql = "SELECT
                            (SELECT count(1) FROM SPRO_CLIENTE C INNER JOIN SPRO_TURMA_ALUNO TA ON TA.ID_CLIENTE = C.ID_CLIENTE WHERE TA.ID_TURMA IN ({$ID_TURMA})) AS QTD,
                            (SELECT count(1) FROM SPRO_CLIENTE C INNER JOIN SPRO_TURMA_ALUNO TA ON TA.ID_CLIENTE = C.ID_CLIENTE WHERE TA.ID_TURMA IN ({$ID_TURMA}) AND FONE_CELULAR IS NOT NULL ) AS QTD_CELULAR
                       ";
                            
                $rs = $this->query($sql);
                $rs = $rs[0]; //Armazena apena sprimeira linha
                
                //Verifica resultado retornado
                if($rs['QTD'] <= 0){
                    $ret->msg = "Nenhum Aluno cadastrado na(s) Turma(s) selecionada(s)!";
                }else{
                    $ret->status    = true;
                    $ret->msg       = "dados carregado com sucesso!";
                    $ret->qtd       = $rs['QTD'];
                    $ret->qtdCel    = $rs['QTD_CELULAR'];
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>

