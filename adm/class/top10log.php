<?php

if(isset($path)){
    require_once $path . "/class/mysql.php";
}else{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/interbits_dev/class/mysql.php";
}


/**
 * Classe para controle de dados de SPRO_TOP10_LOG
 */
class Top10log{
    private $ID_TOP10_LOG;
    private $DATA_LOG;
    private $ID_MATERIA;
    private $ID_FONTE_VESTIBULAR;
    private $POS_1;
    private $POS_2;
    private $POS_3;
    private $POS_4;
    private $POS_5;
    private $POS_6;
    private $POS_7;
    private $POS_8;
    private $POS_9;
    private $POS_10;
    
    /**
     * Função para iniciar o valor da propriedade $ID_TOP10_LOG
     * 
     * @param int $ID_TOP10_LOG
     */
    public function setIdTop10Log($id){
        $this->ID_TOP10_LOG = (int)$id;
    }
    
    /**
     * Função que retorna o valor da propriedade $ID_TOP10_LOG
     * 
     * @return int $ID_TOP10_LOG
     */
    public function getIdTop10Log(){
        return $this->ID_TOP10_LOG;
    }
    
    /**
     * Função para iniciar o valor da propriedade $DATA_LOG
     * 
     * @param string $DATA_LOG
     */
    public function setDataLog($data){
        $this->DATA_LOG = $data;
    }
    
    /**
     * Função que retorna o valor da propriedade $DATA_LOG
     * 
     * @return string $DATA_LOG
     */
    public function getDataLog(){
        return $this->DATA_LOG;
    }
    
    /**
     * Função para iniciar o valor da propriedade $ID_MATERIA
     * 
     * @param int $ID_MATERIA
     */
    public function setIdMateria($id){
        $this->ID_MATERIA = (int)$id;
    }
    
    /**
     * Função que retorna o valor da propriedade $ID_MATERIA
     * 
     * @return int $ID_MATERIA
     */
    public function getIdMateria(){
        return $this->ID_MATERIA;
    }
    
    /**
     * Função para iniciar o valor da propriedade $ID_FONTE_VESTIBULAR
     * 
     * @param int $ID_FONTE_VESTIBULAR
     */
    public function setIdFonteVestibular($ID_FONTE_VESTIBULAR){
        $this->ID_FONTE_VESTIBULAR = (int)$ID_FONTE_VESTIBULAR;
    }
    
    /**
     * Função que retorna o valor da propriedade $ID_FONTE_VESTIBULAR
     * 
     * @return int $ID_FONTE_VESTIBULAR
     */
    public function getIdFonteVestibular(){
        return $this->ID_FONTE_VESTIBULAR;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_1
     * 
     * @param int $POS_1
     */
    public function setPos1($POS_1){
        $this->POS_1 = (int)$POS_1;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_1
     * 
     * @return int $POS_1
     */
    public function getPos1(){
        return $this->POS_1;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_2
     * 
     * @param int $POS_2
     */
    public function setPos2($POS_2){
        $this->POS_2 = (int)$POS_2;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_2
     * 
     * @return int $POS_2
     */
    public function getPos2(){
        return $this->POS_2;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_3
     * 
     * @param int $POS_3
     */
    public function setPos3($POS_3){
        $this->POS_3 = (int)$POS_3;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_3
     * 
     * @return int $POS_3
     */
    public function getPos3(){
        return $this->POS_3;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_4
     * 
     * @param int $POS_4
     */
    public function setPos4($POS_4){
        $this->POS_4 = (int)$POS_4;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_4
     * 
     * @return int $POS_4
     */
    public function getPos4(){
        return $this->POS_4;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_5
     * 
     * @param int $POS_5
     */
    public function setPos5($POS_5){
        $this->POS_5 = (int)$POS_5;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_5
     * 
     * @return int $POS_5
     */
    public function getPos5(){
        return $this->POS_5;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_6
     * 
     * @param int $POS_6
     */
    public function setPos6($POS_6){
        $this->POS_6 = (int)$POS_6;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_6
     * 
     * @return int $POS_6
     */
    public function getPos6(){
        return $this->POS_6;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_7
     * 
     * @param int $POS_7
     */
    public function setPos7($POS_7){
        $this->POS_7 = (int)$POS_7;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_7
     * 
     * @return int $POS_7
     */
    public function getPos7(){
        return $this->POS_7;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_8
     * 
     * @param int $POS_8
     */
    public function setPos8($POS_8){
        $this->POS_8 = (int)$POS_8;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_8
     * 
     * @return int $POS_8
     */
    public function getPos8(){
        return $this->POS_8;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_9
     * 
     * @param int $POS_9
     */
    public function setPos9($POS_9){
        $this->POS_9 = (int)$POS_9;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_9
     * 
     * @return int $POS_9
     */
    public function getPos9(){
        return $this->POS_9;
    }
    
    /**
     * Função para iniciar o valor da propriedade $POS_10
     * 
     * @param int $POS_10
     */
    public function setPos10($POS_10){
        $this->POS_10 = (int)$POS_10;
    }
    
    /**
     * Função que retorna o valor da propriedade $POS_10
     * 
     * @return int $POS_10
     */
    public function getPos10(){
        return $this->POS_10;
    }
    
    public function salvaLog($data = null){
        try{
            $ret    = new stdClass(); //Classe de retorno
            $where  = "";
            
            if($this->ID_MATERIA > 0){
                $where = " AND ID_MATERIA = {$this->ID_MATERIA} ";
            }
            
            if($this->ID_FONTE_VESTIBULAR > 0){
                $where = " AND ID_FONTE_VESTIBULAR = {$this->ID_FONTE_VESTIBULAR} ";
            }
            
            $sql_ver = "SELECT 
                            ID_TOP10_LOG 
                        FROM 
                            SPRO_ADM_TOP10_LOG 
                        WHERE 
                            DATE(DATA_LOG) = DATE(".($data != null ? $data : 'NOW()').")
                            {$where}
                        LIMIT
                            1
                        ;";
            
            MySQL::connect();
            $rs_ver = MySQL::executeQuery($sql_ver);
            
            if(mysql_num_rows($rs_ver) == 1){
                $ret->status = false;
                $ret->msg = "O log da data (".date("d/m/Y").") já existe!";
                
                if($this->ID_MATERIA > 0){
                    $ret->msg .= " Matéria: {$this->ID_MATERIA}";
                }
                
                if($this->ID_FONTE_VESTIBULAR > 0){
                    $ret->msg .= " Fonte: {$this->ID_FONTE_VESTIBULAR}";
                }
                
                return $ret;
            }
            
            $sql = "INSERT INTO
                        SPRO_ADM_TOP10_LOG
                        (
                            DATA_LOG,
                            ID_MATERIA,
                            ID_FONTE_VESTIBULAR,
                            POS_1,
                            POS_2,
                            POS_3,
                            POS_4,
                            POS_5,
                            POS_6,
                            POS_7,
                            POS_8,
                            POS_9,
                            POS_10
                        )
                        VALUES
                        (
                            ".($data != null ? "'" . $data . "'" : 'NOW()').",
                            '{$this->ID_MATERIA}',
                            '{$this->ID_FONTE_VESTIBULAR}',
                            {$this->POS_1},
                            {$this->POS_2},
                            {$this->POS_3},
                            {$this->POS_4},
                            {$this->POS_5},
                            {$this->POS_6},
                            {$this->POS_7},
                            {$this->POS_8},
                            {$this->POS_9},
                            {$this->POS_10}
                        )
                    ;";
                            
            MySQL::executeQuery($sql);              
              
            $ret->status    = true;
            $ret->msg       = "LOG gravado com sucesso!";
            
            if($this->ID_MATERIA > 0){
                $ret->msg .= " Matéria: {$this->ID_MATERIA}";
            }
            
            if($this->ID_FONTE_VESTIBULAR > 0){
                $ret->msg .= " Fonte: {$this->ID_FONTE_VESTIBULAR}";
            }
                
            return $ret;
        }catch(Exception $e){
            echo "Erro salvar LOG TOP 10<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    public function relatorioTop10($data_inicio, $data_final){
        try{
            $ret            = new stdClass(); //Objeto de retorno
            $arr_ret        = array(); //Array com objetos de retorno
            $arr_questoes   = array(); //Array com distinct de questoes
            
            MySQL::connect();
            
            $sql    = "SELECT DATEDIFF('{$data_final}', '{$data_inicio}') as 'DIFF';";
            $rs     = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                throw new Exception("Nenhuma diferença entre datas");
            }
            
            $diff = mysql_result($rs, 0, 'DIFF');
            
            if($diff > 60){
                $ret->status    = false;
                $ret->msg       = "O Período de pesquisa não pode exceder 60 dias";
                
                return $ret;
            }
            
            $sql = "SELECT
                        *
                    FROM
                        SPRO_ADM_TOP10_LOG
                    WHERE
                        DATE(DATA_LOG) BETWEEN '{$data_inicio}' AND '{$data_final}'
                    AND
                        ID_MATERIA = 0
                    AND
                        ID_FONTE_VESTIBULAR = 0
                    ORDER BY
                        DATA_LOG
                    ;";
                        
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                $ret->status    = false;
                $ret->msg       = "Nenhum resultado encontrado para o período selecionado";
                
                return $ret;
            }
            
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
    
    public function getColor($arr){
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
            echo "Erro buscas cores TOP 10<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
    
    function random_color(){
        mt_srand((double)microtime()*1000000);
        $c = '';
        while(strlen($c)<6){
            $c .= sprintf("%02X", mt_rand(0, 255));
        }
        return $c;
    }
    
    public function consultaHistorico(){
        try{
            $sql = "SELECT 
                        ITENS_SELECIONADOS, 
                        DATE(DATA_REGISTRO) as DATA_REGISTRO
                    FROM 
                        SPRO_HISTORICO_GERADOC 
                    -- WHERE
                        -- DATE(DATA_REGISTRO) BETWEEN '2012-09-01' AND '2012-09-05'
                    ORDER BY 
                        DATA_REGISTRO ASC
                    ;";
            
            MySQL::connect();
            $rs = MySQL::executeQuery($sql);
            
            if(mysql_num_rows($rs) <= 0){
                return null;
            }
            
            return $rs;
        }catch(Exception $e){
            echo "Erro buscas cores TOP 10<br />\n";
            echo $e->getMessage() . "<br />\n";
            echo $e->getFile() . " - Linha: " . $e->getLine() ."<br />\n";
            die;
        }
    }
}
?>
