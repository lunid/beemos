<?php
    set_time_limit(0);
    
    require_once $_SERVER['DOCUMENT_ROOT'] . "/interbits_dev/adm/class/questoes.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/interbits_dev/adm/class/materia.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/interbits_dev/adm/class/fonte.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/interbits_dev/adm/class/top10log.php";
    
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
    