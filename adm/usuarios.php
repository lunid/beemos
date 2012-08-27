<?
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
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits | Usuários</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="../js/libs/jquery_171.js"></script>
        
        <link rel="stylesheet" type="text/css" media="screen" href="../js/libs/jqgrid/themes/redmond/jquery-ui-custom.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../js/libs/jqgrid/themes/ui.jqgrid.css" />

        <script src="../js/libs/jqgrid/js/i18n/grid.locale-pt-br.js" type="text/javascript"></script>
        <script src="../js/libs/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
    </head>
    <body>
        <h1>
            Usuários
        </h1>
        <table align="center" style="width:920px;">
            <tr>
                <td>
                    <a href="salvar_usuario.php">NOVO</a>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <?php include 'datagrid.php'; ?>
                </td>
            </tr>
        </table>
    </body>
</html>
