<?php
    namespace db_tables;  

    class AvaliacaoQuestao extends \Table {
        /**
         * Exclui todas as avaliações de uma questão.
         * 
         * @param int $id_questao
         * @return boolean
         * @throws Exception
         */
        public function excluiQuestao($id_questao, $id_usuario){
            try{
                $arrWhere = array("ID_BCO_QUESTAO = %i AND ID_USUARIO = %i", $id_questao, $id_usuario);
                $this->delete($arrWhere);
                
                return TRUE;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function salvaAvaliacaoQuestao(){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar avaliação! Tente mais tarde.";
                
                //Valida valor ID_USUARIO
                if($this->ID_USUARIO <= 0 || $this->ID_USUARIO == null){
                    $ret->msg = "O campo ID_USUARIO é obrigatório para salvar a Avaliação";
                    return $ret;
                }

                //Valida valor ID_BCO_QUESTAO
                if($this->ID_BCO_QUESTAO <= 0 || $this->ID_BCO_QUESTAO == null){
                    $ret->msg = "O campo ID_BCO_QUESTAO é obrigatório para salvar a Avaliação";
                    return $ret;
                }

                //Validação de NOTAS_
                if($this->NOTA_ENUNCIADO <= 0 || $this->NOTA_ENUNCIADO == null){
                    $ret->msg = "O campo NOTA_ENUNCIADO é obrigatório para salvar a Avaliação";
                    return $ret;
                }

                if($this->NOTA_ABRANGENCIA <= 0 || $this->NOTA_ABRANGENCIA == null){
                    $ret->msg = "O campo NOTA_ABRANGENCIA é obrigatório para salvar a Avaliação";
                    return $ret;
                }

                if($this->NOTA_ILUSTRACAO <= 0 || $this->NOTA_ILUSTRACAO == null){
                    $ret->msg = "O campo NOTA_ILUSTRACAO é obrigatório para salvar a Avaliação";
                    return $ret;
                }

                if($this->NOTA_INTERDISCIPLINARIDADE <= 0 || $this->NOTA_INTERDISCIPLINARIDADE == null){
                    $ret->msg = "O campo NOTA_INTERDISCIPLINARIDADE é obrigatório para salvar a Avaliação";
                    return $ret;
                }

                if($this->NOTA_HABILIDADE_COMPETENCIA <= 0 || $this->NOTA_HABILIDADE_COMPETENCIA == null){
                    $ret->msg = "O campo NOTA_HABILIDADE_COMPETENCIA é obrigatório para salvar a Avaliação";
                    return $ret;
                }
                
                if($this->NOTA_ORIGINALIDADE <= 0 || $this->NOTA_ORIGINALIDADE == null){
                    $ret->msg = "O campo NOTA_ORIGINALIDADE é obrigatório para salvar a Avaliação";
                    return $ret;
                }
                
                $this->DATA_AVALIACAO = date("Y-m-d H:i:s");
                $this->save();
                
                $ret->status    = true;
                $ret->msg       = "Avaliação salva com sucesso!";
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function carregaAvaliacaoQuestao(){
            try{
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar avaliação!";
                
                //Valida valor ID_BCO_QUESTAO
                if($this->ID_BCO_QUESTAO <= 0 || $this->ID_BCO_QUESTAO == null){
                    $ret->msg = "O campo ID_BCO_QUESTAO é obrigatório para carregar a Avaliação";
                    return $ret;
                }
                
                $objTableAvaliacao                   = $this;
                $objTableAvaliacao->alias            = 'AQ';
                $objTableAvaliacao->fieldsJoin       = 'ID_BCO_QUESTAO, 
                                                        ID_USUARIO, 
                                                        NOTA_ENUNCIADO, 
                                                        SOBRE_ENUNCIADO, 
                                                        NOTA_ABRANGENCIA, 
                                                        SOBRE_ABRANGENCIA, 
                                                        NOTA_ILUSTRACAO,
                                                        SOBRE_ILUSTRACAO,
                                                        NOTA_INTERDISCIPLINARIDADE,
                                                        SOBRE_INTERDISCIPLINARIDADE,
                                                        NOTA_HABILIDADE_COMPETENCIA,
                                                        SOBRE_HABILIDADE_COMPETENCIA,
                                                        NOTA_ORIGINALIDADE,
                                                        SOBRE_ORIGINALIDADE,
                                                        DATA_AVALIACAO';
                
                $objAdmUsuario                      = new AdmUsuario();
                $objAdmUsuario->alias               = 'U';
                $objAdmUsuario->fieldsJoin          = 'NOME';
                
                $fieldMap = "ID_USUARIO";
                $this->innerJoinFrom($objTableAvaliacao, $objAdmUsuario, $fieldMap);
                
                $rs = $this->setJoin("ID_BCO_QUESTAO = " . $this->ID_BCO_QUESTAO);
                
                if(is_array($rs) && sizeof($rs) == 1){
                    $ret->ID_USUARIO                   = $rs[0]['ID_USUARIO'];
                    $ret->ID_BCO_QUESTAO               = $rs[0]['ID_BCO_QUESTAO'];
                    $ret->NOTA_ENUNCIADO               = $rs[0]['NOTA_ENUNCIADO'];
                    $ret->SOBRE_ENUNCIADO              = $rs[0]['SOBRE_ENUNCIADO'];
                    $ret->NOTA_ABRANGENCIA             = $rs[0]['NOTA_ABRANGENCIA'];
                    $ret->SOBRE_ABRANGENCIA            = $rs[0]['SOBRE_ABRANGENCIA'];
                    $ret->NOTA_ILUSTRACAO              = $rs[0]['NOTA_ILUSTRACAO'];
                    $ret->SOBRE_ILUSTRACAO             = $rs[0]['SOBRE_ILUSTRACAO'];
                    $ret->NOTA_INTERDISCIPLINARIDADE   = $rs[0]['NOTA_INTERDISCIPLINARIDADE'];
                    $ret->SOBRE_INTERDISCIPLINARIDADE  = $rs[0]['SOBRE_INTERDISCIPLINARIDADE'];
                    $ret->NOTA_HABILIDADE_COMPETENCIA  = $rs[0]['NOTA_HABILIDADE_COMPETENCIA'];
                    $ret->SOBRE_HABILIDADE_COMPETENCIA = $rs[0]['SOBRE_HABILIDADE_COMPETENCIA'];
                    $ret->NOTA_ORIGINALIDADE           = $rs[0]['NOTA_ORIGINALIDADE'];
                    $ret->SOBRE_ORIGINALIDADE          = $rs[0]['SOBRE_ORIGINALIDADE'];
                    $ret->DATA_AVALIACAO               = $rs[0]['DATA_AVALIACAO'];
                    $ret->NOME_USUARIO                 = $rs[0]['NOME'];
                    
                    $ret->status = true;
                    $ret->msg    = "Avaliação carregada com sucesso!";
                }
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }

?>
