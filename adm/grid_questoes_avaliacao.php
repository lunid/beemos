<?php
    require_once '../js/libs/jqgrid/jq-config.php';
    // include the jqGrid Class
    require_once "../js/libs/jqgrid/php/jqGrid.php";
    // include the PDO driver class
    require_once "../js/libs/jqgrid/php/jqGridPdo.php";
    
    require_once "../class/mysql.php";
    
    // Connection to the server
    $conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
    // Tell the db that we use utf-8
    $conn->query("SET NAMES utf8");
    
    // Create the jqGrid instance
    $grid = new jqGridRender($conn);
    
    $query = "SELECT @rownum:=@rownum+1 as 'POSICAO', A.ID_BCO_QUESTAO, A.TOTAL_USO FROM (
                                SELECT
                                    @rownum:=0, 
                                    Q.ID_BCO_QUESTAO,
                                    Q.TOTAL_USO
                                FROM 
                                    SPRO_BCO_QUESTAO Q
                                INNER JOIN
                                    SPRO_CLASSIFICACAO_QUESTAO CQ ON CQ.ID_BCO_QUESTAO = Q.ID_BCO_QUESTAO
                                ORDER BY
                                    Q.TOTAL_USO DESC
                                LIMIT
                                    10
                                 ) AS A   ;
                            ";
    
    MySQL::connect();
    $rs     = MySQL::executeQuery($query);
    $model  = array();
    
    if(mysql_num_rows($rs) > 0){
        while($row = mysql_fetch_array($rs)){
            $grid->addRowData($row);
        }
    }
    
    // set the ouput format to json
    $grid->dataType = 'json';
    
    $grid->setPrimaryKeyId('ID_USUARIO'); 
    
    // Let the grid create the model
    $grid->setColModel(
        array(
            array(
                'name'  => 'POSITION', 
                'label' => 'Posição'
            ),
            array(
                'name'  => 'ID_BCO_QUESTAO', 
                'label' => 'Questão'
            ),
            array(
                'name'  => 'TOTAL_USO', 
                'label' => 'Total / Uso'
            )
        )
    );
    
    // Set the url from where we obtain the data
    $grid->setUrl('grid_questoes_avaliacao.php');
    
    // we add actions column at first place 
//    $grid->addCol(array(
//        "name"          => "POSITION",
//        "label"         => "Posição",
//        "editable"      => false,
//        "sortable"      => false,
//        "resizable"     => false,
//        "fixed"         => true,
//        "width"         => 60,
//        "data"          => '1'
//    ), "first"); 
    
    // Set grid caption using the option caption
    $grid->setGridOptions(array(
        "rownumbers"=>true, 
        "rownumWidth"=>35, 
        "caption"   => "Quastões para avaliação",
        "rowNum"    => 10,
        "rowList"   => array(10,20,50),
        "width"     => "900",
        "height"    => "600",
    ));
    
    //$grid->toolbarfilter = true;
    
    // Change some property of the field(s)
    //$grid->setColProperty("POSICAO", array("align" => "right", "label" => "Posição", "width" => "20", "sortable" => true));
    //$grid->setColProperty("ID_BCO_QUESTAO", array("align" => "right", "label" => "Código", "width" => "20", "sortable" => false));
    //$grid->setColProperty("TOTAL_USO", array("align" => "right", "label" => "Total / Uso", "width" => "80", "sortable" => false));
    
    // Enable navigator
    //$grid->navigator = true;
    
    // Disable some actions
    //$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search" => false));
    
    $grid->customFunc = "formataDados";
    
    $grid->renderGrid('#grid','#pager',true, null, null, true,true);
    
    function formataDados($gdata, $conn){
        foreach($gdata->rows as $row){
            $row->TOTAL_USO = number_format($row->TOTAL_USO, 0, '.', '.');
        }
        
        return $gdata;
    }
?>
