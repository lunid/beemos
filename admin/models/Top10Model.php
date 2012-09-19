<?php
    namespace admin\models;
    use \sys\classes\mvc\Model;        
    use \sys\classes\util\Date;
    use \admin\models\tables\BcoQuestao;
    use \admin\models\tables\MateriaQuestao;
    use \admin\models\tables\FonteVestibular;
    use \admin\models\tables\ClassificacaoQuestao;
    use \admin\models\tables\AdmUsuario;
    use \admin\models\tables\AdmTop10Log;
    use \sys\db\ORM; 
    
    class Top10Model extends Model {
        public function listaQuestoesTop10($id_materia = 0, $id_fonte_vestibular = 0){
            try{
                $ret = array();
                
                $questao = new BcoQuestao();
                
                $rs = $questao->listaQuestoesTop10($id_materia, $id_fonte_vestibular);
                
                if(sizeof($rs) > 0){
                    $count          = 0;
                    
                    foreach($rs as $row){
                        $txt_materias   = "";
                        $in_materias    = "";
                    
                        //Concatena as matérias que fazem relação com a questão carregada
                        if($id_materia <= 0){
                            $tb_classificacao_questao   = new ClassificacaoQuestao();
                            $ret_materias               = $tb_classificacao_questao->getMateriasQuestao($row['ID_BCO_QUESTAO']);
                            
                            $txt_materias   = $ret_materias->txt_materias;
                            $in_materias    = $ret_materias->in_materias;
                        }else{
                            $tb_materias    = new MateriaQuestao();
                            $ret_materias   = $tb_materias->traduzMateria($id_materia);
                            
                            $txt_materias   = $ret_materias[0]['MATERIA'];
                            $in_materias    = $ret_materias[0]['ID_MATERIA'];
                        }
                        
                        $tb_admusuario = new AdmUsuario();
                        $arr_usuarios  = $tb_admusuario->getUsuariosQuestao($in_materias);

                        $ret[$count]['questao']         = $row;
                        $ret[$count]['usuarios']        = $arr_usuarios;
                        $ret[$count]['txt_materias']    = $txt_materias;

                        $count++;
                    }
                }
                
                return $ret;
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal - Top10Model <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function getMateriasSelectBox(){
            try{
                $t_materias = new MateriaQuestao();
                
                return $t_materias->getMateriasSelectBox();
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal - Top10Model <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function getFontesSelectBox(){
            try{
                $t_materias = new FonteVestibular();
                
                return $t_materias->getFontesSelectBox();
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal - Top10Model <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function graficoTop10($data_inicio, $data_final){
            try{
                $ret            = new \stdClass(); //Objeto de retorno
                $ret->status    = FALSE;
                
                if(!$diff = Date::dateDiff($data_final, $data_inicio)){
                    $ret->msg = "Falha ao encotrar diferença de datas";
                    return $ret;
                }
                
                /*if($diff->days > 15){
                    $ret->msg = "A diferença entre as datas não pode ser superior a 15 dias";
                    return $ret;
                }*/
                
                $arr_ret        = array(); //Array com objetos de retorno
                $arr_questoes   = array(); //Array com distinct de questoes
                
                $tb_top10   = new AdmTop10Log();
                $rs         = $tb_top10->getTop10Periodo($data_inicio, $data_final);
                
                echo "Teste : " . $rs->count() . "<br />";
                
                if($rs->count() > 0){
                   foreach($rs as $row){
                        $arr_questoes[$row->getPos1()] = '';
                        $arr_questoes[$row->getPos2()] = '';
                        $arr_questoes[$row->getPos3()] = '';
                        $arr_questoes[$row->getPos4()] = '';
                        $arr_questoes[$row->getPos5()] = '';
                        $arr_questoes[$row->getPos6()] = '';
                        $arr_questoes[$row->getPos7()] = '';
                        $arr_questoes[$row->getPos8()] = '';
                        $arr_questoes[$row->getPos9()] = '';
                        $arr_questoes[$row->getPos10()] = '';

                        $arr_ret[] = $row;
                    } 
                }
                
                
                
                die;

                while($row = mysql_fetch_object($rs, 'Top10log')){
                    $arr_questoes[$row->getPos1()] = '';
                    $arr_questoes[$row->getPos2()] = '';
                    $arr_questoes[$row->getPos3()] = '';
                    $arr_questoes[$row->getPos4()] = '';
                    $arr_questoes[$row->getPos5()] = '';
                    $arr_questoes[$row->getPos6()] = '';
                    $arr_questoes[$row->getPos7()] = '';
                    $arr_questoes[$row->getPos8()] = '';
                    $arr_questoes[$row->getPos9()] = '';
                    $arr_questoes[$row->getPos10()] = '';

                    $arr_ret[] = $row;
                }

                ksort($arr_questoes);
                $arr_questoes = $this->getColor($arr_questoes);

                $ret->status    = true;
                $ret->data      = $arr_ret;
                $ret->colors    = $arr_questoes;

                return $ret;
            }catch(Exception $e){
                echo "Erro buscas relatório TOP 10<br />\n";
                echo $e->getMessage() . "<br />\n";
                echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
                die;
            }
        }
        
        public function alteraUsuarioQuestao($id_questao, $id_usuario){
            try{
                return true;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>
