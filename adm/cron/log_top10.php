<?php
    set_time_limit(0);
    
    $dir    = dir(__DIR__);
    $path   = str_replace("adm\cron", "", $dir->path);
    $path   = str_replace("adm/cron", "", $path);
    
    require_once $path . "adm/class/questoes.php";
    require_once $path . "adm/class/materia.php";
    require_once $path . "adm/class/fonte.php";
    require_once $path . "adm/class/top10log.php";
    
    $sql = "SELECT DATA FROM SPRO_QUESTOES_SELECIONADAS_TOTAL GROUP BY DATA ORDER BY DATA DESC;";
    
    $rs = MySQL::executeQuery($sql);
    
    if(mysql_num_rows($rs) > 0){
        while($row = mysql_fetch_object($rs)){
            $sql = "SELECT 
                        ID_BCO_QUESTAO,
                        TOTAL_USO AS QTD
                    FROM
                        SPRO_QUESTOES_SELECIONADAS_TOTAL
                    WHERE
                        DATA <= '{$row->DATA}'
                    GROUP BY 
                        ID_BCO_QUESTAO
                    ORDER BY 
                        2 DESC
                    LIMIT 
                        10
                ;";
            
                       
                        
            $rs_data = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs_data) > 0){
                $top10 = new Top10log();
        
                $pos1   = @mysql_result($rs_data, 0, 'ID_BCO_QUESTAO');
                $pos2   = @mysql_result($rs_data, 1, 'ID_BCO_QUESTAO');
                $pos3   = @mysql_result($rs_data, 2, 'ID_BCO_QUESTAO');
                $pos4   = @mysql_result($rs_data, 3, 'ID_BCO_QUESTAO');
                $pos5   = @mysql_result($rs_data, 4, 'ID_BCO_QUESTAO');
                $pos6   = @mysql_result($rs_data, 5, 'ID_BCO_QUESTAO');
                $pos7   = @mysql_result($rs_data, 6, 'ID_BCO_QUESTAO');
                $pos8   = @mysql_result($rs_data, 7, 'ID_BCO_QUESTAO');
                $pos9   = @mysql_result($rs_data, 8, 'ID_BCO_QUESTAO');
                $pos10  = @mysql_result($rs_data, 9, 'ID_BCO_QUESTAO');
                
                $top10->setPos1($pos1 > 0 ? $pos1 : 0);
                $top10->setPos2($pos2 > 0 ? $pos2 : 0);
                $top10->setPos3($pos3 > 0 ? $pos3 : 0);
                $top10->setPos4($pos4 > 0 ? $pos4 : 0);
                $top10->setPos5($pos5 > 0 ? $pos5 : 0);
                $top10->setPos6($pos6 > 0 ? $pos6 : 0);
                $top10->setPos7($pos7 > 0 ? $pos7 : 0);
                $top10->setPos8($pos8 > 0 ? $pos8 : 0);
                $top10->setPos9($pos9 > 0 ? $pos9 : 0);
                $top10->setPos10($pos10 > 0 ? $pos10 : 0);
                
                $ret = $top10->salvaLog($row->DATA);

                echo $ret->msg . "<br />\n<br />\n";
            }
        }
    }
    
    $questoes = new Questoes();
    
    $rs = $questoes->listaQuestoesTop10Materia();
    
    if(sizeof($rs) > 0){
        $top10 = new Top10log();
        
        $top10->setPos1(isset($rs[0]) ? $rs[0]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos2(isset($rs[1]) ? $rs[1]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos3(isset($rs[2]) ? $rs[2]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos4(isset($rs[3]) ? $rs[3]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos5(isset($rs[4]) ? $rs[4]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos6(isset($rs[5]) ? $rs[5]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos7(isset($rs[6]) ? $rs[6]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos8(isset($rs[7]) ? $rs[7]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos9(isset($rs[8]) ? $rs[8]['questao']->getIdBcoQuestao() : 0);
        $top10->setPos10(isset($rs[9]) ? $rs[9]['questao']->getIdBcoQuestao() : 0);
        
        $ret = $top10->salvaLog();
        
        echo $ret->msg . "<br />\n<br />\n";
    }else{
        echo "Nenhum resultado encontrado para TOP 10";
    }
    
    $materia    = new Materia();
    $materias   = $materia->listaMaterias();
    
    if(sizeof($materias) > 0){
        foreach($materias as $row){
            $rs = $questoes->listaQuestoesTop10Materia($row->getIdMateria());
            
            if(sizeof($rs) > 0){
                $top10 = new Top10log();

                $top10->setIdMateria($row->getIdMateria());

                $top10->setPos1(isset($rs[0]) ? $rs[0]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos2(isset($rs[1]) ? $rs[1]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos3(isset($rs[2]) ? $rs[2]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos4(isset($rs[3]) ? $rs[3]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos5(isset($rs[4]) ? $rs[4]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos6(isset($rs[5]) ? $rs[5]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos7(isset($rs[6]) ? $rs[6]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos8(isset($rs[7]) ? $rs[7]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos9(isset($rs[8]) ? $rs[9]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos10(isset($rs[9]) ? $rs[9]['questao']->getIdBcoQuestao() : 0);

                $ret = $top10->salvaLog();

                echo $ret->msg . "<br />\n<br />\n";
            }else{
                echo "Nenhum TOP 10 encontrado para matéria {$row->getIdMateria()}<br />\n<br />\n";
            }
        }
    }else{
        echo "Nenhuma matéria encontrada para TOP 10";
    }
    
    $fonte  = new Fonte();
    $fontes = $fonte->listaFontes();
    
    if(sizeof($fontes) > 0){
        foreach($fontes as $row){
            $rs = $questoes->listaQuestoesTop10Materia(0, $row->getIdFonteVestibular());
            
            if(sizeof($rs) > 0){
//                echo "<pre style='color:#FF0000;'>";
//                print_r($rs);
//                echo "</pre>";
                
                $top10 = new Top10log();

                $top10->setIdFonteVestibular($row->getIdFonteVestibular());

                $top10->setPos1(isset($rs[0]) ? $rs[0]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos2(isset($rs[1]) ? $rs[1]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos3(isset($rs[2]) ? $rs[2]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos4(isset($rs[3]) ? $rs[3]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos5(isset($rs[4]) ? $rs[4]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos6(isset($rs[5]) ? $rs[5]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos7(isset($rs[6]) ? $rs[6]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos8(isset($rs[7]) ? $rs[7]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos9(isset($rs[8]) ? $rs[8]['questao']->getIdBcoQuestao() : 0);
                $top10->setPos10(isset($rs[9]) ? $rs[9]['questao']->getIdBcoQuestao() : 0);
                
                $ret = $top10->salvaLog();

                echo $ret->msg . "<br />\n<br />\n";
            }else{
                echo "Nenhum TOP 10 encontrado para Fonte {$row->getIdFonteVestibular()}<br />\n<br />\n";
            }
        }
    }else{
        echo "Nenhuma fonte encontrada para TOP 10";
    }
?>
    