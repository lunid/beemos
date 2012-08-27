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
                                U.ID_USUARIO,
                                U.NOME,
                                U.EMAIL,
                                U.STATUS,
                                P.DESCRICAO AS 'PERFIL_DESCRICAO'
                            FROM 
                                SPRO_ADM_USUARIO U
                            INNER JOIN
                                SPRO_ADM_PERFIL P ON P.ID_PERFIL = U.ID_PERFIL
                            ";
    
    // set the ouput format to json
    $grid->dataType = 'json';
    // Let the grid create the model
    $grid->setColModel();
    // Set the url from where we obtain the data
    $grid->setUrl('datagrid.php');
    // Set grid caption using the option caption
    $grid->setGridOptions(array(
        "caption"   => "Usuários Cadastrados",
        "rowNum"    => 10,
        "sortname"  => "NOME",
        "rowList"   => array(10,20,50),
        "width"     => "900"
    ));
    
    $grid->toolbarfilter = true;

    // Change some property of the field(s)
    $grid->setColProperty("ID_USUARIO", array("align" => "right", "label" => "Código", "width" => "90", "sortable" => true));
    $grid->setColProperty("NOME", array("label" => "Nome do usuário", "width" => "250", "sortable" => true));
    $grid->setColProperty("EMAIL", array("label" => "E-mail", "width" => "250", "sortable" => true));
    $grid->setColProperty("PERFIL_DESCRICAO", array("align" => "center", "label" => "Perfil", "width" => "110", "search" => false));
    $grid->setColProperty("STATUS", array("align" => "center", "label" => "Status", "width" => "110", "search" => false));
    
    $grid->renderGrid('#grid','#pager',true, null, null, true,true);
?>
