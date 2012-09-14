<?php
    set_time_limit(0);
    
    $dir    = dir(__DIR__);
    $path   = str_replace("adm\cron", "", $dir->path);
    $path   = str_replace("adm/cron", "", $path);
    
    //die($path . "adm/class/top10log.php\n\n");
    
    require_once $path . "adm/class/top10log.php";
    
    $top10 = new Top10log();
    
    $rs = $top10->consultaHistorico();
    
    if($rs != null){
        $sql = "TRUNCATE TABLE SPRO_QUESTOES_SELECIONADAS;";
        MySQL::executeQuery($sql);
        
        $tam            = mysql_num_rows($rs);
        $count          = 1;
        
        echo($tam . " - Linhas a serem lidas\n\n");
        
        while($row = mysql_fetch_object($rs)){
            $questoes = explode(",", $row->ITENS_SELECIONADOS);
            
            foreach($questoes as $questao){
                if($questao > 0){
                    $sql = "INSERT INTO 
                            SPRO_QUESTOES_SELECIONADAS
                            (
                                ID_BCO_QUESTAO,
                                DATA
                            )
                            VALUES
                            (
                                {$questao},
                                '{$row->DATA_REGISTRO}'
                            )
                        ;";
                
                    MySQL::executeQuery($sql);
                }
            }
            
            echo $count . " - ";
            $count++;
        }
        
        echo "\n\n >>>>>>>>>>>>> Incicando a segunda etapa <<<<<<<<<<<<<<<<<\n\n";
        
        $sql = "SELECT DATA FROM SPRO_QUESTOES_SELECIONADAS GROUP BY DATA ORDER BY DATA;";
        $rs  = MySQL::executeQuery($sql);
        
        if(mysql_num_rows($rs) > 0){
            while($row = mysql_fetch_object($rs)){
               $sql = "INSERT INTO SPRO_QUESTOES_SELECIONADAS_TOTAL
                       SELECT 
                            ID_BCO_QUESTAO, 
                            DATA,
                            COUNT(1) as QTD 
                       FROM 
                            SPRO_QUESTOES_SELECIONADAS
                       WHERE 
                            DATA <= '{$row->DATA}'
                       GROUP BY 
                            ID_BCO_QUESTAO
                       ORDER BY 
                            2 
                       DESC
                ;";
                            
                MySQL::executeQuery($sql);            
                
                echo "Data {$row->DATA} inserida! - ";
            }
        }
        
        echo "\n\n";
    }
    
    echo "FIM";
?>
