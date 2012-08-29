<?
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//

    session_start();
    
    require_once 'class/usuario.php';
    
    if(isset($_SESSION['ADM_USUARIO'])){
        //Valida permissão de acesso ao Usuário
        $ret = Usuario::validaAcesso(unserialize($_SESSION['ADM_USUARIO']));
        
        if(!$ret->status){
            //redirecionando para efetuar login
            header("Location: index.php");  
        }
    }else{
        //redirecionando para efetuar login
        header("Location: index.php");
    }
    
    //Armazena instância do objeto usuário
    $usuario = unserialize($_SESSION['ADM_USUARIO']);
    
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits | Avaliar Questões</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="../js/libs/jquery_171.js"></script>
        
        <link rel="stylesheet" type="text/css" media="screen" href="../js/libs/jqgrid/themes/redmond/jquery-ui-custom.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../js/libs/jqgrid/themes/ui.jqgrid.css" />

        <script src="../js/libs/jqgrid/js/i18n/grid.locale-pt-br.js" type="text/javascript"></script>
        <script src="../js/libs/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
    </head>
    <body>
        <h1>Questões a serem avaliadas</h1>
        <? 
            if($usuario->validaUsuarioAvaliacao(@(int)$_GET['id_materia'])){ 
                include 'grid_questoes_avaliacao.php';
            }else{ 
        ?>
            <h1>Você não possui permissão para avaliar as questões desta matéria</h1>
        <? 
            } 
        ?>
    </body>
</html>
