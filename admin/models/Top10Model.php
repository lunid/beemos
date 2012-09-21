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
                throw $e;
            }
        }
        
        public function getMateriasSelectBox(){
            try{
                $t_materias = new MateriaQuestao();
                
                return $t_materias->getMateriasSelectBox();
            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function getFontesSelectBox(){
            try{
                $t_materias = new FonteVestibular();
                
                return $t_materias->getFontesSelectBox();
            }catch(Exception $e){
                throw $e;
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
                
                $tb_top10   = new AdmTop10Log();
                $rs         = $tb_top10->getTop10Periodo($data_inicio, $data_final);
                
                if($rs->count() > 0){
                   $arr_ret        = array(); //Array com objetos de retorno
                   $arrQuestoes    = array(); //Array com distinct de questoes
                   $arrQuestoesTop = array(); //Array com questões mais utilizadas
                
                   foreach($rs as $row){
                        $arrQuestoes[$row->POS_1]   = '';
                        $arrQuestoes[$row->POS_2]   = '';
                        $arrQuestoes[$row->POS_3]   = '';
                        $arrQuestoes[$row->POS_4]   = '';
                        $arrQuestoes[$row->POS_5]   = '';
                        $arrQuestoes[$row->POS_6]   = '';
                        $arrQuestoes[$row->POS_7]   = '';
                        $arrQuestoes[$row->POS_8]   = '';
                        $arrQuestoes[$row->POS_9]   = '';
                        $arrQuestoes[$row->POS_10]  = '';

                        $arr_ret[] = $row;
                    } 
                    
                    ksort($arrQuestoes);
                    $arrQuestoes = $this->getColor($arrQuestoes);

                    foreach ($arr_ret as $row) { 
                        isset($arrQuestoesTop[$row->POS_1]) ? $arrQuestoesTop[$row->POS_1]++ : $arrQuestoesTop[$row->POS_1] = 1;
                        isset($arrQuestoesTop[$row->POS_2]) ? $arrQuestoesTop[$row->POS_2]++ : $arrQuestoesTop[$row->POS_2] = 1;
                        isset($arrQuestoesTop[$row->POS_3]) ? $arrQuestoesTop[$row->POS_3]++ : $arrQuestoesTop[$row->POS_3] = 1;
                        isset($arrQuestoesTop[$row->POS_4]) ? $arrQuestoesTop[$row->POS_4]++ : $arrQuestoesTop[$row->POS_4] = 1;
                        isset($arrQuestoesTop[$row->POS_5]) ? $arrQuestoesTop[$row->POS_5]++ : $arrQuestoesTop[$row->POS_5] = 1;
                        isset($arrQuestoesTop[$row->POS_6]) ? $arrQuestoesTop[$row->POS_6]++ : $arrQuestoesTop[$row->POS_6] = 1;
                        isset($arrQuestoesTop[$row->POS_7]) ? $arrQuestoesTop[$row->POS_7]++ : $arrQuestoesTop[$row->POS_7] = 1;
                        isset($arrQuestoesTop[$row->POS_8]) ? $arrQuestoesTop[$row->POS_8]++ : $arrQuestoesTop[$row->POS_8] = 1;
                        isset($arrQuestoesTop[$row->POS_9]) ? $arrQuestoesTop[$row->POS_9]++ : $arrQuestoesTop[$row->POS_9] = 1;
                        isset($arrQuestoesTop[$row->POS_10]) ? $arrQuestoesTop[$row->POS_10]++ : $arrQuestoesTop[$row->POS_10] = 1;
                    }

                    asort($arrQuestoesTop);

                    $valid = (sizeof($arrQuestoesTop) - 10);
                    $total = (sizeof($arr_ret) * 10);

                    $count = 0;     

                    $tmp_arrQuestoesTop     = array();

                    foreach($arrQuestoesTop as $key => $value){
                        if($count >= $valid){
                            $perc = (($value / $total) * 100);
                            $perc = round($perc, 1);

                            $tmp_arrQuestoesTop[] = array("questao" => $key, "perc" => $perc);
                        }
                        $count++;
                    }

                    $arrQuestoesTop = array();

                    for($i=9; $i >= 0; $i--){
                        $arrQuestoesTop[] = $tmp_arrQuestoesTop[$i];
                    }

                    $ret->status    = true;
                    $ret->data      = $arr_ret;
                    $ret->colors    = $arrQuestoes;
                    $ret->top       = $arrQuestoesTop;
                }
                
                $ret->msg = "Nenhum resultado encontrado";
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        private function getColor($arr){
            try{
                foreach($arr as &$row){
                    $sel = $this->random_color();

                    if(!array_search($sel, $arr)){
                        $row = $sel;
                    }else{
                        while(array_search($sel, $arr)){
                            $sel = $this->random_color();
                        }

                        $row = $sel;
                    }
                }

                return $arr;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        private function random_color(){
            mt_srand((double)microtime()*1000000);
            $c = '';
            while(strlen($c)<6){
                $c .= sprintf("%02X", mt_rand(0, 255));
            }
            return $c;
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
