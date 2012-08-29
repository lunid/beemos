<?php
    require_once '../js/libs/jqgrid/jq-config.php';
    // include the jqGrid Class
    require_once "../js/libs/jqgrid/php/jqGrid.php";
    // include the PDO driver class
    require_once "../js/libs/jqgrid/php/jqGridPdo.php";
    // Connection to the server
    $conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
    // Tell the db that we use utf-8
    $conn->query("SET NAMES utf8");
    
    // Create the jqGrid instance
    $grid = new jqGridRender($conn);
    // Write the SQL Query
    // We suppose that mytable exists in your database
    $grid->SelectCommand = "SELECT
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
                            ;";
    
    // set the ouput format to json
    $grid->dataType = 'json';
    
    $grid->setPrimaryKeyId('ID_USUARIO'); 
    
    // Let the grid create the model
    $grid->setColModel();
    
    // Set the url from where we obtain the data
    $grid->setUrl('grid_questoes_avaliacao.php');
    
    // we add actions column at first place 
    $grid->addCol(array(
        "name"          => "Posição",
        "editable"      => false,
        "sortable"      => false,
        "resizable"     => false,
        "fixed"         => true,
        "width"         => 60
    ), "first"); 
    
    // Set grid caption using the option caption
    $grid->setGridOptions(array(
        "caption"   => "Quastões para avaliação",
        "rowNum"    => 10,
        "sortname"  => "ID_BCO_QUESTAO",
        "rowList"   => array(10,20,50),
        "width"     => "900",
        "height"    => "600",
    ));
    
    //$grid->toolbarfilter = true;
    
    // Change some property of the field(s)
    $grid->setColProperty("ID_BCO_QUESTAO", array("align" => "right", "label" => "Código", "width" => "20", "sortable" => true));
    $grid->setColProperty("TOTAL_USO", array("label" => "Questão", "width" => "80", "sortable" => true));
    
    // Enable navigator
    //$grid->navigator = true;
    
    // Disable some actions
    //$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search" => false));
    
    $grid->renderGrid('#grid','#pager',true, null, null, true,true);
?>
