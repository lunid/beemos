<?php
    if(isset($_POST['oper'])){
        
    }
    
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
                                P.DESCRICAO AS 'ID_PERFIL'
                            FROM 
                                SPRO_ADM_USUARIO U
                            INNER JOIN
                                SPRO_ADM_PERFIL P ON P.ID_PERFIL = U.ID_PERFIL
                            ";
    
    // set the ouput format to json
    $grid->dataType = 'json';
    
    $grid->setPrimaryKeyId('ID_USUARIO'); 
    
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
    $grid->setColProperty("ID_USUARIO", array("align" => "right", "label" => "Código", "width" => "90", "sortable" => true, "editable" => false));
    $grid->setColProperty("NOME", array("label" => "Nome do usuário", "width" => "250", "sortable" => true, "editable" => true));
    $grid->setColProperty("EMAIL", array("label" => "E-mail", "width" => "250", "sortable" => true, "editable" => false));
    $grid->setColProperty("ID_PERFIL", array("align" => "center", "label" => "Perfil", "width" => "110", "search" => false, "editable" => true, "edittype" => 'select', "editoptions" => array("dataUrl" => "select_perfis.php")));
    $grid->setColProperty("STATUS", array("align" => "center", "label" => "Status", "width" => "110", "search" => false, "editable" => false, "formatter" => "js:formataStatus"));
    
    // Enable navigator
    $grid->navigator = true;
    
    // Disable some actions
    $grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search" => false));
    
    // add a custom button via the build in callGridMethod
    // note the js: before the function
    /*$buttonoptions = array("#pager",
        array(
            "caption" => "Novo Usuário&nbsp;", 
            "onClickButton" => "js:function(){ window.location = 'salvar_usuario.php'; }"
        ),
    );
    
    $grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions); 
    
    $buttonoptions = array("#pager",
        array(
            "caption" => "Editar Usuário&nbsp;", 
            "onClickButton" => "js:function(){ var id = parseInt(jQuery('#grid').jqGrid('getGridParam','selrow')); if(id==0 || isNaN(id)){ alert('Selecione um usuário'); }else{ window.location = 'salvar_usuario.php?id_usuario=' + id; } }"
        ),
    );
    
    $grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions); 
    */
    
    
    // and just enable the inline
    /*$inlineAdd = function(){    
        alert("oneditfunc");      
    }*/
    
    $grid->inlineNav = true;
    
    // We can put JS from php
    $custom = "function formataStatus(cellValue) {
                switch(cellValue){
                    case 'A':
                        return 'Ativo';
                    case 'I':
                        return 'Inativo';
                    case 'B':
                        return 'Bloqueado';
                    default:
                        return cellValue;
                }
                return cellValue
              }";
    
    $grid->setJSCode($custom);
        
    $grid->renderGrid('#grid','#pager',true, null, null, true,true);
?>
