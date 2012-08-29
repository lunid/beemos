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
    
    //Carrega usuário
    $usuario = unserialize($_SESSION['ADM_USUARIO']);
    $rs      = $usuario->carregaMateriasUsuarioAvaliacao();
    
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
    </head>
    <body>
        <h1>Matérias a serem avaliadas:</h1>
        <? 
            if(sizeof($rs) > 0){ 
        ?>
        <ul>
            <?
                foreach($rs as $row){
            ?>
            <li><a href="top10_avaliar_questoes.php?id_materia=<?=$row->ID_MATERIA?>"><?=$row->MATERIA?></a></li>
            <?
                }
            ?>
        </ul>
        <? 
            }else{ 
        ?>
            <h1>Você não possui nenhuma indicação de avaliação no momento</h1>
        <? 
            } 
        ?>
    </body>
</html>
